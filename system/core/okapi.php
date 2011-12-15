<?php 
// require the base files (will do it this way (manually) for now, might add this to the autoload function later
require_once(BASE_PATH . '/system/core/load.php');
require_once(BASE_PATH . '/system/core/model.php');
require_once(BASE_PATH . '/system/core/controller.php');
require_once(BASE_PATH . '/system/core/welcome.php');
// the almighty core class
class Okapi {
	public $load;
	private static $instance;
	public $config;

	private function __construct() {
		// boot up the autoloader
		spl_autoload_register(array($this, 'autoloader'));
		$this->instance = &$this;
		$this->load = new Load();
		$this->load_config();
	}

	public static function singleton()  
	{  
		if( !isset( self::$instance ) )  
		{  
			$obj = __CLASS__;  
			self::$instance = new $obj;  
		}  
		return self::$instance;  
	}

	public function dispatch() {
		$url = trim($_GET['_url'], '/');
		list($controller, $action, $parameters) = explode('/', $url, 3);

		$controller = ucfirst(strtolower($controller));
		$action = strtolower($action);

		// check if there really was a controller specified in the url
		// if not, then show welcome-controller.. OR even better,
		// check config for default controller! (maybe later)
		if (strlen(trim($controller))>0) {
			// woho! we've got a controller specified, let's try and load it!
			// first we check if the controller exists
			if (class_exists($controller)) {
				$this->$controller = new $controller(); 
			} else { // if not, then die!, though we should probably show a 404 instead.. well well, all in good time...
				die('Fuuuuuuu, no controller named "' . $controller . '" was found.');
			}
			// now that the controller is loaded, lets see if any action was specified
			if (strlen(trim($action))>0) {
				// we have a small thing called private actions
				// private actions are really only methods in a class that
				// should not be callable by url, we mark these 
				// with an underscore like this: _secret_method
				// ONE more thing, we do NOT check paramters here, KISS you know...
				// Let the controller action take care of that!
				// This is good because we then give the controller
				// freedom to specify exactly how the parameters should be passed/used,
				// it could for example be in the format p1=val&p2=val2 
				// or p1/val/p2/val2 or p1=val;p2=val2 and so on...
				// we are after all only taking a quick look at the request here to
				// get the ball rolling (and hopefully in the right direction too).
				if (substr($action, 0, 1) == '_') {
					die('crude error reporting in action: ' . $action . ' called was deemed private and thus was NOT called!');
				} else {
					// call the method in the controller with possible parameters
					// all methods should be lowercase!
					// But first! Let's check if the method really exists in that particular controller
					if (method_exists($this->$controller, $action)) {
						$this->$controller->$action($parameters);
					} else {
						$this->$controller->index($parameters); // if the method did not exist we send the user to the index method, with the parameters
					}
				}
			} else {
				// no action specified, using the index-method, 
				// which is mandatory for ALL controllers to have
				$this->$controller->index();
			}
		} else {
			// try and fetch the default controller from config
			// if that fails, fall back th the welcome-controller in the core files.
			if (isset($this->config['default_controller']) && class_exists($this->config['default_controller'])) {
				$this->Controller = new $this->config['default_controller'];
				$this->Controller->index();
			} else {
				$this->Controller = new Welcome();
				$this->Controller->index();
			}
		}
	}

	// take care of autoloading missing classes
	private function autoloader($className) {
		// make sure className is tolower, since all filenames should be lowercase
		$className = strtolower($className);

		//compose file names for all possible paths...
		$model_file = BASE_PATH . '/application/models/' . $className . '.php';
		$controller_file = BASE_PATH . '/application/controllers/' . $className . '.php';

		//fetch file
		if (file_exists($controller_file)) {
			//get file
			include_once($controller_file);
		} else {
			if (file_exists($model_file)) {

				include_once($model_file);
			} else {
				//file does not exist!
				die("File '$filename' containing class '$className' not found.");
			}
		}

		// check and see if the include did declare the class
		if (!class_exists($className, false)) {
			trigger_error("Unable to load class: $className", E_USER_WARNING);
		}
	}

	public function load_config() {
		include_once(BASE_PATH . '/application/config/config.php'); 
		$this->config = $config;
		unset($config); // remove the config array var from the config.php file...

		if (isset($this->config['environment']) && strtolower($this->config['environment']) == "dev") {
			error_reporting(-1);
		}
	}

	public function dump_okapi() {
		echo '<pre>';
		print_r($this); // object debugging ;)
		echo '</pre';
	}
}

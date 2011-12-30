<?php 
// require the base files (will do it this way (manually) for now, might add this to the autoload function later.. TODO: move to a boostrap.php file...
require_once(BASE_PATH . '/system/core/load.php');
require_once(BASE_PATH . '/system/core/model.php');
require_once(BASE_PATH . '/system/core/controller.php');
require_once(BASE_PATH . '/system/core/welcome.php');
require_once(BASE_PATH . '/system/core/common.php');

// the almighty core class
class Okapi {
	private static $instance;
	public $config;

	private function __construct() {
		// boot up the autoloader
		spl_autoload_register(array($this, 'autoloader'));
		$this->instance = &$this;
		$this->load_config();
		if (!isset($this->config['theme']) && !is_dir(BASE_PATH . '/application/themes/' . $this->config['theme'])) {
			echo ' faulty theme settings, default themes is used...';
			$this->config['theme'] = 'default';
		}
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

	public function dispatch($passed_url = '') {
		$url = empty($passed_url) ? isset($_GET['_url']) ? trim($_GET['_url'], '/') : '' : trim($passed_url, '/');
		@list($controller, $action, $parameters) = explode('/', $url, 3);

		$controller = ucfirst(strtolower($controller));
		$action = strtolower($action);
		// check if there really was a controller specified in the url
		// if not, then show welcome-controller.. OR even better,
		// check config for default controller! (maybe later)
		if (strlen(trim($controller))>0) {
			// woho! we've got a controller specified, let's try and load it!
			// first we check if the controller exists
			if (@class_exists($controller)) {
				$this->$controller = new $controller(); 
			} else { // So the specified controller doesn't exits... 
				// check if it's a cononical url first 
				// and if not then send the $controller as $action to the default controller and prepend $action to $parameters.
				if ($can_url = $this->get_canonical($controller)) {
					if ($can_url->external == 1) {
						// external link... just send away!
						header('location: ' . $can_url->realurl);
					} else {
						// internal link.. just call the dispatch method again with realurl as parameter
						$this->dispatch($can_url->realurl);
					}
				} else {
					// no controller, no canurl... send everything to the default controller, let the def controller take care of this load now...
					$parameters = $action . '/' . $parameters;
					$action = strtolower($controller);
					$controller = $this->config['default_controller'];
					$this->dispatch("{$controller}/{$action}/{$parameters}");
				}
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
					die('crude error reporting in action: ' . $action . ' called was deemed private (starting with _) and thus was NOT called!');
				} else {
					// call the method in the controller with possible parameters
					// no longer checking if the method exists since a magic __call in the base controller class takes care of that now...
					$this->$controller->$action($parameters);
					exit;
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
				$this->Controller->index('No specified valid controller found so the welcome controller took over...');
			}
		}
	}

	// take care of autoloading missing classes
	private function autoloader($className) {
		$res = false;
		// make sure className is tolower, since all filenames should be lowercase
		$className = strtolower($className);

		//compose file names for all possible paths...
		$controller_file = BASE_PATH . '/application/controllers/' . $className . '.php';

		// TODO: add stuff to config for user defined subfolders to look for controllers in, then loop over every folder after standard folder....
		//fetch file
		if (file_exists($controller_file)) {
			//get file
			include_once($controller_file);
			$res = true;
		} else {
			//file does not exist!
			$res = false;
		}

		// check and see if the include did declare the class
		if (!class_exists($className, false)) {
			$res = false;
		}
		return $res;
	}

	public function load_config() {
		include_once(BASE_PATH . '/application/config/config.php'); 
		$this->config = $config;
		unset($config); // remove the config array var from the config.php file...

		if (isset($this->config['environment']) && strtolower($this->config['environment']) == "dev") {
			error_reporting(-1);
		}
	}

	private function get_canonical($can) {
		require_once(BASE_PATH . '/system/helpers/database.php');
		$db = new Database();
		$db->connect();
		$can = $db->escape($can);
		return $db->query("SELECT * FROM canonical_urls WHERE canurl = '{$can}' AND active='1' ORDER BY created DESC LIMIT 1")->fetch_object();

	}

	public function dump_okapi() {
		echo '<pre>';
		print_r($this); // object debugging ;)
		echo '</pre';
	}
}

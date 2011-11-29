<?php 
// require the base files (will do it this way (manually) for now, might add this to the autoload function later
require_once(BASE_PATH . '/system/core/load.php');
require_once(BASE_PATH . '/system/core/model.php');
require_once(BASE_PATH . '/system/core/controller.php');
// the almighty core class
class Okapi {
	public $load;
	private static $instance;

	private function __construct() {
		//$this->load = new Load();
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

		//$okapi->load->$controller;
		//$okapi->$controller->$action($parameters);

		// check if there really was a controller specified in the url
		// if not, then show welcome-controller.. OR even better,
		// check config for default controller! (maybe later)
		if (strlen(trim($controller))>0) {
			// woho! we've got a controller specified, let's try and load it!
			echo '<br>$this->load->' .$controller;
			// now that the controller is loaded, lets see if any action was specified
			if (strlen(trim($action))>0) {
				// we have a small thing called private actions
				// private actions are really only methods in a class that
				// should not be callable by url, we mark these 
				// with an underscore like this: _secret_method
				// ONE more thing, we do NOT check paramters here, KISS you know...
				// Let the controller action take care of that!
				// This is good because we then give the controller
				// freedom to specify exactly how the parameters should be passed,
				// it could for example be in the format p1=val&p2=val2 
				// or p1/val/p2/val2 or p1=val;p2=val2 and so on...
				// we are after all only takin a quick look at the request here to
				// get the ball rolling (and hopefully in the right direction)
				if (substr($action, 0, 1) == '_') {
					die('crude error reporting in action: ' . $action . ' called was deemed private and thus was NOT called!');
				} else {
					// call the method in the controller with possible parameters
					echo '<br>$this->' . $controller . '->' . $action . '(' .$parameters .')';
				}
			} else {
				// no action specified, using the index-method, 
				// which is mandatory for ALL controllers to have
				echo '<br>$this->' . $controller . '->index()';
			}

		}
	}

	// take care of autoloading missing classes
	function __autoload($className) {
		// make sure className is tolower, since all filenames should be lowercase
		$className = strtolower($className);

		//compose file names for all possible paths...
		$model_file = BASE_PATH . '/application/models/' . $className . '.php';
		$controller_file = BASE_PATH . '/application/models/' . $className . '.php';

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
	}
}

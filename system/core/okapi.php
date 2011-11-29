<?php 
// the almighty core class
class Okapi {
	public $load;

	public function __construct() {
		//		$this->load = new Load();
	}

	public function dispatch() {
		$url = trim($_GET['_url'], '/');
		list($controller, $action, $parameters) = explode('/', $url);

		//$okapi->load->$controller;
		//$okapi->$controller->$action($parameters);

		print_r($controller);
		echo "<br />";
		print_r($action);
		echo "<br />";
		print_r($parameters);
	}
}

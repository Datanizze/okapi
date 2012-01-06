<?php

class Controller {

	public $load;

	public function __construct() {
		global $okapi;
		$this->load = new load($this);
		$this->config = &$okapi->config;
		// do something with themes here...
	}

	public function index() {
		echo 'This is the index method in the core controller class, you really should override this!';
	}

	// magic overloading for showing 404's when accessing stuff that does not exist...
	public function __call($name, $arguments) {
		die("404, <strong>{$name}</strong> not found... Walk away... Just walk away before something really bad happens...");
	}
}

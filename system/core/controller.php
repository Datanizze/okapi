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
		$this->load->model('model');
		$data = $this->model->getData();
		$this->load->view('View', $data);
	}

	// magic overloading for showing 404's when accessing methods that does not exist...
	public function __call($name, $arguments) {
		die("404, <strong>{$name}</strong> not found... Walk away... Just walk away before something really bad happens...");
	}
}

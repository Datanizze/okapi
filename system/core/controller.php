<?php

class Controller {

	public $load;

	public function __construct() {
		global $okapi;
		$this->load = new load(&$this);
		$this->config = &$okapi->config;
		
		// do something with themes here...
	}

	public function index() {
		$this->load->model('model');
		$data = $this->model->getData();
		$this->load->view('View', $data);
	}

}

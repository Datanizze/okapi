<?php

class Controller {

	public $load;
	public $model;

	// TODO: make protectede/private, only inherited controllers should be instansiable
	public function __construct() {
	}

	public function index() {
		global $okapi;
		$okapi->load->model('Model');
		$data = $okapi->model->getData();
		$okapi->load->view('View', $data);
	}

}

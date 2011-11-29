<?php

public class Okapi_controller {

	public $load;
	public $model;

	protected function __construct() {
		$this->load = new Load();
		$this->model = new Model();

		$this->index();
	}

	public function index() {

		$data = $this->model->user_info();
		$this->load->view('someview.php', $data);
	}

}

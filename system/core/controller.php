<?php

class Controller {

	public $load;
	// this array keeps track of everything that should go into the view(s)
	// thus we will incrementally add to this variable when we have more data to add
	protected $data = array();

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
	public function __call($action, $params) {
		$this->four_o_four($action, $params);
	}

	public function four_o_four($action, $params='') {
		header("HTTP/1.0 404 Not Found");
		$this->_add_data($action);
		$this->_add_data($params);
		$this->load->view('404', $this->data);
	}

	protected function _add_data($new_data, $key='') {
		if ($new_data !=null && !empty($new_data)) 
			$this->data[$key] = $new_data;
	}

}

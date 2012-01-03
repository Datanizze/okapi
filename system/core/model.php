<?php

class Model {

	public function __construct() {
		// does nothing, just exists for future use.. placeholder
	}

	public function get_data() {
		$data = array();

		$data['title']='page title';
		$data['h1']='page header title';

		return $data;
	}
}

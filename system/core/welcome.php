<?php

class Welcome extends Controller {

	public function index() {
		$args = func_get_args();
		if (count($args)) 
			echo $args[0];
		else
			echo 'This is the welcome controller speaking';
	}

	public function install_db() {
		header('location: /cms/install');
	}

}

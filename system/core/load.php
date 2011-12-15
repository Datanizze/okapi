<?php

class Load {

	public function __construct() {
		// don't know it anything is needed here right now...
		global $okapi;
	}

	public function model($model, $db = false) {
		global $okapi;
		$model = strtolower($model);
		$ucmodel = ucfirst($model);
		// 1. check if db, load db if db
		// TODO: load db...

		// 2. load the model
		$okapi->$model = new $ucmodel;
	}

	public function view($view, $data) {
		// expand data if is_array
		extract($data);

		if ($view === 'index.php') {
			die('No access to view index.php is granted here...');
		}

		if (file_exists(APPLICATION_PATH . '/views/' . $view . '.php')) {
			include(APPLICATION_PATH . '/views/' . $view) . '.php';
			// check config if javascript is activated
			// check config for active theme
		}

	}
}

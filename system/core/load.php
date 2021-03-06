<?php

class Load {

	public function __construct(&$controller) {
		$this->contr = &$controller;
	}

	public function model($model, $name='',  $db = false) {
		global $okapi;

		// check if model is in a sub-folder and if so then parse filename and path.
		$path = '';
		if (($last_slash = strrpos($model, '/')) !== false) {

			// get path... everything in front of the last slash
			$path = substr($model, 0, $last_slash+1);

			// get the model
			$model = substr($model, $last_slash+1);
		}

		// check for custom model name
		if ($name=='') {
			$name = $model;
		}

		// check if a resource by the name $name already exists.
		if (@$okapi->$name) {
			@die("A resource with the name $name as already been declared.");
		}

		$model = strtolower($model);

		// check if model is already loaded or something... then don't load it...
		if (class_exists(ucfirst($model), false)) {
		} else if (!file_exists(APPLICATION_PATH . '/models/' . $path . $model . '.php')) {
			die('file containing model ' . $model . ' was not found!');
		} else {
			require_once(APPLICATION_PATH . '/models/' . $path . $model . '.php');
		}

		$model = ucfirst($model);
		$this->contr->$name = new $model;

		// let's give the model an instance of load too, so it can load helpers
		$this->contr->$name->load = new Load($this->contr->$name);

		// load database instance into $this->contr->db
		if ($db) {
			$this->load_database($name);
		}

		// load the url helper by default since it is quite useful for parameter extraction.
		$this->contr->$name->load->helper('url');
	}

	public function view($view, $data=null, $extract = true, $load_header = true, $load_footer = true) {
		$okapi = Okapi::singleton();
		// expand data if is_array

		if ($data != null && $extract) {
			extract($data);
			unset($data);
		}

		if ($view === 'index.php') {
			die('No access to view index.php is granted here...');
		}

		$theme = $okapi->config['theme'];
		unset($okapi); // no automagic access to okapi from any view please :)
		if (file_exists(APPLICATION_PATH . '/themes/' . $theme . '/'. $view . '.php')) {
			$cwd = APPLICATION_PATH . '/themes/' . $theme;
			if ($load_header)
				include($cwd . '/header.php');
			include(APPLICATION_PATH . '/themes/' . $theme . '/' . $view) . '.php';
			if ($load_footer)
				include($cwd . '/footer.php');
		}
	}

	// $model; set if you want to load the helper into a model instead of the calling controller
	public function helper($helper) {
		$path = '';
		if (($last_slash = strrpos($helper, '/')) !== false) {
			// get path... everything in front of the last slash
			$path = substr($helper, 0, $last_slash+1);
			// get the model
			$helper = substr($helper, $last_slash+1);
		}

		$helper = strtolower($helper);

		if (isset($this->contr->$helper)) {
			echo 'helper ' . $helper . 'already loaded in ' . get_class($this->contr);
		} else {

			if (file_exists(BASE_PATH . '/system/helpers/' . $path . $helper . '.php')) {
				include_once(BASE_PATH . '/system/helpers/' . $path . $helper . '.php');
			} else {
				die("could not find any helper with the name '$helper'");
			}

			if ($helper == 'database') {
				$this->load_database($model);
			} else {
				$helperClass = ucfirst($helper);
				$this->contr->$helper = new $helperClass;
			}
		}
	}

	private function load_database($name = '') {
		if (!class_exists('Database', false)) {
			require_once(BASE_PATH . '/system/helpers/database.php');
		}
		if ($name != '') {
			$this->contr->$name->db = new Database();
		} else {
			$this->contr->db = new Database();
		}
	}
}

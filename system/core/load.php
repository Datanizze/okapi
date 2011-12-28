<?php

class Load {

	public function __construct(&$controller) {
		global $okapi;
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
			echo 'file containing model ' . $model . ' was not found!';
			continue;
		} else {
			require_once(APPLICATION_PATH . '/models/' . $path . $model . '.php');
		}

		$model = ucfirst($model);
		$this->contr->$name = new $model;
		
		// let's give the model an instance of load to, so it can load helpers
		$this->contr->$name->load = new Load($this->contr->$name);

		// load database instance into $this->contr->db
		if ($db) {
			$this->load_database($name);
		}
	}

	public function view($view, $data, $extract = true) {
		global $okapi;
		// expand data if is_array
		if ($extract) {
			extract($data);
		}
		
		// TODO: add subfolder checking like in the model loading...
		if ($view === 'index.php') {
			die('No access to view index.php is granted here...');
		}

		if (file_exists(APPLICATION_PATH . '/themes/' . $okapi->config['theme'] . '/'. $view . '.php')) {
			include(APPLICATION_PATH . '/themes/' . $okapi->config['theme'] . '/' . $view) . '.php';
			// check config if javascript is activated
			// check config for active theme
		}
	}

	public function helper($helper) {
		$path = '';
		if (($last_slash = strrpos($helper, '/')) !== false) {
			// get path... everything in front of the last slash
			$path = substr($helper, 0, $last_slash+1);
			// get the model
			$helper = substr($helper, $last_slash+1);
		}

		// check if a resource by the name $name already exists.
		if (@$this->contr->$helper || @$okapi->$helper) {
			die("A helper with the name $helper as already been declared.");
		}

		$helper = strtolower($helper);

		if (file_exists(BASE_PATH . '/system/helpers/' . $path . $helper . '.php')) {
			include_once(BASE_PATH . 'system/helpers/' . $path . $helper . '.php');
		} else {
			die("could not find any helper with the name '$helper'");
		}

		if ($helper == 'database') {
			$this->load_database($helper);
		} else {
			$helperClass = ucfirst($helper);
			$this->contr->$helper = new $helperClass;
		}
	}
	
	private function load_database($name = NULL) {
		if (!class_exists('Database', false)) {
			require_once(BASE_PATH . '/system/helpers/database.php');
		}
		if ($name != NULL) {
			$this->contr->$name->db = new Database();
		} else {
			$this->contr->db = new Database();
		}
	}
}

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
		if ($okapi->$name) {
			die("A resource with the name $name as already been declared.");
		}

		$model = strtolower($model);

		// check if model is already loaded or something... then don't load it...
		if (class_exists(ucfirst($model))) {
		} else if (!file_exists(APPLICATION_PATH . '/models/' . $path . $model . '.php')) {
			echo 'file containing model ' . $model . ' was not found!';
			continue;
		} else {
			require_once(APPLICATION_PATH . '/models/' . $path . $model . '.php');
		}


		// TODO: load db here?

		$model = ucfirst($model);
		$this->contr->$name = new $model;
	}

	public function view($view, $data) {
		// expand data if is_array
		extract($data);

		// TODO: add subfolder checking like in the model loading...
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

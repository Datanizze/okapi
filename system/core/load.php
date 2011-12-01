<?php

class Load {

	public function __construct() {
		// don't know it anything is needed here right now...
		global $okapi;
	}

	public function model($model, $db = false) {
		// if db then load db with default settings from config
	}

	public function view($view, $data) {
		// expand data if is_array
		// check config if javascript is activate
		// check config for active theme
	}
}

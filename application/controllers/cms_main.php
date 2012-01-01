<?php

class Cms_main extends Controller {

	/*public function __construct() {
		parent::__construct();
	}*/
	// this array keeps track of everything that should go into the view(s)
	// thus we will incrementally add to this variable when we have more data to add
	private $data = array();

	public function index() {
		$this->start();
	}

	public function start(/*$stuff=null*/) {
		$this->_load_model();
		// everything but the content view will probably be included by the content view itself for exat positioning and all that.. we will however send all data for the page to content...
		$this->load->view('start', $this->data);
		//$this->load->view('menu');
		//$this->load->view('content');
		//$this->load->view('footer');
	}

	public function admin() {
		$this->_load_model();
		if ($this->cms->check_login()) {
			echo '<bri>logged in, proceeding';
		} else {
			echo '<br>not logged in, sending to login page...';
			$this->load->view('login');
		}
		
	}

	private function _load_model() {
		$this->load->model('cms_model', 'cms', true);
		// lets go ahead and load the menu here too, since all views will have that... I think...
		$this->_add_data($this->cms->get_menu());
	}

	private function _add_data($new_data) {
		if ($new_data !=null && !empty($new_data)) 
			$this->data = array_merge((array)$this->data, (array)$new_data);
	}
}

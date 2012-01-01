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
		set_active_menu_item('home');
		$this->load->view('start', $this->data);
	}

	public function admin() {
		$this->_load_model();
		if (!$this->cms->check_login()) {
			$this->_add_data($this->cms->get_article(), 'articles');
			$this->load->view('cpage', $this->data);
		} else {
			$this->load->view('login');
		}
		
	}

	private function _load_model() {
		$this->load->model('cms_model', 'cms', true);
		// lets go ahead and load the menu here too, since all views will have that... I think...

		$this->_add_data($this->cms->get_menu(), 'menu');
		$this->_add_data($this->cms->get_site_info(), 'site'); // things like, title, meta, extra js/css and so on...
	}

	private function _add_data($new_data, $key='') {
		if ($new_data !=null && !empty($new_data)) 
			$this->data[$key] = $new_data;
	}
}

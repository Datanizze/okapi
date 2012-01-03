<?php

class Cms_main extends Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('auth', 'cms');
		$this->_load_model();
	}
	// this array keeps track of everything that should go into the view(s)
	// thus we will incrementally add to this variable when we have more data to add
	private $data = array();

	public function index() {
		$this->start();
	}

	public function start(/*$stuff=null*/) {
		set_active_menu_item('home');
		$this->load->view('start', $this->data);
	}

	public function admin() {
		set_active_menu_item('admin');
		if ($this->cms->check_login()) {
			$this->_add_data($this->cms->get_article(), 'articles');
			$this->load->view('cpage', $this->data);
		} else {
			$this->login();
		}

	}

	private function _load_model() {
		$this->load->model('cms_model', 'cms', true);
		$this->cms->load->helper('auth');
		// lets go ahead and load the menu here too, since all views will have that... I think...

		$logged_in = $this->cms->check_login();
		$authed = '<span class="';
		$authed .= $logged_in ? 'success">Logged in.</span>' : 'error">NOT logged in.</span>';

		$this->_add_data($authed, 'authed');
		$this->_add_data($this->cms->get_menu($logged_in ? 2 : 0), 'menu');
		$this->_add_data($this->cms->get_site_info(), 'site'); // things like, title, meta, extra js/css and so on...
	}

	private function _add_data($new_data, $key='') {
		if ($new_data !=null && !empty($new_data)) 
			$this->data[$key] = $new_data;
	}

	public function login() {
		set_active_menu_item('login');
		$this->_add_data($this->cms->do_login(), 'login_status');
		if (isset($this->data['login_status']) && $this->data['login_status']==1) 
			header('location: /');
		else
			$this->load->view('login', $this->data);
	}


	public function logout() {
		$this->cms->do_logout();
	}
}

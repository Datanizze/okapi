<?php

class Cms extends Controller {

	public function __construct() {
		parent::__construct();
		$this->_load_model();
	}
	// this array keeps track of everything that should go into the view(s)
	// thus we will incrementally add to this variable when we have more data to add
	private $data = array();
	private $logged_in = null;

	public function index() {
		$this->start();
	}

	public function start(/*$stuff=null*/) {
		set_active_menu_item('home');
		$this->load->view('start', $this->data);
	}

	public function admin($params = null) {
		set_active_menu_item('admin');
		if (isset($params)) {
			$this->load->helper('url');
			$params = $this->url->p2a($params);
			set_active_menu_item($params[0], 'active_submenu_item');
		}

		if ($this->logged_in) {
			$this->_add_data($this->cms->get_article(), 'articles');
			$this->load->view('cpage', $this->data);
		} else {
			$this->login('admin');
		}

	}

	private function _load_model() {
		$this->load->model('cms_model', 'cms', true);
		$this->cms->load->helper('auth');
		// lets go ahead and load the menu here too, since all views will have that... I think...

		if($this->logged_in == null)
			$this->logged_in = $this->cms->check_login();

		$authed = '<span class="';
		$authed .= $this->logged_in ? 'success">Logged in. <a href="/logout">Logout</a></span>' : 'error">NOT logged in.</span>';

		$this->_add_data($authed, 'authed');
		$this->_add_data($this->cms->get_menu($this->logged_in ? 2 : 0), 'menu');
		$this->_add_data($this->cms->get_site_info(), 'site'); // things like, title, meta, extra js/css and so on...
	}

	private function _add_data($new_data, $key='') {
		if ($new_data !=null && !empty($new_data)) 
			$this->data[$key] = $new_data;
	}

	public function login($forward_to='') {
		set_active_menu_item('login');
		if(!empty($_POST['forward_to']))
			$forward_to = $_POST['forward_to'];

		$this->_add_data($forward_to, 'forward_to');
		$this->_add_data($this->cms->do_login(), 'login_status');
		if (isset($this->data['login_status']) && is_bool($this->data['login_status']) && $this->data['login_status']==true) {
			if (!empty($forward_to)) {
				header('location: ' ./* $_SERVER['SERVER_NAME']. '/' .*/ $forward_to);
			} else {
				header('location: /');
			}
		} else {
			$this->load->view('login', $this->data);
		}
	}

	public function logout() {
		$this->cms->do_logout();
	}

	public function install() {
		echo 'Installing CMS database tables';
		$install = $this->cms->do_install($this->logged_in);
	}

	public function __call($action, $params) {
		$this->four_o_four($action, $params);
	}

	public function four_o_four($action, $params) {
		header("HTTP/1.0 404 Not Found");
		$this->_add_data($action);
		$this->_add_data($params);
		$this->load->view('404', $this->data);
	}
}

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
	private $theme_path = '';

	public function index() {
		$this->start();
	}

	public function start(/*$stuff=null*/) {
		set_active_menu_item('home');
		$this->load->view('start', $this->data);
	}

	public function admin($params = null) {
		set_active_menu_item('admin');
		$subaction = 'admin';

		$params_a = array();
		$action = 'admin';
		if (isset($params)) {
			$this->load->helper('url');
			$params_a = $this->url->p2a($params, '/', 2);
			$action = $params_a[0];
			set_active_menu_item($action, 'active_submenu_item');
		}

		if ($this->logged_in) {
			$action_params = isset($params_a[1]) ? $params_a[1] : null;
			$this->_perform_action($action, $action_params);
		} else {
			$fwd = 'cms/admin';
			$fwd .= isset($params) ? '/' . $params : '';
			$this->login($fwd);
		}
	}

	private function _perform_action($action, $parameters=null) {
		if (isset($parameters)) {
			$parameters = $this->url->p2a($parameters);
		}

		// get subaction.. like edit, add, remove and so on
		$subaction =  isset($parameters[0]) ? $parameters[0] : null;
		// get subaction key, for edit and other subaction that needs something to work on.
		$subaction_key = isset($parameters[1]) ? $parameters[1] : null;

		switch ($action) {
		case 'cpage':
			$this->_cpage($subaction, $subaction_key);
			break;
		case 'canurl':
			$this->_can_url($subaction, $subaction_key);
			$this->_add_data($this->cms->get_canurl(), 'canurls');
			break;
		case  'main_menu':
			$this->_main_menu($subaction, $subaction_key);
			$this->_add_data($this->cms->get_main_menu(), 'main_menu_items');
			break;
		default:
			$this->load->view('adm/admin', $this->data);
			break;
		}
	}

	private function _cpage($action=null, $action_key=null) {
		if (isset($action)) {
			$this->_add_data($action, 'action');

			if ($action == 'add') {
				if (isset($_POST['submit'])) {
					unset($_POST['submit']);
					if(empty($_POST['title']) || empty($_POST['content'])) {
						$result = array('status' => 'error', 'message' => '<strong>Title</strong> and/or <strong>content</strong> empty!, please correct before saving.');
					} else {
						$result = $this->cms->save('articles', $_POST);
					}
					$this->_add_data($result, 'status');
				}
			} elseif (isset($action_key)) {
				switch($action) {
				case 'edit':
					if (isset($_POST['submit'])) {
						unset($_POST['submit']);
						$result = $this->cms->save('articles', $_POST);
						unset($_POST); // we really don't need this anymore.. I think? :S
						$this->_add_data($result, 'status');
					}
					$article =  $this->cms->get('articles', $action_key);
					if ($article == null) {
						$this->four_o_four('page', $action . '/' . $action_key);
					} else {
						$this->_add_data($article[0], 'article');
					}
					break;
				case 'delete':
					$result = $this->cms->delete('articles', $action_key);
					header('location: ' . URL_ROOT . 'cms/admin/cpage/');
					// below not used anymore... above header call is an ugly static workaround.. .:(
					$this->_add_data($result, 'status');
					$this->_add_data($this->cms->get('articles'), 'articles');
				case 'activate':
				case 'deactivate':
					$result = $this->cms->$action('articles', $action_key);
					echo $result;
					header('location: ' . URL_ROOT . 'cms/admin/cpage/');
					// below not used anymore... above header call is an ugly static workaround.. .:(
					$this->_add_data($result, 'status');
					$this->_add_data($this->cms->get('articles'), 'articles');
					break;
				}
			} else {
				$this->_add_data(array('status' => 'error', 'message' => "No key set, can't {$action} if don't know what to {$action}!"), 'status');
			}
		} else {
			$this->_add_data($this->cms->get('articles'), 'articles');
		}
		$this->load->view('adm/cpage', $this->data);
	}

	private function _load_model() {
		$this->load->model('cms_model', 'cms', true);
		$this->cms->load->helper('auth');
		// lets go ahead and load the menu here too, since all views will have that... I think...

		if($this->logged_in == null)
			$this->logged_in = $this->cms->check_login();

		$okapi = Okapi::singleton(); // need to get theme path
		$this->theme_path = APPLICATION_PATH . '/themes/' . $okapi->config['theme'] . '/';
		unset($okapi); // done... unset!

		$authed = '<span class="';
		$authed .= $this->logged_in ? 'success">Logged in. <a href="/cms/logout">Logout</a></span>' : 'error">Not logged in.</span>';

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
				$loc = 'location: ' . URL_ROOT . $forward_to;
				header($loc);
			} else {
				header('location: ' . URL_ROOT);
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

	public function four_o_four($action, $params='') {
		header("HTTP/1.0 404 Not Found");
		$this->_add_data($action);
		$this->_add_data($params);
		$this->load->view('404', $this->data);
	}

	public function page($page_key='') {
		$show_deactivated = false;
		if ($this->logged_in) {
			$show_deactivated = true;
		}

		$article =  $this->cms->get('articles', $page_key, $show_deactivated);
		if ($article == null) {
			$this->four_o_four('page', $page_key);
		} else {
			$this->_add_data($article[0], 'article');
			$this->load->view('page', $this->data);
		}
	}
}

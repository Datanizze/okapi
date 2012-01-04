<?php

class Cms_model extends Model {

	public function get_site_info() {
		if (file_exists(APPLICATION_PATH . '/config/site_config.php')) {
			include(APPLICATION_PATH . '/config/site_config.php');
			return $site_config;
		} 

	}

	public function check_login() {
		return $this->auth->is_authenticated();
	}

	public function do_login() {
		$retval = false;
		if(isset($_POST['submit'])) {
			if (!empty($_POST['username']) && !empty($_POST['password']))  {
				$retval = $this->auth->authenticate($_POST['username'], $_POST['password']);
				if (!$retval) 
					$retval = '<p><span class="error"> Wrong Username and/or Password, try again.</span></p>';
			}
		}
		return $retval;
	}

	public function do_logout() {
		$this->auth->logout();
		header('location: /');
	}

	public function get_menu($auth = 0) { // $auth: 0 = get menuitems for users not logged in, 1 = opposite of 0, 2 = both 0 & 1 -1 = let this method decide with help from auth helper
		$auth = $auth<2 ? ' AND `logged_in`="' . $auth . '"' : '';
		$query = "SELECT * FROM `main_menu` WHERE `alive`=1{$auth} ORDER BY `weight` DESC";
		$res = $this->db->query($query);

		return $this->get_array($res);
	}

	public function get_article($article_id = null) {
		$query = "SELECT `key`,`title`,`content`,`content_type`,`active`,`created`,`published`,`modified`,`author` FROM article";

		if ($article_id != NULL) {
			if (is_numeric(trim($article_id))) {// get article by id
				$query .= " WHERE `id`='{$article_id}' LIMIT 1";
			} else { // get article by key
				$key = $this->db->escape($article_id);
				$query .= " WHERE `key`='{$key}' LIMIT 1";
			}
		}
		$res = $this->db->query($query);
		return $this->get_array($res);
	}

	private function get_array($sql_result, $free_result_when_done = true) {
		$ret = array();
		if ($sql_result->num_rows > 0) {
			while ($row = $sql_result->fetch_assoc())
				array_push($ret, $row);
		} 
		if($free_result_when_done)
			$this->db->free_result();

		return $ret;
	}
}

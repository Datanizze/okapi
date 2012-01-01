<?php

class Cms_model extends Model {

	public function get_site_info() {
		if (file_exists(APPLICATION_PATH . '/config/site_config.php')) {
			include(APPLICATION_PATH . '/config/site_config.php');
			return $site_config;
		} 
		
	}

	public function check_login() {
		if (!isset($this->auth)) 
			$this->load->helper('auth');

		return $this->auth->is_authenticated();
	}

	public function get_menu() {
		$res = $this->db->query("SELECT * FROM `main_menu` WHERE `alive`=1 ORDER BY `weight` DESC");

		return $this->get_array($res);
	}

	public function get_article($article_id = null) {
		$query = "SELECT * FROM article";

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

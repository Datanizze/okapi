<?php

class Cms_model extends Model {
	
	public function check_login() {
		if (!isset($this->auth)) 
			$this->load->helper('auth');

		return $this->auth->is_authenticated();
	}

	public function get_menu() {
		$retval = array('menu' => array());
		$res = $this->db->query("SELECT * FROM `main_menu` WHERE `alive`=1 ORDER BY `weight` DESC");
		
		while($row = $res->fetch_assoc()) {
			array_push($retval['menu'], $row);
		}

		return $retval;
	}
}

<?php

class Model {

	public function __construct() {
		// does nothing, just exists for future use.. placeholder
	}

	public function get_data() {
		$data = array();

		$data['title']='page title';
		$data['h1']='page header title';

		return $data;
	}

	public function get_site_info() {
		if (file_exists(APPLICATION_PATH . '/config/site_config.php')) {
			include(APPLICATION_PATH . '/config/site_config.php');
			return $site_config;
		} 
	}

	private function get_array($sql_result, $free_result_when_done = true) {
		$ret = null;
		if (is_object($sql_result) && $sql_result->num_rows > 0) {
			$ret = array();
			while ($row = $sql_result->fetch_assoc()) {
				array_push($ret, $row);
			}
		} 
		if($free_result_when_done)
			$this->db->free_result();

		return $ret;
	}

}

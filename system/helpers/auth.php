<?php
require_once(BASE_PATH . '/system/helpers/database.php');

class Auth {

	private $db;

	public function __construct() {
		$this->db = new Database();
		$this->db->connect();
	}

	public function __destruct() {
		$this->db->close();
	}

	// also session validation (?)
	public function is_authenticated() {
		$retval = false;
		$session_key = isset($_SESSION[BASE_PATH . '_key']) ? $_SESSION[BASE_PATH . '_key'] : '';
		if (!empty($session_key)) {
			// the key is sha1(username . salt)
			if (isset($_SESSION[BASE_PATH . '_username'])) {
				$username = $_SESSION[BASE_PATH . '_username'];
				$res = $this->db->query("SELECT salt FROM users WHERE username = '{$username}' LIMIT 1");

				if ($res->num_rows > 0) {
					$row = $res->fetch_object();
					$salt = $row->salt;
					echo sha1($username . $salt);

					if ($session_key == sha1($username . $salt)) {
						$retval = true;
					}
				}
			}
			return $retval;
		}
	}

	public function is_in_group($group_id) {

	}

	public function authenticate($user, $password) {

	}

	public function logout() {

	}

	public function salt($password, $salt) {
		return sha1('[okapi]-:-[' . $pass_salt . ']');
	}

	public function register($username, $password, $email, $real_name='') {
		$salt = sha1(substr($password, (strlen($password)/2)*-1));
		$salted_pass = sha1('[' . $password . ']-:-[' . $salt . ']');
		return $this->db->query("INSERT INTO users (username, password, email, real_name, salt, last_ip) VALUES ('{$username}', '{$salted_pass}', '{$email}', '{$real_name}', '{$salt}', '{$_SERVER['REMOTE_ADDR']}')");
	}

}

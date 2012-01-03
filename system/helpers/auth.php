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
					// echo sha1($username . $salt);

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

	public function authenticate($username, $password) {
		$retval = false; // always assume fail :D, makes if's easier and lesser too...

		$username = $this->db->escape($username);
		$password = $this->db->escape($password);
		$res = $this->db->query("SELECT * FROM users WHERE `username`=\"{$username}\" LIMIT 1");
		if($res->num_rows>0) {
			$user = $res->fetch_assoc();
			$pass = $this->salt($password, $user['salt']);
			echo $pass;
			if ($pass == $user['password']) {
				// yay, we're logged in.. let's save som session date...
				// TODO: maybe now when were logged in check for groups and add relevant data to _SESSION?
				$_SESSION[BASE_PATH . '_key'] = sha1($user['username'] . $user['salt']);
				$_SESSION[BASE_PATH . '_username'] = $user['username'];
				$_SESSION[BASE_PATH . '_real_name'] = $user['real_name'];
				$retval = true;
			}
		}

		$res->close();
		return $retval;

	}

	public function logout() {
		$_SESSION[BASE_PATH . '_key'] = '';
		$_SESSION[BASE_PATH . '_username'] = '';
		$_SESSION[BASE_PATH . '_real_name'] = '';

		unset($_SESSION[BASE_PATH . '_key']);
		unset($_SESSION[BASE_PATH . '_username']);
		unset($_SESSION[BASE_PATH . '_real_name']);

		return true;
	}

	public function salt($password, $salt) {
		return sha1('[' . $password .']-:-[' . $salt . ']');
	}

	public function register($username, $password, $email, $real_name='') {
		$salt = sha1(substr($password, (strlen($password)/2)*-1));
		$salted_pass = sha1('[' . $password . ']-:-[' . $salt . ']');
		return $this->db->query("INSERT INTO users (username, password, email, real_name, salt, last_ip) VALUES ('{$username}', '{$salted_pass}', '{$email}', '{$real_name}', '{$salt}', '{$_SERVER['REMOTE_ADDR']}')");
	}

}

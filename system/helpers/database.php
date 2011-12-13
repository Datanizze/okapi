<?php

class Database {

	public $debug = true; // show error messages or not...

	private static $instance; // singleton is the shit!

	// settings are fetched from $okapi->config['db'] by default
	// construct also takes a connection array which for now should contain
	// host, database, user, password. It can be in any order but the keys need to match these for success.
	$host = '';
	$user = '';
	$password = '';
	$database = '';

	$conn = 0; // store the connection id, the one we get from mysql(i)_connect
	$query_id = 0; // store the most recent query result.
	public $affected_rows; // current row number

	$errorNo = 0;
	$error = "";

	private function __construct($db = null) {
		// TODO: error checking of connection settings before connecting...
		global $okapi;
		if($db == null) {// load default settings from config.
			$this->host = $okapi->config['db']['host'];
			$this->user = $okapi->config['db']['password'];
			$this->password = $okapi->config['db']['password'];
			$this->database = $okapi->config['db']['database'];
		} else {
			// check if $db is_array, fetch settings from there...
		}
	}

	public static function instance($db = null) {
		if (!self::$instance) {
			self::$instance = new Database($db);
		}
		return self::$instance;
	}

	// connect to db
	public function connect() {
		$this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);

		if ($this->conn->connect_error) { // connect failed...
			$this->doh("Could not connect to <strong>{$this->server}</strong>.<br /> ({$this->conn->connect_errno}) {$this->connect_error}");
		}

		// setcharset... could be fetched from config in future but for now we shall force utf-8!
		$this->db->set_charset("utf8");

		// unset connection data so it can't be var_dump'ed or print_r'd
		// Don't really know why we do this since the connection info can be found in the $okapi->config... hmm...
		$this->host='';
		$this->user='';
		$this->password='';
		$this->database='';
	}

	public function close() {
		if (!@$this->conn->close()) {
			$this->doh("Closing db connection failed.");
		}
	}

	// escapes shit for sql_injection prevention
	public function escape($string) {
		if (get_magic_quotes_runtime())
			$string = stripslashes($string);
		return @$this->conn->real_escape_string($string);
	}

	public function query($sql) {
		$this->query_id = @$this->conn->query($sql);

		if (!this->query_id) {
			$this->doh("<strong>MySQL query failed:</strong> {$sql}.";
		}

		$this->affected_rows = $this->conn->affected_rows;

		return $this->query_id;
	}

	// do query, return first row from result then free resultset. returns result.
	public function query_first($query) {
		$query_id = $this->query($query);
		$res = $this->fetch($query_id);
		$this->free_result($query_id);
		return $res;
	}

	public function fetch($query_id=-1) {
		if ($query_id!=-1) {
			$this->query_id = $query_id;
		}

		if (isset($this->query_id)) {
			$record = $this->conn->fetch_assoc();
		} else {
			$this->doh("invalid query: <strong>{$this->query_id}</strong>. Norecords could be fetched.");
		}
	}

	public function fetch_array($sql) {
		$query_id = $this->query($sql);
		$res = array();

		while($row = $this->fetch($query_id)) {
			$res[] = $row;
		}

		$this->free_result($query_id);
		return $res;
	}

	public function update($table, $data, $where='1') {
		$query = "UPDATE `{$table}` SET ";

		foreach ($data as $key => $val) {
			if (strtolower($val)=='null'
				$query .= "`{$key}` = NULL, "; // handles value NuLL
			elseif (strtolower($val) == 'now()')
				$query .= "`{$val}` = NOW(), ";
			else
				$query .= "`{$key}`='" . $this->escape($val) . "', ";
		}
		$query = rtrim($query, ', ') . " WHERE {$where};";

		return $this->query($query);
	}

	public function insert($table, $data) {
		$query = "INSERT INTO `{$table}` ";
		$v = '';
		$n = '';

		foreach($data as $key => $val) {
			$n .= "`$key`, ";
			if (strtolower($val) == 'null')
				$v .= "NULL, ";
			elseif (strtolower($val) == 'now()')
				$v .= "NOW(), ";
			else
				$v .= "'" . $this->escape($val) . "', ";
		}

		$query .= "(" . rtrim($n, ', ') . ") VALUES (" . rtrim($v, ', ') . ");";
		if ($this->query($query)) 
			return $this->insert_id;
		else 
			return false;
	}

	public function doh($msq='') {
		if (!empty($this->conn)) {
			$this->error = $this->conn->error;
		} else {
			$this->error = $this-conn->error;
			$msg="<strong>WARNING:</strogn> no conn found. Are you sure you're connected to the db?. <br /> {$msg}";
		}

		if (!this->debug)
			return;
?>
						<table align="center" border="1" cellspacing="0" style="background:white;color:black;width:80%;">
								<tr><th colspan=2>Database Error</th></tr>
										<tr><td align="right" valign="top">Message:</td><td><?php echo $msg; ?></td></tr>
												<?php if(!empty($this->error)) echo '<tr><td align="right" valign="top" nowrap>MySQL Error:</td><td>'.$this->error.'</td></tr>'; ?>
					<tr><td align="right">Date:</td><td><?php echo date("l, F j, Y \a\\t g:i:s A"); ?></td></tr>
								<?php if(!empty($_SERVER['REQUEST_URI'])) echo '<tr><td align="right">Script:</td><td><a href="'.$_SERVER['REQUEST_URI'].'">'.$_SERVER['REQUEST_URI'].'</a></td></tr>'; ?>
					<?php if(!empty($_SERVER['HTTP_REFERER'])) echo '<tr><td align="right">Referer:</td><td><a href="'.$_SERVER['HTTP_REFERER'].'">'.$_SERVER['HTTP_REFERER'].'</a></td></tr>'; ?>
					</table>
<?php
	}

	public function host_info() {
		return $this->connId->host_info;
	}
}


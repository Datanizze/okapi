<?php

class Database {
	private $debug = false;
	
	/**
	* internal stuff, fun to have?
	*/
	private $nrOfExecutedQueries;
	private $lastQueryResult;
	
	private $conn;
	private $prefix;
	
	public function connect($host='', $user='', $pass='', $database='', $prefix='') {
		
		if ($host=='' || $host=='' || $host=='' || $host=='' || $prefix=='') {
			global $okapi;
			$host = empty($host) ? $okapi->config['db']['host'] : $host;
			$user = empty($user) ? $okapi->config['db']['user'] : $user;
			$pass = empty($pass) ? $okapi->config['db']['password'] : $pass;
			$database = empty($database) ? $okapi->config['db']['database'] : $database;
			$this->prefix = empty($prefix) ? $okapi->config['db']['prefix'] : $prefix;
		}
		
		$this->nrOfExecutedQueries = 0;
		$this->lastQueryResult = NULL;
		
		$this->conn = new mysqli($host, $user, $pass, $database);
		
		if ($this->conn->connect_error) {
			$this->fuu('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
		}
	}
	
	public function query($query, $dbg = false) {
		$this->nrOfExecutedQueries++;
		$this->lastQueryResult = $this->conn->query($query);
		
		return $this->lastQueryResult;
	}

	public function freeResult($result = NULL) {
		if ($result == NULL) {
			$result = $this->lastQueryResult;
		}
		
		if ($result == NULL || $this->lastQueryResult->num_rows < 1) {
			return NULL;
		} else {
			$result->cloe();
		}
	}
	
	public function close() {
		$this->conn->close();
	}
	
	// decided to create a debug method since it's easier to maintain if one would want to alter debugging of the database, takes message as first parameter and level (e.g. debug=0, warning=1, error=2)
	private function fuu($msg, $lvl=2, $dbg=-1) {
		$dbg = ($dbg==-1) ? $this->debug : $dbg;
		switch($lvl) {
		case 0:
			if ($dbg)
				echo $msg;
			break;
		case 1:
			break;
		case 2:
		default:
			die($msg); // only die for now, maybe add some fancy html output later...
			break;
		}
	}
}

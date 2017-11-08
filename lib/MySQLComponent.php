<?php

class MySQLComponent {

	private $_host = null;
	private $_dbname = null;
	private $_user = null;
	private $_password = null;
	private $_charaset = null;

	private $_connect = null;

	function __construct($host, $dbname, $user, $password, $charaset = 'utf8') {
		$this->_host = $host;
		$this->_dbname = $dbname;
		$this->_user = $user;
		$this->_password = $password;
		$this->_charaset = $charaset;
	}

	function connect() {
		$this->_connect = new PDO('mysql:host='.$this->_host.';dbname='.$this->_dbname.';charset='.$this->_charaset, $this->_user, $this->_password, [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		]);
	}

	function close() {
		$this->_connect = null;
	}

	function select($sql, $bind_values='') {
		$stmt = $this->_connect->prepare($sql);

		if ($bind_values != '') {
			$len = count($bind_values);
			for($i = 0; $i < $len; $i++) {
				$stmt->bindValue($i + 1, $bind_values[$i]['value']);
			}
		}
		
		$stmt->execute();
		$rows = $stmt->fetchAll();

		$stmt = null;

		return $rows;
	}

	function insert($sql, $bind_values) {
		$stmt = $this->_connect->prepare($sql);
		$len = count($bind_values);
		
		for($i = 0; $i < $len; $i++) {
			$stmt->bindValue($i + 1, $bind_values[$i]['value']);
		}
		$stmt->execute();
		$stmt = null;
	}

	function lastInsertId($name = null) {
		if ($this->_connect) {
			if ($name) return $this->_connect->lastInsertId($name);
			else return $this->_connect->lastInsertId();
		}

		return null;
	}

	function delete($sql, $bind_values) {
		$stmt = $this->_connect->prepare($sql);
		$len = count($bind_values);
		for($i = 0; $i < $len; $i++) {
			$stmt->bindValue($i + 1, $bind_values[$i]['value']);
		}
		$stmt->execute();
		$stmt = null;
	}

	function getCount($sql) {
		$stmt = $this->_connect->prepare($sql);
		$stmt->execute();
		$count = intval($stmt->fetchColumn());
		$stmt = null;
		return $count;
	}

	function commit() {
		if ($this->_connect) {
			$this->_connect->commit();
		}
	}

	function beginTransaction() {
		if ($this->_connect) {
			$this->_connect->beginTransaction();
		}
	}

	function rollBack() {
		if ($this->_connect) {
			$this->_connect->rollBack();
		}
	}
}
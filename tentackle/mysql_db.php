<?php

/*
 * Copyright (c) 2008 Meding Software Technik -- All Rights Reserved
 */

/**
 * The MySQL driver for tentackle
 * 
 * @author <a href="mailto:uwe@uwemeding.com">Uwe Meding</a>
 */
class MySQLTentackle implements Tentackle {

	private $dbhost;
	private $dbuser;
	private $dbpass;
	private $dbname;
	private $dbport;
	private $connection;
	private $defaultIdSource;

	/**
	 * Construct the tentackle database connection
	 * @param type $dbhost the database host
	 * @param type $dbuser the database user
	 * @param type $dbpass the database password
	 * @param type $dbname the database name
	 */
	public function __construct($dbhost, $dbuser, $dbpass, $dbname, $dbport) {
		$this->dbhost = $dbhost;
		$this->dbuser = $dbuser;
		$this->dbpass = $dbpass;
		$this->dbname = $dbname;
		$this->dbport = $dbport;

		$this->connection = mysql_connect($dbhost, $dbuser, $dbpass);
		if (!$this->connection) {
			throw new DbRuntimeException(mysql_error());
		}
		if (!mysql_select_db($dbname, $this->connection)) {
			throw new DbRuntimeException(mysql_error());
		}

		$this->defaultIdSource = new MySQLIdSource();
	}

	/**
	 * Get the database handle
	 * @return the database handle 
	 */
	public function newDb() {
		$db = new Db($this);
		$db->setIdSource($this->defaultIdSource);
		return $db;
	}

	/**
	 * Get the connection
	 * @return the low level mysql connection
	 */
	public function getConnection() {
		return $this->connection;
	}

	/**
	 * Get the host name
	 * @return the host name 
	 */
	public function getHost() {
		return $this->dbhost;
	}

	/**
	 * Get the user name
	 * @return the user name 
	 */
	public function getUsername() {
		return $this->dbuser;
	}

	/**
	 * Get the database name
	 * @return the database name 
	 */
	public function getName() {
		return $this->dbname;
	}

	public function sqlLimitClause($limit) {
		return " LIMIT ".$limit;
	}

	public function sqlOffsetClause($offset) {
		return " LIMIT 99999999 OFFSET ".$offset;
	}

	public function sqlLimitOffsetClause($limit, $offset) {
		return " LIMIT ".$limit." OFFSET ".$offset;
	}

	/**
	 * Execute an update
	 * @param Db $db the database handle
	 * @param Statement $stmt the statement
	 * @return type the result or resource
	 * @throws DbRuntimeException 
	 */
	public function executeUpdate(Db $db, Statement $stmt) {
		return $this->executeStatement($db, $stmt);
	}

	/**
	 * Execute a query
	 * @param Db $db the database handle
	 * @param Statement $stmt the statement
	 * @return type the result or resource
	 * @throws DbRuntimeException 
	 */
	public function executeQuery(Db $db, Statement $stmt) {
		return new ResultSet($db, $this->executeStatement($db, $stmt));
	}

	/**
	 * Execute a statement
	 * @param Db $db the database handle
	 * @param Statement $stmt the statement
	 * @return type the result or resource
	 * @throws DbRuntimeException 
	 */
	private function executeStatement(Db $db, Statement $stmt) {
		$result = mysql_query($stmt->getSQLString(), $this->connection);
		if (!$result) {
			throw new DbRuntimeException(mysql_error(), $db);
		}
		return $result;
	}

	/**
	 * Fetch the next row, the result is returned as an assoc array
	 * @param Db $db the database handle
	 * @param resource $resource the resource
	 */
	public function fetchNextRow(ResultSet $rs) {
		$result = mysql_fetch_assoc($rs->getResource());
		if (mysql_errno() == 0) {
			if ($result) {
				$rs->updateRowData($result);
				return true;
			} else {
				$rs->updateRowData(array());
				return false;
			}
		} else {
			throw new DbRuntimeException(mysql_error(), $db);
		}
	}

	/**
	 * Close the row fetcher
	 * @param resource $resource is the resource
	 * @return type the result
	 * @throws DbRuntimeException 
	 */
	public function closeRowFetcher(ResultSet $rs) {
		$result = mysql_free_result($rs->getResource());
		if (!$result) {
			throw new DbRuntimeException(mysql_error(), $db);
		}
		return $result;
	}

	/**
	 * Start a transaction
	 * @return type 
	 */
	public function txnBegin() {
		mysql_query("START TRANSACTION", $this->connection);
		return mysql_query("BEGIN", $this->connection);
	}

	/**
	 * Commit a pending transaction
	 * @return type 
	 */
	public function txnCommit() {
		return mysql_query("COMMIT", $this->connection);
	}

	/**
	 * Rollback a transaction
	 * @return type 
	 */
	public function txnRollback() {
		return mysql_query("ROLLBACK", $this->connection);
	}

	/**
	 * Create a string representation
	 * @return a human readable string 
	 */
	public function __toString() {
		$url = "mysql://$this->dbuser:XXX@$this->dbhost:$this->dbport/$this->dbname";
		return "[$url]";
	}

}

/**
 * Id Source 
 */
class MySQLIdSource implements IdSource {

	private static $lastId = 0;

	public function nextId() {
		do {
			$mt = explode(' ', microtime());
			$id = intval(1000 * ($mt[0] + $mt[1]));
		} while ($id == self::$lastId);
		self::$lastId = $id;
		return $id;
	}

}

?>

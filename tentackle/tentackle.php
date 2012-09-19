<?php

/*
 * Copyright (c) 2008 Meding Software Technik -- All Rights Reserved
 * 
 * @author <a href="mailto:uwe@uwemeding.com">Uwe Meding</a>
 */

require_once('tentackle/DbRuntimeException.php');
require_once('tentackle/Statement.php');
require_once('tentackle/PreparedStatement.php');
require_once('tentackle/ResultSet.php');
require_once('tentackle/Db.php');
require_once('tentackle/IdSource.php');
require_once('tentackle/DbObject.php');

/**
 * The tentackle interface 
 */
interface Tentackle {

	/**
	 * Get a database handle 
	 */
	public function newDb();

	/**
	 * Get the database host name 
	 */
	public function getHost();

	/**
	 * Get the database username 
	 */
	public function getUsername();

	/**
	 * Get the database name 
	 */
	public function getName();

	// ==== SQL formatting ====================================

	public function sqlLimitClause($limit);

	public function sqlOffsetClause($offset);

	public function sqlLimitOffsetClause($limit, $offset);

	// ==== QUERY/UPDATE support ==============================

	/**
	 * Execute an update on the database 
	 * @param $db is the database handle
	 * @param $stmt is the database statement
	 */
	public function executeUpdate(Db $db, Statement $stmt);

	/**
	 * Execute a query on the database
	 * @param $db is the database handle
	 * @param $stmt is the database statement 
	 */
	public function executeQuery(Db $db, Statement $stmt);

	public function fetchNextRow(ResultSet $resource);

	public function closeRowFetcher(ResultSet $resource);

	// ==== TXN support =======================================

	/**
	 * Begin a transaction 
	 */
	public function txnBegin();

	/**
	 * Commit a pending transaction 
	 */
	public function txnCommit();

	/**
	 * Rollback a pending transaction 
	 */
	public function txnRollback();
}

/**
 * The MySQL Tentackle factory 
 */
class TentackleFactory {

	private function __construct() {
		
	}

	/**
	 * Get a tentackle handle from a url :
	 * pattern:  mysql://USER:PASS@HOST[:PORT]/NAME
	 * for example: mysql://dbuser:dbpass@somehost.com:3306/laber
	 */
	public static function fromURL($url) {
		$props = parse_url($url);
		if (!isset($props[port])) {
			$props['port'] = 3306;
		}
		$props['name'] = trim(substr($props['path'], 1)); // drop the leading  /
		$props['type'] = $props['scheme'];

		return self::fromProperties($props);
	}

	/**
	 * Get a tentackle handle from a properties list. The following names are 
	 * expected:
	 *    $props['type']    -- db type name
	 *    $props['host']    -- db hostname
	 *    $props['user']    -- db username
	 *    $props['pass']    -- db password
	 *    $props['port']    -- db port
	 *    $props['name']    -- db name
	 * @param type $props 
	 */
	public static function fromProperties($props) {
		return self::fromSettings($props['type'], $props['host'], $props['user'], $props['pass'], $props['name'], $props['port']);
	}

	/**
	 * Get a tentackle handle from settings
	 * @param type $host is the database host
	 * @param type $user is the database user
	 * @param type $pass is the database password
	 * @param type $name is the database name
	 * @param type $port is the database port
	 */
	public static function fromSettings($type, $host, $user, $pass, $name, $port = 3306) {
		switch ($type) {
			default:
				throw new DBRuntimeException($type . ": unsupported database");

			case "mysql":
				return new MySQLTentackle($host, $user, $pass, $name, $port);
		}
	}

}

// MySQL is all we support at this point
require_once('tentackle/mysql_db.php');


// Ensure all unhandled errors are thrown as exception
set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext) {
			// if error_reporting doesn't say to ignore this error type
			if (error_reporting() & $errno) {
				throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
			}
		});
?>

<?php

/*
 * Copyright (c) 2008 Meding Software Technik -- All Rights Reserved
 */

/**
 * Is an abstract handle to the database. It isolates any implementation
 * from the gory details of a the database interface.
 *
 * @author <a href="mailto:uwe@uwemeding.com">Uwe Meding</a>
 */
class Db {

	public static $WHEREALL_CLAUSE = " WHERE 1=1";
	private $tentackle;
	var $idSource;
	var $preparedStatementPool;

	function __construct(Tentackle $tentackle) {
		$this->tentackle = $tentackle;
		$this->preparedStatementPool = array("*UNDEFINED*");
	}

	/**
	 * Get the id source
	 * @return the id source 
	 */
	public function getIdSource() {
		return $this->idSource;
	}

	/**
	 * Set the id source
	 * @param IdSource $idSource 
	 */
	public function setIdSource(IdSource $idSource) {
		$this->idSource = $idSource;
	}

	/**
	 * Prepares a statement
	 * @param $stmt 
	 */
	public function prepareStatement($stmt) {
		$this->preparedStatementPool[] = $stmt;
		return count($this->preparedStatementPool) - 1;
	}

	/**
	 * Get a particular prepared statement
	 * @param $stmtId is the statement id
	 * @return \PreparedStatement 
	 */
	public function getPreparedStatement($stmtId) {
		return new PreparedStatement($this, $this->preparedStatementPool[$stmtId]);
	}

	public function __toString() {
		return print_r($this->preparedStatementPool, true);
	}

	/**
	 * Detach the database handle 
	 */
	public function detach() {
		
	}

	// =============================================
	// SQL formatting support

	public function sqlFormatLimitClause($limit) {
		return $this->tentackle->sqlLimitClause($limit);
	}

	public function sqlFormatOffsetClause($offset) {
		return $this->tentackle->sqlOffsetClause($offset);
	}

	public function sqlFormatLimitOffsetClause($limit, $offset) {
		return $this->tentackle->sqlLimitOffsetClause($limit, $offset);
	}
	
	// =============================================
	// QUERY/UPDATE support
	
	/**
	 * Do the the update
	 * @param Statement $stmt is the statement
	 * @return type 
	 */
	public function do_update(Statement $stmt) {
		return $this->tentackle->executeUpdate($this, $stmt);
	}

	/**
	 * Do the query
	 * @param Statement $stmt is the statement
	 * @return type 
	 */
	public function do_query(Statement $stmt) {
		return $this->tentackle->executeQuery($this, $stmt);
	}

	

	// =============================================
	// RESULT SET support

	public function rs_next(ResultSet $rs) {
		return $this->tentackle->fetchNextRow($rs);
	}

	public function rs_close(ResultSet $rs) {
		return $this->tentackle->closeRowFetcher($rs);
	}

	// =============================================
	// TXN support

	/**
	 * Start a transaction
	 * @return type 
	 */
	public function begin() {
		return $this->tentackle->txnBegin();
	}

	/**
	 * Commit a pending transaction
	 * @return type 
	 */
	public function commit() {
		return $this->tentackle->txnCommit();
	}

	/**
	 * Rollback a transaction
	 * @return type 
	 */
	public function rollback() {
		return $this->tentackle->txnRollback();
	}

}

?>

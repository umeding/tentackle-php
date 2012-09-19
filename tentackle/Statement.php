<?php

/*
 * Copyright (c) 2008 Meding Software Technik -- All Rights Reserved
 */

/**
 * Abstraction of a general SQL statment.
 * 
 * @author <a href="mailto:uwe@uwemeding.com">Uwe Meding</a>
 */
class Statement {

	var $db;
	var $stmt;

	var $ready;
	var $cancelled;
	var $running;

	/**
	 * Construct a database statement
	 * @param type $db the database connection
	 */
	public function __construct(Db $db, $stmt) {
		$this->db = $db;
		$this->stmt = $stmt;
	}


	/**
	 * Get the SQL string
	 * @return the SQL string
	 */
	public function getSQLString() {
		return $this->stmt;
	}

	/**
	 * Get a human readable string
	 * @return type 
	 */
	public function __toString() {
		return $this->stmt;
	}

	/**
	 * Get the database connection
	 * @return the database connection
	 */
	public function getDb() {
		return $this->db;
	}

	/**
	 * Marks a statement ready for consumption 
	 */
	public function markReady() {
		if ($this->isMarkReady()) {
			throw new DbRuntimeException("statement " . $this->stmt . " marked ready but not consumed yet", $this->db);
		}
		$this->ready = TRUE;
		$this->cancelled = FALSE;
	}

	/**
	 * Unmarks (consumes) this statement previously marked ready 
	 */
	public function unmarkReady() {
		if (!$this->isMarkedReady()) {
			throw new DbRuntimeException("statement " . $this->stmt . " already consumed");
		}
		$this->ready = FALSE;
		$this->running = FALSE;
	}

	/**
	 * Test if this statement is marked ready
	 * @return the ready mark 
	 */
	public function isMarkedReady() {
		return $this->ready;
	}

	/**
	 * Consume the statement without executing it 
	 */
	public function consume() {
		$this->unmarkReady();
		
	}

	/**
	 * Test if this statement has been cancelled
	 *
	 * @return type 
	 */
	public function isCancelled() {
		return $this->cancelled;
	}

	/**
	 * Test if this statement is closed
	 */
	public function isClosed() {
		return $this->stmt == NULL;
	}

	/**
	 * Close this statement 
	 */
	public function close() {

		if (!$this->isClosed()) {
			if ($this->isMarkedReady()) {
				// statement is not consumed -> cleanup
				$this->ready = FALSE;
			}
			$this->stmt = NULL;
		}
	}

	protected function detachDb() {
		$db = $this->getDb();
		if($db != null) {
			$db->detach();
		}
		$db = NULL;
	}

	/**
	 * Execute an update
	 * @return type 
	 */
	public function executeUpdate() {
		return $this->db->do_update($this);
	}

	/**
	 * Execute a query
	 * @return type 
	 */
	public function executeQuery() {
		return $this->db->do_query($this);
	}

}

?>

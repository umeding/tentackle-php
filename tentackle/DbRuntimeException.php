<?php

/**
 * Copyright (c) 2009 Meding Software Technik -- All Rights Reserved 
 * 
 * @author <a href="mailto:uwe@uwemeding.com">Uwe Meding</a>
 */

class DBRuntimeException extends Exception {

	var $db;

	function __construct($message, $db=NULL) {
		parent::__construct($message);
		$this->db = $db;
	}

	/**
	 * Get the database handle
	 * @return the database handle
	 */
	public function getDb() {
		return $this->db;
	}
}

?>
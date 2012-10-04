<?php

/*
 * Copyright (c) 2009 Meding Software Technik -- All Rights Reserved
 */

/**
 * DbObjectClassVariables
 *
 * @author uwe
 */
class DbObjectClassVariables {

	private static $classMap = array();
	private $classname;
	private $tablename;
	// the class variables
	public $insertStmtId;
	public $updateStmtId;
	public $selectStmtId;
	public $selectAllStmtId;
	public $deleteStmtId;
	public $selectSerialStmtId;
	public $updateSerialStmtId;

	public function __construct($classname, $tablename) {
		$this->classname = $classname;
		$this->tablename = $tablename;
		// save the class variables
		self::$classMap[$tablename] = $this;
	}

	/**
	 * Get the class name
	 * @return type 
	 */
	public function getClassName() {
		return $this->classname;
	}

	/**
	 * Get the tablename;
	 * @return type 
	 */
	public function getTableName() {
		return $this->tablename;
	}

	/**
	 * Get the variables for a tablename
	 * @param type $tablename 
	 */
	public static function getVariables($tablename) {
		return self::$classMap[$tablename];
	}

}

?>
<?php

/*
 * Copyright (c) 2008 Meding Software Technik -- All Rights Reserved
 */

/**
 * The DbObject is the main workhorse that interacts with the database.
 * 
 * @author <a href="mailto:uwe@uwemeding.com">Uwe Meding</a>
 */
class DbObject {

	public static $FIELD_ID = "id";
	public static $FIELD_SERIAL = "serial";
	var $id;
	var $serial;
	private $db;
	// This is static so we can reuse the same statements multiple times
	private static $insertStmtId;
	private static $updateStmtId;
	private static $selectStmtId;
	private static $selectAllStmtId;
	private static $deleteStmtId;
	private static $selectSerialStmtId;
	private static $updateSerialStmtId;

	public function __construct($db = NULL) {
		$this->db = $db;
		$this->id = 0;
		$this->serial = 0;
	}

	/**
	 * Get the database connection
	 * @return type 
	 */
	public function getDb() {
		return $this->db;
	}

	/**
	 * Set the database connection
	 * @param $db is the database connection
	 */
	public function setDb(Db $db) {
		$this->db = $db;
	}

	/**
	 * Get the SQL text to retrieve all objects
	 * @return the sql text 
	 */
	public function getAllSqlFields() {
		return "SELECT * FROM " . $this->getTableName() . Db::$WHEREALL_CLAUSE;
	}

	/**
	 * Get the insert statement id
	 * @return the insert statement id 
	 */
	public function getInsertStatementId() {
		return self::$insertStmtId;
	}

	/**
	 * Set the insert statement id
	 * @param $stmtId is the insert statement id
	 */
	public function setInsertStatementId($stmtId) {
		self::$insertStmtId = $stmtId;
	}

	/**
	 * Get the update statement identifier
	 * @return the update statement id
	 */
	public function getUpdateStatementId() {
		return self::$updateStmtId;
	}

	/**
	 * Set the update statement identifier
	 * @param $stmtId the update statement identifier
	 */
	public function setUpdateStatementId($stmtId) {
		self::$updateStmtId = $stmtId;
	}

	/**
	 * Get the select statement id
	 * @return select statement id 
	 */
	public function getSelectStatementId() {
		return self::$selectStmtId;
	}

	/**
	 * Set the select statement id
	 * @param $stmtId the select statement id
	 */
	public function setSelectStatementId($stmtId) {
		self::$selectStmtId = $stmtId;
	}

	/**
	 * Prepare the select statement
	 * @return type the select statement id
	 */
	public function prepareSelectStatement() {
		$stmtId = $this->getSelectStatementId();
		if ($stmtId == 0) {
			$stmtId = $this->getDb()->prepareStatement(
					"SELECT * FROM " . $this->getTableName() . " WHERE " . self::$FIELD_ID . "=?"
			);
			$this->setSelectStatementId($stmtId);
		}
		return $stmtId;
	}

	/**
	 * Get the select statement id
	 * @return select statement id 
	 */
	public function getSelectAllStatementId() {
		return self::$selectAllStmtId;
	}

	/**
	 * Set the select statement id
	 * @param $stmtId the select statement id
	 */
	public function setSelectAllStatementId($stmtId) {
		self::$selectAllStmtId = $stmtId;
	}

	/**
	 * Prepare the select statement
	 * @return type the select statement id
	 */
	public function prepareSelectAllStatement() {
		$stmtId = $this->getSelectAllStatementId();
		if ($stmtId == 0) {
			$stmtId = $this->getDb()->prepareStatement(
					"SELECT * FROM " . $this->getTableName()
			);
			$this->setSelectAllStatementId($stmtId);
		}
		return $stmtId;
	}

	/**
	 * Get the select serial statement id
	 * @return select statement id 
	 */
	public function getSelectSerialStatementId() {
		return self::$selectSerialStmtId;
	}

	/**
	 * Set the select statement id
	 * @param $stmtId the select statement id
	 */
	public function setSelectSerialStatementId($stmtId) {
		self::$selectSerialStmtId = $stmtId;
	}

	/**
	 * Prepare the select serial statememt
	 * @return type the select  serial statement id
	 */
	public function prepareSelectSerialStatement() {
		$stmtId = $this->getSelectSerialStatementId();
		if ($stmtId == 0) {
			$stmtId = $this->getDb()->prepareStatement(
					"SELECT " . self::$FIELD_SERIAL . " FROM " . $this->getTableName() . " WHERE " . self::$FIELD_ID . "=?"
			);
			$this->setSelectSerialStatementId($stmtId);
		}
		return $stmtId;
	}

	/**
	 * Get the update serial statement id
	 * @return select statement id 
	 */
	public function getUpdateSerialStatementId() {
		return self::$updateSerialStmtId;
	}

	/**
	 * Set the update statement id
	 * @param $stmtId the update statement id
	 */
	public function setUpdateSerialStatementId($stmtId) {
		self::$updateSerialStmtId = $stmtId;
	}

	/**
	 * Prepare the select serial statememt
	 * @return type the select  serial statement id
	 */
	public function prepareUpdateSerialStatement() {
		$stmtId = $this->getUpdateSerialStatementId();
		if ($stmtId == 0) {
			$stmtId = $this->getDb()->prepareStatement(
					"UPDATE " . $this->getTableName() . " SET " . self::$FIELD_SERIAL . "=" . self::$FIELD_SERIAL . "+1 WHERE " . self::$FIELD_ID . "=?"
			);
			$this->setUpdateSerialStatementId($stmtId);
		}
		return $stmtId;
	}

	/**
	 * Get the delete statement id
	 * @return the delete statement id 
	 */
	public function getDeleteStatementId() {
		return self::$deleteStmtId;
	}

	/**
	 * Set the delete statement identifier
	 * @param $stmtId the delete statement identifier
	 */
	public function setDeleteStatementId($stmtId) {
		self::$deleteStmtId = $stmtId;
	}

	public function prepareDeleteStatement() {
		$stmtId = $this->getDeleteStatementId();
		if ($stmtId == 0) {
			$stmtId = $this->getDb()->prepareStatement(
					"DELETE FROM " . $this->getTableName() . " WHERE " . self::$FIELD_ID . "=?"
			);
			$this->setDeleteStatementId($stmtId);
		}
		return $stmtId;
	}


	/**
	 * Get the text to select all the fields
	 * @return type 
	 */
	public function getSqlAllFields() {
		return "* FROM ".$this->getTableName().Db::$WHEREALL_CLAUSE;
	}

	/**
	 * Get the sql string to select all colums of the class
	 * @return the select statement
	 */
	public function getSqlSelectAllFields() {
		return "SELECT ".$this->getSqlAllFields();
	}

	/**
	 * Get a new id from the database 
	 */
	private function newId() {
		if ($this->id == 0) {
			$idSource = $this->db->getIdSource();
			$this->id = $idSource->nextId();
		}
	}

	public function orderBy() {
		return null;
	}

	/**
	 * Select a row
	 * @param $id is the row id
	 */
	public function select($id) {
		$ps = $this->db->getPreparedStatement($this->prepareSelectStatement());
		$ps->setValue(1, $id);
		$rs = $ps->executeQuery();
		try {
			if ($rs->next()) {
				$this->getFields($rs);
				$obj = $this;
			} else {
				$obj = NULL;
			}
			$rs->close();
			return $obj;
		} catch (Exception $ex) {
			$rs->close();
			if ($ex instanceof DBRuntimeException) {
				throw $ex;
			} else {
				throw new DbRuntimeException($ex->getTraceAsString(), $this->db);
			}
		}
	}

	/**
	 * Select everything in a table 
	 */
	public function selectAll() {
		$stmtId = $this->prepareSelectAllStatement();
		$ps = $this->db->getPreparedStatement($stmtId);
		$list = array();
		$rs = $ps->executeQuery();
		try {
			while ($rs->next()) {
				$obj = $this->newObject();
				$obj->getFields($rs);
				$list[] = $obj;
			}
			$rs->close();
		} catch (Exception $ex) {
			$rs->close();
			if ($ex instanceof DBRuntimeException) {
				throw $ex;
			} else {
				throw new DbRuntimeException($ex->getTraceAsString(), $this->db);
			}
		}
		return $list;
	}

	/**
	 * Create a new object of this type
	 * @return type 
	 */
	public function newObject() {
		$classname = get_class($this);
		return new $classname($this->db);
	}

	/**
	 * Save this object 
	 */
	public function save() {
		if ($this->id <= 0) {
			if ($this->id == 0) {
				$this->newId();
			}
			$this->insert();
		} else {
			$this->update();
		}
	}

	/**
	 * Insert this object 
	 */
	public function insert() {
		$this->db->begin();
		$oldId = $this->id;
		$oldSerial = $this->serial;
		try {
			$this->serial++;

			$stmtId = $this->prepareInsertStatement();
			$ps = $this->db->getPreparedStatement($stmtId);
			$this->setFields($ps);
			$ps->executeUpdate();

			$this->db->commit();
		} catch (Exception $ex) {
			// cleanup after exception
			$this->db->rollback();
			$this->id = $oldId;
			$this->serial = $oldSerial;
			if ($ex instanceof DBRuntimeException) {
				throw $ex;
			} else {
				throw new DbRuntimeException($ex->getTraceAsString(), $this->db);
			}
		}
	}

	/**
	 * Update this object 
	 */
	public function update() {
		$this->db->begin();
		try {
			$stmtId = $this->prepareUpdateStatement();
			$ps = $this->db->getPreparedStatement($stmtId);
			$this->setFields($ps);
			$ps->executeUpdate();

			$this->db->commit();
		} catch (Exception $ex) {
			$this->db->rollback();
		}
	}

	/**
	 * Delete this object 
	 */
	public function delete() {
		$this->db->begin();
		try {
			$stmtId = $this->prepareDeleteStatement();
			$ps = $this->db->getPreparedStatement($stmtId);
			$ps->setValue(1, $this->id);
			$ps->setValue(2, $this->serial);
			$ps->executeUpdate();

			$this->db->commit();
		} catch (Exception $ex) {
			$this->db->rollback();
		}
	}

	/**
	 * Get the table name 
	 * @return the table name
	 */
	public function getTableName() {
		return "*UNDEFINED TABLENAME*";
	}

	public final function getId() {
		return $this->id;
	}

	/**
	 * Set the id for this record
	 * @param $id is the id
	 */
	public final function setId($id) {
		$this->id = $id;
	}

	/**
	 * Get the table serial number 
	 */
	public final function getSerial() {
		return $this->serial;
	}

	/**
	 * Set the serial number on the record
	 * @param $serial is the serial number
	 */
	public final function setSerial($serial) {
		$this->serial = $serial;
	}

}

?>

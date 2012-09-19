<?php
/*
 * Copyright (c) 2009 Meding Software Technik -- All Rights Reserved
 */

/**
 * A reasonable abstraction for query results.
 * 
 * @author <a href="mailto:uwe@uwemeding.com">Uwe Meding</a>
 */
class ResultSet {

	private $db;
	private $resource;
	private $row;

	public function __construct(Db $db, $resource) {
		$this->db = $db;
		$this->resource = $resource;
		$this->row = array();
	}

	public function __toString() {
		return "ResultSet: " . print_r($this->row, true);
	}

	public function getResource() {
		return $this->resource;
	}

	public function updateRowData($rowData) {
		$this->row = $rowData;
	}

	/**
	 * Get the next row 
	 * @return the next row
	 */
	public function next() {
		return $this->db->rs_next($this);
	}

	/**
	 * Close this result set 
	 */
	public function close() {
		return $this->db->rs_close($this);
	}

	/**
	 * Get a value by position
	 * @param type $nameOrPos 
	 */
	public function getValueByPos($pos) {
		if (is_numeric($pos)) {
			$index = $pos - 1;
			if ($index < 0) {
				throw new DBRuntimeException($pos . ": position not found in result set", $this->db);
			}
			if ($index > count($this->row)) {
				throw new DBRuntimeException($pos . ": position not found in result set", $this->db);
			}
			$keys = array_keys($this->row);
			$name = $keys[$index];
			return $this->row[$name];
		} else {
			throw new DBRuntimeException($pos . ": position not found in result set", $this->db);
		}
	}

	/**
	 * Get a value by name
	 * @param type $nameOrPos 
	 */
	public function getValueByName($name) {
		if (isset($this->row[$name])) {
			return $this->row[$name];
		} else {
			throw new DBRuntimeException($name . ": name not found in result set", $this->db);
		}
	}

}
?>
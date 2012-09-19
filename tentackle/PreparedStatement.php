<?php

/*
 * Copyright (c) 2008 Meding Software Technik -- All Rights Reserved
 */

/**
 * Implementation of a prepared statement equivalent. Allows the
 * substititon of '?' parameters, but other than that does very 
 * little optimization.
 * 
 * @author <a href="mailto:uwe@uwemeding.com">Uwe Meding</a>
 */
class PreparedStatement extends Statement {

	private $format;
	private $subs;

	public function __construct(Db $db, $stmt) {
		parent::__construct($db, $stmt);

		// decompose the SQL statement and peel out
		// the '?' positions.
		$this->format = explode('?', $stmt);
		$pos = 0;
		$len = strlen($stmt);
		for ($i = 0; $i < $len; $i++) {
			if ($stmt[$i] === '?') {
				$this->subs[++$pos] = '?';
			}
		}
	}

	/**
	 * Set a value at a position
	 * @param type $pos the position
	 * @param type $value  the value
	 */
	public function setValue($pos, $value) {
		if (isset($this->subs[$pos])) {
			// Save the substition
			$this->subs[$pos] = is_numeric($value) ? $value : "'" . mysql_real_escape_string($value) . "'";

			// Do the substition
			$mapped = array_map(
					create_function('$a,$b', 'return "$a$b";'), $this->format, $this->subs);
			$this->stmt = implode(' ', $mapped);
		}
	}
}

?>

<?php

/*
 * Copyright (c) 2012 Meding Software Technik -- All Rights Reserved
 */

/**
 * Timestamp
 *
 * @author uwe
 */
class Timestamp {

	private $timeval;

	public function __construct($timeval) {
		$this->timeval = $timeval;
	}

	public function getTime() {
		return $this->timeval;
	}

	/**
     * Get the now timestamp
	 * @return \Timestamp 
	 */
	public static function now() {
		$mt = explode(' ', microtime());
		$millis = intval(1000 * ($mt[0] + $mt[1]));
		return new Timestamp($millis);
	}

	/**
	 * Take a reasonable date/time description and create a timestamp
	 * @param string $dateTimeString
	 * @param type $time_zone
	 * @return \Timestamp 
	 */
	public static function valueOf($dateTimeString = NULL, $time_zone = NULL) {

		if (is_null($dateTimeString)) {
			$dateTimeString = '@' . microtime(true);
		}

		if (is_null($time_zone)) {
			$time_zone = date_default_timezone_get(); //new DateTimeZone('UTC');
		}

		if (preg_match('/@(\\\\d+)\\\\.(\\\\d+)/', $dateTimeString, $matches)) {
			$time = $matches[1];
			$millisecond = $matches[2];
		} else {
			$time = $dateTimeString;
			$millisecond = 0;
		}
		$tz = new DateTimeZone($time_zone);
		$dt = new DateTime($time, $tz);
		$timeval = $dt->getTimestamp() * 1000 + $millisecond;

		return new Timestamp($timeval);
	}

	/**
	 * Get the sql date
	 *
	 * @return type 
	 */
	public function getSQLDate() {
		$ts = intval($this->timeval / 1000);
		$tz = new DateTimeZone(date_default_timezone_get());

		$dt = new DateTime("@$ts", $tz);
		$date = date("Y-m-d H:i:s", $dt->format('U'));
		return $date;
	}

	/**
	 * Get the timestamp in 'date' format
	 * @return type 
	 */
	public function __toString() {
		$ts = intval($this->timeval / 1000);
		$tz = new DateTimeZone(date_default_timezone_get());

		$dt = new DateTime("@$ts", $tz);
		$date = date("D d M Y H:i:s T O", $dt->format('U'));
		return $date;
	}

}

?>

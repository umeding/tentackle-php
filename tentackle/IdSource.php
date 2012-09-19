<?php

/*
 * Copyright (c) 2008 Meding Software Technik -- All Rights Reserved
 */

/**
 * Record id creation source
 * 
 * @author <a href="mailto:uwe@uwemeding.com">Uwe Meding</a>
 */
interface IdSource {

	/**
	 * Get the next available id 
	 * @return next available id
	 */
	public function nextId();
}

?>

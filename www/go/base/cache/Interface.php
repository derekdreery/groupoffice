<?php
/*
 * 
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 */

/**
 * Abstract class for simple key value store.
 * 
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl> 
 * @package GO.base.cache
 */

interface GO_Base_Cache_Interface{
	/**
	 * Store any value in the cache
	 * @param string $key
	 * @param mixed $value Will be serialized
	 * @param int $ttl Seconds to live
	 */
	public function set($key, $value, $ttl=0);
	
	/**
	 * Get a value from the cache
	 * 
	 * @param string $key
	 * @return boolean 
	 */
	public function get($key);
	
	/**
	 * Delete a value from the cache
	 * 
	 * @param string $key 
	 */
	public function delete($key);
	
	/**
	 * Flush all values 
	 */
	public function flush();
}
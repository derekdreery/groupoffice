<?php
/**
 * Abstract class for simple key value store.
 */
interface GO_Base_Cache_Interface{
	
	public function set($key, $value);
	public function get($key);
	public function delete($key);
	public function flush();
}
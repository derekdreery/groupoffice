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
 * Simple key value store that caches on disk.
 * 
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl> 
 * @package GO.base.cache
 */
class GO_Base_Cache_Disk implements GO_Base_Cache_Interface{
	
	private $_values;
	private $_file;
	private $_dirty=false;
	
	public function __construct(){
		$this->_file = GO::config()->file_storage_path.'cache/diskCache.txt';
		if(!GO::config()->debug)
			$this->_load();
	}
	
	private function _load(){
		if(!isset($this->_values)){
			
			if(file_exists($this->_file)){
				$data = file_get_contents($this->_file);
				$this->_values = unserialize($data);
			}else
			{
				$this->_values = array();
			}
		}
	}
	
	public function set($key, $value){
		$this->_values[$key]=$value;
		
		$this->_dirty=true;
		
	}
	
	public function get($key){
		return isset($this->_values[$key]) ? $this->_values[$key] : false;
	}
	
	public function delete($key){
		unset($this->_values[$key]);
		$this->_dirty=true;
	}
	public function flush(){
		$this->_values=array();
		$this->_dirty=true;
	}
	
	public function __destruct(){
		if($this->_dirty && !GO::config()->debug)
			file_put_contents($this->_file, serialize($this->_values));
	}
}
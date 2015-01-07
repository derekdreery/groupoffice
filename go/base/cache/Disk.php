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
	
	private $_ttls;
	private $_ttlFile;
	private $_ttlsDirty=false;
	private $_dir;
	
	private $_time;
	
	public function __construct(){
//		GO::debug("Using GO_Base_Cache_Disk cache");
		
		$this->_dir = GO::config()->tmpdir.'diskcache/';
		
		if(!is_dir($this->_dir))
			mkdir($this->_dir, 0777, true);
		
		$this->_ttlFile = $this->_dir.'ttls.txt';
		//if(!GO::config()->debug)
		$this->_load();
		
		$this->_time=time();
	}
	
	private function _load(){
		if(!isset($this->_ttls)){
			
			if(file_exists($this->_ttlFile)){
				$data = file_get_contents($this->_ttlFile);
				$this->_ttls = unserialize($data);
			}else
			{
				$this->_ttls = array();
			}
		}
	}
	/**
	 * Store any value in the cache
	 * @param string $key
	 * @param mixed $value Will be serialized
	 * @param int $ttl Seconds to live
	 */
	public function set($key, $value, $ttl=0){
		
		//don't set false values because unserialize returns false on failure.
		if($key===false)
			return true;
		
		$key = GO_Base_Fs_File::stripInvalidChars($key,'-');
						
		if($ttl){
			$this->_ttls[$key]=$this->_time+$ttl;
			$this->_ttlsDirty=true;
		}
		
		return file_put_contents($this->_dir.$key, serialize($value));
		
	}
	
	/**
	 * Get a value from the cache
	 * 
	 * @param string $key
	 * @return boolean 
	 */
	public function get($key){
		
		$key = GO_Base_Fs_File::stripInvalidChars($key, '-');
		
		if(!empty($this->_ttls[$key]) && $this->_ttls[$key]<$this->_time){
			unlink($this->_dir.$key);
			return false;
		}elseif(!file_exists($this->_dir.$key))
		{
			return false;
		}else
		{
			$data = file_get_contents($this->_dir.$key);
			$unserialized = unserialize($data);
			
			if($unserialized===false){
				trigger_error("Could not unserialize key data from file ".$this->_dir.$key);
				return false;
			}else
			{			
				return $unserialized;
			}
		}
	}
	
	/**
	 * Delete a value from the cache
	 * 
	 * @param string $key 
	 */
	public function delete($key){
		$key = GO_Base_Fs_File::stripInvalidChars($key, '-');
		
		unset($this->_ttls[$key]);
		$this->_ttlsDirty=true;
		@unlink($this->_dir.$key);
	}
	/**
	 * Flush all values 
	 */
	public function flush(){
		$this->_ttls=array();
		$this->_ttlsDirty=true;
		$folder = new GO_Base_Fs_Folder($this->_dir);
		$folder->delete();
		$folder->create(0777);
	}
	
	public function __destruct(){
		if($this->_ttlsDirty)
			file_put_contents($this->_ttlFile, serialize($this->_ttls));
	}
}
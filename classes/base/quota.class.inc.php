<?php
class quota {
	
	var $usage=0;
	var $quota=0;
	
	function __construct(){
		global $GO_CONFIG;
		
		$this->quota=$GO_CONFIG->quota;
		$this->usage = intval($GO_CONFIG->get_setting('file_storage_usage'));
		
	}
	
	function get(){
		return $this->usage;		
	}
	
	function set($usage){
		global $GO_CONFIG;
		$this->usage=$usage;
		return $GO_CONFIG->save_setting('file_storage_usage', $usage);		
	}
	
	function reset(){
		global $GO_CONFIG;
		
		$this->usage = File::get_directory_size($GO_CONFIG->file_storage_path);
		return $GO_CONFIG->save_setting('file_storage_usage', $this->usage);
	}
	
	function check($usage)
	{
		return $this->quota==0 || $this->usage+$usage<=$this->quota;
	}
	
	function add($usage)
	{
		if($this->quota>0)
		{
			$this->usage+=$usage;
			$this->set($this->usage);
		}
	}	
	
	function delete($path)
	{
		if($this->quota>0)
		{
			if(is_dir($path))
			{
				$size = File::get_directory_size($path);
				
			}else
			{
				$size = filesize($path);
			}			
			$this->add(-$size);
		}
	}
}
?>
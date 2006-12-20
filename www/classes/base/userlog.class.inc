<?php
class userlog{

	var $handle;
	var $file;
	
	function userlog($file, $mode='w')
	{
		global $GO_CONFIG;
		
		$this->file=$file;
		//$file = $GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'].'/'.$file;
	
		$this->handle=fopen($file, 'w');
		
		$this->log('Log opened at: '.date(date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], get_time())));
	}
	
	function reopen()
	{
		$this->handle=fopen($this->file, 'a');
	}
	
	function move($new_file)
	{
		$this->close();		
		
		mkdir_recursive(dirname($new_file));
		
		go_log(LOG_DEBUG, 'Logfile renamed from: '.$this->file.' to '.$new_file);
		if(!rename($this->file, $new_file))
		{
			return false;
		}
		$this->file=$new_file;
		$this->handle=fopen($new_file, 'a');		
	}
	
	function log($message)
	{
		fwrite($this->handle, $message."\n");	
	}
	
	function close()
	{
		fclose($this->handle);
	}
}

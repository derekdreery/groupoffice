<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package go.basic
 */

/**
 * This class is used to log information for for a specific user
 * 
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Id$
 * @copyright Copyright Intermesh
 * @package go.basic
 * @since Group-Office 1.0
 */
class userlog{

	var $handle;
	var $file;
	var $mode;
	var $max_size=0;
	
	function userlog($file, $mode='w', $max_size=0)
	{
		global $GO_CONFIG;
		
		$this->max_size=$max_size;
		$this->mode=$mode;
		$this->file=$file;
		//$file = $GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'].'/'.$file;
	
		$this->handle=fopen($file, 'w');
		
		$this->log('Log opened at: '.date(date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], time())));
	}
	
	function reopen()
	{
		$this->handle=fopen($this->file, 'a');
	}
	
	function move($new_file)
	{
		$this->close();		
		
		if(!rename($this->file, $new_file))
		{
			return false;
		}
		$this->file=$new_file;
		$this->handle=fopen($new_file, 'a');
		
		/*if(($this->mode=='a' || $this->mode=='a+') && file_exists($new_file) && ($this->max_size ==0 || filesize($new_file)<$this->max_size))
		{			
			$this->handle=fopen($new_file, 'a+');
			fwrite($this->handle,file_get_contents($this->file));	
			unlink($this->file);
			$this->file=$new_file;
		}else {
		
		
			mkdir_recursive(dirname($new_file));
			
			go_log(LOG_DEBUG, 'Logfile renamed from: '.$this->file.' to '.$new_file);
			if(!rename($this->file, $new_file))
			{
				return false;
			}
			$this->file=$new_file;
			$this->handle=fopen($new_file, 'a');		
		}*/
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

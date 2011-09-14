<?php
/**
 * A filesystem folder
 */
class GO_Base_Fs_Folder extends GO_Base_Fs_Base {


	/**
	 * Get folder directory listing.
	 * 
	 * @param boolean $getHidden
	 * @return GO_Base_Fs_File or GO_Base_Fs_Folder
	 */
	public function ls($getHidden=false) {
		if (!$dir = @opendir($this->path))
			throw new Exception("Could not open " . $this->path);

		$folders = array();
		while ($item = readdir($dir)) {
			$folderPath = $this->path.'/'.$item;
			if ($item != "." && $item != ".." &&
							($getHidden || !(strpos($item, ".") === 0) )) {
			
				if(is_dir($folderPath))
					$folders[] = new GO_Base_Fs_Folder($folderPath);
				else
					$folders[] = new GO_Base_Fs_File($folderPath);
			}
		}
		closedir($dir);
		
		return $folders;
	}
	
	/**
	 * Delete the folder
	 * 
	 * @return boolean 
	 */
	public function delete(){
		
		$items = $this->ls(true);
		
		foreach($items as $item){
			if(!$item->delete())
				return false;
		}
		
		return !is_dir($this->path) || rmdir($this->path);
	}
	
	/**
	 * Create the folder
	 * 
	 * @return boolean 
	 */
	public function create(){
		
		if(is_dir($this->path))
			return true;
		
		if(mkdir($this->path, GO::config()->folder_create_mode,true)){
			if(GO::config()->file_change_group)
				chgrp ($this->path, GO::config()->file_change_group);
			
			return true;
		}else
		{
			return false;
		}
	}
	
	
	/**
	 * Checks if a filename exists and renames it.
	 *
	 * @param	string $filepath The complete path to the file
	 * @access public
	 * @return string  New filepath
	 */
	public function appendNumberToNameIfExists()
	{
		$origPath = $this->path;
		$x=1;
		while($this->exists())
		{			
			$this->path=$origPath.'_'.$x;
			$x++;
		}
		return $this->path;
	}
	

}
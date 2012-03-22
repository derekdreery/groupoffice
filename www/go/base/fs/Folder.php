<?php
/*
 * Copyright Intermesh BV
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * A folder on the filesystem
 * 
 * @package GO.base.fs
 * @version $Id: RFC822.class.inc 7536 2011-05-31 08:37:36Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @copyright Copyright Intermesh BV.
 */
class GO_Base_Fs_Folder extends GO_Base_Fs_Base {

	
	/**
	 * Get folder directory listing.
	 * 
	 * @param boolean $getHidden
	 * @return GO_Base_Fs_File or GO_Base_Fs_Folder
	 */
	public function ls($getHidden=false) {
		if (!$dir = opendir($this->path))
			return false;

		$folders = array();
		while ($item = readdir($dir)) {
			$folderPath = $this->path.'/'.$item;
			if ($item != "." && $item != ".." &&
							($getHidden || !(strpos($item, ".") === 0) )) {
			
				if(is_file($folderPath))					
					$folders[] = new GO_Base_Fs_File($folderPath);
				else
					$folders[] = new GO_Base_Fs_Folder($folderPath);
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
		
		//GO::debug("DELETE: ".$this->path());
		
		if(!$this->exists())
			return true;
		
		//just delete symlink and not contents of linked folder!
		if(is_link($this->path))
			return unlink($this->path);
		
		$items = $this->ls(true);
		
		foreach($items as $item){			
			if(!$item->delete())
				return false;
		}
		
		return !is_dir($this->path) || rmdir($this->path);
	}
	
	private function _validateSrcAndDestPath($srcPath, $destPath){
		if(strpos($srcPath.'/', $destPath.'/')===0)
		{
			$msg = 'The destination is located inside the source directory.';
			if(GO::config()->debug)
				$msg .= "\n\n".$srcPath.' -> '.$destPath;
			throw new Exception($msg);
		}
	}
	
	
	/**
	 * Move the folder to another folder.
	 * 
	 * @param GO_Base_Fs_Folder $destinationFolder 
	 * @param string $newFolderName Optionally rename the folder too.
	 * @param boolean $appendNumberToNameIfDestinationExists Rename the folder like "folder (1)" if it already exists.	 * 
	 * @return GO_Base_Fs_Folder $destinationFolder
	 */
	public function move($destinationFolder,$newFolderName=false,$appendNumberToNameIfDestinationExists=false){
		if(!$this->exists())
			return false;
		
		if(is_link($this->path)){
			$link = new GO_Base_Fs_File($this->path);
			return $link->move($destinationFolder, $newFolderName, false, $appendNumberToNameIfDestinationExists);
		}
		
		$this->_validateSrcAndDestPath($destinationFolder->path(), $this->path());
				
		if(!$newFolderName)
			$newFolderName=$this->name();
		
		$newPath = $destinationFolder->path().'/'.$newFolderName;		
				
		if($appendNumberToNameIfDestinationExists){
			$folder = new GO_Base_Fs_Folder($newPath);
			$folder->appendNumberToNameIfExists();
			$newPath = $folder->path();
		}		
		
		//do nothing if path is the same.
		if($newPath==$this->path())
			return true;
			
		$movedFolder = new GO_Base_Fs_Folder($newPath);
		if(!$movedFolder->create())
			throw new Exception ("Could not create ".$destinationFolder->path());		
		
		$ls = $this->ls(true);
		foreach($ls as $fsObject){
			$fsObject->move($movedFolder);
		}
		
		$this->delete();
		
		$this->path = $movedFolder->path();
		
		return true;
	}
	
	/**
	 * Copy a folder to another folder.
	 * 
	 * @param GO_Base_Fs_Folder $destinationFolder 
	 * @return boolean
	 */
	public function copy($destinationFolder){
		$this->_validateSrcAndDestPath($destinationFolder->path(), $this->path());
		
		GO::debug('folder::copy: '.$this->path().' > '.$destinationFolder->path());
		
		$copiedFolder = new GO_Base_Fs_Folder($destinationFolder->path().'/'.$this->name());
		if(!$copiedFolder->create())
			throw new Exception ("Could not create ".$destinationFolder->path());
		
		$ls = $this->ls(true);
		foreach($ls as $fsObject){
			if($fsObject->isFolder()){				
				$newDestinationFolder= new GO_Base_Fs_Folder($destinationFolder->path().'/'.$this->name());				
				$fsObject->copy($newDestinationFolder);
			}else
			{
				$fsObject->copy($copiedFolder);
			}
		}
		
		return $copiedFolder;
	}
	
	/**
	 * Create the folder
	 * 
	 * @param int $permissionsMode <p>
	 * Note that mode is not automatically
	 * assumed to be an octal value, so strings (such as "g+w") will
	 * not work properly. To ensure the expected operation,
	 * you need to prefix mode with a zero (0):
	 * </p>
	 * 
	 * @return boolean 
	 */
	public function create($permissionsMode=false){
		
		if(!$permissionsMode)
			$permissionsMode=GO::config()->folder_create_mode;		
		
		if(is_dir($this->path)){
			chmod($this->path, $permissionsMode);
			return true;
		}		
		
		if(mkdir($this->path, $permissionsMode,true)){
			if(GO::config()->file_change_group)
				chgrp ($this->path, GO::config()->file_change_group);
			
			return true;
		}else
		{
			GO::debug("Failed to create ".$this->path);
			
			return false;
		}
	}
	
	/**
	 * Set's default permissions and group ownership
	 */
	public function setDefaultPermissions(){
		chmod($this->path, GO::config()->folder_create_mode);
		if(!empty(GO::config()->file_change_group))
			chgrp($this->path, GO::config()->file_change_group);
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
			$this->path=$origPath.' ('.$x.')';
			$x++;
		}
		return $this->path;
	}
	
	/**
	 * Check if the given folder is a subfolder of this folder.
	 * 
	 * @param GO_Base_Fs_Folder $subFolder
	 * @return boolean 
	 */
	public function isSubFolderOf($parent){
		return strpos($this->path().'/', $parent->path().'/')===0;
	}
	

}
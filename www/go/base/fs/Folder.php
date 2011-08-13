<?php

class GO_Base_Fs_Folder extends GO_Base_Fs_File {


	
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
	
	public function delete(){
		
		$items = $this->ls(true);
		
		foreach($items as $item){
			if(!$item->delete())
				return false;
		}
		
		return rmdir($this->path);
	}
	
	public function create(){
		return !is_dir($this->path) ? mkdir($this->path, GO::config()->folder_create_mode,true) : true;
	}
	
	

}
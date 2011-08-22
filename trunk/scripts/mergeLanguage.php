<?php
require('../www/GO.php');

$folder = new GO_Base_Fs_Folder(GO::config()->root_path.'language');

$children = $folder->ls();

foreach($children as $child){
	if($child instanceof GO_Base_Fs_Folder){
		
		$section = $child->name();
		
		
		foreach($child->ls() as $file){
			if(preg_match('/^[^\.]+\.php/', $file->name()))
				$file->delete();			
		}
		
		
		foreach($child->ls() as $file){
			//echo $file->name()."\n";
			
			$iso = substr($file->name(),0,strpos($file->name(),'.'));
			
			$newFile = $child->path().'/'.$iso.'.php';
			
			$contents = file_get_contents($file->path());
			
			$contents = str_replace('<?php','', $contents);
			
			$contents =preg_replace('/\/\*.*\*\//mUs', "", $contents);
			$contents =preg_replace("/^\/\/.*/m", "", $contents);
			$contents =preg_replace("/require\(.*\);/", "", $contents);
	
			
			if($file->extension()=='php'){
				$contents = str_replace('$lang[\''.$section.'\']', '$l', $contents);
				$contents = str_replace('$lang["'.$section.'"]', '$l', $contents);	
			}else
			{
				$contents = preg_replace('/^GO\.lang[\s]*=[\s]*\{\};/m', '', $contents);				
				$contents = preg_replace('/^GO\.lang\.'.$section.'\.([a-zA-Z0-9_]+)[\s]*=/m', '$l["\\1"]=', $contents);
				$contents = preg_replace('/^GO\.lang\.([a-zA-Z0-9_]+)[\s]*=/m', '$l["\\1"]=', $contents);				
			}			
			
			$contents = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $contents);
			
			$contents = preg_replace('/([^;]+)[\n]{1}/', "\\1;\n", $contents);
			
			if(!file_exists($newFile))
				file_put_contents($newFile, "<?php\n\n");
			
			file_put_contents($newFile, $contents, FILE_APPEND);
			
		}
	}
}
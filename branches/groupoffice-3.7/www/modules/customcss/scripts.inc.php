<?php
if(isset($GO_MODULES->modules['files'])){
	require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
	$files = new files();

	$folder = $files->resolve_path('public/customcss', true);
	
	if($folder['acl_id']!=$GO_MODULES->modules['customcss']['acl_id']){
		$up_folder['id']=$folder['id'];
		$up_folder['acl_id']=$GO_MODULES->modules['customcss']['acl_id'];
		$files->update_row('fs_folders', 'id', $up_folder);
	}	
	
	$GO_SCRIPTS_JS .= 'GO.customcss.filesFolderId='.$folder['id'].';';
}
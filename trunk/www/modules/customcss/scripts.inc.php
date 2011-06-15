<?php
if(isset(GO::modules()->modules['files'])){
	require_once(GO::modules()->modules['files']['class_path'].'files.class.inc.php');
	$files = new files();

	$folder = $files->resolve_path('public/customcss', true);

	$GO_SCRIPTS_JS .= 'GO.customcss.filesFolderId='.$folder['id'].';';
}
<?php
if(GO::modules()->files){
	
	$folder = GO_Files_Model_Folder::model()->findByPath ('public/customcss', true);

	$GO_SCRIPTS_JS .= 'GO.customcss.filesFolderId='.$folder->id.';';
}
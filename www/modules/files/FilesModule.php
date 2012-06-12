<?php
/*
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * This class is used to parse and write RFC822 compliant recipient lists
 * 
 * @package GO.modules.files
 * @version $Id: RFC822.class.inc 7536 2011-05-31 08:37:36Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @copyright Copyright Intermesh BV.
 */

class GO_Files_FilesModule extends GO_Base_Module{	

	public function checkDatabase(&$response) {
		
		//create user home folders
		$stmt = GO_Base_Model_User::model()->find(array('ignoreAcl'=>true));
		
		while($user = $stmt->fetch()){
			$folder = GO_Files_Model_Folder::model()->findHomeFolder($user);
			//$folder->syncFilesystem();
			
			//$folder = GO_Files_Model_Folder::model()->findByPath('users/'.$user->username, true);
			if(!$folder->acl){
				$folder->setNewAcl($user->id);
				$folder->user_id=$user->id;
				$folder->visible=0;
				$folder->readonly=1;
				$folder->save();
			}
			//$folder->syncFilesystem();		
			
		}
		
		parent::checkDatabase($response);
	}
	
	public static function saveUser($user, $wasNew) {
		if($wasNew){
			$folder = GO_Files_Model_Folder::model()->findHomeFolder($user);			
		}
	}
	
	public static function deleteUser($user) {
		$folder = GO_Files_Model_Folder::model()->findByPath('users/'.$user->username, true);
		if($folder)
			$folder->delete();
	}
	
	public function autoInstall() {
		return true;
	}
	
}
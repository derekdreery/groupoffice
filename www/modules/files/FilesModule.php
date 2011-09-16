<?php
class GO_Files_FilesModule extends GO_Base_Module{	

	public function checkDatabase(&$response) {
		
		$stmt = GO_Base_Model_User::model()->find(array('ignoreAcl'=>true));
		
		while($user = $stmt->fetch()){
			
		}
		
		parent::checkDatabase($response);
	}
}
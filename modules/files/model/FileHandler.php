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
 * @author Merijn Schering <mschering@intermesh.nl
 * @property int $user_id
 * @property string $extension
 * @property string $cls
 */

class GO_Files_Model_FileHandler extends GO_Base_Db_ActiveRecord {
	

	public function tableName(){
		return 'fs_filehandlers';
	}
	
	public function primaryKey() {
		return array('user_id','extension');
	}	
}
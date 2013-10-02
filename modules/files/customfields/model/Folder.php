<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: GO_Files_Model_FolderCustomFieldsRecord.php 7607 2011-09-01 16:03:03Z <<USERNAME>> $
 * @copyright Copyright Intermesh
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */  

/**
 * The GO_Files_Model_FolderCustomFieldsRecord model
 *
 */

class GO_Files_Customfields_Model_Folder extends GO_Customfields_Model_AbstractCustomFieldsRecord{

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Notes_Model_CustomFieldsRecord 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	public function extendsModel(){
		return "GO_Files_Model_Folder";
	}
}

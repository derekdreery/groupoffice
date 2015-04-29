<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: GO_Tasks_Model_TaskCustomFieldsRecord.php 7607 2011-09-20 09:51:47Z <<USERNAME>> $
 * @copyright Copyright Intermesh
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 * @package GO.modules.Tasks
 */  
	 
/**
 * The CustomField Model for the GO_Tasks_Model_Task
 *
 * @package GO.modules.Tasks
 *
 */	
 
class GO_Tasks_Customfields_Model_Task extends GO_Customfields_Model_AbstractCustomFieldsRecord{
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Tasks_Model_CustomFieldsRecord 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	public function extendsModel(){
		return "GO_Tasks_Model_Task";
	}
}
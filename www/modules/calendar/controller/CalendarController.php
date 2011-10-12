<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: GO_Calendar_Controller_Calendar.php 7607 2011-09-14 10:07:02Z <<USERNAME>> $
 * @copyright Copyright Intermesh
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */  

/**
 * The GO_Calendar_Controller_Calendar controller
 *
 */

class GO_Calendar_Controller_Calendar extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Calendar_Model_Calendar';
	
	protected function getStoreParams($params) {
		if(!empty($params['resources'])){
			return GO_Base_Db_FindParams::newInstance()
							->criteria(GO_Base_Db_FindCriteria::newInstance()
											->addCondition('group_id', 1,'!='));
		}else
		{
			return parent::getStoreParams($params);
		}
	}
	
	
	protected function remoteComboFields() {
		return array(
				'user_id'=>'$model->user->name',
				'tasklist_id' => '$model->tasklist->name'
		);
	}
	
	
	
	
}
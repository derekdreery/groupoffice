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
	
	public function actionWritableCalendarsWithGroup($params){
		
		$store = GO_Base_Data_Store::newInstance(GO_Calendar_Model_Calendar::model());
		
		$findParams = $store->getDefaultParams()
						->join(GO_Calendar_Model_Group::model()->tableName(), GO_Base_Db_FindCriteria::newInstance()->addCondition('group_id', 'g.id', '=', 't', true, true),'g')
						->order(array('g.name','t.name'))
						->select('t.*,g.name AS group_name')
						->permissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION);
		
		$stmt = GO_Calendar_Model_Calendar::model()->find($findParams);
		
		
		$store->setStatement($stmt);
		
		$store->getColumnModel()->setFormatRecordFunction(array($this, 'formatCalendarWithGroup'));
		
		return $store->getData();
		
	}
	
	public function formatCalendarWithGroup($record, $model, $store){
		
		$record['user_name']=$model->user->name;
		$record['group_name']=$model->group_name;
		if(GO::modules()->customfields)
			$record['customfields']=GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Calendar_Model_Event", $model->group_id);
		
		
		return $record;
	}	
	
}
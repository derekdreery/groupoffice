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

class GO_Calendar_Controller_Participant extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Calendar_Model_Participant';
	
	protected function getStoreParams($params) {
		$c = GO_Base_Db_FindParams::newInstance()
						->criteria(GO_Base_Db_FindCriteria::newInstance()
										->addModel(GO_Calendar_Model_Participant::model())
										->addCondition('event_id', $params['event_id'])
										);
		return $c;
	}
	
	protected function prepareStore(GO_Base_Data_Store $store) {
		
		$store->getColumnModel()->setFormatRecordFunction(array($this, 'formatParticipantRecord'));
		
		return $store;
	}
	
	public function formatParticipantRecord($record, $model, $store){
		$record['available']=$model->isAvailable();
		
		return $record;
	}
	
	
	public function actionLoadOrganizer($params){
		
		$calendar = GO_Calendar_Model_Calendar::model()->findByPk($params['calendar_id']);
		
		$response['user_id']=$calendar->user_id;
		$response['name']=$calendar->user->name;
		$response['email']=$calendar->user->email;
		$response['status']=  GO_Calendar_Model_Participant::STATUS_ACCEPTED."";
		$response['is_organizer']="1";
		$response['available']= GO_Calendar_Model_Participant::userIsAvailable($params['start_time'],$params['end_time'],$calendar->user_id);
		$response['success']=true;
		
		return $response;
	}
	


}
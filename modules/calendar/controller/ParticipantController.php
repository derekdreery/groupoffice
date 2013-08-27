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
		
		$user = $calendar->user_id==1 ? GO::user() : $calendar->user;
		
		$participant = new GO_Calendar_Model_Participant();
		$participant->user_id=$user->id;
		$participant->name=$user->name;
		$participant->email=$user->email;
		$participant->is_organizer=true;
		
		return array('success'=>true, 'organizer'=>$participant->toJsonArray($params['start_time'],$params['end_time']));
	}
	
	public function actionReload($params){
		$event = empty($params['event_id']) ? false : GO_Calendar_Model_Event::model()->findByPk($params['event_id']);

		$participantAttrs=json_decode($params['participants']);

		$store = new GO_Base_Data_ArrayStore();
		
		foreach($participantAttrs as $participantAttr) {
			$participant = new GO_Calendar_Model_Participant();
			$participant->setAttributes($participantAttr);
			if($event)
				$participant->event_id=$event->id;
			
			$store->addRecord($participant->toJsonArray($params['start_time'], $params['end_time']));
		}
		
		return $store->getData();
	}

	public function actionGetContacts($params){
		$ids = json_decode($params['contacts']);

		$store = new GO_Base_Data_ArrayStore();

		foreach($ids as $contact_id){

			$contact=GO_Addressbook_Model_Contact::model()->findByPk($contact_id);

			$participant = new GO_Calendar_Model_Participant();
			if(($user = $contact->goUser)){				
				$participant->user_id=$user->id;
				$participant->name=$user->name;
				$participant->email=$user->email;
			}else{
				$participant->name=$contact->name;
				$participant->email=$contact->email;
			}

			$store->addRecord($participant->toJsonArray($params['start_time'], $params['end_time']));
		}
		
		return $store->getData();
	}
	
	
	public function actionGetCompanies($params){
		$ids = json_decode($params['companies']);

		$store = new GO_Base_Data_ArrayStore();

		foreach($ids as $company_id){

			$company=GO_Addressbook_Model_Company::model()->findByPk($company_id);

			$participant = new GO_Calendar_Model_Participant();
			$participant->name=$company->name;
			$participant->email=$company->email;

			$store->addRecord($participant->toJsonArray($params['start_time'], $params['end_time']));
		}
		
		return $store->getData();
	}
	
	
	public function actionGetUsers($params){
		$ids = json_decode($params['users']);

		$store = new GO_Base_Data_ArrayStore();

		foreach($ids as $user_id){

			$user=GO_Base_Model_User::model()->findByPk($user_id, false,  true);

			$participant = new GO_Calendar_Model_Participant();
			$participant->user_id=$user->id;
			$participant->name=$user->name;
			$participant->email=$user->email;
			$participant->is_organizer=!empty($params['is_organizer']);

			$store->addRecord($participant->toJsonArray($params['start_time'], $params['end_time']));
		}
		
		return $store->getData();
	}
	
	
	public function actionGetAddresslists($params){
		$ids = json_decode($params['addresslists']);

		$store = new GO_Base_Data_ArrayStore();
		
		$addedContacts=array();
		
		foreach($ids as $addresslist_id){

			$addresslist = GO_Addressbook_Model_Addresslist::model()->findByPk($addresslist_id, false, true);
			
			$stmt = $addresslist->contacts();
			
			foreach($stmt as $contact){
				
				if(!in_array($contact->id, $addedContacts)){
					
					$addedContacts[]=$contact->id;
					$participant = new GO_Calendar_Model_Participant();
					if(($user = $contact->goUser)){						
						$participant->user_id=$user->id;
						$participant->name=$user->name;
						$participant->email=$user->email;
					}else{
						$participant->name=$contact->name;
						$participant->email=$contact->email;
					}

					$store->addRecord($participant->toJsonArray($params['start_time'], $params['end_time']));
				}
			}
			
			$addedCompanies=array();
			
			$stmt = $addresslist->companies();
			
			foreach($stmt as $company){
				
				if(!in_array($company->id, $addedCompanies)){
					
					$addedCompanies[]=$company->id;
					$participant = new GO_Calendar_Model_Participant();
					$participant->name=$company->name;
					$participant->email=$company->email;					

					$store->addRecord($participant->toJsonArray($params['start_time'], $params['end_time']));
				}
			}
		}
		
		return $store->getData();
	}
	
	public function actionGetUserGroups($params){
		$ids = json_decode($params['groups']);

		$store = new GO_Base_Data_ArrayStore();
		
		$addedUsers=array();

		foreach($ids as $group_id){

			$group=GO_Base_Model_Group::model()->findByPk($group_id, false, true);
			
			$stmt = $group->users();
			
			foreach($stmt as $user){
				
				if(!in_array($user->id, $addedUsers)){
					
					$addedUsers[]=$user->id;
					
					$participant = new GO_Calendar_Model_Participant();
					$participant->user_id=$user->id;
					$participant->name=$user->name;
					$participant->email=$user->email;

					$store->addRecord($participant->toJsonArray($params['start_time'], $params['end_time']));
				}
			}
		}
		
		return $store->getData();
	}
	
	
	
	public function actionFreeBusyInfo($params) {

		$event_id = empty($params['event_id']) ? 0 : $params['event_id'];
		$emails = json_decode($params['emails'], true);
		$names = isset($params['names']) ? json_decode($params['names'], true) : $emails;

		$date=getdate(GO_Base_Util_Date::to_unixtime($params['date']));

		$daystart = mktime(0,0,0,$date['mon'], $date['mday'], $date['year']);
		$dayend = mktime(0,0,0,$date['mon'], $date['mday']+1, $date['year']);

		$merged_free_busy = array();
		for ($i = 0; $i < 1440; $i+=15) {
			$merged_free_busy[$i] = 0;
		}

		$response['results'] = array();
		$response['success'] = true;
		while ($email = array_shift($emails)) {
			$participant['name'] = array_shift($names);
			$participant['email'] = $email;
			$participant['freebusy'] = array();

			$user = GO_Base_Model_User::model()->findSingleByAttribute('email', $email);
			if ($user) {

				$participantModel = new GO_Calendar_Model_Participant();
				$participantModel->user_id=$user->id;
				$participantModel->name=$user->name;
				$participantModel->email=$user->email;
				$participantModel->event_id=$event_id;
				
				

				if ($participantModel->hasFreeBusyAccess()) {

					$freebusy = $participantModel->getFreeBusyInfo($daystart, $dayend);
					foreach ($freebusy as $min => $busy) {
						if ($busy == 1) {
							$merged_free_busy[$min] = 1;
						}
						$participant['freebusy'][] = array(
								'time' => date('G:i', mktime(0, $min)),
								'busy' => $busy);
					}
				}
			}
			$response['results'][] = $participant;
		}


		$participant['name'] = GO::t('allTogether','calendar');
		$participant['email'] = '';
		$participant['freebusy'] = array();

		foreach ($merged_free_busy as $min => $busy) {
			$participant['freebusy'][] = array(
					'time' => date($_SESSION['GO_SESSION']['time_format'], mktime(0, $min)),
					'busy' => $busy);
		}

		$response['results'][] = $participant;
		
		return $response;
	}

}
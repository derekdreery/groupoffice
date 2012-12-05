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
	
	
//	public function actionLoadOrganizer($params){
//		
//		$calendar = GO_Calendar_Model_Calendar::model()->findByPk($params['calendar_id']);
//		
//		$response['user_id']=$calendar->user_id;
//		$response['name']=$calendar->user->name;
//		$response['email']=$calendar->user->email;
//		$response['status']=  GO_Calendar_Model_Participant::STATUS_ACCEPTED."";
//		$response['is_organizer']="1";
//		$response['available']= GO_Calendar_Model_Participant::userIsAvailable($params['start_time'],$params['end_time'],$calendar->user_id);
//		$response['success']=true;
//		
//		return $response;
//	}

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

			$user=GO_Base_Model_User::model()->findByPk($user_id);

			$participant = new GO_Calendar_Model_Participant();
			$participant->user_id=$user->id;
			$participant->name=$user->name;
			$participant->email=$user->email;

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


}
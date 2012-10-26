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
	
	protected function allowGuests() {
		return array('exportics');
	}
	
//	protected function ignoreAclPermissions() {
//		return array('exportics');
//	}

	protected function getStoreParams($params) {
		
		$findParams =GO_Base_Db_FindParams::newInstance();
		
		$c = $findParams->getCriteria();
		
		if(!empty($params['resources'])){
			$c->addCondition('group_id', 1,'!=');
		}else
		{
			$c->addCondition('group_id', 1,'=');
		}
		return $findParams;
	}
	
	protected function remoteComboFields() {
		return array(
				'user_id'=>'$model->user->name',
				'tasklist_id' => '$model->tasklist->name'
		);
	}
	
	protected function afterLoad(&$response, &$model, &$params) {
		
		$url = GO::createExternalUrl('calendar', 'openCalendar', array(array(
			'calendars'=>array($response['data']['id']),
			'group_id'=>$response['data']['group_id'])
				));

		$response['data']['url']='<a class="normal-link" target="_blank" href="'.$url.'">'.GO::t('rightClickToCopy','calendar').'</a>';
		$response['data']['ics_url']='<a class="normal-link" target="_blank" href="'.GO::url("calendar/calendar/exportIcs", array("calendar_id"=>$response['data']['id'],"months_in_past"=>1)).'">'.GO::t('rightClickToCopy','calendar').'</a>';

		
		return parent::afterLoad($response, $model, $params);
	}
	
	protected function actionCalendarsWithGroup($params){
		
		$store = GO_Base_Data_Store::newInstance(GO_Calendar_Model_Calendar::model());
		
		if(!isset($params['permissionLevel']))
			$params['permissionLevel']=GO_Base_Model_Acl::READ_PERMISSION;
		
		$store->getColumnModel()->formatColumn('permissionLevel', '$model->permissionLevel');
		
		$this->processStoreDelete($store, $params);
		
		$findParams = $store->getDefaultParams($params)
						->join(GO_Calendar_Model_Group::model()->tableName(), GO_Base_Db_FindCriteria::newInstance()->addCondition('group_id', 'g.id', '=', 't', true, true),'g')
						->order(array('g.name','t.name'))						
						->select('t.*,g.name AS group_name')
						->permissionLevel($params['permissionLevel']);
		
		if(!empty($params['resourcesOnly']))
			$findParams->getCriteria ()->addCondition ('group_id', 1,'>');
		elseif(!empty($params['calendarsOnly']))
			$findParams->getCriteria ()->addCondition ('group_id', 1,'=');
		
		$stmt = GO_Calendar_Model_Calendar::model()->find($findParams);
		
		
		$store->setStatement($stmt);

		
		return $store->getData();
	}
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		
		$columnModel->formatColumn('user_name','$model->user->name');
		
		return parent::formatColumns($columnModel);
	}
	
	protected function beforeStore(&$response, &$params, &$store) {
		$store->setDefaultSortOrder('name','ASC');
		return parent::beforeStore($response, $params, $store);
	}
	
	public function formatStoreRecord($record, $model, $store) {
		
		$record['group_name']= !empty($model->group) ? $model->group->name : '';
		if(GO::modules()->customfields)
			$record['customfields']=GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Calendar_Model_Event", $model->group_id);
		
		
		return $record;
	}	
	
//	public function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
			
//		if(!empty($params['tasklists'])){
//			$visible_tasklists = json_decode($params['tasklists']);
//		
//			foreach($visible_tasklists as $vtsklst) {
//				if($vtsklst->visible)
//					$model->addManyMany('visible_tasklists', $vtsklst->id);
//				else
//					$model->removeManyMany ('visible_tasklists', $vtsklst->id);
//			}
//		}
		
//		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
//	}
	
	public function actionImportIcs($params) {
		ini_set('max_execution_time',300);
		
		GO::session()->closeWriting();
		GO::$disableModelCache=true;
		
		$response = array( 'success' => true );
		$count = 0;
		if (!file_exists($_FILES['ical_file']['tmp_name'][0])) {
			throw new Exception(GO::t('noFileUploaded'));
		}else {
			$file = new GO_Base_Fs_File($_FILES['ical_file']['tmp_name'][0]);
			$i = new GO_Base_Vobject_Iterator($file, "VEVENT");
			foreach($i as $vevent){					

				$event = new GO_Calendar_Model_Event();					
				$event->importVObject( $vevent, array('calendar_id'=>$params['calendar_id']) );
				$count++;
			}
		}
		$response['feedback'] = sprintf(GO::t('import_success','calendar'), $count);
		return $response;
	}
	
	
	public function actionExportIcs($params){
		
		$calendar = GO_Calendar_Model_Calendar::model()->findByPk($params["calendar_id"],false, true);
		
		if(!$calendar->public && !$calendar->checkPermissionLevel(GO_Base_Model_Acl::READ_PERMISSION))
			throw new GO_Base_Exception_AccessDenied();
		
		$c = new GO_Base_VObject_VCalendar();				
		$c->add(new GO_Base_VObject_VTimezone());
		
		$months_in_past = isset($params['months_in_past']) ? intval($params['months_in_past']) : 0;
		
		$findParams = GO_Base_Db_FindParams::newInstance();
		$findParams->getCriteria()->addCondition("calendar_id", $params["calendar_id"]);
		
		if(!empty($params['months_in_past']))		
			$stmt = GO_Calendar_Model_Event::model()->findForPeriod($findParams, GO_Base_Util_Date::date_add(time(), 0, -$months_in_past));
		else
			$stmt = GO_Calendar_Model_Event::model()->find($findParams);
		
		//todo optimize memory
			
		
		GO_Base_Util_Http::outputDownloadHeaders(new GO_Base_FS_File($calendar->name.'.ics'));
		
		echo "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Intermesh//NONSGML ".GO::config()->product_name." ".GO::config()->version."//EN
";
		$t = new GO_Base_VObject_VTimezone();
		echo $t->serialize();
		
		while($event = $stmt->fetch()){
			$v = $event->toVObject();
			echo $v->serialize();
		}
		
		echo "END:VCALENDAR\n";
	}	
	
	
	
	public function actionEmpty($params){
		$calendar = GO_Calendar_Model_Calendar::model()->findByPk($params['calendar_id']);
		
		if(!$calendar)
			throw new GO_Base_Exception_NotFound();
		
		$events = $calendar->events;
		
		foreach($events as $event){
			$event->delete();
		}
		
		$response['success']=true;
		
		return $response;
	}
}
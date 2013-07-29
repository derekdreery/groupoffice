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
				'tasklist_id' => '$model->tasklist?$model->tasklist->name:""'
		);
	}
	
	protected function afterLoad(&$response, &$model, &$params) {
		
		$url = GO::createExternalUrl('calendar', 'openCalendar', array(array(
			'calendars'=>array($response['data']['id']),
			'group_id'=>$response['data']['group_id'])
				));

		// Show "None" in the Caldav Tasklist selection when tasklist_id is 0
		if(empty($response['data']['tasklist_id']))
				$response['data']['tasklist_id'] = "";
		
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
		
		$store->setDefaultSortOrder(array('g.name','t.name'), array('ASC','ASC'));
		
		$findParams = $store->getDefaultParams($params)
						->join(GO_Calendar_Model_Group::model()->tableName(), GO_Base_Db_FindCriteria::newInstance()->addCondition('group_id', 'g.id', '=', 't', true, true),'g')				
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
		GO::setMaxExecutionTime(0);
		
		GO::session()->closeWriting();
		GO::$disableModelCache=true;
		
		$response = array( 'success' => true );
		$count = 0;
		$failed=array();
		if (!file_exists($_FILES['ical_file']['tmp_name'][0])) {
			throw new Exception(GO::t('noFileUploaded'));
		}else {
			$file = new GO_Base_Fs_File($_FILES['ical_file']['tmp_name'][0]);
			$i = new GO_Base_Vobject_Iterator($file, "VEVENT");
			foreach($i as $vevent){					
			
				$event = new GO_Calendar_Model_Event();
				try{
					$event->importVObject( $vevent, array('calendar_id'=>$params['calendar_id']) );
					$count++;
				}catch(Exception $e){
					$failed[]=$e->getMessage();
				}
			}
		}
		$response['feedback'] = sprintf(GO::t('import_success','calendar'), $count);
		
		if(count($failed)){
			$response['feedback'] .= "\n\n".count($failed)." events failed: ".implode("\n", $failed);
		}
		
		return $response;
	}
	
	/**
	* Load the user_calendar_colors 
	* 
	* @param array $params
	* @return array 
	*/	
	public function actionLoadColors($params){
		$store = GO_Base_Data_Store::newInstance(GO_Calendar_Model_Calendar::model());
		
		$findParams = $store->getDefaultParams($params)
						->join(GO_Calendar_Model_CalendarUserColor::model()->tableName(), GO_Base_Db_FindCriteria::newInstance()
										->addCondition('id', 'col.calendar_id', '=', 't', true, true)->addCondition('user_id', GO::user()->id, '=','col'),'col','LEFT')
						->order(array('t.name'))	
						->select('col.*,name,id');
		
		$findParams->getCriteria()->addCondition('group_id', 1);
		
		$stmt = GO_Calendar_Model_Calendar::model()->find($findParams);
		
		$store->setStatement($stmt);

		$store->getColumnModel()->setFormatRecordFunction(array($this,'getCalendarColor'));
		
		return $store->getData();
	}
	
	private $_colors = array(
		'F0AE67','FFCC00','FFFF00','CCFF00','66FF00',
		'00FFCC','00CCFF','0066FF','95C5D3','6704FB',
		'CC00FF','FF00CC','CC99FF','FB0404','FF6600',
		'C43B3B','996600','66FF99','999999','00FFFF'
	);
	private $_colorIndex = 0;
	
	public function getCalendarColor($formattedrecord,$model,$controller){

//		if(empty($formattedrecord->color))
//			$color = false;
		//$color = $model->getColor(GO::user()->id);
		if(empty($formattedrecord['color'])){
			if($this->_colorIndex >= count($this->_colors))
				$this->_colorIndex = 0;
			
			$formattedrecord['color'] = $this->_colors[$this->_colorIndex];
			$this->_colorIndex++;
		}
		
		return $formattedrecord;
	}
	
	
	/**
	 * Save the user_calendar_colors
	 * 
	 * @param array $params
	 * @return array 
	 */
	public function actionSubmitColors($params){
		$params = json_decode($params['griddata']);
		
		foreach($params as $cC){			
			$calendarColor = GO_Calendar_Model_CalendarUserColor::model()->findByPk(array('calendar_id'=>$cC->id,'user_id'=>GO::user()->id));
			
			if(!$calendarColor){
				$calendarColor = new GO_Calendar_Model_CalendarUserColor();
				$calendarColor->user_id = GO::user()->id;
				$calendarColor->calendar_id = $cC->id;
			}
			
			$calendarColor->color = $cC->color;
			
			$calendarColor->save();
		}
		$response['success']=true;

		return $response;
	}

	
	public function actionExportIcs($params){
		
		$calendar = GO_Calendar_Model_Calendar::model()->findByPk($params["calendar_id"],false, true);
		
		if(!$calendar->public && !$calendar->checkPermissionLevel(GO_Base_Model_Acl::READ_PERMISSION))
			throw new GO_Base_Exception_AccessDenied();
		
		$c = new GO_Base_VObject_VCalendar();				
		$c->add(new GO_Base_VObject_VTimezone());
		
		$months_in_past = isset($params['months_in_past']) ? intval($params['months_in_past']) : 0;
		
		$findParams = GO_Base_Db_FindParams::newInstance()->select("t.*");
		$findParams->getCriteria()->addCondition("calendar_id", $params["calendar_id"]);
		
		if(!empty($params['months_in_past']))		
			$stmt = GO_Calendar_Model_Event::model()->findForPeriod($findParams, GO_Base_Util_Date::date_add(time(), 0, -$months_in_past));
		else
			$stmt = GO_Calendar_Model_Event::model()->find($findParams);		
		
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
	
	public function actionTruncate($params){
		$calendar = GO_Calendar_Model_Calendar::model()->findByPk($params['calendar_id']);
		
		if(!$calendar)
			throw new GO_Base_Exception_NotFound();
		
		$calendar->truncate();
		
		$response['success']=true;
		
		return $response;
	}
	
	public function actionRemoveDuplicates($params){
		
		$this->render('externalHeader');
		
		GO::setMaxExecutionTime(300);
		GO::setMemoryLimit(1024);
		
		$calendar = GO_Calendar_Model_Calendar::model()->findByPk($params['calendar_id']);
		
		if(!$calendar)
			throw new GO_Base_Exception_NotFound();
		
		GO_Base_Fs_File::setAllowDeletes(false);
		//VERY IMPORTANT:
		GO_Files_Model_Folder::$deleteInDatabaseOnly=true;
		
		
		GO::session()->closeWriting(); //close writing otherwise concurrent requests are blocked.
		
		$checkModels = array(
				"GO_Calendar_Model_Event"=>array('name', 'start_time', 'end_time', 'rrule','calendar_id'),
			);		
		
		foreach($checkModels as $modelName=>$checkFields){
			
			if(empty($params['model']) || $modelName==$params['model']){

				echo '<h1>'.GO::t('removeDuplicates').'</h1>';

				$checkFieldsStr = 't.'.implode(', t.',$checkFields);
				$findParams = GO_Base_Db_FindParams::newInstance()
								->ignoreAcl()
								->select('t.id, count(*) AS n, '.$checkFieldsStr)
								->group($checkFields)
								->having('n>1');
				
				$findParams->getCriteria()->addCondition('calendar_id', $calendar->id);

				$stmt1 = GO::getModel($modelName)->find($findParams);

				echo '<table border="1">';
				echo '<tr><td>ID</th><th>'.implode('</th><th>',$checkFields).'</th></tr>';

				$count = 0;

				while($dupModel = $stmt1->fetch()){
					
					$select = 't.id';
					
					if(GO::getModel($modelName)->hasFiles()){
						$select .= ', t.files_folder_id';
					}

					$findParams = GO_Base_Db_FindParams::newInstance()
								->ignoreAcl()
								->select($select.', '.$checkFieldsStr)
								->order('id','ASC');
					
					$findParams->getCriteria()->addCondition('calendar_id', $calendar->id);

					foreach($checkFields as $field){
						$findParams->getCriteria()->addCondition($field, $dupModel->getAttribute($field));
					}							

					$stmt = GO::getModel($modelName)->find($findParams);

					$first = true;

					while($model = $stmt->fetch()){
						echo '<tr><td>';
						if(!$first)
							echo '<span style="color:red">';
						echo $model->id;
						if(!$first)
							echo '</span>';
						echo '</th>';				

						foreach($checkFields as $field)
						{
							echo '<td>'.$model->getAttribute($field,'html').'</td>';
						}

						echo '</tr>';

						if(!$first){							
							if(!empty($params['delete'])){

								if($model->hasLinks() && $model->countLinks()){
									echo '<tr><td colspan="99">'.GO::t('skippedDeleteHasLinks').'</td></tr>';
								}elseif(($filesFolder = $model->getFilesFolder(false)) && ($filesFolder->hasFileChildren() || $filesFolder->hasFolderChildren())){
									echo '<tr><td colspan="99">'.GO::t('skippedDeleteHasFiles').'</td></tr>';
								}else{									
									$model->delete();
								}
							}

							$count++;
						}

						$first=false;
					}
				}	
					

				echo '</table>';

				echo '<p>'.sprintf(GO::t('foundDuplicates'),$count).'</p>';
				echo '<br /><br /><a href="'.GO::url('calendar/calendar/removeDuplicates', array('delete'=>true, 'calendar_id'=>$calendar->id)).'">'.GO::t('clickToDeleteDuplicates').'</a>';
				
			}
		}
		
		$this->render('externalFooter');
		
		
	}
}
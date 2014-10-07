<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * The GO_Tasks_Controller_Tasklist controller
 *
 * @package GO.modules.Tasks
 * @version $Id: GO_Tasks_Controller_Tasklist.php 7607 2011-09-20 10:08:21Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */

class GO_Tasks_Controller_Tasklist extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Tasks_Model_Tasklist';
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('user_name','$model->user->name');
		
		return parent::formatColumns($columnModel);
	}
	
	protected function beforeStoreStatement(array &$response, array &$params, GO_Base_Data_AbstractStore &$store, GO_Base_Db_FindParams $storeParams) {
		
		$multiSel = new GO_Base_Component_MultiSelectGrid(
						'ta-taskslists', 
						"GO_Tasks_Model_Tasklist",$store, $params);		
		$multiSel->setFindParamsForDefaultSelection($storeParams);
		$multiSel->formatCheckedColumn();
		
		return parent::beforeStoreStatement($response, $params, $store, $storeParams);
	}
	
	protected function beforeStore(&$response, &$params, &$store) {
		$store->setDefaultSortOrder('name','ASC');
		return parent::beforeStore($response, $params, $store);
	}
	
	protected function remoteComboFields(){
		return array(
				'user_name'=>'$model->user->name'
				);
	}
	
	public function actionImportIcs($params) {
		$response = array( 'success' => true );
		$count = 0;
		$failed=array();
		if (!file_exists($_FILES['ical_file']['tmp_name'][0])) {
			throw new Exception($lang['common']['noFileUploaded']);
		}else {
			$file = new GO_Base_Fs_File($_FILES['ical_file']['tmp_name'][0]);
			$file->convertToUtf8();
			$contents = $file->getContents();
			$vcal = GO_Base_VObject_Reader::read($contents);
			GO_Base_VObject_Reader::convertVCalendarToICalendar($vcal);
			foreach($vcal->vtodo as $vtask) {
				$event = new GO_Tasks_Model_Task();			
				try{
					$event->importVObject( $vtask, array('tasklist_id'=>$params['tasklist_id']) );
		
					$count++;
				}catch(Exception $e){
					$failed[]=$e->getMessage();
				}
			}
		}
		$response['feedback'] = sprintf(GO::t('import_success','tasks'), $count);
		
		if(count($failed)){
			$response['feedback'] .= "\n\n".count($failed)." tasks failed: ".implode('\n', $failed);
		}
		return $response;
	}
	
	
	public function actionTruncate($params){
		$tasklist = GO_Tasks_Model_Tasklist::model()->findByPk($params['tasklist_id']);
		
		if(!$tasklist)
			throw new GO_Base_Exception_NotFound();
		
		$tasklist->truncate();
		
		$response['success']=true;
		
		return $response;
	}
	
	
	public function actionRemoveDuplicates($params){
		
		GO::setMaxExecutionTime(300);
		GO::setMemoryLimit(1024);
		
		$this->render('externalHeader');
		
		$tasklist = GO_Tasks_Model_Tasklist::model()->findByPk($params['tasklist_id']);
		
		if(!$tasklist)
			throw new GO_Base_Exception_NotFound();
		
		GO_Base_Fs_File::setAllowDeletes(false);
		//VERY IMPORTANT:
		GO_Files_Model_Folder::$deleteInDatabaseOnly=true;
		
		
		GO::session()->closeWriting(); //close writing otherwise concurrent requests are blocked.
		
		$checkModels = array(
				"GO_Tasks_Model_Task"=>array('name', 'start_time', 'due_time', 'rrule', 'user_id', 'tasklist_id'),
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
				
				$findParams->getCriteria()->addCondition('tasklist_id', $tasklist->id);

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
					
					$findParams->getCriteria()->addCondition('tasklist_id', $tasklist->id);

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
				echo '<br /><br /><a href="'.GO::url('tasks/tasklist/removeDuplicates', array('delete'=>true, 'tasklist_id'=>$tasklist->id)).'">'.GO::t('clickToDeleteDuplicates').'</a>';
				
			}
		}
		
		$this->render('externalFooter');
		
		
	}
}
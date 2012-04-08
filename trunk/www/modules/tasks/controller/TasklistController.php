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
	
	protected function remoteComboFields(){
		return array(
				'user_name'=>'$model->user->name'
				);
	}
	
	public function actionImportIcs($params) {
		$response = array( 'success' => true );
		$count = 0;
		if (!file_exists($_FILES['ical_file']['tmp_name'][0])) {
			throw new Exception($lang['common']['noFileUploaded']);
		}else {
			$file = new GO_Base_Fs_File($_FILES['ical_file']['tmp_name'][0]);
			$file->convertToUtf8();
			$contents = $file->getContents();
			$vcal = GO_Base_VObject_Reader::read($contents);
			foreach($vcal->vtodo as $vtask) {
				$event = new GO_Tasks_Model_Task();
				$event->importVObject( $vtask, array('tasklist_id'=>$params['tasklist_id']) );
				$count++;
			}
		}
		$response['feedback'] = sprintf(GO::t('import_success','tasklist'), $count);
		return $response;
	}
}
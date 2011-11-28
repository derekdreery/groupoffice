<?php
class GO_Addressbook_Controller_Addressbook extends GO_Base_Controller_AbstractModelController{
	
	/*
	 * This function initiates the contact filter by checked addressbooks.
	 */
	protected function getStoreMultiSelectProperties(){
		return array(
			'requestParam'=>'books',
			//'permissionsModel'=>'GO_Addressbook_Model_Addressbook'
			//'titleAttribute'=>'name'
		);
	}	
	
	protected $model = 'GO_Addressbook_Model_Addressbook';
	
//	protected function actionUpload($params) {
//		$addressbook_id = isset($params['addressbook_id']) ? ($params['addressbook_id']) : 0;
//		$import_filetype = isset($params['import_filetype']) ? ($params['import_filetype']) : null;
//		$import_filename = isset($_FILES['import_file']['tmp_name']) ? ($_FILES['import_file']['tmp_name']) : null;
//		$separator	= isset($params['separator']) ? ($params['separator']) : ',';
//		$quote	= isset($params['quote']) ? ($params['quote']) : '"';
//
//	  $response['success'] = true;
//
//		$_SESSION['GO_SESSION']['addressbook']['import_file'] =GO::config()->tmpdir.uniqid(time());
//		GO::debug($import_filename);
//
//		if(!move_uploaded_file($import_filename, $_SESSION['GO_SESSION']['addressbook']['import_file'])) {
//			throw new Exception('Could not move '.$import_filename);
//	  }
//	  File::convert_to_utf8($_SESSION['GO_SESSION']['addressbook']['import_file']);
//
//	  switch($import_filetype) {
//			case 'vcf':
//				ini_set('max_execution_time', 360);
//				ini_set('memory_limit', '256M');
//				require_once (GO::modules()->path."classes/vcard.class.inc.php");
//				$vcard = new vcard();
//				$response['success'] = $this->_importVCF($_SESSION['GO_SESSION']['addressbook']['import_file'], $GLOBALS['GO_SECURITY']->user_id, ($_POST['addressbook_id']));
//				break;
//	  }
//
//		return $response;
//	}
	
	public function actionSearchSender($params) {
		$addressbooks = GO_Addressbook_Model_Addressbook::model()->find(
			GO_Base_Db_FindCriteria::newInstance()
				->addCondition('email',$params['email'])
		);
		
		$ab_ids = array();
		foreach ($addressbooks as $ab)
			$ab_ids[] = $ab->id;

		$criteria = GO_Base_Db_FindCriteria::newInstance()
			->addCondition('email',$params['email']);
		if (!empty($ab_ids))
			$criteria->addInCondition('addressbook_id', $ab_ids);
		
		$contacts = GO_Addressbook_Model_Contact::model()->find($criteria);		
		$response['total'] = count($contacts);
		$response['results']=array();

		foreach($contacts as $contact)
		{
			$addressbook = GO_Addressbook_Model_Addressbook::model()->findByPk($contact->addressbook_id);
			$res_contact['id']=$contact->id;
			$res_contact['name']=$contact->name.' ('.$addressbook->name.')';

			$response['results'][]=$res_contact;
		}
		return $response;
	}
	
	public function formatStoreRecord($record, $model, $store) {
		
		$record['user_name']=$model->user->name;
		if(GO::modules()->customfields){
			$record['contactCustomfields']=GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Addressbook_Model_Contact", $model->id);
			$record['companyCustomfields']=GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Addressbook_Model_Company", $model->id);
		}
		
		return parent::formatStoreRecord($record, $model, $store);
	}
	
	/**
	 * Imports VCF file.
	 * Command line call: /path/to/groupoffice/groupoffice addressbook/addressbook/importVcf --file=filename.txt
	 * @param Array $params Parameters. MUST contain string $params['file'].
	 */
	public function actionImportVcf($params){
		
		$file = new GO_Base_Fs_File($params['file']);
		$data = $file->getContents();

		$vcalendar = GO_Base_VObject_Reader::read($data);
		
		foreach($vcalendar->vtodo as $vtodo) {
			$task = new GO_Task_Model_Task();
			$task->importVObject($vtodo);
		}
	}
}


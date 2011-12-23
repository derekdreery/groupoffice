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
	 * Function exporting addressbook contents to VCFs. Must be called from export.php.
	 * @param type $params 
	 */
	public function exportVCard($params) {
		$addressbook = GO_Addressbook_Model_Addressbook::model()->findByPk($params['addressbook_id']);
		
		$filename = $addressbook->name.'.vcf';
		GO_Base_Util_Http::outputDownloadHeaders(new GO_Base_FS_File($filename));		
	
		foreach ($addressbook->contacts as $contact)
			echo $contact->toVObject()->serialize();
	}
	
	protected function actionUpload($params) {
		$params['a'] = $addressbook_id = $params['addressbook_id'];
		$import_filetype = isset($params['import_filetype']) ? ($params['import_filetype']) : null;
		
		if (!empty($_FILES['import_file']['tmp_name']))
			$import_filename = ($_FILES['import_file']['tmp_name']);
		elseif (!empty($params['import_file']))
			$import_filename = ($params['import_file']);
		
		$separator	= isset($params['separator']) ? ($params['separator']) : ',';
		$quote	= isset($params['quote']) ? ($params['quote']) : '"';
		$params['file'] = $_SESSION['GO_SESSION']['addressbook']['import_file'] =GO::config()->tmpdir.uniqid(time());
		$response['success'] = true;
		GO::debug($import_filename);

		if(!move_uploaded_file($import_filename, $_SESSION['GO_SESSION']['addressbook']['import_file'])) {
			throw new Exception('Could not move '.$import_filename);
	  }

		$file = new GO_Base_Fs_File($_SESSION['GO_SESSION']['addressbook']['import_file']);
	  $file->convertToUtf8();

	  switch($import_filetype) {
			case 'vcf':
				ini_set('max_execution_time', 360);
				ini_set('memory_limit', '256M');
				$response = array_merge($response,$this->actionImportVcf($params));
				break;
	  }		
		return $response;
	}
	
	/**
	 * Imports VCF file.
	 * Example command line call: /path/to/groupoffice/groupoffice addressbook/addressbook/importVcf --file=filename.txt --addressbook_id=1
	 * @param Array $params Parameters. MUST contain string $params['file'].
	 */
	public function actionImportVcf($params){
		$file = new GO_Base_Fs_File($params['file']);
		$data = $file->getContents();
		$contact = new GO_Addressbook_Model_Contact();
		$vcard = GO_Base_VObject_Reader::read($data);
		$params['a'] = !empty($params['a']) ? $params['a'] : 1;
		
		if (is_array($vcard)) {
			foreach ($vcard as $item) {
				$contact->importVObject(
					$item,
					array(
						'addressbook_id' => $params['a']
					)
				);
			}
		} else {
			$contact->importVObject(
				$vcard,
				array(
					'addressbook_id' => $params['a']
				)
			);
		}
		return array('success'=>true);
	}
}
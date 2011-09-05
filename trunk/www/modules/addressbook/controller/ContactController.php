<?php
class GO_Addressbook_Controller_Contact extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Addressbook_Model_Contact';
	
	protected function beforeSubmit(&$response, &$model, &$params) {
				
		if(!empty($params['company_id']) && !is_numeric($params['company_id'])){
			$company = GO_Addressbook_Model_Company::model()->findSingleByAttributes(array(
				'addressbook_id'=>$model->addressbook_id,
				'name'=>$params['company_id']
			));
			
			if(!$company)
			{
				$company = new GO_Addressbook_Model_Company();
				$company->name=$params['company_id'];
				$company->addressbook_id=$model->addressbook_id;
				if(!$company->save())
					throw new GO_Base_Exception_Save("Company ".$company->name);
			}
			$model->company_id=$company->id;
			unset($params['company_id']);
			
		}
		
		return parent::beforeSubmit($response, $model, $params);
	}
	
	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
		
		if(!empty($params['delete_photo'])){
			$model->photo='';
		}
		
		if (isset($_FILES['image']['tmp_name'][0]) && is_uploaded_file($_FILES['image']['tmp_name'][0])) {
			$f = new GO_Base_Fs_Folder(GO::config()->tmpdir);
			$f->create();
			
			move_uploaded_file($_FILES['image']['tmp_name'][0], GO::config()->tmpdir . $_FILES['image']['name'][0]);
			
			$model->photo = GO::config()->tmpdir . $_FILES['image']['name'][0];

			$response['photo_url'] = $model->photoURL;
		}
		
		
		//todo
		
//		if ($GLOBALS['GO_MODULES']->has_module('mailings')) {
//				require_once($GLOBALS['GO_MODULES']->modules['mailings']['class_path'] . 'mailings.class.inc.php');
//				$ml = new mailings();
//				$ml2 = new mailings();
//
//				$ml->get_authorized_mailing_groups('write', $GLOBALS['GO_SECURITY']->user_id, 0, 0);
//				while ($ml->next_record()) {
//					$is_in_group = $ml2->contact_is_in_group($contact_id, $ml->f('id'));
//					$should_be_in_group = isset($_POST['mailing_' . $ml->f('id')]);
//
//					if ($is_in_group && !$should_be_in_group) {
//						$ml2->remove_contact_from_group($contact_id, $ml->f('id'));
//					}
//					if (!$is_in_group && $should_be_in_group) {
//						$ml2->add_contact_to_mailing_group($contact_id, $ml->f('id'));
//					}
//				}
//			}
		
		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}
	
	
	protected function afterLoad(&$response, &$model, &$params) {
		
		$response['data']['photo_url']=$model->photoURL;		
		
		return parent::afterLoad($response, $model, $params);
	}	
	
	
	public function actionPhoto($params){

		$contact = GO::getModel($this->model)->findByPk($params['id']);
		
		if(empty($contact->photo))
			exit("No photo set");
		
		$file = new GO_Base_Fs_File($contact->photo);	

		header('Content-Length: '.$file->size());
		header('Content-Transfer-Encoding: binary');

		header("Last-Modified: ".gmdate("D, d M Y H:i:s", $file->mtime())." GMT");
		header("ETag: ".md5_file($file->path()));


		header("Expires: " . date("D, j M Y G:i:s ", time()+86400) . 'GMT');//expires in 1 day
		header('Cache-Control: cache');
		header('Pragma: cache');

		if (GO_Base_Util_Common::isInternetExplorer()) {
			header('Content-Type: application/download');
			header('Content-Disposition: inline; filename="'.$file->name().'"');
		}else {
			header('Content-Type: '.$file->mimeType());
			header('Content-Disposition: inline; filename="'.$file->name().'"');
		}

		$file->output();
	}
	

	protected function afterDisplay(&$response, &$model, &$params) {
			
		$response['data']['name']=$model->name;
		$response['data']['photo_url']=$model->photo_url;
		
		$company = $model->company();
		if($company){					
			$response['data']['company_name'] = $company->name;
			$response['data']['company_name2'] = $company->name2;
		} else {
			$response['data']['company_name'] = '';
			$response['data']['company_name2'] = '';
		}
		
		$response['data']['google_maps_link']=GO_Base_Util_Common::googleMapsLink(
						$model->address, $model->address_no,$model->city, $model->country);
		
		$response['data']['formatted_address']=nl2br(GO_Base_Util_Common::formatAddress(
						$model->country, $model->address, $model->address_no,$model->zip, $model->city, $model->state
						));
		
		
		
		return parent::afterDisplay($response, $model, $params);
	}
	
	
//	protected function getGridMultiSelectProperties(){
//		return array(
//				'requestParam'=>'notes_categories_filter',
//				'permissionsModel'=>'GO_Notes_Model_Category',
//				'titleAttribute'=>'name'
//				);
//	}	
//	
//	protected function getGridParams($params){
//		return array(
//				'ignoreAcl'=>true,
//				'joinCustomFields'=>true,
//				'by'=>array(array('category_id', $this->multiselectIds, 'IN'))
//		);
//	}
//  
//  protected function prepareGrid($grid){		
//    $grid->formatColumn('user_name','$model->user->name');
//    return $grid;
//  }
//	
//	protected function remoteComboFields(){
//		return array('category_id'=>'$model->category->name');
//	}
}


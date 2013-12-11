<?php

class GO_Email_Controller_Alias extends \GO\Base\Controller\AbstractModelController {

	protected $model = 'GO_Email_Model_Alias';

	protected function beforeStore(&$response, &$params, &$store) {

		$store->setDefaultSortOrder('name');

		return parent::beforeStore($response, $params, $store);
	}

	protected function getStoreParams($params) {
		
		if(empty($params['account_id'])){
			$findParams = \GO\Base\Db\FindParams::newInstance()
							->select('t.*')
							->joinModel(array(
									'model' => 'GO_Email_Model_AccountSort',
									'foreignField' => 'account_id', //defaults to primary key of the remote model
									'localField' => 'account_id', //defaults to primary key of the model
									'type' => 'LEFT',
									'tableAlias'=>'sor',
									"criteria"=>  \GO\Base\Db\FindCriteria::newInstance()->addCondition('user_id', \GO::user()->id,"=",'sor')
							))
							->ignoreAdminGroup()
							->permissionLevel(\GO\Base\Model\Acl::CREATE_PERMISSION)
							->order(array('order','default'), array('DESC','DESC'));
		}else
		{
			$findParams = \GO\Base\Db\FindParams::newInstance();
			$findParams->getCriteria()->addCondition("account_id", $params['account_id'])->addCondition("default", 1,'!=');
		}

		return $findParams;
	}

	public function formatStoreRecord($record, $model, $store) {

		$r = new \GO\Base\Mail\EmailRecipients();
		$r->addRecipient($model->email, $model->name);
		$record['from'] = (string) $r;
		$record['html_signature'] = \GO\Base\Util\String::text_to_html($model->signature);
		$record['plain_signature'] = $model->signature;
		$record['template_id']=0;
		
		if(\GO::modules()->addressbook){
			$defaultAccountTemplateModel = \GO_Addressbook_Model_DefaultTemplateForAccount::model()->findByPk($model->account_id);
			if($defaultAccountTemplateModel){
				$record['template_id']=$defaultAccountTemplateModel->template_id;
			}else{
				$defaultUserTemplateModel = \GO_Addressbook_Model_DefaultTemplate::model()->findByPk(\GO::user()->id);
				if(!$defaultUserTemplateModel){
					$defaultUserTemplateModel= new \GO_Addressbook_Model_DefaultTemplateForAccount();
					$defaultUserTemplateModel->account_id = $model->account_id;
					$defaultUserTemplateModel->save();
				}
				$record['template_id']=$defaultUserTemplateModel->template_id;
			}
		}
		unset($record['signature']);
		

		return parent::formatStoreRecord($record, $model, $store);
	}

}

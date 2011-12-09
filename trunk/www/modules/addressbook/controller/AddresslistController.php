<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 */class GO_Addressbook_Controller_Addresslist extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Addressbook_Model_Addresslist';	

	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		
		$columnModel->formatColumn('user_name', '$model->user->name');
		
		return parent::formatColumns($columnModel);
	}
	
	protected function afterLoad(&$response, &$model, &$params) {
		$response['data']['user_name'] = $model->user->name;
		return $response;
	}

	/**
	 * This function is made specially to convert paramaters from the EmailComposer
	 * to match GO_Base_Mail_Message::handleFormInput in actionSendMailing.
	 * @param Array $params Parameters from EmailComposer
	 * @return Array $params Parameters for GO_Base_Mail_Message::handleFormInput 
	 */
	private function _convertOldParams($params) {
		$params['inlineAttachments'] = json_decode($params['inline_attachments']);

		foreach ($params['inlineAttachments'] as $k => $ia) {
			// tmpdir part may already be at the beginning of $ia['tmp_file']
			if (strpos($ia->tmp_file,GO::config()->tmpdir)==0)
				$ia->tmp_file = substr($ia->tmp_file,strlen(GO::config()->tmpdir));
			
			$params['inlineAttachments'][$k] = $ia;
		}
		$params['inlineAttachments'] = json_encode($params['inlineAttachments']);

		if (!empty($params['content_type']) && strcmp($params['content_type'],'html')!=0)
			$params['body'] = $params['textbody'];
			
		// Replace "[id:" string part in subject by the actual alias id
		if (!empty($params['alias_id']) && !empty($params['subject']))
			$params['subject'] = str_replace('[id:','['.$params['alias_id'].':',$params['subject']);
		
		return $params;
	}
	
	public function actionSendMailing($params){
		if(empty($params['addresslist_id'])) {
			throw new Exception(GO::t('feedbackNoReciepent','email'));
		}else {
			try {
				$params = $this->_convertOldParams($params);
				
				$message = GO_Base_Mail_Message::newInstance();
				$message->handleEmailFormInput($params); // insert the inline and regular attachments in the MIME message
				// TODO: set message receipt to: $params['notification']
				// (See also $swift->message->setReadReceiptTo($swift->account['email']));
			
				$mailing['alias_id']=$params['alias_id'];
				$mailing['subject']=$params['subject'];
//				$mailing['status']=0;
//				$mailing['sent']=0;
//				$mailing['errors']=0;
//				$mailing['total']=0;
				$mailing['addresslist_id']=$params['addresslist_id'];
				$mailing['message_path']=GO::config()->file_storage_path.'mailings/'.GO::user()->id.'_'.date('Ymd_Gis').'.eml';
				
						
				// Set From address
				$alias = GO_Email_Model_Alias::model()->findByPk($params['alias_id']);
				$message->setFrom($alias->email,$alias->name);

				// Set the message subject
				if (!empty($params['subject']))
					$message->setSubject($params['subject']);

				// Set the priority of the message using $params['priority']
				if (!empty($params['priority']))
					$message->setPriority($params['priority']);
				
				// Set the message body
				if ($params['content_type']=='html') {
					$message->setHtmlAlternateBody($params['body']);
				} else {
					$message->setBody($params['body']);
				}
				
				if(!is_dir(dirname($mailing['message_path'])))
					mkdir(dirname($mailing['message_path']), 0755, true);

				// Write message MIME source to message path
				file_put_contents($mailing['message_path'], $message->toString());

				$sentMailing = GO_Addressbook_Model_SentMailing::model();
				$sentMailing->setAttributes($mailing);
				$sentMailing->save();
				
				$nMailsToSend = 0;
				
				// Clear list of company recipients to send to, if there are any in this list
				$sendmailingCompany = GO_Addressbook_Model_SendmailingCompany::model();
				$sendmailingCompany->deleteByAttribute('addresslist_id', $sentMailing->id);
				
				// Add company recipients to this list and count them
				$stmt = GO_Addressbook_Model_Addresslist::model()->findByPk($sentMailing->addresslist_id)->companies();
				while ($alCompany = $stmt->fetch()) {
					if (!empty($alCompany->email) && !empty($alCompany->email_allowed)) {
						$sendmailingCompany->setAttributes(
							array(
								'addresslist_id' => $sentMailing->addresslist_id,
								'company_id' => $alCompany->id
							));
						$sendmailingCompany->save();
						$nMailsToSend++;
					}
				}

				// Clear list of contact recipients to send to, if there are any in this list
				$sendmailingContact = GO_Addressbook_Model_SendmailingContact::model();
				$sendmailingContact->deleteByAttribute('addresslist_id', $sentMailing->id);

				// Add contact recipients to this list and count them
				$stmt = GO_Addressbook_Model_Addresslist::model()->findByPk($sentMailing->addresslist_id)->contacts();
				while ($alContact = $stmt->fetch()) {
					if (!empty($alContact->email) && !empty($alContact->email_allowed)) {
						$sendmailingContact->setAttributes(
							array(
								'addresslist_id' => $sentMailing->addresslist_id,
								'contact_id' => $alContact->id
							));
						$sendmailingContact->save();
						$nMailsToSend++;
					}
				}
				
				$sentMailing->setAttributes(
						array(
							"status" => 1,
							"total" => $nMailsToSend
						)
					);
				$sentMailing->save();
				
				// MVC version of $ml->launch_mailing($mailing_id); ......
				$log = GO::config()->file_storage_path.'log/mailings/';
				if(!is_dir($log))
					mkdir($log, 0755,true);

				$log .= $sentMailing->id.'.log';
				$cmd = GO::config()->cmd_php.' '.GO::modules()->addressbook->path.'sendmailing.php '.GO::config()->get_config_file().' '.$sentMailing->id.' >> '.$log;

				if (!is_windows())
					$cmd .= ' 2>&1 &';

				file_put_contents($log, Date::get_timestamp(time())."\r\n".$cmd."\r\n\r\n", FILE_APPEND);
				if(is_windows())
					pclose(popen("start /B ". $cmd, "r"));
				else
					exec($cmd);

				$response['success']=true;
			} catch (Exception $e) {
				$response['feedback'] = GO::t('feedbackUnexpectedError','email') . $e->getMessage();
			}
		}
		return $response;
	}
	
//	public function actionAddressbookFields($params) {
//		$ab->query("SHOW COLUMNS FROM ab_contacts");
//		$contact_types = array();
//		while ($ab->next_record()) {
//			$contact_types[$ab->record['Field']] = getSimpleType($ab->record['Type']);
//		}
//
//		$ab->query("SHOW COLUMNS FROM ab_companies");
//		$company_types = array();
//		while ($ab->next_record()) {
//			$company_types[$ab->record['Field']] = getSimpleType($ab->record['Type']);
//		}
//
//		if($_POST['type']=='contacts')
//		{
//
//			$response['results']=array(
//				//array('field'=>'ab_contacts.name', 'label'=>$lang['common']['name'], 'type'=>$contact_types['name']),
//				array('field'=>'ab_contacts.title', 'label'=>$lang['common']['title'], 'type'=>$contact_types['title']),
//				array('field'=>'ab_contacts.first_name', 'label'=>$lang['common']['firstName'], 'type'=>$contact_types['first_name']),
//				array('field'=>'ab_contacts.middle_name', 'label'=>$lang['common']['middleName'], 'type'=>$contact_types['middle_name']),
//				array('field'=>'ab_contacts.last_name', 'label'=>$lang['common']['lastName'], 'type'=>$contact_types['last_name']),
//				array('field'=>'ab_contacts.initials', 'label'=>$lang['common']['initials'], 'type'=>$contact_types['initials']),
//				array('field'=>'ab_contacts.sex', 'label'=>$lang['common']['sex'], 'type'=>$contact_types['sex']),
//				array('field'=>'ab_contacts.birthday', 'label'=>$lang['common']['birthday'], 'type'=>$contact_types['birthday']),
//				array('field'=>'ab_contacts.email', 'label'=>$lang['common']['email'], 'type'=>$contact_types['email']),
//				array('field'=>'ab_contacts.country', 'label'=>$lang['common']['country'], 'type'=>$contact_types['country']),
////					array('field'=>'ab_contacts.iso_address_format', 'label'=>$lang['common']['address_format'], 'type'=>$contact_types['iso_address_format']),
//				array('field'=>'ab_contacts.state', 'label'=>$lang['common']['state'], 'type'=>$contact_types['state']),
//				array('field'=>'ab_contacts.city', 'label'=>$lang['common']['city'], 'type'=>$contact_types['city']),
//				array('field'=>'ab_contacts.zip', 'label'=>$lang['common']['zip'], 'type'=>$contact_types['zip']),
//				array('field'=>'ab_contacts.address', 'label'=>$lang['common']['address'], 'type'=>$contact_types['address']),
//				array('field'=>'ab_contacts.address_no', 'label'=>$lang['common']['addressNo'], 'type'=>$contact_types['address_no']),
//				array('field'=>'ab_contacts.home_phone', 'label'=>$lang['common']['phone'], 'type'=>$contact_types['home_phone']),
//				array('field'=>'ab_contacts.work_phone', 'label'=>$lang['common']['workphone'], 'type'=>$contact_types['work_phone']),
//				array('field'=>'ab_contacts.fax', 'label'=>$lang['common']['fax'], 'fax'=>$contact_types['fax']),
//				array('field'=>'ab_contacts.work_fax', 'label'=>$lang['common']['workFax'], 'type'=>$contact_types['work_fax']),
//				array('field'=>'ab_contacts.cellular', 'label'=>$lang['common']['cellular'], 'type'=>$contact_types['cellular']),
//				array('field'=>'ab_companies.name', 'label'=>$lang['common']['company'], 'type'=>$company_types['name']),
//				array('field'=>'ab_contacts.department', 'label'=>$lang['common']['department'], 'type'=>$contact_types['department']),
//				array('field'=>'ab_contacts.function', 'label'=>$lang['common']['function'], 'type'=>$contact_types['function']),
//				array('field'=>'ab_contacts.comment', 'label'=>$lang['addressbook']['comment'], 'type'=>$contact_types['comment']),
//				array('field'=>'ab_contacts.salutation', 'label'=>$lang['common']['salutation'], 'type'=>$contact_types['salutation'])
//			);
//
//			$model="GO_Addressbook_Model_Contact";
//		}else
//		{
//			$response['results']=array(
//				array('field'=>'ab_companies.name', 'label'=>$lang['common']['name'], 'type'=>$company_types['name']),
//				array('field'=>'ab_companies.name2', 'label'=>$lang['common']['name2'], 'type'=>$company_types['name2']),
//				//array('field'=>'ab_companies.title', 'label'=>$lang['common']['title'], 'type'=>$company_types['title']),
//				array('field'=>'ab_companies.email', 'label'=>$lang['common']['email'], 'type'=>$company_types['email']),
//				array('field'=>'ab_companies.country', 'label'=>$lang['common']['country'], 'type'=>$company_types['country']),
////					array('field'=>'ab_companies.iso_address_format', 'label'=>$lang['common']['address_format'], 'type'=>$company_types['iso_address_format']),
//				array('field'=>'ab_companies.state', 'label'=>$lang['common']['state'], 'type'=>$company_types['state']),
//				array('field'=>'ab_companies.city', 'label'=>$lang['common']['city'], 'type'=>$company_types['city']),
//				array('field'=>'ab_companies.zip', 'label'=>$lang['common']['zip'], 'type'=>$company_types['zip']),
//				array('field'=>'ab_companies.address', 'label'=>$lang['common']['address'], 'type'=>$company_types['address']),
//				array('field'=>'ab_companies.address_no', 'label'=>$lang['common']['addressNo'], 'type'=>$company_types['address_no']),
//
//				array('field'=>'ab_companies.post_country', 'label'=>$lang['common']['postCountry'], 'type'=>$company_types['post_country']),
//				array('field'=>'ab_companies.post_state', 'label'=>$lang['common']['postState'], 'type'=>$company_types['post_state']),
//				array('field'=>'ab_companies.post_city', 'label'=>$lang['common']['postCity'], 'type'=>$company_types['post_city']),
//				array('field'=>'ab_companies.post_zip', 'label'=>$lang['common']['postZip'], 'type'=>$company_types['post_zip']),
//				array('field'=>'ab_companies.post_address', 'label'=>$lang['common']['postAddress'], 'type'=>$company_types['post_address']),
//				array('field'=>'ab_companies.post_address_no', 'label'=>$lang['common']['postAddressNo'], 'type'=>$company_types['post_address_no']),
//
//				array('field'=>'ab_companies.phone', 'label'=>$lang['common']['phone'], 'type'=>$company_types['phone']),
//				array('field'=>'ab_companies.fax', 'label'=>$lang['common']['name'], 'type'=>$company_types['fax']),
//
//				array('field'=>'ab_companies.comment', 'label'=>$lang['addressbook']['comment'], 'type'=>$company_types['comment'])
//
//			);
//			$model="GO_Addressbook_Model_Company";
//		}
//
//		if (isset($GLOBALS['GO_MODULES']->modules['customfields'])) {
//			require_once($GO_CONFIG->root_path.'GO.php');
//
//			$stmt = GO_Customfields_Model_Category::model()->findByModel($model);
//			while($category = $stmt->fetch()){
//				$fstmt = $category->fields();
//				while($field = $fstmt->fetch()){
//					$arr=$field->getAttributes();
//					$arr['dataname']=$field->columnName();
//					$fields[]=$arr;
//					if(empty($field->exclude_from_grid))
//							$response['results'][] = array('id'=>$arr['id'], 'field'=>$field->columnName() ,'custom'=>true,'name' => $arr['name'] . ':' . $arr['name'],'label' => $arr['name'] . ':' . $arr['name'], 'value' => '`cf:' . $category->name . ':' . $arr['name'] . '`', 'type' => $arr['datatype']);
//				}
//			}
//		}
//	}	
	public function actionContacts($params){
		
		$store = GO_Base_Data_Store::newInstance(GO_Addressbook_Model_Contact::model());
						
		$store->getColumnModel()->formatColumn('name', '$model->name',array(),array('first_name','last_name'));
		
		$store->processDeleteActions($params, "GO_Addressbook_Model_AddresslistContact", array('addresslist_id'=>$params['addresslist_id']));
		
		$response = array();
		
		if (!empty($params['add_addressbook_id'])) {
			$addressbook = GO_Addressbook_Model_Addressbook::model()->findByPk($params['add_addressbook_id']);
			$model = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id']);
			$stmt = $addressbook->contacts();
			while ($contact = $stmt->fetch()) {
				$model->addManyMany('contacts',$contact->id);
			}
		} elseif (!empty($params['add_keys'])) {
			$add_keys = json_decode($params['add_keys'], true);
			$model = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id']);
			foreach ($add_keys as $add_key)
				$model->addManyMany('contacts',$add_key);
		}
			
		$stmt = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id'])->contacts($store->getDefaultParams($params));
		
		$store->setDefaultSortOrder('name', 'ASC');
		$store->setStatement ($stmt);

		return array_merge($response,$store->getData());
		
	}	
	
	public function actionCompanies($params){
		
		$store = GO_Base_Data_Store::newInstance(GO_Addressbook_Model_Company::model());
						
		$store->getColumnModel()->formatColumn('name', '$model->name',array(),array('first_name','last_name'));
		
		$store->processDeleteActions($params, "GO_Addressbook_Model_AddresslistCompany", array('addresslist_id'=>$params['addresslist_id']));
		
		$response = array();
		
		if (!empty($params['add_addressbook_id'])) {
			$addressbook = GO_Addressbook_Model_Addressbook::model()->findByPk($params['add_addressbook_id']);
			$model = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id']);
			$stmt = $addressbook->companies();
			while ($company = $stmt->fetch()) {
				$model->addManyMany('companies',$company->id);
			}
		} elseif (!empty($params['add_keys'])) {
			$add_keys = json_decode($params['add_keys'], true);
			$model = !isset($model) ? GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id']) : $model;
			foreach ($add_keys as $add_key)
				$model->addManyMany('companies',$add_key);
		} 
			
		$stmt = GO_Addressbook_Model_Addresslist::model()->findByPk($params['addresslist_id'])->companies($store->getDefaultParams($params));
		
		$store->setDefaultSortOrder('name', 'ASC');
		$store->setStatement ($stmt);

		return array_merge($response,$store->getData());
		
	}	
	
	// TODO: get cross-session "selected addresslist" identifiers for getting store
}


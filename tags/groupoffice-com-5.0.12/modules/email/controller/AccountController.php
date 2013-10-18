<?php

class GO_Email_Controller_Account extends GO_Base_Controller_AbstractModelController {

	protected $model = "GO_Email_Model_Account";
	
	
//	protected function actionTest($params){
//		
//		GO::$disableModelCache=true;
//		
//		for($i=0;$i<1000;$i++){
//			
//			echo $i."<br>";
//			${"account".$i} = GO_Email_Model_Account::model()->findSingle();
//			
//			${"account".$i}->openImapConnection("INBOX");
//		}
//		
//		
//		
//	}

	protected function getStoreParams($params) {

		$findParams = GO_Base_Db_FindParams::newInstance()
						->select("t.*,a.email, a.name")
						->searchFields(array('a.email','a.name','t.host'))
						->joinModel(array(
				'tableAlias' => 'a',
				'model' => 'GO_Email_Model_Alias',
				'foreignField' => 'account_id', //defaults to primary key of the remote model
				'type' => 'INNER',
				'criteria' => GO_Base_Db_FindCriteria::newInstance()->addCondition('default', 1, '=', 'a')
						));

		return $findParams;
	}
	
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('user_name', '$model->user->name');
		return parent::formatColumns($columnModel);
	}

	protected function afterLoad(&$response, &$model, &$params) {

		$response['data']['password'] = $model->decryptPassword();
		$response['data']['smtp_password'] = $model->decryptSmtpPassword();

		$alias = $model->getDefaultAlias();

		$response['data']['mbroot'] = trim($response['data']['mbroot'], './');

		$response['data']['email'] = $alias->email;
		$response['data']['name'] = $alias->name;
		$response['data']['signature'] = $alias->signature;

		return parent::afterLoad($response, $model, $params);
	}

	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {

		if (empty($params['id'])) {
			$model->addAlias($params['email'], $params['name']);
		} else {
			$alias = $model->getDefaultAlias();
			$alias->name = $params['name'];
			
			if(isset($params['email']))
				$alias->email = $params['email'];
			
			$alias->signature = $params['signature'];
			$alias->save();
		}

		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}

	protected function remoteComboFields() {
		return array('user_id' => '$model->user->name');
	}

	protected function actionCheckUnseen($params) {

		$response=array("success"=>true);
		$response['email_status']['total_unseen']=0;
		$response['email_status']['unseen']=array();
		
		GO::session()->closeWriting();
		
		$findParams = GO_Base_Db_FindParams::newInstance()
						->ignoreAdminGroup()
						->select('t.*');

		$stmt = GO_Email_Model_Account::model()->find($findParams);

		while ($account = $stmt->fetch()) {
			try {
				if($account->getDefaultAlias()){					
					
					$checkMailboxArray = $account->getAutoCheckMailboxes();
					
					//$imap = $account->openImapConnection();

					$existingCheckMailboxArray = array();
					
					foreach ($checkMailboxArray as $checkMailboxName) {			
						if(!empty($checkMailboxName)){						
							$mailbox = new GO_Email_Model_ImapMailbox($account, array('name'=>$checkMailboxName));
							if($mailbox->exists()){
								if(!isset($response['email_status']['has_new']) && $mailbox->hasAlarm()){
									$response['email_status']['has_new']=true;
								}
								$mailbox->snoozeAlarm();

								$response['email_status']['unseen'][]=array('account_id'=>$account->id,'mailbox'=>$checkMailboxName, 'unseen'=>$mailbox->unseen);
								$response['email_status']['total_unseen'] += $mailbox->unseen;	

								$existingCheckMailboxArray[] = $checkMailboxName;	
							}
						}
					}
					
					$account->check_mailboxes = implode(',',$existingCheckMailboxArray);
					if($account->isModified("check_mailboxes"))
						$account->save();
					
					if(($imap = $account->getImapConnection())){
						$imap->disconnect();
					}
					
				}
				
			} catch (Exception $e) {
				GO::debug($e->getMessage());
			}
		}
		
		GO::debug("Total unseen: ".$response['email_status']['total_unseen']);
		
//$response['email_status']['has_new']=true;	
		return $response;
	}
	
	public function actionSubscribtionsTree($params){
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		
		$rootMailboxes = $account->getRootMailboxes(false, false);
		
		//GO::debug($rootMailboxes);
		if ($params['node'] == 'root') 
			return $this->_getMailboxTreeNodes($rootMailboxes, true);
		else{
			$parts = explode('_', base64_decode($params['node']));
			$type = array_shift($parts);
			$accountId = array_shift($parts);
			$mailboxName = implode('_', $parts);

			$account = GO_Email_Model_Account::model()->findByPk($accountId);

			$mailbox = new GO_Email_Model_ImapMailbox($account, array('name' => $mailboxName));
			return $this->_getMailboxTreeNodes($mailbox->getChildren(false, false), true);
		}
	}
	
	private function _getUsage(GO_Email_Model_Account $account){
		$usage="";
		
		$quota = $account->openImapConnection()->get_quota();
		
		if(isset($quota['usage'])) {
			if(!empty($quota['limit'])) {
				$percentage = ceil($quota['usage']*100/$quota['limit']);
				$usage = sprintf(GO::t('usage_limit','email'), $percentage.'%', GO_Base_Util_Number::formatSize($quota['limit']*1024));
				
				$round5 = floor($usage/5)*5;

				$usage='<span class="em-usage-'.$round5.'">'.$usage.'</span>';
				
			}	else {
				$usage = sprintf(GO::t('usage','email'), GO_Base_Util_Number::formatSize($quota['usage']*1024));
			}
		}
		//var_dump($usage);
		return $usage;
	}

	public function actionTree($params) {
		GO::session()->closeWriting();
		

		$response = array();
		
		if(!isset($params['node'])){
			return $response;
		}elseif ($params['node'] == 'root') {
			
			$findParams = GO_Base_Db_FindParams::newInstance()
						->select('t.*')
						->joinModel(array(
								'model' => 'GO_Email_Model_AccountSort',
								'foreignField' => 'account_id', //defaults to primary key of the remote model
								'localField' => 'id', //defaults to primary key of the model
								'type' => 'LEFT',
								'tableAlias'=>'s',
								'criteria'=>  GO_Base_Db_FindCriteria::newInstance()->addCondition('user_id', GO::user()->id,'=','s')
						))
						->ignoreAdminGroup()
						->order('order', 'DESC');
			
			$stmt = GO_Email_Model_Account::model()->find($findParams);

			while ($account = $stmt->fetch()) {

				$alias = $account->getDefaultAlias();
				if($alias){
					$nodeId=base64_encode('account_' . $account->id);
					
					$node = array(
							'text' => $alias->email,
							'name' => $alias->email,
							'id' => $nodeId,
							'isAccount'=>true,
							'permission_level'=>$account->getPermissionLevel(),
							'hasError'=>false,
							'iconCls' => 'folder-account',
							'expanded' => $this->_isExpanded($nodeId),
							'noselect' => false,
							'account_id' => $account->id,
							'mailbox' => rtrim($account->mbroot,"./"),							
							'noinferiors' => false,
							//'inbox_new' => 0,
							//'usage' => "",
							//"acl_supported"=>false
					);
					if($account->getPermissionLevel() <= GO_Base_Model_Acl::READ_PERMISSION)
					  $node['cls'] = 'em-readonly';
		
//					try{						
//						if($node['expanded']){
//							$account->openImapConnection();
//							$rootMailboxes = $account->getRootMailboxes(true);
//							$node['children']=$this->_getMailboxTreeNodes($rootMailboxes);
//						}
//						
//					}catch(GO_Base_Mail_ImapAuthenticationFailedException $e){
//						//$this->_checkImapConnectException($e,$node);
//						$node['isAccount'] = false;
//						$node['hasError'] = true;
//						$node['text'] .= ' ('.GO::t('error').')';
//						$node['children']=array();
//						$node['expanded']=true;
//						$node['qtipCfg'] = array('title'=>GO::t('error'), 'text' =>htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));	
//					}
					
					$response[] = $node;
				}
			}
		} else {
//			$this->_setExpanded($params['node']);
			
			$params['node']=base64_decode($params['node']);

			$parts = explode('_', $params['node']);
			$type = array_shift($parts);
			$accountId = array_shift($parts);
			$mailboxName = implode('_', $parts);
			
			$account = GO_Email_Model_Account::model()->findByPk($accountId);
			
			if($type=="account"){
				$response=$this->_getMailboxTreeNodes($account->getRootMailboxes(true));
			}else{
				$mailbox = new GO_Email_Model_ImapMailbox($account, array('name' => $mailboxName));
				$response = $this->_getMailboxTreeNodes($mailbox->getChildren());
			}
		}

		return $response;
	}
	
//	private function _checkImapConnectException(Exception $e, &$node) {
//		if (strpos($e->getMessage(),'Authentication failed')==0) {
//			$node['isAccount'] = false;
//			$node['hasError'] = true;
//			$node['text'] .= ' ('.GO::t('error').')';
//			$node['children']=array();
//			$node['expanded']=true;
//			$node['qtipCfg'] = array('title'=>GO::t('error'), 'text' =>htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));	
//		} else {
//			throw $e;
//		}
//	}

	/**
	 * 
	 * @param type $mailboxes
	 * @param boolean $fetchAllWithSubscribedFlag Get all children with the "Subscribed" flag
	 * @return type
	 */
	private function _getMailboxTreeNodes($mailboxes, $fetchAllWithSubscribedFlag=false) {
		$nodes = array();
		foreach ($mailboxes as $mailbox) {
			
			//skip mailboxes with nonexistent flags if we're not listing subscribtions
			if(!$fetchAllWithSubscribedFlag && !$mailbox->isVisible())// && !$mailbox->haschildren)
				continue;
			
			/* @var $mailbox GO_Email_Model_ImapMailbox */
//			if (!$mailbox->subscribed)
//				continue;

			$nodeId = base64_encode('f_' . $mailbox->getAccount()->id . '_' . $mailbox->name);
			
			$text = $mailbox->getDisplayName();
						
			if(!$fetchAllWithSubscribedFlag){				
				if ($mailbox->unseen > 0) {
					$text .= '&nbsp;<span class="em-folder-status" id="status_' . $nodeId . '">(' . $mailbox->unseen . ')</span>';
				} else {
					$text .= '&nbsp;<span class="em-folder-status" id="status_' . $nodeId . '"></span>';
				}
			}
			
//			GO::debug($mailbox);

//			$children = $this->_getMailboxTreeNodes($mailbox->getChildren());
			
			$node = array(
					'text' => $text,
					'mailbox' => $mailbox->name,
					'account_id' => $mailbox->getAccount()->id,
					'iconCls' => 'folder-default',
					'id' => $nodeId,
					'draggable'=>$mailbox->getAccount()->getPermissionLevel() > GO_Base_Model_Acl::READ_PERMISSION,
					'permission_level'=>$mailbox->getAccount()->getPermissionLevel(),
					'noselect' => $mailbox->noselect,
					'disabled' =>$fetchAllWithSubscribedFlag && $mailbox->noselect,
					'noinferiors' => $mailbox->noinferiors,
					'children' => !$mailbox->haschildren ? array() : null,
					'expanded' => !$mailbox->haschildren,
//					'usage'=>'',
//					'acl_supported'=>false,
					'cls'=>$mailbox->noselect==1 ? 'em-tree-node-noselect' : ""
							//'children'=>$children,
							//'expanded' => !count($children),
			);
			
			if (!$fetchAllWithSubscribedFlag && $mailbox->unseen > 0) {
				$node['cls'] .= ' ml-folder-unseen';
			}
			
//			GO::debug($node);
			
			if($mailbox->name=='INBOX'){
				$node['usage']=$this->_getUsage($mailbox->getAccount());
				$node['acl_supported']=$mailbox->getAccount()->openImapConnection()->has_capability('ACL');
			}

			if ($mailbox->haschildren && $this->_isExpanded($nodeId)) {
				$node['children'] = $this->_getMailboxTreeNodes($mailbox->getChildren(false, !$fetchAllWithSubscribedFlag),$fetchAllWithSubscribedFlag);
				$node['expanded'] = true;
			}
			
			if($fetchAllWithSubscribedFlag){
				$node['checked']=$mailbox->subscribed;
			}

			//if($mailbox->hasnochildren)

			$sortIndex = 5;

			switch ($mailbox->name) {
				case 'INBOX':
					$node['iconCls'] = 'email-folder-inbox';
					$sortIndex = 0;
					break;
				case $mailbox->getAccount()->sent:
					$node['iconCls'] = 'email-folder-sent';
					$sortIndex = 1;
					break;
				case $mailbox->getAccount()->trash:
					$node['iconCls'] = 'email-folder-trash';
					$sortIndex = 3;
					break;
				case $mailbox->getAccount()->drafts:
					$node['iconCls'] = 'email-folder-drafts';
					$sortIndex = 2;
					break;
				case 'INBOX.Spam':
				case 'Spam':
					$node['iconCls'] = 'email-folder-spam';
					$sortIndex = 4;
					break;
			}
			
			$nodes[$sortIndex .'-'. $mailbox->name] = $node;
		}
		GO_Base_Util_Array::caseInsensitiveSort($nodes);

		return array_values($nodes);
	}

	private $_treeState;

	private function _isExpanded($nodeId) {
		if (!isset($this->_treeState)) {
			$state = GO::config()->get_setting("email_accounts_tree", GO::user()->id);
			
			if(empty($state)){
				$decoded = base64_decode($nodeId);
				//account and inbox nodes are expanded by default
				if((stristr($decoded, 'account') || substr($decoded,-6)=='_INBOX')){
					return true;
				}else
				{
					return false;
				}
			}
			
			$this->_treeState = json_decode($state);
		}

		return in_array($nodeId, $this->_treeState);
	}

//	private function _setExpanded($nodeId){	
//		
//		if(!$this->_isExpanded($nodeId)){
//			$this->_treeState[]=$nodeId;
//			GO::config()->save_setting("email_accounts_tree", json_encode($this->_treeState), GO::user()->id);
//		}
//	}

	protected function actionSaveTreeState($params) {
		$response['success'] = GO::config()->save_setting("email_accounts_tree", $params['expandedNodes'], GO::user()->id);
		return $response;
	}
	
	
	protected function actionSaveSort($params){
		$sort_order = json_decode($params['sort_order'], true);		
		$count = count($sort_order);
		
		GO_Email_Model_AccountSort::model()->deleteByAttribute("user_id", GO::user()->id);

		for($i=0;$i<$count;$i++) {
			
			$as = new GO_Email_Model_AccountSort();
			$as->order=$count-$i;
			$as->account_id=$sort_order[$i];
			$as->save();
		}
		
		return array("success"=>true);
	}
	
	
	protected function actionUsernames($params){
		
//		$store = GO_Base_Data_Store::newInstance(GO_Email_Model_Account::model());
//		$findParams= $store->getDefaultParams($params)->group('username');
		
		
		
		$store = GO_Base_Data_Store::newInstance(GO_Base_Model_User::model());
		$findParams= $store->getDefaultParams($params);
		$findParams->joinModel(array( 
					'model'=>'GO_Email_Model_Account',
					'localTableAlias'=>'t', //defaults to "t" 
					'localField'=>'id', //defaults to "id"  
					'foreignField'=>'user_id', //defaults to primary key of the remote model 
					'tableAlias'=>'acc', //Optional table alias  
					'type'=>'INNER', //defaults to INNER, 
				//	'criteria'=>'' //GO_Base_Db_FindCriteria Optional extra join parameters
			));
		
		$findParams->select('acc.username');
		$findParams->joinCustomFields(false);
		$findParams->group(array('acc.username'));
		
		$stmt = GO_Base_Model_User::model()->find($findParams);
		
		//$stmt = GO_Email_Model_Account::model()->find($findParams);
		$store->setStatement($stmt);
		
		return $store->getData();
	}

	protected function actionSavePassword($params) {
		
		$accountModel = GO_Email_Model_Account::model()->findByPk($params['id']);
		$accountModel->password = $params['password'];
		$accountModel->save();
		
		return array('success'=>true);
		
	}

	protected function actionCopyMailTo($params) {
		
		$srcMessages = json_decode($params['srcMessages']);
		
		foreach ($srcMessages as $srcMessageInfo) {
			$srcAccountModel = GO_Email_Model_Account::model()->findByPk($srcMessageInfo->accountId);
			$srcImapMessage = GO_Email_Model_ImapMessage::model()->findByUid($srcAccountModel, $srcMessageInfo->mailboxPath, $srcMessageInfo->mailUid);
			
			$targetAccountModel = GO_Email_Model_Account::model()->findByPk($params['targetAccountId']);
			
			if($targetAccountModel->getPermissionLevel() <= GO_Base_Model_Acl::READ_PERMISSION)
			  throw new GO_Base_Exception_AccessDenied();
			
			$targetImapConnection = $targetAccountModel->openImapConnection($params["targetMailboxPath"]);

			$targetImapConnection->append_message($params['targetMailboxPath'], $srcImapMessage->getSource());
		}
		
		return array('success'=>true);
	}
	
}
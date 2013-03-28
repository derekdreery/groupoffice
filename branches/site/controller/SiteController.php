<?php

class GO_Site_Controller_Site extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Site_Model_Site';
	
	protected function actionRedirectToFront($params){
		
		$site = GO_Site_Model_Site::model()->findByPk($params['id']);
		
		header("Location: ".$site->getBaseUrl());
		exit();
	}
	
	protected function actionTree($params){
		$response=array();
	
		if(!isset($params['node']))
			return $response;
		
		$args = explode('_', $params['node']);
		
		$siteId = $args[0];
		
		if(!isset($args[1]))
			$type = 'root';
		else
			$type = $args[1];
		
		if(isset($args[2]))
			$parentId = $args[2];
		else
			$parentId = null;
		
		switch($type){
			case 'root':
				$response = GO_Site_Model_Site::getTreeNodes();
				break;
			case 'content':
				
				if($parentId === null){
					$response = GO_Site_Model_Content::getTreeNodes($siteId);
				} else {
					$parentNode = GO_Site_Model_Content::model()->findByPk($parentId);
					if($parentNode)
						$response = $parentNode->getChildrenTree();
				}
				break;
//			case 'news':
//				$response = GO_Site_Model_News::getTreeNodes($site);
//				break;
		}
		
		return $response;
	}
	
	
	public function actionTreeMAIL($params) {
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
}
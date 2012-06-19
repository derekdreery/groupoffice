<?php

class GO_Email_Controller_Account extends GO_Base_Controller_AbstractModelController {

	protected $model = "GO_Email_Model_Account";

	protected function getStoreParams($params) {

		$findParams = GO_Base_Db_FindParams::newInstance()
						->select("t.*,a.email, a.name")
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

		require_once(GO::config()->root_path . 'Group-Office.php');
		global $GO_SECURITY, $GO_MODULES;

		GO::session()->closeWriting();


		require_once ($GLOBALS['GO_MODULES']->modules['email']['class_path'] . "email.class.inc.php");
		require_once ($GLOBALS['GO_MODULES']->modules['email']['class_path'] . "cached_imap.class.inc.php");

		$imap = new cached_imap();
		$email = new email();
		$email2 = new email();


		$response['success'] = true;
		$count = $email->get_accounts($GLOBALS['GO_SECURITY']->user_id);
		$response['email_status'] = array();
		while ($email->next_record()) {
			try {
				$account = $imap->open_account($email->f('id'), 'INBOX', false);

				if ($account) {
					$inbox = $email2->get_folder($email->f('id'), 'INBOX');

					$unseen = $imap->get_unseen();

					//$response['email_status'][$inbox['id']]=$account;
					$response['email_status'][$inbox['id']]['unseen'] = $unseen['count'];
					$response['email_status'][$inbox['id']]['messages'] = $imap->selected_mailbox['messages'];
				}
			} catch (Exception $e) {
				go_debug($e->getMessage());
			}
			$imap->disconnect();
		}

		return $response;
	}
	
	public function actionSubscribtionsTree($params){
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		
		$rootMailboxes = $account->getRootMailboxes(false, false);
		
		//GO::debug($rootMailboxes);
		if ($params['node'] == 'root') 
			return $this->_getMailboxTreeNodes($rootMailboxes, true);
		else{
			$parts = explode('_', $params['node']);
			$accountId = $parts[1];
			$mailboxName = $parts[2];

			$account = GO_Email_Model_Account::model()->findByPk($accountId);

			$mailbox = new GO_Email_Model_ImapMailbox($account, array('name' => $mailboxName));
			return $this->_getMailboxTreeNodes($mailbox->getChildren(false, false), true);
		}
	}

	public function actionTree($params) {

		$response = array();

		if ($params['node'] == 'root') {
			
			$findParams = GO_Base_Db_FindParams::newInstance()
						->select('t.*')
						->joinModel(array(
								'model' => 'GO_Email_Model_AccountSort',
								'foreignField' => 'account_id', //defaults to primary key of the remote model
								'localField' => 'id', //defaults to primary key of the model
								'type' => 'LEFT'
						))
						->ignoreAdminGroup()
						->order('order', 'DESC');
			
			$stmt = GO_Email_Model_Account::model()->find($findParams);

			while ($account = $stmt->fetch()) {

				$alias = $account->getDefaultAlias();
				if($alias){
					$node = array(
							'text' => $alias->email,
							'name' => $alias->email,
							'id' => 'account_' . $account->id,
							'iconCls' => 'folder-account',
							'expanded' => true,
							'noselect' => false,
							'account_id' => $account->id,
							'mailbox' => 'INBOX',
							'children' => $this->_getMailboxTreeNodes($account->getRootMailboxes(true)),
							'noinferiors' => false,
							'inbox_new' => 0,
							'usage' => "",
							'parentExpanded' => true
					);

					$response[] = $node;
				}
			}
		} else {
//			$this->_setExpanded($params['node']);

			$parts = explode('_', $params['node']);
			$accountId = $parts[1];
			$mailboxName = $parts[2];

			$account = GO_Email_Model_Account::model()->findByPk($accountId);

			$mailbox = new GO_Email_Model_ImapMailbox($account, array('name' => $mailboxName));
			$response = $this->_getMailboxTreeNodes($mailbox->getChildren());
		}

//		var_dump($response);
//		exit();

		return $response;
	}

	private function _getMailboxTreeNodes($mailboxes, $subscribtions=false) {
		$nodes = array();
		foreach ($mailboxes as $mailbox) {
			/* @var $mailbox GO_Email_Model_ImapMailbox */
//			if (!$mailbox->subscribed)
//				continue;

			$nodeId = 'f_' . $mailbox->getAccount()->id . '_' . $mailbox->name;
			
			$text = $mailbox->getDisplayName();
			
			if(!$subscribtions){				
				if ($mailbox->unseen > 0) {
					$text .= '&nbsp;<span class="em-folder-status" id="status_' . $nodeId . '">(' . $mailbox->unseen . ')</span>';
				} else {
					$text .= '&nbsp;<span class="em-folder-status" id="status_' . $nodeId . '"></span>';
				}
			}

			//$children = $this->_getMailboxTreeNodes($mailbox->getChildren());

			$node = array(
					'text' => $text,
					'mailbox' => $mailbox->getName(true),
					'account_id' => $mailbox->getAccount()->id,
					'iconCls' => 'folder-default',
					'id' => $nodeId,
					'noselect' => $mailbox->noselect,
					'disabled' =>$mailbox->noselect,
					'noinferiors' => $mailbox->noinferiors,
					'children' => !$mailbox->haschildren ? array() : null,
					'expanded' => !$mailbox->haschildren
							//'children'=>$children,
							//'expanded' => !count($children),
			);

			if ($mailbox->haschildren && $this->_isExpanded($nodeId)) {
				$node['children'] = $this->_getMailboxTreeNodes($mailbox->getChildren(!$subscribtions, !$subscribtions),$subscribtions);
				$node['expanded'] = true;
			}
			
			if($subscribtions){
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
				case 'Spam':
					$node['iconCls'] = 'email-folder-spam';
					$sortIndex = 4;
					break;
			}

			$nodes[$sortIndex . $mailbox->name] = $node;
		}
		ksort($nodes);

		return array_values($nodes);
	}

	private $_treeState;

	private function _isExpanded($nodeId) {
		if (!isset($this->_treeState)) {
			$state = GO::config()->get_setting("email_accounts_tree", GO::user()->id);
			$this->_treeState = empty($state) ? array() : json_decode($state);
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

}
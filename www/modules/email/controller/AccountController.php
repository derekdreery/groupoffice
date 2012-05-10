<?php

class GO_Email_Controller_Account extends GO_Base_Controller_AbstractModelController {

	protected $model = "GO_Email_Model_Account";

	protected function actionTest($params) {
		$account = GO_Email_Model_Account::model()->findSingle();

		echo 'test';
		$folders = $account->getAllMailboxesWithStatus();
		var_dump($folders);
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

	public function actionTree($params) {
		$stmt = GO_Email_Model_Account::model()->find();

		$response = array();
		while ($account = $stmt->fetch()) {

			$alias = $account->getDefaultAlias();

			$node = array(
					'text' => $alias->email,
					'name' => $alias->email,
					'id' => 'account_' . $account->id,
					'iconCls' => 'folder-account',
					'expanded' => true,
					'noselect'=>false,
					'account_id' => $account->id,
					'mailbox' => 'INBOX',
					'children' => $this->_getMailboxTreeNodes($account->getAllMailboxes(true, true)),
					'noinferiors' => false,
					'inbox_new' => 0,
					'usage' => "",
					'parentExpanded' => true
			);

			$response[] = $node;
		}

//		var_dump($response);
//		exit();

		return $response;
	}

	private function _getMailboxTreeNodes($mailboxes) {
		$nodes = array();
		foreach ($mailboxes as $mailbox) {
			/* @var $mailbox GO_Email_Model_ImapMailbox */
			if(!$mailbox->subscribed)
				continue;
					
			$nodeId ='f_' . $mailbox->getAccount()->id . '_' . $mailbox->name;
			if ($mailbox->unseen > 0) {
				$statusHtml = '&nbsp;<span class="em-folder-status" id="status_' . $nodeId . '">(' . $mailbox->unseen . ')</span>';
			} else {
				$statusHtml = '&nbsp;<span class="em-folder-status" id="status_' . $nodeId . '"></span>';
			}
			
			$children = $this->_getMailboxTreeNodes($mailbox->getChildren());

			$node = array(
					'text' => $mailbox->getDisplayName().$statusHtml,
					'mailbox' => $mailbox->getName(true),
					'account_id'=>$mailbox->getAccount()->id,
					'iconCls' => 'folder-default',
					
					'id' => $nodeId,
					'noselect'=>$mailbox->noselect,
					'noinferiors'=>$mailbox->noinferiors,
					//'children' => $mailbox->hasnochildren ? array() : null
					//'expanded' => $mailbox->hasnochildren,
					'children'=>$children,
					'expanded' => !count($children),
			);
			
			$sortIndex=5;

			switch ($mailbox->name) {
				case 'INBOX':
					$node['iconCls'] = 'email-folder-inbox';
					$sortIndex=0;
					break;
				case $mailbox->getAccount()->sent:
					$node['iconCls'] = 'email-folder-sent';
					$sortIndex=1;
					break;
				case $mailbox->getAccount()->trash:
					$node['iconCls'] = 'email-folder-trash';
					$sortIndex=3;
					break;
				case $mailbox->getAccount()->drafts:
					$node['iconCls'] = 'email-folder-drafts';
					$sortIndex=2;
					break;
				case 'Spam':
					$node['iconCls'] = 'email-folder-spam';
					$sortIndex=4;
					break;
			}

			$nodes[$sortIndex.$mailbox->name] = $node;
		}
		ksort($nodes);
		
		return array_values($nodes);
	}

}
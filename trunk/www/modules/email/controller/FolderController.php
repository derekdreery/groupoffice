<?php

class GO_Email_Controller_Folder extends GO_Base_Controller_AbstractController {
	protected function actionCreate($params){
		
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
				
		$mailbox = new GO_Email_Model_ImapMailbox($account, array("name"=>$params["parent"]));
		$success = $mailbox->createChild($params["name"]);
		
		
		return array("success"=>$success);
	}
	
	protected function actionRename($params){
		
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
				
		$mailbox = new GO_Email_Model_ImapMailbox($account, array("name"=>$params["mailbox"]));
		$response['success'] = $mailbox->rename($params["name"]);
		
		if(!$response['success'])
			$response['feedback']="Failed to rename ".$params['mailbox']." to ".$params['name'];
		
		
		return $response;
	}
	
	protected function actionSubscribe($params){
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
				
		$mailbox = new GO_Email_Model_ImapMailbox($account, array("name"=>$params["mailbox"]));
		$response['success'] = $mailbox->subscribe();
		
		if(!$response['success'])
			$response['feedback']="Failed to subscribe to ".$params['mailbox'];
		return $response;
	}
	
	protected function actionUnsubscribe($params){
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
				
		$mailbox = new GO_Email_Model_ImapMailbox($account, array("name"=>$params["mailbox"]));
		$response['success'] = $mailbox->unsubscribe();
		
		if(!$response['success'])
			$response['feedback']="Failed to unsubscribe from ".$params['mailbox'];
		
		return $response;
	}
	
	protected function actionDelete($params){
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
				
		$mailbox = new GO_Email_Model_ImapMailbox($account, array("name"=>$params["mailbox"]));
		$success = $mailbox->delete();
		
		return array("success"=>$success);
	}
	
	protected function actionTruncate($params){
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
				
		$mailbox = new GO_Email_Model_ImapMailbox($account, array("name"=>$params["mailbox"]));
		$success = $mailbox->truncate();
		
		return array("success"=>$success);
	}
	
	protected function actionMove($params){
		
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
				
		$sourceMailbox = new GO_Email_Model_ImapMailbox($account, array("name"=>$params["sourceMailbox"]));
		$targetMailbox = new GO_Email_Model_ImapMailbox($account, array("name"=>$params["targetMailbox"]));
			
		
		$response['success'] = $sourceMailbox->move($targetMailbox);
		if(!$response['success'])
			$response['feedback']="Could not move folder $sourceMailbox to $targetMailbox";
		
		
		return $response;
	}
	
	protected function actionStore($params){
		
		GO::session()->closeWriting();
		
		$response = array(
				"results"=>array(),
				"success"=>true
		);
		
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		$mailboxes = $account->getAllMailboxes(false, false);
		foreach($mailboxes as $mailbox){
			$response['results'][]=array('name'=>$mailbox->name);
		}
		
		return $response;		
	}
	
	
	protected function actionAclStore($params) {
		
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		$imap = $account->openImapConnection($params['mailbox']);
		
		if (isset($params['delete_keys'])) {
			try {
				$response['deleteSuccess'] = true;
				$delete_ids = json_decode($params['delete_keys']);
				foreach ($delete_ids as $id) {
					$imap->delete_acl($params['mailbox'], $id);
				}
			} catch (Exception $e) {
				$response['deleteSuccess'] = false;
				$response['deleteFeedback'] = $e->getMessage();
			}
		}

		$response['success']=true;
		$response['results'] = $imap->get_acl($params['mailbox']);

		foreach ($response['results'] as &$record) {
			$record['read'] = strpos($record['permissions'], 'r') !== false;
			$record['write'] = strpos($record['permissions'], 'w') !== false;
			$record['delete'] = strpos($record['permissions'], 't') !== false;
			$record['createmailbox'] = strpos($record['permissions'], 'k') !== false;
			$record['deletemailbox'] = strpos($record['permissions'], 'x') !== false;
			$record['admin'] = strpos($record['permissions'], 'a') !== false;
		}
		
		return $response;
	}
	
	protected function actionSetAcl($params) {
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		$imap = $account->openImapConnection($params['mailbox']);

		$perms = '';

		//lrwstipekxacd

		if (isset($params['read'])) 
			$perms .='lrs';		

		if (isset($params['write'])) 
			$perms .='wip';		

		if (isset($params['delete'])) 
			$perms .='te';		

		if (isset($params['createmailbox'])) 
			$perms .='k';
		
		if (isset($params['deletemailbox'])) 
			$perms .='x';
		
		if (isset($params['admin'])) 
			$perms .='a';
		

		$response['success'] = $imap->set_acl($params['mailbox'], $params['identifier'], $perms);
		
		if(!$response['success'])
			$response['feedback']=$imap->last_error();
		return $response;
	}
}
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
		$success = $mailbox->rename($params["name"]);
		
		
		return array("success"=>$success);
	}
	
	protected function actionMove($params){
		
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
				
		$sourceMailbox = new GO_Email_Model_ImapMailbox($account, array("name"=>$params["sourceMailbox"]));
		$targetMailbox = new GO_Email_Model_ImapMailbox($account, array("name"=>$params["targetMailbox"]));
			
		
		$success = $sourceMailbox->move($targetMailbox);
		
		
		return array("success"=>$success);
	}
	
	protected function actionStore($params){
		
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
}
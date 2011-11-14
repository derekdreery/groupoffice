<?php

class GO_Email_Controller_Account extends GO_Base_Controller_AbstractController {

	public function actionCheckUnseen($params) {
		
		require_once(GO::config()->root_path.'Group-Office.php');
		global $GO_SECURITY, $GO_MODULES;
		
		require_once ($GLOBALS['GO_MODULES']->modules['email']['class_path'] . "email.class.inc.php");
		require_once ($GLOBALS['GO_MODULES']->modules['email']['class_path'] . "cached_imap.class.inc.php");

		$imap = new cached_imap();
		$email = new email();
		$email2 = new email();


		$response['success']=true;
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

}
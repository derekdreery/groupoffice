<?php
class GO_Email_Controller_LinkedEmail extends GO_Base_Controller_AbstractController{
	
	public function link($params) {
		
		
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
		
		$imapMessage = GO_Email_Model_ImapMessage::model()->findByUid($account, $params['mailbox'], $uid);
		
		$path = 'email/'.$account->id.'/'.$uid.'_'.$imapMessage->udate.'.eml';
		
		$imapMessage->saveToFile(GO::config()->file_storage_path.$path);
		
		$linkedEmail = new GO_Email_Model_LinkedEmail();
		$linkedEmail->from = (string) $imapMessage->from;
		$linkedEmail->to = (string) $imapMessage->to;
		$linkedEmail->cc =  (string) $imapMessage->cc;
		$linkedEmail->bcc = (string) $imapMessage->bcc;
			

		if(empty($link_message['subject'])) {
			global $GO_LANGUAGE, $lang;
			$GLOBALS['GO_LANGUAGE']->require_language_file('email');

			$link_message['subject']=$lang['email']['no_subject'];
		}

		foreach($links as $link) {
			$sr = $search->get_search_result($link['link_id'], $link['link_type']);

			$result['links'][]=$sr;

			if($sr) {
				$link_message['acl_id']=$sr['acl_id'];
				$this->insert_row('em_links',$link_message);
				$this->cache_message($link_message['link_id']);

				$GO_LINKS->add_link($link['link_id'], $link['link_type'], $link_message['link_id'], 9,$to_folder_id,0, $link_description, $link_description);
			}else {
				$imap->disconnect();
				throw new Exception('Cached record not found!');
			}
		}	

		$result['link_message']=$link_message;

		return $result;
	}
}
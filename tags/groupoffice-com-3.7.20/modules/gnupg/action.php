<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
require('../../Group-Office.php');
$GO_SECURITY->json_authenticate('gnupg');
require_once ($GO_MODULES->modules['gnupg']['class_path'].'gnupg.class.inc.php');
$gnupg = new gnupg();
$task=isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

try{
	switch($task)
	{
		case 'check_public_key_attachment':
			
			require_once ($GO_CONFIG->class_path."mail/imap.class.inc");
			require_once ($GO_MODULES->modules['email']['class_path']."cached_imap.class.inc.php");
			require_once ($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
			
			$email = new email();
			$imap = new cached_imap();

			$account = $imap->open_account($_REQUEST['account_id'], $_REQUEST['mailbox']);

			if($account['user_id']!=$GO_SECURITY->user_id) {
				die($lang['common']['accessDenied']);
			}
			
			$data = $imap->get_message_part_decoded($_REQUEST['uid'], $_REQUEST['imap_id'], $_REQUEST['encoding'], $_REQUEST['charset']);
			
			$response['success']=true;
			$response['is_public_key']=$gnupg->is_public_key($data);
			
			if($response['is_public_key'])
			{
				$_SESSION['GO_SESSION']['gnupg']['public_key_attachment']=$data;
			}
			
			$imap->disconnect();
		break;
		
		case 'import_public_key_attachment': 
			$gnupg->import($_SESSION['GO_SESSION']['gnupg']['public_key_attachment']);

			unset($_SESSION['GO_SESSION']['gnupg']['public_key_attachment']);
			
			require($GO_LANGUAGE->get_language_file('gnupg'));
			
			$response['feedback']=$lang['gnupg']['importSuccessful'];
			$response['success']=true;		
		
		break;
		
		
		case 'sign_key':

			$gnupg->sign_key($_POST['private_key'], String::get_email_from_string($_POST['public_key']), $_POST['password']);
			$response['success']=true;
		
		break;
		case 'gen_key':
			session_write_close();

			require_once($GO_MODULES->modules['email']['class_path'].'email.class.inc.php');
			$email = new email();
			
			$account = $email->get_account($_POST['account_id']);
			
			$response['success']=$gnupg->gen_key($account['name'], $account['email'], $_POST['passphrase'], $_POST['comment']);
			if(!$response['success'])
			{
				$reponse['error']=$gnupg->error;
			}
			
			break;
		case 'import_key':

			session_write_close();

				if (!is_uploaded_file($_FILES['keys']['tmp_name'][0]))
				{
					throw new Exception('No file received');
				}
				
				$data = file_get_contents($_FILES['keys']['tmp_name'][0]);		

				
				$gnupg->import($data);
				
				$response['success']=true;			
			
			break;
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);

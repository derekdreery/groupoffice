<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: json.tpl 2030 2008-06-04 10:12:13Z mschering $
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
		case 'gen_key':

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

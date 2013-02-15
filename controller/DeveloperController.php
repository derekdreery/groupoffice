<?php

class GO_Core_Controller_Developer extends GO_Base_Controller_AbstractController {

	public function actionCreateManyUsers($params) {
		
		if(!GO::user()->isAdmin())
			throw new Exception("You must be logged in as admin");
		
		$amount = 1000;
		$prefix = 'user';
		$domain = 'intermesh.dev';

		for ($i = 0; $i < $amount; $i++) {		

			echo "Creating $prefix$i\n";
			
			$user = GO_Base_Model_User::model()->findSingleByAttribute('username', $prefix . $i);
			if(!$user){
				$user = new GO_Base_Model_User();
				$user->username = $prefix . $i;
				$user->email = $prefix . $i . '@' . $domain;
				$user->password = $prefix . $i;
				$user->first_name = $prefix;
				$user->last_name = $i;
				$user->save();
				$user->checkDefaultModels();
			}

			if (GO::modules()->isInstalled('email') && GO::modules()->isInstalled('postfixadmin')) {

				$domainModel = GO_Postfixadmin_Model_Domain::model()->findSingleByAttribute('domain', $domain);

				if (!$domainModel) {
					$domainModel = new GO_Postfixadmin_Model_Domain();
					$domainModel->domain = $domain;
					$domainModel->save();
				}

				$mailboxModel = GO_Postfixadmin_Model_Mailbox::model()->findSingleByAttributes(array('domain_id' => $domainModel->id, 'username' => $user->email));

				if (!$mailboxModel) {
					$mailboxModel = new GO_Postfixadmin_Model_Mailbox();
					$mailboxModel->domain_id = $domainModel->id;
					$mailboxModel->username = $user->email;
					$mailboxModel->password = $prefix . $i;
					$mailboxModel->name = $user->name;	
					$mailboxModel->save();	
				}
				
				
				
				$accountModel = GO_Email_Model_Account::model()->findSingleByAttributes(array('user_id'=>$user->id, 'username'=>$user->email));
				
				if(!$accountModel){
					$accountModel = new GO_Email_Model_Account();
					$accountModel->user_id = $user->id;
					$accountModel->host = "localhost";
					$accountModel->port = 143;

					$accountModel->name = $user->name;
					$accountModel->username = $user->email;

					$accountModel->password = $prefix . $i;

					$accountModel->smtp_host = 'localhost';
					$accountModel->smtp_port = 25;
					$accountModel->save();

					$accountModel->addAlias($user->email, $user->name);
				}
			}
		}
		
		echo "Done\n\n";
	}

}
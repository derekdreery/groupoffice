<?php

class GO_Smime_EventHandlers {

	public static function loadAccount(GO_Email_Controller_Account $controller, &$response, GO_Email_Model_Account $account, $params) {
		$cert = GO_Smime_Model_Certificate::model()->findByPk($account->id);
		if ($cert && !empty($cert->cert)) {
			$response['data']['cert'] = true;
			$response['data']['always_sign'] = $cert->always_sign;
		}
	}

	public static function submitAccount(GO_Email_Controller_Account $controller, &$response, GO_Email_Model_Account $account, $params, $modifiedAttributes) {
		
		if (isset($_FILES['cert']['tmp_name'][0]) && is_uploaded_file($_FILES['cert']['tmp_name'][0])) {
			//check Group-Office password
			if(!GO::user()->checkPassword($params['smime_password']))
				throw new Exception(GO::t('badGoLogin','smime'));
			
			$certData = file_get_contents($_FILES['cert']['tmp_name'][0]);
			
			//smime password may not match the Group-Office password
			openssl_pkcs12_read($certData, $certs, $params['smime_password']);
			if (!empty($certs))
				throw new Exception(GO::t('smime_pass_matches_go','smime'));

			//password may not be empty.
			openssl_pkcs12_read($certData, $certs, "");
			if (!empty($certs))
				throw new Exception(GO::t('smime_pass_empty','smime'));
			
		}
		
		$cert = GO_Smime_Model_Certificate::model()->findByPk($account->id);
		if(!$cert){
			$cert = new GO_Smime_Model_Certificate();
			$cert->account_id=$account->id;
		}
		
		if(isset($certData))
			$cert->cert = $certData;
		elseif(!empty($params['delete_cert']))
			$cert->cert='';
		
		$cert->always_sign=!empty($params['always_sign']);
		$cert->save();

		if (!empty($cert->cert))
			$response['cert'] = true;
	}
	
	public static function aliasesStore(GO_Email_Controller_Alias $controller, &$response, GO_Base_Data_Store $store, $params){
		
		foreach($response['results'] as &$alias){
			$cert = GO_Smime_Model_Certificate::model()->findByPk($alias['account_id']);
			
			if($cert){
				$alias['has_smime_cert']=true;
				$alias['always_sign']=$cert->always_sign;
			}
		}
	}
	
	public static function beforeSend(GO_Email_Controller_Message $controller, array &$response, GO_Base_Mail_SmimeMessage $message, GO_Base_Mail_Mailer $mailer, GO_Email_Model_Account $account, GO_Email_Model_Alias $alias, $params){
		if(!empty($params['sign_smime'])){			
		
			//$password = trim(file_get_contents("/home/mschering/password.txt"));
			$password = GO::session()->values['smime']['passwords'][$account->id];
			
			$cert = GO_Smime_Model_Certificate::model()->findByPk($account->id);

			$message->setSignParams($cert->cert, $password, isset(GO::config()->smime_sign_extra_certs) ? GO::config()->smime_sign_extra_certs : "");
		}		
		
		if(!empty($params['encrypt_smime'])){		
			
			if(!isset($cert))
				$cert = GO_Smime_Model_Certificate::model()->findByPk($account->id);
			
			$password = GO::session()->values['smime']['passwords'][$account->id];
			openssl_pkcs12_read ($cert->cert, $certs, $password);

			if(!isset($certs['cert']))
				throw new Exception("Failed to get your public key for encryption");				

			
			$to = $message->getTo();
			
			$cc = $message->getCc();
			
			$bcc = $message->getBcc();
			
			if(is_array($cc))
				$to = array_merge($to, $cc);
			
			if(is_array($bcc))
				$to = array_merge($to, $bcc);
			
			//lookup all recipients
			$failed=array();
			$public_certs=array($certs['cert']);
			foreach($to as $email=>$name){
				$cert = $smime->get_public_certificate($GLOBALS['GO_SECURITY']->user_id, $email);				
				if(!$cert){
					$failed[]=$email;
				}				
				$public_certs[]=$cert['cert'];
			}
			
			if(count($failed))
				throw new Exception(sprintf(GO::t('noPublicCertForEncrypt','smime'), implode(', ',$failed)));
			
			$message->setEncryptParams($public_certs);
		}
	}

}
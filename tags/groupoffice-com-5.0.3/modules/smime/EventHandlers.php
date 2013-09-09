<?php

class GO_Smime_EventHandlers {

	public static function loadAccount(GO_Email_Controller_Account $controller, &$response, GO_Email_Model_Account $account, $params) {
		$cert = GO_Smime_Model_Certificate::model()->findByPk($account->id);
		if ($cert && !empty($cert->cert)) {
			$response['data']['cert'] = true;
			$response['data']['always_sign'] = $cert->always_sign;
		}
	}
	
	public static function deleteAccount(GO_Email_Model_Account $account){
		$cert = GO_Smime_Model_Certificate::model()->findByPk($account->id);
		if($cert)
			$cert->delete();		
	}

	public static function submitAccount(GO_Email_Controller_Account $controller, &$response, GO_Email_Model_Account $account, $params, $modifiedAttributes) {

		if (isset($_FILES['cert']['tmp_name'][0]) && is_uploaded_file($_FILES['cert']['tmp_name'][0])) {
			//check Group-Office password
			if (!GO::user()->checkPassword($params['smime_password']))
				throw new Exception(GO::t('badGoLogin', 'smime'));

			$certData = file_get_contents($_FILES['cert']['tmp_name'][0]);

			//smime password may not match the Group-Office password
			openssl_pkcs12_read($certData, $certs, $params['smime_password']);
			if (!empty($certs))
				throw new Exception(GO::t('smime_pass_matches_go', 'smime'));

			//password may not be empty.
			openssl_pkcs12_read($certData, $certs, "");
			if (!empty($certs))
				throw new Exception(GO::t('smime_pass_empty', 'smime'));
		}

		$cert = GO_Smime_Model_Certificate::model()->findByPk($account->id);
		if (!$cert) {
			$cert = new GO_Smime_Model_Certificate();
			$cert->account_id = $account->id;
		}

		if (isset($certData))
			$cert->cert = $certData;
		elseif (!empty($params['delete_cert']))
			$cert->cert = '';

		$cert->always_sign = !empty($params['always_sign']);
		$cert->save();

		if (!empty($cert->cert))
			$response['cert'] = true;
	}

	public static function aliasesStore(GO_Email_Controller_Alias $controller, &$response, GO_Base_Data_Store $store, $params) {

		foreach ($response['results'] as &$alias) {
			$cert = GO_Smime_Model_Certificate::model()->findByPk($alias['account_id']);

			if ($cert) {
				$alias['has_smime_cert'] = true;
				$alias['always_sign'] = $cert->always_sign;
			}
		}
	}

	public static function viewMessage(GO_Email_Controller_Message $controller, array &$response, GO_Email_Model_ImapMessage $imapMessage, GO_Email_Model_Account $account, $params) {
		
		if ($imapMessage->content_type == 'application/pkcs7-mime' || $imapMessage->content_type == 'application/x-pkcs7-mime') {

			$encrypted = !isset($imapMessage->content_type_attributes['smime-type']) || ($imapMessage->content_type_attributes['smime-type'] != 'signed-data' || $imapMessage->content_type_attributes['smime-type'] != 'enveloped-data');
			if ($encrypted) {

				GO::debug("Message is encrypted");

				$cert = GO_Smime_Model_Certificate::model()->findByPk($account->id);

				if (!$cert || empty($cert->cert)) {					
					GO::debug('SMIME: No private key at all found for this account');
					$response['htmlbody'] =GO::t('noPrivateKeyForDecrypt','smime');
					return false;
				}

				if (isset($params['password']))
					GO::session()->values['smime']['passwords'][$account->id] = $params['password'];

				if (!isset(GO::session()->values['smime']['passwords'][$account->id])) {
					$response['askPassword'] = true;
					GO::debug("Need to ask for password");
					return false;
				}
			}

			$attachments = $imapMessage->getAttachments();
			$att = array_shift($attachments);
			
			

//			array (
//      'type' => 'application',
//      'subtype' => 'pkcs7-mime',
//      'smime-type' => 'enveloped-data',
//      'name' => 'smime.p7m',
//      'id' => false,
//      'encoding' => 'base64',
//      'size' => '2302',
//      'md5' => false,
//      'disposition' => false,
//      'language' => false,
//      'location' => false,
//      'charset' => false,
//      'lines' => false,
//      'number' => 1,
//      'extension' => 'p7m',
//      'human_size' => '2,2 KB',
//      'tmp_file' => false,
//    )

			$infile = GO_Base_Fs_File::tempFile();
			$outfile = GO_Base_Fs_File::tempFile();

			//$outfilerel = $reldir . 'unencrypted.txt';

			if ($encrypted) {
				GO::debug('Message is encrypted');

				if(!$imapMessage->saveToFile($infile->path()))
					throw new Exception("Could not save IMAP message to file for decryption");
				
				$password = GO::session()->values['smime']['passwords'][$account->id];
				openssl_pkcs12_read($cert->cert, $certs, $password);

				if (empty($certs)) {
					//password invalid
					$response['askPassword'] = true;
					GO::debug("Invalid password");
					return false;
				}

				$return = openssl_pkcs7_decrypt($infile->path(), $outfile->path(), $certs['cert'], array($certs['pkey'], $password));

				$infile->delete();

				if (!$return || !$outfile->exists() || !$outfile->size()) {					
					$response['htmlbody'] = GO::t('decryptionFailed','smime') . '<br />';
					while ($str = openssl_error_string()) {
						$response['htmlbody'].='<br />' . $str;
					}
					GO::debug("Decryption failed");
					return false;
				}else
				{
					$message = GO_Email_Model_SavedMessage::model()->createFromMimeData($outfile->getContents());
					$newResponse = $message->toOutputArray(true);
					foreach($newResponse as $key=>$value){
						if(!empty($value) || $key=='attachments')
							$response[$key]=$value;
					}
					$response['smime_encrypted']=true;
					$response['path']=$outfile->stripTempPath();
				}
			}else
			{
				GO::debug('Message is NOT encrypted');
			}
		}
	}

	public static function beforeSend(GO_Email_Controller_Message $controller, array &$response, GO_Base_Mail_SmimeMessage $message, GO_Base_Mail_Mailer $mailer, GO_Email_Model_Account $account, GO_Email_Model_Alias $alias, $params) {
		if (!empty($params['sign_smime'])) {

			//$password = trim(file_get_contents("/home/mschering/password.txt"));
			$password = GO::session()->values['smime']['passwords'][$account->id];

			$cert = GO_Smime_Model_Certificate::model()->findByPk($account->id);
			$message->setSignParams($cert->cert, $password);
		}

		if (!empty($params['encrypt_smime'])) {

			if (!isset($cert))
				$cert = GO_Smime_Model_Certificate::model()->findByPk($account->id);

			$password = GO::session()->values['smime']['passwords'][$account->id];
			openssl_pkcs12_read($cert->cert, $certs, $password);

			if (!isset($certs['cert']))
				throw new Exception("Failed to get your public key for encryption");


			$to = $message->getTo();

			$cc = $message->getCc();

			$bcc = $message->getBcc();

			if (is_array($cc))
				$to = array_merge($to, $cc);

			if (is_array($bcc))
				$to = array_merge($to, $bcc);

			//lookup all recipients
			$failed = array();
			$publicCerts = array($certs['cert']);
			foreach ($to as $email => $name) {
				$pubCert = GO_Smime_Model_PublicCertificate::model()->findSingleByAttributes(array('user_id' => GO::user()->id, 'email' => $email));
				if (!$pubCert) {
					$failed[] = $email;
				}else
				{
					$publicCerts[] = $pubCert->cert;
				}
			}

			if (count($failed))
				throw new Exception(sprintf(GO::t('noPublicCertForEncrypt', 'smime'), implode(', ', $failed)));

			$message->setEncryptParams($publicCerts);
		}
	}

}
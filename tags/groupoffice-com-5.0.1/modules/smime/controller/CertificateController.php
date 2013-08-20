<?php

class GO_Smime_Controller_Certificate extends GO_Base_Controller_AbstractController {

	public function actionDownload($params) {

		//fetch account for permission check.
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);

		$cert = GO_Smime_Model_Certificate::model()->findByPk($account->id);
		if (!$cert)
			throw new GO_Base_Exception_NotFound();

		$filename = str_replace(array('@', '.'), '-', $account->getDefaultAlias()->email) . '.p12';

		$file = new GO_Base_Fs_File($filename);
		GO_Base_Util_Http::outputDownloadHeaders($file);

		echo $cert->cert;
	}

	public function actionCheckPassword($params) {
		//fetch account for permission check.
		$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);

		$cert = GO_Smime_Model_Certificate::model()->findByPk($account->id);

		openssl_pkcs12_read($cert->cert, $certs, $params['password']);

		$response['success'] = true;
		$response['passwordCorrect'] = !empty($certs);

		if ($response['passwordCorrect']) {
			//store in session for later usage
			GO::session()->values['smime']['passwords'][$params['account_id']] = $params['password'];
		}
		return $response;
	}

	public function actionVerify($params) {

		$response['success'] = true;

		//if file was already stored somewhere after decryption
		if(!empty($params['cert_id'])){
			$cert = GO_Smime_Model_PublicCertificate::model()->findByPk($params['cert_id']);
			$certData=$cert->cert;
		}else 
		{
			if (!empty($params['filepath'])) {
				$srcFile = new GO_Base_Fs_File(GO::config()->tmpdir.$params['filepath']);
			}elseif(!empty($params['account_id'])){
				$account = GO_Email_Model_Account::model()->findByPk($params['account_id']);
				$imapMessage = GO_Email_Model_ImapMessage::model()->findByUid($account, $params['mailbox'], $params['uid']);

				$srcFile = GO_Base_Fs_File::tempFile();
				if (!$imapMessage->saveToFile($srcFile->path()))
					throw new Exception("Could not fetch message from IMAP server");

				$this->_decryptFile($srcFile, $account);
			}

			$pubCertFile = GO_Base_Fs_File::tempFile();
			//Command line:
			//openssl smime -verify -in msg.txt
			$valid = openssl_pkcs7_verify($srcFile->path(), null, $pubCertFile->path(), $this->_getRootCertificates());
			$srcFile->delete();

			if ($valid) {
				if ($pubCertFile->exists()) {
					$certData = $pubCertFile->getContents();

					$arr = openssl_x509_parse($certData);
					$email = GO_Base_Util_String::get_email_from_string($arr['extensions']['subjectAltName']);

					$pubCertFile->delete();

					$this->_savePublicCertificate($certData, $email);
				} else {					
					throw new Exception('Certificate appears to be valid but could not get certificate from signature. SSL Error: '.openssl_error_string());
				}

				if (empty($certData))
					throw new Exception('Certificate appears to be valid but could not get certificate from signature.');
			}
		}
	
		
		if(!isset($arr) && isset($certData)){
			$arr = openssl_x509_parse($certData);
			$email = GO_Base_Util_String::get_email_from_string($arr['extensions']['subjectAltName']);
		}else if(empty($email)){
			$email = 'unknown';
		}

		$response['html'] = '';
		$response['cls'] = '';
		$response['text'] = '';

		if (isset($params['account_id'])) {
			if (!$valid) {

				$response['cls'] = 'smi-invalid';
				$response['text'] = GO::t('invalidCert', 'smime');

				$response['html'] .= '<h1 class="smi-invalid">' . GO::t('invalidCert', 'smime') . '</h1>';
				$response['html'] .= '<p>';
				while ($msg = openssl_error_string())
					$response['html'] .= $msg . "<br />\n";
				$response['html'] .= '</p>';
			} else if (strtolower($email) != strtolower($params['email'])) {

				$response['cls'] = 'smi-certemailmismatch';
				$response['text'] = GO::t('certEmailMismatch', 'smime');

				$response['html'] .= $response['short_html'] = '<h1 class="smi-certemailmismatch">' . GO::t('certEmailMismatch', 'smime') . '</h1>';
			} else {
				$response['cls'] = 'smi-valid';
				$response['text'] = GO::t('validCert', 'smime');

				$response['html'] .= $response['short_html'] = '<h1 class="smi-valid">' . GO::t('validCert', 'smime') . '</h1>';
			}
		}

		if (!isset($params['account_id']) || $valid) {
			$response['html'] .= '<table>';
			$response['html'] .= '<tr><td width="100">' . GO::t('name') . ':</td><td>' . $arr['name'] . '</td></tr>';
			$response['html'] .= '<tr><td width="100">'.GO::t('email','smime').':</td><td>' . $email . '</td></tr>';
			$response['html'] .= '<tr><td>'.GO::t('hash','smime').':</td><td>' . $arr['hash'] . '</td></tr>';
			$response['html'] .= '<tr><td>'.GO::t('serial_number','smime').':</td><td>' . $arr['serialNumber'] . '</td></tr>';
			$response['html'] .= '<tr><td>'.GO::t('version','smime').':</td><td>' . $arr['version'] . '</td></tr>';
			$response['html'] .= '<tr><td>'.GO::t('issuer','smime').':</td><td>';

			foreach ($arr['issuer'] as $skey => $svalue) {
				$response['html'] .= $skey . ':' . $svalue . '; ';
			}

			$response['html'] .= '</td></tr>';
			$response['html'] .= '<tr><td>'.GO::t('valid_from','smime').':</td><td>' . GO_Base_Util_Date::get_timestamp($arr['validFrom_time_t']) . '</td></tr>';
			$response['html'] .= '<tr><td>'.GO::t('valid_to','smime').':</td><td>' . GO_Base_Util_Date::get_timestamp($arr['validTo_time_t']) . '</td></tr>';
			$response['html'] .= '</table>';
		}


		return $response;
	}

	private function _savePublicCertificate($certData, $email) {


		$cert = GO_Smime_Model_PublicCertificate::model()->findSingleByAttributes(array('email' => $email, 'user_id' => GO::user()->id));
		if (!$cert) {
			$cert = new GO_Smime_Model_PublicCertificate();
			$cert->email = $email;
			$cert->user_id = GO::user()->id;
		}
		$cert->cert = $certData;
		$cert->save();
	}

	private function _getRootCertificates() {
		$certs = array();

//		if(isset($GLOBALS['GO_CONFIG']->smime_root_cert_location)){
//			
//			$GLOBALS['GO_CONFIG']->smime_root_cert_location=rtrim($GLOBALS['GO_CONFIG']->smime_root_cert_location, '/');		
//			
//			if(is_dir($GLOBALS['GO_CONFIG']->smime_root_cert_location)){				
//							
//				$dir = opendir($GLOBALS['GO_CONFIG']->smime_root_cert_location);
//				if ($dir) {
//					while ($item = readdir($dir)) {
//						if ($item != '.' && $item != '..') {
//							$certs[] = $GLOBALS['GO_CONFIG']->smime_root_cert_location.'/'.$item;
//						}
//					}
//					closedir($dir);
//				}
//			}elseif(file_exists($GLOBALS['GO_CONFIG']->smime_root_cert_location)){
//				$certs[]=$GLOBALS['GO_CONFIG']->smime_root_cert_location;
//			}
//		}
//		
		if (isset(GO::config()->smime_root_cert_location) && file_exists(GO::config()->smime_root_cert_location))
			$certs[] = GO::config()->smime_root_cert_location;

		return $certs;
	}

	private function _decryptFile(GO_Base_Fs_File $srcFile, GO_Email_Model_Account $account) {
		$data = $srcFile->getContents();
		if (strpos($data, "enveloped-data") || strpos($data, 'Encrypted Message')) {
			$cert = GO_Smime_Model_Certificate::model()->findByPk($account->id);
			$password = GO::session()->values['smime']['passwords'][$params['account_id']];
			openssl_pkcs12_read($cert->cert, $certs, $password);

			$decryptedFile = GO_Base_Fs_File::tempFile();

			$ret = openssl_pkcs7_decrypt($srcFile->path(), $decryptedFile->path(), $certs['cert'], array($certs['pkey'], $password));
			$decryptedFile->move($srcFile->parent()->path(), $srcFile->name());
		}
	}

}
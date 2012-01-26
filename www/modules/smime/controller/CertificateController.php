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

		$response['success']=true;
		$response['passwordCorrect'] = !empty($certs);

		if ($response['passwordCorrect']) {
			//store in session for later usage
			GO::session()->values['smime']['passwords'][$params['account_id']] = $params['password'];
		}
		return $response;
	}
	
	public function actionVerify($params){
		
	}

}
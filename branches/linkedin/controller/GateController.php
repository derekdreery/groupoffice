<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

class GO_Linkedin_Controller_Gate extends GO_Base_Controller_AbstractController {

	protected function actionConnectToLinkedin($params) {
		
		$linkedIn = new GO_Linkedin_Util_Linkedin(array(
				'appKey'       => GO::config()->linkedin_application_api_key,
				'appSecret'    => GO::config()->linkedin_application_secret_key,
				'callbackUrl'  => NULL 
			));
		
		$reqToken = $linkedIn->retrieveTokenRequestForNetwork();
		
		if (empty($reqToken['success']))
			throw new Exception('Problem while communicating with LinkedIn.');
		
//		$OAuth = new OAuth(GO::config()->linkedin_api_key, GO::config()->linkedin_secret_key);
//		$reqToken = $OAuth->getRequestToken('https://api.linkedin.com/uas/oauth/requestToken?scope=r_network');
//		
//		$_SESSION['GO_SESSION']['linkedin']['oauth_token'] = $reqToken['oauth_token'];
//		$_SESSION['GO_SESSION']['linkedin']['oauth_token_secret'] = $reqToken['oauth_token_secret'];
		
		GO::session()->values['request'] = $reqToken['linkedin'];
		
		return array(
				'success' => true,
				'authenticateUrl' => 'https://www.linkedin.com/uas/oauth/authenticate?oauth_token='.$reqToken['linkedin']['oauth_token']
			);
		
	}
	
	/**
	 * This action is meant to be called by the user right after actionConnectToLinkedin
	 * @param type $params
	 * @return type
	 * @throws Exception
	 */
	protected function actionRequestAccessToken($params) {

		if (empty($params['addressbookId']))
			throw new Exception('Invalid parameter "addressbookId" in the current request.');
		
		if (empty(GO::session()->values['request']))
			throw new Exception('This action requires the OAuth token.');
		
		$linkedIn = new GO_Linkedin_Util_Linkedin(array(
				'appKey'       => GO::config()->linkedin_application_api_key,
				'appSecret'    => GO::config()->linkedin_application_secret_key,
				'callbackUrl'  => NULL 
			));
		
		$accessToken = $linkedIn->retrieveTokenAccess(GO::session()->values['request']['oauth_token'], GO::session()->values['request']['oauth_token_secret'], $params['pinVerification']);

		if (!empty($accessToken['linkedin']['oauth_problem']) && $accessToken['linkedin']['oauth_problem'] == 'token_rejected')
			throw new Exception('Access code rejected.');

		if (empty($accessToken['success']))
			throw new Exception('Problem while communicating with LinkedIn.');
//				
//		$_SESSION['oauth']['linkedin']['access'] = $accessToken['linkedin'];
		
		$autoImportModel = GO_Linkedin_Model_AutoImport::model()->findByPk($params['addressbookId']);
		if (!$autoImportModel) {
			$autoImportModel = new GO_Linkedin_Model_AutoImport();
			$autoImportModel->addressbook_id = $params['addressbookId'];
		}
		$autoImportModel->access = json_encode($accessToken['linkedin']);
		$autoImportModel->save();
			
		return array('success'=>true);
	}
	
	protected function actionImportContacts($params) {
		
		if (empty($params['addressbookId']))
			throw new Exception('Invalid parameter "addressbookId" in the current request.');
		
		$autoImportModel = GO_Linkedin_Model_AutoImport::model()->findByPk($params['addressbookId']);
		
		if (!$autoImportModel)
			throw new Exception(GO::t('noLinkedinCredentials','linkedin'));

		$accessInfoString = $autoImportModel->getDecryptedAccessInfo();
		$accessInfoArray = json_decode($accessInfoString,true);
		
		if (empty($accessInfoArray))
			throw new Exception('This action requires the OAuth access token.');
		
		$linkedIn = new GO_Linkedin_Util_Linkedin(array(
				'appKey'       => GO::config()->linkedin_application_api_key,
				'appSecret'    => GO::config()->linkedin_application_secret_key,
				'callbackUrl'  => NULL 
			));
		
		$linkedIn->setToken($accessInfoArray);
		
		$connectionsResponse = $linkedIn->connections('~/connections:(first-name,last-name,picture-url,skills,location,positions,honors,certifications,educations,member-url-resources,distance)');
		var_dump($connectionsResponse['linkedin']);exit();
		if (empty($connectionsResponse['success']))
			throw new Exception('Unable to retrieve connection info.');
		
		$domDoc = new DOMDocument();
		$xmlReader = new XMLReader();
		$xmlReader->XML($connectionsResponse['linkedin']);
		while ($xmlReader->name !== 'person')
			$xmlReader->read();
		
		$linkedinContacts = array();
		
		while ($xmlReader->name === 'person') {
			$domNode = simplexml_import_dom($domDoc->importNode($xmlReader->expand(),true));
						
			$firstNameString = 'first-name';
			$lastNameString = 'last-name';
			$pictureString = 'picture-url';
			$apiStdProfileReqString = 'api-standard-profile-request';
						
			$linkedinContacts[] = array(
				'id' => strip_tags($domNode->id->asXML()),
				'first_name' => strip_tags($domNode->$firstNameString->asXML()),
				'last_name' => strip_tags($domNode->$lastNameString->asXML()),
				'headline' => strip_tags($domNode->headline->asXML()),
				'area' => strip_tags($domNode->location->name->asXML()),
				'country' => trim(strip_tags($domNode->location->country->asXML())),
				'industry' => strip_tags($domNode->industry->asXML()),
				'photoUrl' => strip_tags($domNode->$pictureString->asXML())
			);
			
			$xmlReader->next('person');
		}
		
		foreach ($linkedinContacts as $linkedinContactInfo) {
			
//			$profileResponse = $linkedIn->profile('id='.$linkedinContactInfo['id'].':(positions)');
//			
//			var_dump($profileResponse['linkedin']);
//			//exit();
			
//			$contactModel = GO_Linkedin_Model_ImportedContact::model()->createImportedContactModel($linkedinContactInfo,$params['addressbookId']);
//			$contactModel->save();
		}
		
		return array('success'=>true);
	}
	
	protected function actionRemoveAccessToken($params) {
		if (empty($params['addressbookId']))
			throw new Exception('Invalid parameter "addressbookId" in the current request.');
		
		$autoImportModel = GO_Linkedin_Model_AutoImport::model()->findByPk($params['addressbookId']);
		$autoImportModel->delete();
		
		return array('success'=>true);
	}

}


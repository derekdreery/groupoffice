<?php

class GO_Core_Controller_Settings extends GO_Base_Controller_AbstractController {
	
	public function actionSubmit($params){
			
		if(!empty($params["dateformat"])){
			$dateparts = explode(':',$params["dateformat"]);
			$params['date_separator'] = $dateparts[0];
			$params['date_format'] = $dateparts[1];
		}
		
		$user = GO_Base_Model_User::model()->findByPk($params['id']);
		$user->setAttributes($params);
					
		if (!empty($params["password1"]) || !empty($params["password2"])) {
			
			if(!$user->checkPassword($params['current_password']))
				throw new Exception(GO::t('badPassword'));
			
			if ($params["password1"] != $params["password2"]) {
				throw new Exception(GO::t('error_match_pass', 'users'));
			}
			if (!empty($params["password2"])) {
				$user->setAttribute('password', $_POST['password2']);
			}
		}
		
		$response['success']=$user->save();
		
		GO::modules()->callModuleMethod('submitSettings', array(&$this, &$params, &$response));
		
		
		
		return $response;
	}
	
	public function actionLoad($params){
		
		$user = GO_Base_Model_User::model()->findByPk($params['id']);
		
		
		$response['data']=$user->getAttributes('formatted');
		
		if($user->contact)
			$response['data']=array_merge($response['data'],$user->contact->getAttributes('formatted'));
		
		if(!empty($response['data']['date_separator'])&& !empty($response['data']['date_format'])){
			$response['data']['dateformat'] = $response['data']['date_separator'].':'.$response['data']['date_format'];
		}
		
		$response['success']=true;
		
		GO::modules()->callModuleMethod('loadSettings', array(&$this, &$params, &$response));
		
		return $response;
	}
	
}

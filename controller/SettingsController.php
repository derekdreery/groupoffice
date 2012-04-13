<?php

class GO_Core_Controller_Settings extends GO_Base_Controller_AbstractController {
	
	protected function actionSubmit($params){
			
		if(!empty($params["dateformat"])){
			$dateparts = explode(':',$params["dateformat"]);
			$params['date_separator'] = $dateparts[0];
			$params['date_format'] = $dateparts[1];
		}
		
		$user = GO_Base_Model_User::model()->findByPk($params['id']);
		
					
		if (!empty($params["password"]) || !empty($params["passwordConfirm"])) {
			
			if(!$user->checkPassword($params['current_password']))
				throw new Exception(GO::t('badPassword'));
			
//			if ($params["password"] != $params["passwordConfirm"]) {
//				throw new Exception(GO::t('error_match_pass', 'users'));
//			}
//			if (!empty($params["passwordConfirm"])) {
//				$user->setAttribute('password', $_POST['passwordConfirm']);
//			}
		}else
		{
			unset($params['password']);
		}
		$user->setAttributes($params);
		
		$response['success']=$user->save();
		
		GO::modules()->callModuleMethod('submitSettings', array(&$this, &$params, &$response, $user), false);
		
		
		GO_Base_Session::setCompatibilitySessionVars();
		
		
		return $response;
	}
	
	protected function actionLoad($params){
		
		$user = GO_Base_Model_User::model()->findByPk($params['id']);
		
		
		$response['data']=$user->getAttributes('formatted');
		unset($response['data']['password']);
		
		if($user->contact)
			$response['data']=array_merge($response['data'],$user->contact->getAttributes('formatted'));
		
		if(!empty($response['data']['date_separator'])&& !empty($response['data']['date_format'])){
			$response['data']['dateformat'] = $response['data']['date_separator'].':'.$response['data']['date_format'];
		}
		
		$response['success']=true;
		
		GO::modules()->callModuleMethod('loadSettings', array(&$this, &$params, &$response, $user));
		
		return $response;
	}
	
}

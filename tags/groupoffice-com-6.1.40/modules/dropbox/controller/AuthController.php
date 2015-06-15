<?php

namespace GO\Dropbox\Controller;

use \Dropbox as dbx;
use GO;
use GO\Base\Controller\AbstractController;
use GO\Dropbox\DropboxModule;
use GO\Dropbox\Model\User;
use GO\Dropbox\Model\Sync;
use GO\Dropbox\Model\DropboxClient;

class AuthController extends AbstractController {
	

	protected function checkSecurityToken() {
		if ($this->getAction() == 'callback')
			return true;
		return parent::checkSecurityToken();
	}
	
	protected function actionStart(){
	
		$dropbox = new DropboxClient();
		if($dropbox->IsAuthorized()) {
			echo 'Already connected';
			return;
		}
		
		// redirect user to dropbox auth page
		$return_url = GO::url('dropbox/auth/callback',array(), false);
		$auth_url = $dropbox->BuildAuthorizeUrl($return_url);
		$request_token = $dropbox->GetRequestToken();
		GO::session()->values['dropbox_request_token'] = serialize($request_token);
		//store_token($request_token, $request_token['t']);
		header("Location: ".$auth_url);
	}
	
	protected function actionCallback(){
		
		$dropbox = new DropboxClient();
		
		$requestToken = @unserialize(GO::session()->values['dropbox_request_token']);
		unset(GO::session()->values['dropbox_request_token']);		
		
		$accessToken = $dropbox->GetAccessToken($requestToken);

		$user = User::model()->findByPk(GO::user()->id);
		if(!$user){
			$user = new User();
			$user->user_id=GO::user()->id;
		}
		$user->dropbox_user_id=$dropbox->uid; 
		$user->access_token = serialize($accessToken);
		$user->save();
		
		if($dropbox->IsAuthorized()){
		
			$this->render("externalHeader");

			echo "<h1>".GO::t('connected','dropbox')."</h1>";
			echo "<p>".GO::t('done','dropbox')."</p>";
			//echo '<a href="'.\GO::url('dropbox/auth/sync').'">'.\GO::t('syncNow','dropbox').'</p>';

			echo '<button onclick="window.close();">'.GO::t('cmdClose').'</button>';

			$this->render("externalFooter");
		}

	}

	protected function actionSync(){
		
		
		$this->render("externalHeader");
		
		echo "<h1>".GO::t('syncing','dropbox')."</h1>";
		
		$sync = new Sync();
		$sync->start();
		
		echo "<p>".GO::t('done','dropbox')."</p>";
		
		$this->render("externalFooter");
	}
	
	
	protected function actionDisconnect(){
		$dbUser = User::model()->findByPk(GO::user()->id);
		
		$response['success']=$dbUser ? $dbUser->delete() : true;
		
		return $response;
	}

	
	
}
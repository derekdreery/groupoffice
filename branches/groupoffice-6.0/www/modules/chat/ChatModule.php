<?php

namespace GO\Chat;

use GO;


class ChatModule extends \GO\Professional\Module{
	
	public static function initListeners() {
		
		
		$c = new \GO\Core\Controller\AuthController();
		$c->addListener('headstart', 'GO\Chat\ChatModule', 'headstart');
		
		GO::session()->addListener('login', 'GO\Chat\ChatModule', 'login');
		
		parent::initListeners();
	}
	
	public static function headstart(){

		$url = GO::config()->host.'modules/chat/converse.js-0.7.4/';
		
		$head = '
    <link rel="stylesheet" type="text/css" media="screen" href="'.$url.'converse.css">
    <!--<script data-main="main" src="'.$url.'components/requirejs/require.js"></script>-->
    <script src="'.$url.'builds/converse.min.js"></script>
		';
		
		echo $head;
	}
	
	public static function login($username, $password, $user){
		if(GO::modules()->chat){
			
			require GO::config()->root_path . 'modules/chat/xmpp-prebind-php/lib/XmppPrebind.php';
			
			GO::debug("CHAT: Pre binding to XMPP HOST: ".self::getXmppHost()." BOSH URI: ".self::getBoshUri()." with user $username");
			
			$xmppPrebind = new \XmppPrebind(self::getXmppHost(), self::getBoshUri(), 'Work', strpos(self::getBoshUri(),'https')!==false, false);
			if($xmppPrebind->connect($username, $password)){
				
				try{
					$xmppPrebind->auth();

					GO::debug("CHAT: connect successfull");
					// array containing sid, rid and jid			
					GO::session()->values['chat']['prebind']= $xmppPrebind->getSessionInfo();
				}catch(Exception $e){
					GO::debug("CHAT: Authentication failed: ".$e);
				}
			}else
			{
				GO::debug("CHAT: failed to connect");
			}
		}
	}
	
	public static function getPrebindInfo(){
		return isset(GO::session()->values['chat']['prebind']) ? GO::session()->values['chat']['prebind'] : false;
	}
	
	public static function getBoshUri(){
//		$jabberHost = 'intermesh.group-office.com';
//		$boshUri = 'https://' . $jabberHost . ':5281/http-bind';
		
		$proto = GO::request()->isHttps() ? 'https' : 'http';
		
		return isset(GO::config()->chat_bosh_uri) ? GO::config()->chat_bosh_uri : $proto.'://' . self::getXmppHost() . ':5281/http-bind';
	}
	
	public static function getXmppHost(){
		//$jabberHost = 'intermesh.group-office.com';
		return isset(GO::config()->chat_xmpp_host) ? GO::config()->chat_xmpp_host : $_SERVER['HTTP_HOST'];
		
	}
	
}
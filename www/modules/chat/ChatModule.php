<?php

namespace GO\Chat;

use GO;


class ChatModule extends \GO\Base\Module{
	
	public static function initListeners() {
		
		
		$c = new \GO\Core\Controller\AuthController();
		$c->addListener('headstart', 'GO\Chat\ChatModule', 'headstart');
		
		GO::session()->addListener('login', 'GO\Chat\ChatModule', 'login');
		
		parent::initListeners();
	}
	
	public static function headstart(){
		
		
		//regenerate Prosody groups file
		$aclMtime = GO::config()->get_setting('chat_acl_mtime');
		if($aclMtime != GO::modules()->chat->acl->mtime || !self::getGroupsFile()->exists()){		
			self::generateGroupsFile();
			
			GO::config()->save_setting('chat_acl_mtime',GO::modules()->chat->acl->mtime);
		}
		
		

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
	
	/**
	 * Get the file with groups info for Prosody
	 * 
	 * @return \GO\Base\Fs\File
	 */
	public static function getGroupsFile(){
		$folder = new GO\Base\Fs\Folder(GO::config()->file_storage_path.'chat');
		
		$folder->create();
		
		$file = $folder->createChild('groups.txt');
		
		return $file;
	}
	
	public static function generateGroupsFile(){
		
		$file = self::getGroupsFile();
		
		$fp = fopen($file->path(), 'w');
		
		fwrite($fp, "[".GO::config()->product_name." ".strtolower(GO::t('users'))."]\n");
		
		$xmppHost = self::getXmppHost();
				
		\GO\Base\Model\Acl::getAuthorizedUsers(GO::modules()->chat->acl_id, \GO\Base\Model\Acl::READ_PERMISSION, function($user) use ($fp, $xmppHost){
			
			$line = $user->username.'@'.$xmppHost.'='.$user->name."\n";
			fwrite($fp, $line);			
		});	
		
		fclose($fp);
		
	}
	
}
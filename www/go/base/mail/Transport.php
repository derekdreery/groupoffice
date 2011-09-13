<?php
class GO_Base_Mail_Transport extends Swift_SmtpTransport{
	
	public static function newGoInstance(){
		$o = self::newInstance(GO::config()->smtp_server, GO::config()->smtp_port, GO::config()->smtp_encryption);
		
		if(!empty(GO::config()->smtp_username)){
			$o->setUsername(GO::config()->smtp_username)
				->setPassword(GO::config()->smtp_password);
		}
		return $o;
	}	
}

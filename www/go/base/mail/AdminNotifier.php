<?php
class GO_Base_Mail_AdminNotifier {
	
	/**
	 * Can be used to notify the administrator by email
	 * 
	 * @param string $subject
	 * @param string $message 
	 */
	public static function sendMail($subject, $message){
		$subject = "NOTICE from ".GO::config()->title.": ".$subject;
		
		$message = GO_Base_Mail_Message::newInstance();
		$message->setSubject($subject);

		$message->setBody($message);
		$message->addFrom(GO::config()->webmaster_email,GO::config()->title);
		$message->addTo(GO::config()->webmaster_email,'Webmaster');

		GO_Base_Mail_Mailer::newGoInstance()->send($message);
	}
}
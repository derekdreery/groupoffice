<?php
namespace GO\Base\Mail;

class AdminNotifier {
	
	/**
	 * Can be used to notify the administrator by email
	 * 
	 * @param string $subject
	 * @param string $message 
	 */
	public static function sendMail($subject, $body){
		$subject = "ALERT: ".$subject;
		
		$message = \GO\Base\Mail\Message::newInstance();
		$message->setSubject($subject);

		$message->setBody($body,'text/plain');
		$message->setFrom(\GO::config()->webmaster_email,\GO::config()->title);
		$message->addTo(\GO::config()->webmaster_email,'Webmaster');

		\GO\Base\Mail\Mailer::newGoInstance()->send($message);
	}
}
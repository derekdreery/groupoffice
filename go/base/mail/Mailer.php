<?php
/*
 * Copyright Intermesh BV
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 *
 */

/**
 * This class is used to parse and write RFC822 compliant recipient lists
 * 
 * @package GO.base.mail
 * @version $Id: RFC822.class.inc 7536 2011-05-31 08:37:36Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @copyright Copyright Intermesh BV.
 */


namespace GO\Base\Mail;


class Mailer extends \Swift_Mailer{
	
	/**
   * Create a new Mailer instance.
   * 
	 * @var \Swift_SmtpTransport $transport. 
	 * Optionally supply a transport class. If omitted a Transport 
	 * object will be created that uses the smtp settings from config.php
	 * 
   * @return Mailer
   */
  public static function newGoInstance($transport=false)
  {
		if(!$transport)
			$transport=Transport::newGoInstance();
		
    $mailer = new self($transport);		
		return $mailer;
  }
	
	public function send(\Swift_Mime_Message $message, &$failedRecipients = null) {
		
		if(\GO::config()->debug)
			\GO::debug("Sending e-mail to ".implode(",",array_keys($message->getTo())));
		
		if(\GO::modules()->isInstalled("log")){
			
			$str = "";
			
			$from = $message->getFrom ();
			if(!empty($from))
				$str .= implode(",",array_keys($from));
			else
				$str .= "unknown";
			
			$str .= " -> ";
			
			$to = $message->getTo ();
			if(!empty($to))
				$str .= implode(",",array_keys($to));
			
			$to = $message->getCc ();
			if(!empty($to))
				$str .= implode(",",array_keys($to));
			
			$to = $message->getBcc ();
			if(!empty($to))
				$str .= implode(",",array_keys($to));
			
			\GO\Log\Model\Log::create ("email", $str);
		}
		
//		debug_print_backtrace();
//		exit("NO MAIL");
		
		return parent::send($message, $failedRecipients);
	}
	
}
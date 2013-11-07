<?php
/*
 * Copyright Intermesh
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
 * @package GO.module.email
 * @version $Id: RFC822.class.inc 7536 2011-05-31 08:37:36Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @copyright Copyright Intermesh BV.
 */

class GO_Email_Transport extends Swift_SmtpTransport{
	
	public static function newGoInstance(GO_Email_Model_Account $account){
		$encryption = empty($account->smtp_encryption) ? null : $account->smtp_encryption;
		$o = self::newInstance($account->smtp_host, $account->smtp_port, $encryption);
		
		if(!empty($account->smtp_username)){
			$o->setUsername($account->smtp_username)
				->setPassword($account->decryptSmtpPassword());
		}
		return $o;
	}	
}

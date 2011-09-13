<?php
class GO_Base_Mail_Mailer extends Swift_Mailer{
	
	/**
   * Create a new Mailer instance.
   * 
   * @return GO_Base_Mail_Mailer
   */
  public static function newGoInstance()
  {
    return new self(GO_Base_Mail_Transport::newGoInstance());
  }
	
}
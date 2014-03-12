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
 * @package GO.modules.files
 * @version $Id: RFC822.class.inc 7536 2011-05-31 08:37:36Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @copyright Copyright Intermesh BV.
 */


namespace GO\Email;
use GO;

class EmailModule extends \GO\Base\Module{	

	public static function initListeners() {

		$c = new \GO\Core\Controller\ReminderController();
		$c->addListener('reminderdisplay', "GO\Email\EmailModule", "reminderDisplay");

		$c = new \GO\Core\Controller\AuthController();
		$c->addListener('head', 'GO\Email\EmailModule', 'head');

		\GO\Base\Model\User::model()->addListener('delete', "GO\Email\EmailModule", "deleteUser");

		return parent::initListeners();
	}
	public function autoInstall() {
		return true;
	}

	public static function head(){

		$font_size = \GO::user() ? \GO::config()->get_setting('email_font_size', \GO::user()->id) : false;
		if(!$font_size)
			$font_size='12px';

		echo "\n<!-- Inserted by EmailModule::head() -->\n<style>\n".
		'.message-body,.message-body p, .message-body li, .go-html-formatted td, .em-composer .em-plaintext-body-field{'.
			'font-size: '.$font_size.';!important'.
		"}\n</style>\n<!-- End EmailModule::head() -->\n";
	}


	public static function deleteUser($user) {
		Model\Account::model()->deleteByAttribute('user_id', $user->id);
		Model\Label::model()->deleteUserLabels($user->id);
	}


	public static function submitSettings(&$settingsController, &$params, &$response, $user) {

		\GO::config()->save_setting('email_use_plain_text_markup', isset($params['use_html_markup']) ? '0' : '1', \GO::user()->user_id);
		\GO::config()->save_setting('email_skip_unknown_recipients', isset($params['skip_unknown_recipients']) ? '1' : '0', \GO::user()->user_id);
		\GO::config()->save_setting('email_always_request_notification', isset($params['always_request_notification']) ? '1' : '0', \GO::user()->user_id);
		\GO::config()->save_setting('email_always_respond_to_notifications', isset($params['always_respond_to_notifications']) ? '1' : '0', \GO::user()->user_id);
		\GO::config()->save_setting('email_font_size', $params['font_size'], \GO::user()->user_id);

		return parent::submitSettings($settingsController, $params, $response, $user);
	}

	public static function reminderDisplay($controller, &$html, $params){
		if(!empty($params['unseenEmails']))
			$html .= '<p>'.str_replace('{new}', $params['unseenEmails'], GO::t('youHaveNewMails','email')).'</p>';
	}

	public static function firstRun()
	{
		parent::firstRun();
		Model\Label::model()->createDefaultLabels(GO::user()->id);
	}
}

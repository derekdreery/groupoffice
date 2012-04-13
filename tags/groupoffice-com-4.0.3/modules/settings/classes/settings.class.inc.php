<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: settings.class.inc.php 5874 2011-01-04 15:23:26Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

class settings // extends db
{

	public function __on_load_listeners($events) {

		$events->add_listener('inline_scripts', __FILE__, 'settings','inline_scripts');

	}

	public static function inline_scripts(){

		global $GO_CONFIG;

		$t = $GO_CONFIG->get_setting('login_screen_text_enabled');
		
		if(!empty($t)){
			$login_screen_text = $GO_CONFIG->get_setting('login_screen_text');
			$login_screen_text_title = $GO_CONFIG->get_setting('login_screen_text_title');
			echo 'GO.mainLayout.on("login", function(mainLayout){mainLayout.msg("'.String::escape_javascript ($login_screen_text_title).'", "'.String::escape_javascript ($login_screen_text).'", 3600, 400);});';
		}
	}

	//private $db;

	public function settings()
	{
		global $GO_CONFIG, $GO_MODULES, $GO_LANGUAGE;
		//$this->db = new db();
	}

	public function getSettingByName($name)
	{
		return $GLOBALS['GO_CONFIG']->get_setting($name);
	}

	public function getAllSettings()
	{
		return $GLOBALS['GO_CONFIG']->get_settings();
	}

	public function getSettingsByUser($userid)
	{
		return $GLOBALS['GO_CONFIG']->get_settings($userid);
	}

	public function save_setting($name, $value, $userid)
	{
		$GLOBALS['GO_CONFIG']->save_setting($name, $value, $userid);
		
		return $GLOBALS['GO_CONFIG']->get_setting($name);
	}
}
?>

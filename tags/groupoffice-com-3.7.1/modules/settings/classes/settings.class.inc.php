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

	//private $db;

	public function settings()
	{
		global $GO_CONFIG, $GO_MODULES, $GO_LANGUAGE;
		//$this->db = new db();
	}

	public function getSettingByName($name)
	{
		return $GO_CONFIG->get_setting($name);
	}

	public function getAllSettings()
	{
		return $GO_CONFIG->get_settings();
	}

	public function getSettingsByUser($userid)
	{
		return $GO_CONFIG->get_settings($userid);
	}

	public function save_setting($name, $value, $userid)
	{
		$GO_CONFIG->save_setting($name, $value, $userid);
		
		return $GO_CONFIG->get_setting($name);
	}
}
?>

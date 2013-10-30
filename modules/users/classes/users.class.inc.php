<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

class users extends db
{

	public function get_register_email(){
		global $GO_CONFIG, $GO_LANGUAGE, $lang;
		$r=array(
			'register_email_subject' => $GLOBALS['GO_CONFIG']->get_setting('register_email_subject'),
			'register_email_body' => $GLOBALS['GO_CONFIG']->get_setting('register_email_body')
		);

		$GLOBALS['GO_LANGUAGE']->require_language_file('users');
		
		if(!$r['register_email_subject']){
			$r['register_email_subject']=$lang['users']['register_email_subject'];
		}
		if(!$r['register_email_body']){
			$r['register_email_body']=$lang['users']['register_email_body'];
		}
		return $r;
	}

  

	public function get_users($group_id) {
		$sql = "SELECT * FROM go_users_groups ug ".
			"INNER JOIN go_users u ".
			"ON ug.user_id=u.id ".
			"WHERE ug.group_id='$group_id' ";
		$this->query($sql);
		return $this->num_rows();
	}

	public function get_user($id) {
		$sql = "SELECT * FROM go_users WHERE id='$id'";
		$this->query($sql);
		if ($this->num_rows()==1)
			return $this->next_record();
		else
			return false;
	}

}
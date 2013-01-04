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
 * @author Merijn Schering <mschering@intermesh.nl>
 */

class freebusypermissions extends db
{
	public function __on_load_listeners($events) {
		$events->add_listener('has_freebusy_access', __FILE__, 'freebusypermissions','has_freebusy_access');
		$events->add_listener('load_settings', __FILE__, 'freebusypermissions', 'load_settings');
	}

	public static function has_freebusy_access($request_user_id, $target_user_id, &$permission){
		global $GO_SECURITY;

		$fp = new freebusypermissions();


		$acl_id = $fp->get_acl($target_user_id);
		if(!$GO_SECURITY->has_permission($request_user_id, $acl_id)){
			$permission=false;
		}  else {
			$permission=true;
		}
	}

	public static function load_settings($response) {
		$fp = new freebusypermissions();
		$response['data']['freebusypermissions_acl_id']=$fp->get_acl($_POST['user_id']);
	}

	public function get_acl($user_id){
		$sql = "SELECT acl_id FROM fb_acl WHERE user_id=?";
		$this->query($sql, 'i',$user_id);

		$r = $this->next_record();

		if(!$r){
			global $GO_SECURITY;
			$r['acl_id'] = $GO_SECURITY->get_new_acl('freebusypermissions', $user_id);
			$r['user_id']=$user_id;

			$this->insert_row('fb_acl', $r);
		}

		return $r['acl_id'];
	}

}
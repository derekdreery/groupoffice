<?php
class wordpress extends db{

	public function set_contact_wp_user($contact_id, $wp_user_id){
		$r['contact_id']=$contact_id;
		$r['wp_user_id']=$wp_user_id;

		return $this->replace_row('gw_contacts_wp_users', $r);
	}

	public function get_contact_id_by_wp_user_id($wp_user_id){
		$sql = "SELECT contact_id FROM gw_contacts_wp_users WHERE wp_user_id=".intval($wp_user_id);
		$this->query($sql);

		$record = $this->next_record();
		if(!$record)
			return false;

		return $record['contact_id'];
	}

}
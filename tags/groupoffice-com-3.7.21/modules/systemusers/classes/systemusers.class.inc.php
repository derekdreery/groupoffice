<?php

class systemusers extends db {

	public function __on_load_listeners($events) {
		$events->add_listener('before_add_user', __FILE__, 'systemusers', 'before_add_user');
		$events->add_listener('update_user', __FILE__, 'systemusers', 'update_user');
		//$events->add_listener('user_delete', __FILE__, 'systemusers', 'user_delete');
		$events->add_listener('delete_email_account', __FILE__, 'systemusers', 'delete_vacation');
	}


	public static function before_add_user($user)
	{
		global $GO_CONFIG, $GO_MODULES, $lang;

		
		if(!$user['username'] || !$user['password'])
		{
			throw new Exception($lang['common']['missingField']);
		}

		if(!strpos($user['username'],'@')){
			exec($GO_CONFIG->cmd_sudo.' '.$GO_MODULES->modules['systemusers']['path'].'sudo.php '.$GO_CONFIG->get_config_file().' add_user '.$user['username'].' '.$user['password'], $output, $status);
			if($status)
			{
				throw new Exception("Adding a system user failed. Did you configure sudo for the systemusers module?".implode("\n",$output));
			}
		}
	}

    public static function update_user($user)
    {
		global $GO_CONFIG, $GO_MODULES;

		if(isset($user['password']) && $user['id'])
		{
			exec($GO_CONFIG->cmd_sudo.' '.$GO_MODULES->modules['systemusers']['path'].'sudo.php '.$GO_CONFIG->get_config_file().' update_user '.$user['id'].' '.$user['password'], $output, $status);
			if($status)
			{
				throw new Exception("Updating the system user failed. Did you configure sudo for the systemusers module?".$output[0]);
			}
		}
    }

    public static function user_delete($user)
	{
		global $GO_CONFIG, $GO_MODULES;
		
		exec($GO_CONFIG->cmd_sudo.' '.$GO_MODULES->modules['systemusers']['path'].'sudo.php '.$GO_CONFIG->get_config_file().' delete_user '.$user['username'], $output, $status);
		if($status)
		{
			throw new Exception(str_replace('<br />',"\n", $output[0]));
		}
	}

	function get_vacation($account_id) {
		$this->query("SELECT * FROM syu_vacation WHERE account_id=?", 'i', $account_id);
		return $this->next_record();
	}
	function add_vacation($vacation) {
		if($this->insert_row('syu_vacation', $vacation)) {
			return $vacation['account_id'];
		}
		return false;
	}
	function update_vacation($vacation) {
		$r = $this->update_row('syu_vacation', 'account_id', $vacation);
		return $r;
	}
	function delete_vacation($account_id) {
		$su = new systemusers();
		return $su->query("DELETE FROM syu_vacation WHERE account_id=?", 'i', $account_id);
	}

	function get_vacation_account_ids() {
		$this->query("SELECT account_id FROM syu_vacation");
		return $this->num_rows();
	}

}

?>

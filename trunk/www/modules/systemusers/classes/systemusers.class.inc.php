<?php

class systemusers extends db
{
    
	public function __on_load_listeners($events){		
		$events->add_listener('before_add_user', __FILE__, 'systemusers', 'before_add_user');
		$events->add_listener('update_user', __FILE__, 'systemusers', 'update_user');
        $events->add_listener('user_delete', __FILE__, 'systemusers', 'user_delete');
		$events->add_listener('delete_email_account', __FILE__, 'systemusers', 'delete_vacation');
	}


	public static function before_add_user($user)
	{
		global $GO_CONFIG, $GO_LANGUAGE;

		exec($GO_CONFIG->cmd_sudo.' useradd -m '.$user['username'], $arg2, $arg3);

		if($arg3)
		{
			require($GLOBALS['GO_LANGUAGE']->get_language_file('systemusers'));
			throw new Exception(str_replace('{USERNAME}', $user['username'], $lang['systemusers']['return_error']));
		}
		
		exec('echo '.$user['username'].':'.$user['password'].' | '.$GO_CONFIG->cmd_sudo.' '.$GO_CONFIG->cmd_chpasswd);
		
		if(!empty($GO_CONFIG->cmd_edquota) && !empty($GO_CONFIG->quota_protouser))
		{
			exec($GO_CONFIG->cmd_sudo.' '.$GO_CONFIG->cmd_edquota.' -p '.$GO_CONFIG->quota_protouser.' '.$GO_CONFIG->id.'_'.$user['username']);
		}
	}

    public static function update_user($user)
    {      
		global $GO_CONFIG, $GO_USERS;	
		
		if(!empty($user['password']))
		{
			if(!isset($user['username']))
			{
				$GO_USERS->get_user($user['id']);
				$user['username'] = $GO_USERS->f('username');
			}
			
			exec('echo '.$user['username'].':'.$user['password'].' | '.$GO_CONFIG->cmd_sudo.' '.$GO_CONFIG->cmd_chpasswd);		
		}	
    }

    public static function user_delete($user)
	{
		global $GO_CONFIG;

		exec($GO_CONFIG->cmd_sudo.' userdel '.$user['username']);
	}


	function get_vacation($account_id)
	{
		$this->query("SELECT * FROM syu_vacation WHERE account_id=?", 'i', $account_id);
		return $this->next_record();
	}
	function add_vacation($vacation)
	{
		if($this->insert_row('syu_vacation', $vacation))
		{
			return $vacation['account_id'];
		}
		return false;
	}
	function update_vacation($vacation)
	{
		$r = $this->update_row('syu_vacation', 'account_id', $vacation);
		return $r;
	}
	function delete_vacation($account_id)
	{
		$su = new systemusers();
		return $su->query("DELETE FROM syu_vacation WHERE account_id=?", 'i', $account_id);
	}

	function get_vacation_account_ids()
	{
		$this->query("SELECT account_id FROM syu_vacation");
		return $this->num_rows();
	}

}

?>

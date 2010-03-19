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

function connect($account_id, $mailbox='INBOX', $halt_on_error=true)
{
	global $email, $imap, $GO_SECURITY, $lang;

	if (!$account = $email->get_account($account_id)) {
		$response['success']=false;
		$response['feedback']=$lang['common']['selectError'];
		echo json_encode($response);
		exit();
	}

	if($account['user_id']!=$GO_SECURITY->user_id && !$GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
	{
		$response['success']=false;
		$response['feedback']=$lang['common']['accessDenied'];
		echo json_encode($response);
		exit();
	}
	if (!$imap->open($account, $mailbox)) {
		if(!$halt_on_error)
		return false;

		$response['success']=false;
		$response['feedback']= sprintf($lang['email']['feedbackCannotConnect'], $account['host'],  $imap->last_error(), $account['port']);
		echo json_encode($response);
		exit();
	}

	if(!defined('IMAP_CONNECTED'))
	{
		define('IMAP_CONNECTED', true);
	}

	return $account;

}

function load_template($template_id, $to='', $keep_tags=false)
{
	global $GO_CONFIG, $GO_MODULES, $GO_LANGUAGE, $GO_SECURITY, $GO_USERS;

	require_once ($GO_CONFIG->class_path.'mail/mimeDecode.class.inc');
	require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc.php');
	require_once($GO_MODULES->modules['mailings']['class_path'].'templates.class.inc.php');

	$ab = new addressbook();
	$tp = new templates();

	$template_body = '';

	$template = $tp->get_template($template_id);

	require_once($GO_CONFIG->class_path.'mail/Go2Mime.class.inc.php');
	$go2mime = new Go2Mime();
	$response['data'] = $go2mime->mime2GO($template['content'], $GO_MODULES->modules['mailings']['url'].'mimepart.php?template_id='.$template_id, true, true);
	unset($response['data']['to'],$response['data']['cc'], $response['data']['bcc'],$response['data']['subject']);

	if(!$keep_tags)
	{
		$values=array();
		$contact_id=0;
		//if contact_id is not set but email is check if there's contact info available
		if (!empty($to)) {

			if ($contact = $ab->get_contact_by_email($to, $GO_SECURITY->user_id)) {

				$values = array_map('htmlspecialchars', $contact);
			}elseif($user = $GO_USERS->get_user_by_email($to))
			{
				$values = array_map('htmlspecialchars', $user);
			}else
			{
				$ab->search_companies($GO_SECURITY->user_id, $to, 'email',0,0,1);
				if($ab->next_record())
				{
					$values = array_map('htmlspecialchars', $ab->record);
				}
			}
		}
		$tp->replace_fields($response['data']['body'], $values);
	}
	
	if($_POST['content_type']=='plain')
	{
		$response['data']['body']=String::html_to_text($response['data']['body'], false);
	}

	//$response['data']['to']=$to;

	return $response;
}

class email extends db
{
	var $last_error;
	var $mail;
	
	public function __on_load_listeners($events){
		$events->add_listener('user_delete', __FILE__, 'email', 'user_delete');
		$events->add_listener('build_search_index', __FILE__, 'email', 'build_search_index');
		$events->add_listener('save_settings', __FILE__, 'email', 'save_settings');
		$events->add_listener('check_database', __FILE__, 'email', 'check_database');
		$events->add_listener('login', __FILE__, 'email', 'login');
	}

	public static function login()
	{
		global $GO_SECURITY;

		//clear old cache
		$db = new db();
		$sql = "DELETE FROM em_messages_cache WHERE udate<".Date::date_add(time(),-21)." AND account_id IN (SELECT id FROM em_accounts WHERE user_id=".$GO_SECURITY->user_id.")";
		$db->query($sql);
	}
	
	public static function save_settings(){

		global $GO_MODULES, $GO_CONFIG, $GO_SECURITY;

		if($GO_MODULES->has_module('email'))
		{
			$value = isset($_POST['use_html_markup']) ? '0' : '1';
			$GO_CONFIG->save_setting('email_use_plain_text_markup', $value, $GO_SECURITY->user_id);
		}
	}

	function get_servermanager_mailbox_info($account)
	{
		global $GO_CONFIG, $GO_MODULES;

		if(isset($GO_MODULES->modules['serverclient']))
		{
			require_once($GO_MODULES->modules['serverclient']['class_path'].'serverclient.class.inc.php');
			$sc = new serverclient();

			foreach($sc->domains as $domain)
			{
				if(strpos($account['username'], '@'.$domain))
				{
					$sc->login();

					$params=array(
						'task'=>'serverclient_get_mailbox',
						'username'=>$account['username'],
						'password'=>$account['password']														
					);
					$server_response = $sc->send_request($GO_CONFIG->serverclient_server_url.'modules/postfixadmin/json.php', $params);
					go_debug($server_response);
					//go_log(LOG_DEBUG, var_export($server_response, true));
					return json_decode($server_response, true);
				}
			}
		}
		return false;
	}

	function get_accounts($user_id=0, $start=0, $offset=0, $sort='standard', $dir='ASC')
	{
		$sql = "SELECT al.name, al.email, al.signature, al.id AS default_alias_id, a.*,u.first_name, u.middle_name, u.last_name FROM em_accounts a ".
			"INNER JOIN go_users u on u.id=a.user_id ".
			"INNER JOIN em_aliases al ON (al.account_id=a.id AND al.`default`='1') ";

		if($user_id > 0)
		{
			$sql .= " WHERE user_id='".$this->escape($user_id)."'";
			$sql .= " ORDER BY ".$this->escape($sort.' '.$dir);
		}else {
			$sql = 
			$sql .= " ORDER BY ".$this->escape($sort.' '.$dir);
		}

		$this->query($sql);
		$count =  $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}


	function link_message($message)
	{
		global $GO_LINKS;

		$message['link_id']=$this->nextid('em_links');

		if(empty($message['subject'])){
			global $GO_LANGUAGE, $lang;
			$GO_LANGUAGE->require_language_file('email');

			$message['subject']=$lang['email']['no_subject'];
		}

		$this->insert_row('em_links',$message);



		$this->cache_message($message['link_id']);

		return $message['link_id'];
	}

	function delete_linked_message($link_id)
	{
		global $GO_CONFIG;

		require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
		$search = new search();
		$search->delete_search_result($link_id, 9);

		$message = $this->get_linked_message($link_id);

		@unlink($GO_CONFIG->file_storage_path.$message['path']);

		$sql ="DELETE FROM em_links WHERE link_id=".$this->escape($link_id);
		return $this->query($sql);
	}

	function get_linked_message($id)
	{
		$sql = "SELECT * FROM em_links WHERE link_id=".$this->escape($id);
		$this->query($sql);
		return $this->next_record();
	}



	function update_settings($settings)
	{
		if(!isset($settings['user_id']))
		{
			global $GO_SECURITY;
			$settings['user_id'] = $GO_SECURITY->user_id;
		}
		return $this->update_row('em_settings', 'user_id', $settings);
	}

	function get_settings($user_id)
	{
		$this->query("SELECT * FROM em_settings WHERE user_id='".$this->escape($user_id)."'");
		if ($this->next_record())
		{
			return $this->record;
		}else
		{
			global $GO_MODULES;

			$addressbook_id=0;
			if(isset($GO_MODULES->modules['addressbook']) && $GO_MODULES->modules['addressbook']['read_permission'])
			{
				require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc.php');
				$ab = new addressbook();

				if($addressbook=$ab->get_addressbook())
				{
					$addressbook_id=$addressbook['id'];
				}
			}

			$this->query("INSERT INTO em_settings (user_id, send_format, add_recievers, add_senders, request_notification, charset, beep, auto_check) VALUES ('$user_id', 'text/HTML', $addressbook_id, '0', '0', 'UTF-8','1','1')");
			return $this->get_settings($user_id);
		}
	}

	function add_account($account)
	{
		global $GO_CONFIG, $GO_LANGUAGE;


		require_once($GO_CONFIG->class_path."mail/imap.class.inc");
		$this->mail= new imap();

		if (!$this->mail->open(
		$account['host'],
		$account['type'],
		$account['port'],
		$account['username'],
		$account['password'],
			"INBOX", 
		0,
		$account['use_ssl'],
		$account['novalidate_cert']))
		{
			return false;
		}else
		{
			if (!$account['mbroot'] = $this->mail->check_mbroot($account['mbroot']))
			{
				$account['mbroot'] = '';
			}

			$account['spamtag']='***SPAM***';
			$account['trash'] = '';
			$account['sent'] = '';
			$account['drafts'] = '';
			$account['spam'] = '';


			if ($account['type']=='imap')
			{
				$account=$this->set_default_folders($account);

				$mailboxes =  $this->mail->get_mailboxes($account['mbroot']);
				$subscribed =  $this->mail->get_subscribed($account['mbroot']);
			}else
			{
				$mailboxes = array();
				$subscribed = array();
			}
			$this->mail->close();

			$account['id'] = $this->nextid("em_accounts");
			
			
			$alias['default']='1';
			$alias['account_id']=$account['id'];
			$alias['name']=$account['name'];
			$alias['email']=$account['email'];
			if(!empty($account['signature']))
				$alias['signature']=$account['signature'];
			
			$this->add_alias($alias);				
			
			unset($account['name'],$account['email'],$account['signature']);
					
			$this->insert_row('em_accounts', $account);
				
				
			$this->_synchronize_folders($account, $mailboxes, $subscribed);
				
			return $account['id'];
				
		}
	}

	function set_default_folders($account){
			
		global $GO_LANGUAGE;

		require($GO_LANGUAGE->get_language_file('email'));

		$mailboxes =  $this->mail->get_mailboxes($account['mbroot']);
		$subscribed =  $this->mail->get_subscribed($account['mbroot']);

		$mailbox_names = array();
		foreach($mailboxes  as $mailbox)
		{
			$mailbox_names[]=$mailbox['name'];
		}

		$subscribed_names = array();
		foreach($subscribed as $mailbox)
		{
			$subscribed_names[]=$mailbox['name'];
		}

		if($this->_add_folder($account['mbroot'].$lang['email']['trash'], $mailbox_names, $subscribed_names))
		{
			$account['trash'] = $account['mbroot'].$lang['email']['trash'];
		}elseif($account['mbroot'] = $this->mail->check_mbroot($mailbox_names[0]))
		{
			if($this->_add_folder($account['mbroot'].$lang['email']['trash'], $mailbox_names, $subscribed_names))
			{
				$account['trash'] = $account['mbroot'].$lang['email']['trash'];
			}
		}

		if($this->_add_folder($account['mbroot'].$lang['email']['sent'], $mailbox_names, $subscribed_names))
		{
			$account['sent'] = $account['mbroot'].$lang['email']['sent'];
		}

		if($this->_add_folder($account['mbroot'].$lang['email']['drafts'], $mailbox_names, $subscribed_names))
		{
			$account['drafts'] = $account['mbroot'].$lang['email']['drafts'];
		}

		if($this->_add_folder($account['mbroot'].'Spam', $mailbox_names, $subscribed_names))
		{
			$account['spam']= $account['mbroot'].'Spam';
		}

		return $account;
	}




	function _update_account($account)
	{
		if(isset($account['name']))
		{
			$alias['default']='1';
			$alias['account_id']=$account['id'];
			$alias['name']=$account['name'];
			
			if(isset($account['email']))
				$alias['email']=$account['email'];

			if(isset($account['signature']))
				$alias['signature']=$account['signature'];
			
			$this->update_row('em_aliases',array('account_id', 'default'), $alias);
			
			unset($account['name'],$account['email'],$account['signature']);
		}
		
		return $this->update_row('em_accounts', 'id', $account,'', false);
	}

	function update_account($account)
	{
		global $GO_CONFIG;

		require_once($GO_CONFIG->class_path."mail/imap.class.inc");
		$this->mail= new imap();

		$oldaccount = $this->get_account($account['id']);


		if ($this->mail->open(
		$account['host'],
		$account['type'],
		$account['port'],
		$account['username'],
		$account['password'],
			"INBOX", 
		0,
		$account['use_ssl'],
		$account['novalidate_cert']))
		{

			if (!$mbroot = $this->mail->check_mbroot($account['mbroot']))
			{
				$account['mbroot'] = '';
			}
				
			if($oldaccount['type']=='pop3' && $account['type']=='imap')
			{
				$account=$this->set_default_folders($account);
				$mailboxes =  $this->mail->get_mailboxes($account['mbroot']);
				$subscribed =  $this->mail->get_subscribed($account['mbroot']);
				$this->_synchronize_folders($account, $mailboxes, $subscribed);
			}elseif($oldaccount['type']=='imap' && $account['type']=='pop3')
			{
				$this->delete_folders($account['id']);
				
				$mailboxes =  $this->mail->get_mailboxes($account['mbroot']);
				$subscribed =  $this->mail->get_subscribed($account['mbroot']);
				$this->_synchronize_folders($account, $mailboxes, $subscribed);
			}

			$this->mail->close();

			return $this->_update_account($account);
		}
		return false;
	}

	function _add_folder($name, $mailbox_names, $subscribed_names)
	{
		$name = $this->mail->utf7_imap_encode($name);
		if (!in_array($name, $mailbox_names))
		{
			return @$this->mail->create_folder($name);
		}else
		{
			if (!in_array($name, $subscribed_names))
			{
				return $this->mail->subscribe($name);
			}
			return true;
		}
		return false;
	}

	function update_password($host, $username, $password)
	{
		$sql = "UPDATE em_accounts SET password='".$this->escape($password).
		"' WHERE username='".$this->escape($username)."' AND host='".$this->escape($host)."'";

		return $this->query($sql);
	}

	function update_folders($account_id, $sent, $trash, $drafts, $spam)
	{
		$account['sent']=$sent;
		$account['drafts']=$drafts;
		$account['spam']=$spam;
		$account['trash']=$trash;
		$account['id']=$account_id;

		return $this->update_row('em_accounts', 'id', $account);
	}

	function get_account($account_id, $alias_id=0)
	{
		$sql = "SELECT a.*, al.name, al.email, al.signature, al.id AS default_alias_id FROM em_accounts a INNER JOIN em_aliases al ON ";
		if(empty($alias_id))
		{
			$sql .= "(al.account_id=a.id AND al.`default`='1') WHERE a.id=".$this->escape($account_id);
		}else
		{
			$sql .= "al.account_id=a.id WHERE al.id=".$this->escape($alias_id);
		}	

		$this->query($sql);
		if ($this->next_record(DB_ASSOC))
		{
			return $this->record;
		}else
		{
			return false;
		}
	}

	function get_account_folders($account_id, $mbroot=false)
	{
		if ($mbroot)
			$sql = "SELECT mbroot, sent, drafts, trash, spam FROM em_accounts WHERE id = ?";
		else
			$sql = "SELECT sent, drafts, trash, spam FROM em_accounts WHERE id = ?";
		$this->query($sql, 'i', array($account_id));

		return ($this->next_record(DB_ASSOC)) ? $this->record : false;
	}


	function delete_account($id)
	{
		global $GO_EVENTS;
		
		$id = $this->escape($id);
		$sql = "DELETE FROM em_accounts WHERE id='$id'";
		if ($this->query($sql))
		{
			$sql = "DELETE FROM em_aliases WHERE account_id='$id'";
			$this->query($sql);
			$sql = "DELETE FROM em_folders WHERE account_id='$id'";
			$this->query($sql);
			$sql = "DELETE FROM em_filters WHERE account_id='$id'";
			$this->query($sql);

			$GO_EVENTS->fire_event('delete_email_account', $id);
		}
	}


	/*
	 gets the subfolder of a folder id. Account id is only usefull for the root
	 level where all folders have parent 0
	 */

	function get_subscribed($account_id, $folder_id=-1)
	{
		$sql = "SELECT id,account_id,name,delimiter,attributes,parent_id,unseen,msgcount FROM em_folders";

		if($account_id>0)
		{
			$sql .= " WHERE account_id='".$this->escape($account_id)."'".
				" AND (subscribed='1' OR name='INBOX')";
		}else {
			$sql .= " WHERE (subscribed='1' OR name='INBOX')";
		}

		if ($folder_id > -1)
		{
			$sql .= " AND parent_id='".$this->escape($folder_id)."'";
		}
		$sql .= " ORDER BY sort_order ASC, name ASC";

		$this->query($sql);
		return $this->num_rows();
	}

	function get_auto_check_folders($account_id)
	{
		$sql = "SELECT * FROM em_folders WHERE account_id='".$this->escape($account_id)."'".
		" AND (auto_check='1' OR name='INBOX') ORDER BY sort_order ASC, name ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_mailboxes($account_id, $folder_id=-1)
	{
		$sql = "SELECT id,account_id,name,subscribed,parent_id,delimiter,attributes,sort_order,msgcount,unseen FROM em_folders WHERE account_id='".$this->escape($account_id)."'";

		if ($folder_id > -1)
		{
			$sql .= " AND parent_id='".$this->escape($folder_id)."'";
		}
		$sql .= " ORDER BY sort_order ASC, name ASC";

		$this->query($sql);
		return $this->num_rows();
	}

	function get_folders($account_id, $folder_id=-1)
	{
		$sql = "SELECT id,account_id,name,subscribed,parent_id,delimiter,attributes,sort_order,msgcount,unseen FROM em_folders";

		if($account_id>0)
		{
			$sql .= " WHERE account_id='".$this->escape($account_id)."'";
			if ($folder_id > -1)
			{
				$sql .= " AND parent_id='".$this->escape($folder_id)."'";
			}
		}elseif ($folder_id > -1)
		{
			$sql .= " WHERE parent_id='".$this->escape($folder_id)."'";
		}
		$sql .= " ORDER BY sort_order ASC, name ASC";

		$this->query($sql);
		return $this->num_rows();
	}

	function get_folders_by_path($account_id, $path_name)
	{
		$sql = "SELECT * FROM em_folders WHERE account_id = ? AND name LIKE ?";
		$this->query($sql, 'is', array($account_id, $path_name.'%'));
	}
	/*
	 function get_all_folders($account_id, $subscribed_only=false)
	 {
	 if ($subscribed_only)
	 {
	 $sql = "SELECT * FROM em_folders WHERE account_id='$account_id' AND ".
	 "subscribed='1' ORDER BY NAME ASC";
	 }else
	 {
	 $sql = "SELECT * FROM em_folders WHERE account_id='$account_id' ORDER ".
	 "BY NAME ASC";
	 }
	 $this->query($sql);
	 return $this->num_rows();
	 }

	 */
	function add_folder($account_id, $name, $parent_id=0, $subscribed=1,
	$delimiter='/', $attributes=0, $sort_order=10)
	{
		$folder['id'] = $this->nextid("em_folders");
		$folder['parent_id']=$parent_id;
		$folder['account_id']=$account_id;
		$folder['subscribed']=$subscribed;
		$folder['name']=$name;
		$folder['attributes']=$attributes;
		$folder['delimiter']=$delimiter;
		$folder['sort_order']=$sort_order;

		$this->insert_row('em_folders',$folder);

		return $folder['id'];
	}
	function rename_folder($account_id, $old_name, $new_name, $new_parent_id=-1)
	{
		$sql = "UPDATE em_folders SET name='".$this->escape($new_name)."'";

		if($new_parent_id > -1)
		{
			$sql .= ",parent_id='".$this->escape($new_parent_id)."' ";
		}

		$sql .= "WHERE name='".$this->escape($old_name)."' AND account_id='".$this->escape($account_id)."'";

		$this->query($sql);

		$saved_folders = $this->get_account_folders($account_id);
		$folders = array();
		foreach($saved_folders as $key => $value) {
			if($value == $old_name)
			{
				$folders[$key] = $new_name;
			}
		}

		if(count($folders))
		{
			$folders['id'] = $account_id;
			$this->update_row('em_accounts', 'id', $folders);
		}

		$sql = "UPDATE em_filters SET folder='".$this->escape($new_name)."' ".
		"WHERE folder='".$this->escape($old_name)."' AND ".
		"account_id='".$this->escape($account_id)."'";

		return $this->query($sql);
	}

	function update_folder($folder_id, $parent_id, $subscribed, $attributes, $sort_order=10)
	{
		$folder['id'] = $folder_id;
		$folder['parent_id']=$parent_id;
		$folder['subscribed']=$subscribed;
		$folder['attributes']=$attributes;
		$folder['sort_order']=$sort_order;

		$this->update_row('em_folders','id', $folder);
	}

	function __update_folder($folder)
	{
		return $this->update_row('em_folders','id', $folder);
	}

	function __add_folder($folder)
	{
		$folder['id'] = $this->nextid("em_folders");
		if ($folder['id'] > 0)
		{
			if ($this->insert_row('em_folders', $folder))
			{
				return $folder['id'];
			}
		}
		return false;
	}

	function delete_folder($account_id, $name)
	{
		$sql = "DELETE FROM em_folders WHERE account_id='".$this->escape($account_id)."' ".
		"AND name='".$this->escape($name)."'";
		$this->query($sql);

		$sql = "DELETE FROM em_filters WHERE account_id='".$this->escape($account_id)."' ".
		"AND folder='".$this->escape($name)."'";
		return $this->query($sql);
	}
	function folder_exists($account_id, $name)
	{
		$sql = "SELECT id FROM em_folders WHERE name='".$this->escape($name)."' AND account_id='".$this->escape($account_id)."'";
		$this->query($sql);
		if ($this->next_record())
		{
			return $this->f("id");
		}else
		{
			return false;
		}
	}

	function get_folder($account_id, $name)
	{
		$sql = "SELECT * FROM em_folders WHERE name='".$this->escape($name)."' AND ".
		"account_id='".$this->escape($account_id)."'";
		$this->query($sql);
		if ($this->next_record(DB_ASSOC))
		{
			return $this->record;
		}else
		{
			return false;
		}
	}

	function get_folder_by_id($folder_id)
	{
		$sql = "SELECT * FROM em_folders WHERE id=".$this->escape($folder_id);
		$this->query($sql);
		if($this->next_record(DB_ASSOC))
		{
			return $this->record;
		}
		return false;
	}

	function get_unseen_recursive($folder_id)
	{
		$email = new email();
		$unseen = 0;
		if($folder = $email->get_folder_by_id($folder_id))
		{
			//echo $folder['name'].'<br>';
			$unseen += $folder['unseen'];
			$email->get_folders($folder['account_id'], $folder['id']);
			while($email->next_record())
			{
				$unseen += $this->get_unseen_recursive($email->f('id'));
			}
		}
		return $unseen;
	}

	function get_account_unseen($account_id)
	{
		$unseen = 0;
		$this->get_subscribed($account_id);
		while($this->next_record())
		{
			$unseen+=$this->f('unseen');
		}
		return $unseen;
	}

	function subscribe($account_id, $name)
	{
		return $this->query("UPDATE em_folders SET subscribed='1' ".
		"WHERE account_id='".$this->escape($account_id)."' AND name='".$this->escape($name)."'");
	}

	function unsubscribe($account_id, $name)
	{
		return $this->query("UPDATE em_folders SET subscribed='0' ".
		"WHERE account_id='".$this->escape($account_id)."' AND name='".$this->escape($name)."'");
	}

	function is_mbroot($folder_name, $delimiter, $mbroot)
	{
		if(substr($mbroot,-1,1)==$delimiter)
		{
			$mbroot = substr($mbroot, 0, -1);
		}
		return $mbroot==$folder_name;
	}

	/*
	 Gets the parent_id from a folder path
	 */
	function get_parent_id($account, $path, $delimiter)
	{
		$mbroot = $account['mbroot'];

		if ($pos = strrpos($path, $delimiter))
		{
			$parent_name = substr($path, 0, $pos);
			if ($parent_folder = $this->get_folder($account['id'], $parent_name))
			{
				if($this->is_mbroot($parent_folder['name'],$delimiter, $account['mbroot']))
				{
					return 0;
				}else
				{
					return $parent_folder['id'];
				}
			}
		}else
		{
			return 0;
		}
		return false;

	}

	function delete_folders($account_id)
	{
		$sql = "DELETE FROM em_folders WHERE account_id='".$this->escape($account_id)."'";
		return $this->query($sql);
	}

	function cache_accounts($user_id, $auto_check_only=false)
	{

		if($this->get_accounts($user_id))
		{
			$email = new email();

			while($this->next_record())
			{
				$email->cache_account_status($this->record,$auto_check_only);
			}
		}
	}

	function cache_account_status($account,$auto_check_only=false)
	{
		$mail = new imap();
		$email = new email();

		if (!$mail->open(
		$account['host'],
		$account['type'],
		$account['port'],
		$account['username'],
		$account['password'],
		'INBOX',
		0,
		$account['use_ssl'],
		$account['novalidate_cert']))
		{
			return false;
		}

		if($auto_check_only)
		{
			$this->get_auto_check_folders($account['id']);
		}else {
			$this->get_subscribed($account['id']);
		}

		while($this->next_record())
		{
			$folder['id'] = $this->f('id');
			if($status = $mail->status($this->f('name'), SA_UNSEEN+SA_MESSAGES))
			{
				if($status->messages!=$this->f('msgcount') || $status->unseen!=$this->f('unseen'))
				{
					$folder['msgcount'] = $status->messages;
					$folder['unseen'] = $status->unseen;
					$email->__update_folder($folder);
				}
			}

		}
		$mail->close();
	}

	function cache_folder_status($imap, $account_id, $mailbox)
	{
		$cached_folder = $this->get_folder($account_id, $mailbox);
			
		if($status = $imap->status($mailbox, SA_UNSEEN+SA_MESSAGES))
		{
			$folder['id']=$cached_folder['id'];
			$folder['msgcount'] = $status->messages;
			$folder['unseen'] = $status->unseen;

			if($status->messages!=$cached_folder['msgcount'] || $status->unseen!=$cached_folder['unseen'])
			{
				$this->__update_folder($folder);
			}
			return $folder;
		}
		return false;
	}


	function get_total_unseen($user_id)
	{
		$sql = "SELECT SUM(unseen) FROM em_folders INNER JOIN em_accounts ON em_folders.account_id=em_accounts.id WHERE user_id='".$this->escape($user_id)."'";
		$this->query($sql);
		$this->next_record();
		return $this->f(0);
	}

	function _synchronize_folders($account, $mailboxes, $subscribed)
	{
		$mail = new imap();

		$mailbox_names = array();

		$subscribed_names = array();
		while($mailbox = array_shift($subscribed))
		{
			$subscribed_names[]=$mailbox['name'];
		}

		foreach($mailboxes as $mailbox)
		{
			$mailbox_names[] = $mailbox['name'];
			$folder['account_id'] = $account['id'];
			$folder['parent_id'] = $this->get_parent_id($account, $mailbox['name'], $mailbox['delimiter']);
			$folder['attributes'] = $mailbox['attributes'];
			$folder['name'] = $mailbox['name'];

			$folder['subscribed']=in_array($mailbox['name'], $subscribed_names) ? '1' : '0';
			//$folder['subscribed']='1';
			$folder['delimiter'] = $mailbox['delimiter'];
			/*if($status = $mail->status($mailbox['name'], SA_UNSEEN+SA_MESSAGES))
			 {
				$folder['msgcount'] = $status->messages;
				$folder['unseen'] = $status->unseen;
				}*/

			$unenc_name = $mail->utf7_imap_decode($mailbox['name']);

			if($unenc_name == 'INBOX')
			{
				$folder['sort_order'] = 0;
			}elseif($unenc_name == $account['sent'])
			{
				$folder['sort_order'] = 1;
			}elseif($unenc_name == $account['drafts'])
			{
				$folder['sort_order'] = 2;
			}elseif($unenc_name == $account['trash'])
			{
				$folder['sort_order'] = 3;
			}elseif($unenc_name == $account['spam'])
			{
				$folder['sort_order'] = 4;
			}else
			{
				$folder['sort_order'] = 10;
			}

			$folder = $folder;

			if ($existing_folder = $this->get_folder($account['id'],$mailbox['name']))
			{
				$folder['id'] = $existing_folder['id'];
				$this->__update_folder($folder);
			}else
			{
				$folder['id'] = $this->__add_folder($folder);
			}
		}

		//Courier doesn't return INBOX
		if(!in_array('INBOX', $mailbox_names))
		{
			$mailbox_names[] = 'INBOX';
			$folder['name']='INBOX';
			$folder['account_id'] = $account['id'];
			$folder['sort_order']=0;
			$folder['subscribed']=true;
			$folder['delimiter'] = '';
			$folder['parent_id']=0;
			$folder['attributes']='';
			/*if($status = $mail->status('INBOX', SA_UNSEEN+SA_MESSAGES))
			 {
				$folder['msgcount'] = $status->messages;
				$folder['unseen'] = $status->unseen;
				}*/

			if ($existing_folder = $this->get_folder($account['id'],'INBOX'))
			{
				$folder['id'] = $existing_folder['id'];
				$this->__update_folder($folder);
			}else
			{
				$folder['id'] = $this->__add_folder($folder);
			}
		}

		//$mail->close();

		/*
		 get all the Group-Office folders and delete the folders that no longer
		 exist on the IMAP server
		 */

		$this->get_folders($account['id']);
		$emailobj = new email();
		while ($this->next_record())
		{
			if (!in_array($this->f('name'), $mailbox_names))
			{
				$emailobj->delete_folder($account['id'], $this->f('name'));
			}
		}
	}


	function synchronize_folders($account, $mail=false)
	{
		if(!$mail)
		{
			$mail = new imap();

			if (!$mail->open(
			$account['host'],
			$account['type'],
			$account['port'],
			$account['username'],
			$account['password'],
			'INBOX',
			0,
			$account['use_ssl'],
			$account['novalidate_cert']))
			{
				return false;
			}
			$close_connection = true;
		}
			

		$subscribed =  $mail->get_subscribed($account['mbroot']);		
		$mailboxes =  $mail->get_mailboxes($account['mbroot']);

		$this->_synchronize_folders($account, $mailboxes, $subscribed);

		if(isset($close_connection))
		{
			$mail->close();
		}
	}



	function get_filters($account_id)
	{
		$sql = "SELECT * FROM em_filters WHERE account_id='".$this->escape($account_id)."' ".
		" ORDER BY priority ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function add_filter($filter)
	{
		$filter['id'] = $this->nextid("em_filters");
		if ($filter['id'] > 0 && $this->insert_row('em_filters',$filter))
		{
			return $filter['id'];
		}else
		{
			return false;
		}
	}

	function get_filter($filter_id)
	{
		$sql = "SELECT * FROM em_filters WHERE id='$filter_id'";
		$this->query($sql);
		if ($this->next_record(DB_ASSOC))
		{
			return $this->record;
		}else
		{
			return false;
		}
	}

	function update_filter($filter)
	{
		return $this->update_row('em_filters','id', $filter);

	}

	function delete_filter($id)
	{
		$sql = "DELETE FROM em_filters WHERE id='$id'";
		$this->query($sql);
	}

	function move_up($move_up_id, $move_dn_id, $move_up_pr, $move_dn_pr)
	{
		if ($move_up_pr == $move_dn_pr)
		$move_up_pr++;

		$sql = "UPDATE em_filters SET priority='".$this->escape($move_up_pr)."' WHERE id='".$this->escape($move_up_id)."'";
		$this->query($sql);

		$sql = "UPDATE em_filters SET priority='".$this->escape($move_dn_pr)."' WHERE id='".$this->escape($move_dn_id)."'";
		$this->query($sql);
	}



	function register_attachment($tmp_file, $filename, $filesize, $filemime='',
	$disposition='attachment', $content_id='')
	{
		global $GO_CONFIG;

		$filename = ($filename);
		$tmp_file = ($tmp_file);


		$attachment['file_name'] = $filename;
		$attachment['tmp_file'] =  $tmp_file;
		$attachment['file_size'] = $filesize;
		$attachment['file_mime'] = $filemime;
		$attachment['disposition'] = $disposition;
		$attachment['content_id'] = $content_id;

		$_SESSION['attach_array'][] = $attachment;
	}

	function get_zip_of_attachments($account_id, $uid, $mailbox='INBOX')
	{
		global $GO_CONFIG;

		$tmpdir = $GO_CONFIG->tmpdir.uniqid(time());
		if(!mkdir($tmpdir))
		{
			return false;
		}

		$account = $this->get_account($account_id);

		$imap = new imap();
		if(!$imap->open($account['host'],
		$account['type'],
		$account['port'],
		$account['username'],
		$account['password'],
		$mailbox,
		null,
		$account['use_ssl'],
		$account['novalidate_cert']))
		{
			return false;
		}

		if(!$message = $imap->get_message($uid))
		{
			return false;
		}

		for ($i = 0; $i < count($message['parts']); $i ++) {
			/*if ((eregi("ATTACHMENT", $message['parts'][$i]["disposition"])  ||
			 eregi("INLINE", $message['parts'][$i]["disposition"]) &&
			 !empty($message['parts'][$i]["name"])) ||
			 !empty($message['parts'][$i]["name"])){*/

			if (
			(
			stripos($message['parts'][$i]["disposition"],"ATTACHMENT")!==false  ||
			($message['parts'][$i]["name"] != '' && empty($message['parts'][$i]["id"])
			)
			&& !($message['parts'][$i]['type']=='APPLEDOUBLE' && $message['parts'][$i]['mime']== 'application/APPLEFILE')
			)
			){

				$filename = $tmpdir.'/'.$message['parts'][$i]["name"];
				$x=0;
				while(file_exists($filename))
				{
					$x++;
					$filename = File::strip_extension($filename).' ('.$x.').'.File::get_extension($filename);
				}

				if(!$fp = fopen($filename,'w+'))
				{
					return false;
				}
				if(!fwrite($fp, $imap->view_part($uid, $message['parts'][$i]["number"], $message['parts'][$i]["transfer"])))
				{
					return false;
				}
				fclose($fp);
			}
		}
		chdir($tmpdir);
		exec($GO_CONFIG->cmd_zip.' -r "attachments.zip" *.*');
		$data = file_get_contents(	$tmpdir.'/attachments.zip');
		exec('rm -Rf '.$tmpdir);
		return $data;
	}


	function get_default_account_id($user_id)
	{
		$sql = "SELECT id FROM em_accounts WHERE user_id='".$this->escape($user_id)."' AND standard=1";

		$this->query($sql);

		if ($this->next_record()) {

			return $this->f("id");

		} else {
			return false;
		}
	}

	function __on_delete_link($id, $link_type)
	{

		if($link_type==9)
		{
			$this->delete_linked_message($id);
		}

		/* {ON_DELETE_LINK_FUNCTION} */
	}

	function user_delete($user)
	{
		go_debug($user);
		$email = new email();
		$del = new email();
		$email->get_accounts($user['id']);
		while ($email->next_record())
		{
			$del->delete_account($email->f("id"));
		}
	}

	function cache_message($message_id)
	{
		global $GO_MODULES, $GO_CONFIG, $GO_LANGUAGE;
		require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
		$search = new search();

		require($GO_LANGUAGE->get_language_file('email'));

		$sql  = "SELECT * FROM em_links WHERE link_id=?";
		$this->query($sql,'i', $message_id);


		$record = $this->next_record();
		if($record)
		{
			$cache['id']=$this->f('link_id');
			$cache['user_id']=$this->f('user_id');
			$cache['module']='email';
			$cache['name'] = htmlspecialchars($this->f('subject'), ENT_QUOTES, 'utf-8');
			$cache['link_type']=9;
			$cache['description']=$lang['email']['from'].': '.$this->f('from');
			$cache['type']=$lang['link_type'][9];
			$cache['keywords']=$search->record_to_keywords($this->record).','.$cache['type'];
			$cache['mtime']=$this->f('time');
			$cache['acl_id']=$this->f('acl_id');

			$search->cache_search_result($cache);
		}
	}

	/**
	 * When a global search action is performed this function will be called for each module
	 *
	 * @param int $last_sync_time The time this function was called last
	 */
	public static function build_search_index()
	{
		$email2 = new email();
		$sql = "SELECT link_id FROM em_links";
		$email2->query($sql);
		$email = new email();
		while($record = $email2->next_record())
		{
			$email->cache_message($record['link_id']);
		}
		/* {ON_BUILD_SEARCH_INDEX_FUNCTION} */
	}
	
	public static function check_database(){
		
		global $GO_CONFIG, $GO_LINKS;
		
		require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
		$search = new search();
			
		$sql = "SELECT link_id FROM em_links";
		$email = new email();
		$email2 = new email();
		$email->query($sql);
		while($record = $email->next_record())
		{
			$search->global_search(1, '', 0, 1, 'name','ASC', array(), $record['link_id'], 9,-1);
			$sr = $search->next_record();
			if($sr)
			{
				$record['acl_id']=$sr['acl_id'];
				$email2->update_row('em_links', 'link_id', $record);
			}
		}
		
		$email->build_search_index();
	}
	
		/**
	 * Add a Alias
	 *
	 * @param Array $alias Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */
	function add_alias($alias)
	{
		$alias['id']=$this->nextid('em_aliases');
		if($this->insert_row('em_aliases', $alias))
		{
			return $alias['id'];
		}
		return false;
	}
	/**
	 * Update a Alias
	 *
	 * @param Array $alias Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */
	function update_alias($alias)
	{
		$r = $this->update_row('em_aliases', 'id', $alias);
		return $r;
	}
	/**
	 * Delete a Alias
	 *
	 * @param Int $alias_id ID of the alias
	 *
	 * @access public
	 * @return bool True on success
	 */
	function delete_alias($alias_id)
	{
		return $this->query("DELETE FROM em_aliases WHERE id=?", 'i', $alias_id);
	}
	/**
	 * Gets a Alias record
	 *
	 * @param Int $alias_id ID of the alias
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_alias($alias_id)
	{
		$this->query("SELECT * FROM em_aliases WHERE id=?", 'i', $alias_id);
		return $this->next_record();		
	}
	/**
	 * Gets a Alias record by the name field
	 *
	 * @param String $name Name of the alias
	 *
	 * @access public
	 * @return Array Record properties
	 */
	function get_alias_by_name($name)
	{
		$this->query("SELECT * FROM em_aliases WHERE name=?", 's', $name);
		return $this->next_record();		
	}
	/**
	 * Gets all Aliases
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_aliases($account_id, $all=false)
	{
		$sql = "SELECT * FROM em_aliases WHERE account_id=".$this->escape($account_id);
		
		if(!$all)
		{
			$sql .= " AND `default`!='1'";
		}
		
		$sql .= " ORDER BY name ASC, email ASC";
			
		$this->query($sql);
		return $this->num_rows();
	}
	
/**
	 * Gets all Aliases
	 *
	 * @param Int $start First record of the total record set to return
	 * @param Int $offset Number of records to return
	 * @param String $sortfield The field to sort on
	 * @param String $sortorder The sort order
	 *
	 * @access public
	 * @return Int Number of records found
	 */
	function get_all_aliases($user_id)
	{
		$sql = "SELECT a.* FROM em_aliases a INNER JOIN em_accounts e ON e.id=a.account_id WHERE e.user_id=".$this->escape($user_id);		
		$sql .= " ORDER BY `standard` ASC, `default` DESC, name ASC";
			
		$this->query($sql);
		return $this->num_rows();
	}
	
	
/* {CLASSFUNCTIONS} */

}

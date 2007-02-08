<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

class email extends db
{
	var $last_error;
	var $mail;

	function get_accounts($user_id=0, $start=0, $offset=0)
	{
		$sql = "SELECT * FROM emAccounts";

		if($user_id > 0)
		{
			$sql .= " WHERE user_id='$user_id'";
		}
		$sql .= " ORDER BY email ASC, standard DESC";

		$this->query($sql);
		$count =  $this->num_rows();

		if($offset>0)
		{
			$sql .= "LIMIT $start, $offset";
			$this->query($sql);
		}
		return $count;
	}


	function link_message($message)
	{
		global $GO_LINKS;

		$message['link_id']=$GO_LINKS->get_link_id();
		$this->insert_row('em_links',$message);
		return $message['link_id'];
	}

	function delete_linked_message($link_id)
	{
		$sql ="DELETE FROM em_links WHERE link_id=$link_id";
		return $this->query($sql);
	}


	function get_linked_messages($links, $sort_index='time', $sort_order='ASC',$start=0,$offset=0)
	{
		if(!count($links))
		{
			return 0;
		}
		$sql = "SELECT * FROM em_links WHERE link_id IN (".implode(',',$links).") ORDER BY `$sort_index` $sort_order";
		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT $start,$offset";
			$this->query($sql);
		}

		return $count;
	}

	function set_sorting($user_id, $sort_field, $sort_order)
	{
		$sql = "UPDATE em_settings SET sort_field='$sort_field' , sort_order='$sort_order' WHERE user_id='$user_id'";
		return $this->query($sql);
	}

	function save_send_format($user_id, $send_format)
	{
		$sql = "UPDATE em_settings SET send_format='$send_format'  WHERE user_id='$user_id'";
		return $this->query($sql);
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
		$this->query("SELECT * FROM em_settings WHERE user_id='$user_id'");
		if ($this->next_record())
		{
			return $this->Record;
		}else
		{
			$this->query("INSERT INTO em_settings (user_id, send_format, add_recievers, add_senders, request_notification, charset) VALUES ('$user_id', 'text/HTML', '0', '0', '0', 'UTF-8')");
			return $this->get_settings($user_id);
		}
	}

	function add_account($user_id, $type, $host, $port, $use_ssl, $novalidate_cert, $mbroot, $username, $password, $name, $email, $signature, $auto_check=0)
	{
		global $GO_CONFIG, $GO_LANGUAGE;

		require_once($GO_CONFIG->class_path."mail/imap.class.inc");
		require($GO_LANGUAGE->get_language_file('email'));
		$this->mail= new imap();

		if (!$this->mail->open($host, $type, $port, $username, $password, "INBOX", 0, $use_ssl, $novalidate_cert))
		{
			$this->last_error = "<p class=\"Error\">".imap_last_error()."</p>";
			return false;
		}else
		{
			if (!$account['mbroot'] = $this->mail->check_mbroot($mbroot))
			{
				$account['mbroot'] = '';
			}
			$account['spamtag']='***SPAM***';
			$account['trash'] = '';
			$account['sent'] = '';
			$account['drafts'] = '';
			$account['spam'] = '';

			if ($type=='imap')
			{
				$mailboxes =  $this->mail->get_mailboxes($mbroot);
				$subscribed =  $this->mail->get_subscribed($mbroot);

				$mailbox_names = array();
				while($mailbox = array_shift($mailboxes))
				{
					$mailbox_names[]=$mailbox['name'];
				}

				$subscribed_names = array();
				while($mailbox = array_shift($subscribed))
				{
					$subscribed_names[]=$mailbox['name'];
				}

				if($this->_add_folder($account['mbroot'].$ml_trash, $mailbox_names, $subscribed_names))
				{
					$account['trash'] = $account['mbroot'].utf7_imap_encode($ml_trash);
				}elseif($account['mbroot'] = $this->mail->check_mbroot($mailbox_names[0]))
				{
					if($this->_add_folder($account['mbroot'].utf7_imap_encode($ml_trash), $mailbox_names, $subscribed_names))
					{
						$account['trash'] = $account['mbroot'].utf7_imap_encode($ml_trash);
					}
				}

				if($this->_add_folder($account['mbroot'].utf7_imap_encode($ml_sent_items), $mailbox_names, $subscribed_names))
				{
					$account['sent'] = $account['mbroot'].utf7_imap_encode($ml_sent_items);
				}

				if($this->_add_folder($account['mbroot'].utf7_imap_encode($ml_drafts), $mailbox_names, $subscribed_names))
				{
					$account['drafts'] = $account['mbroot'].utf7_imap_encode($ml_drafts);
				}

				if($this->_add_folder($account['mbroot'].utf7_imap_encode($ml_spam), $mailbox_names, $subscribed_names))
				{
					$account['spam']= $account['mbroot'].utf7_imap_encode($ml_spam);
				}
			}
			$this->mail->close();

			if ($this->get_accounts($user_id) == 0)
			{
				$account['standard']="1";
			}else
			{
				$account['standard']="0";
			}
			$account['id'] = $this->nextid("emAccounts");
			if (!$account['id'])
			{
				$this->last_error = "<p class=\"Error\">".$strSaveError."</p>";
				return false;
			}else
			{
				$account['type']=$type;
				$account['user_id']=$user_id;
				$account['host']=$host;
				$account['port']=$port;
				$account['use_ssl']=$use_ssl;
				$account['novalidate_cert']=$novalidate_cert;
				$account['username']=$username;
				$account['password']=$password;
				$account['name']=$name;
				$account['email']=$email;
				$account['signature']=$signature;
				$account['auto_check']=$auto_check;


				if(!$this->insert_row('emAccounts', $account))
				{
					$this->last_error = "<p class=\"Error\">".$strSaveError."</p>";
					return false;
				}else
				{
					return $account['id'];
				}
			}
		}
	}

	function disable_auto_check($account_id)
	{
		return $this->query("UPDATE emAccounts SET auto_check='0' WHERE id='$account_id'");
	}

	function _update_account($account)
	{
		return $this->update_row('emAccounts', 'id', $account);
	}

	function update_account($account_id, $type, $host, $port, $use_ssl, $novalidate_cert, $mbroot, $username, $password, $name, $email, $signature, $auto_check=0, $user_id=0)
	{
		global $GO_CONFIG;

		require_once($GO_CONFIG->class_path."mail/imap.class.inc");
		$this->mail= new imap();

		if ($this->mail->open($host, $type, $port, $username, $password, "INBOX", 0, $use_ssl, $novalidate_cert))
		{
			if (!$mbroot = $this->mail->check_mbroot($mbroot))
			{
				$mbroot = '';
			}
			$sql  = "UPDATE emAccounts SET type='$type', host='$host', port='$port'".
			", use_ssl='$use_ssl', novalidate_cert='$novalidate_cert'".
			", mbroot='$mbroot', username='$username', password='".$password."',".
			" name='$name', email='$email'".
			", signature='$signature', auto_check='$auto_check'";
			if($user_id>0)
			{
				$sql .=", user_id=$user_id";
			}

			$sql .=" WHERE id='$account_id'";

			if ($this->query($sql))
			{
				$this->mail->close();
			}
			return true;
		}
		return false;
	}

	function _add_folder($name, $mailbox_names, $subscribed_names)
	{
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
		$sql = "UPDATE emAccounts SET password='".$password.
		"' WHERE username='$username' AND host='$host'";
		return $this->query($sql);
	}

	function update_folders($account_id, $sent, $trash, $drafts, $spam)
	{
		$sql = "UPDATE emAccounts SET sent='$sent', ".
		"trash='$trash',drafts='$drafts', spam='$spam'  WHERE id='$account_id'";
		return $this->query($sql);
	}

	function get_account($id = 0)
	{
		if ($id == 0)
		{
			$sql = "SELECT * FROM emAccounts WHERE standard='1' AND user_id='".
			$_SESSION['GO_SESSION']['user_id']."'";
		}else
		{
			$sql = "SELECT * FROM emAccounts WHERE id='$id'";
		}

		$this->query($sql);
		if ($this->next_record())
		{
			return $this->Record;
		}else
		{
			return false;
		}
	}

	function is_default_account($id)
	{
		$sql = "SELECT * FROM emAccounts WHERE id='$id' AND standard='1'";
		$this->query($sql);
		if ($this->num_rows() == 1)
		{
			return true;
		}else
		{
			return false;
		}
	}

	function delete_account($user_id, $id)
	{
		$default = $this->is_default_account($id);

		$sql = "DELETE FROM emAccounts WHERE id='$id'";
		if ($this->query($sql))
		{
			$sql = "DELETE FROM emFolders WHERE account_id='$id'";
			$this->query($sql);
			$sql = "DELETE FROM emFilters WHERE account_id='$id'";
			$this->query($sql);
		}
		if ($default)
		{
			$this->get_accounts($user_id);
			$this->next_record();
			$this->set_as_default($this->f("id"), $user_id);
		}
		return true;
	}

	function set_as_default($account_id, $user_id)
	{
		$sql = "UPDATE emAccounts SET standard='0' WHERE user_id='$user_id'".
		"AND standard='1'";
		$this->query($sql);
		$sql = "UPDATE emAccounts SET standard='1' WHERE id='$account_id'";
		$this->query($sql);
	}
	/*
	gets the subfolder of a folder id. Account id is only usefull for the root
	level where all folders have parent 0
	*/

	function get_subscribed($account_id, $folder_id=-1)
	{
		$sql = "SELECT * FROM emFolders WHERE account_id='$account_id'".
		" AND (subscribed='1' OR name='INBOX')";

		if ($folder_id > -1)
		{
			$sql .= " AND parent_id='$folder_id'";
		}
		$sql .= " ORDER BY sort_order ASC, name ASC";

		$this->query($sql);
		return $this->num_rows();
	}

	function get_mailboxes($account_id, $folder_id=-1)
	{
		$sql = "SELECT * FROM emFolders WHERE account_id='$account_id'";

		if ($folder_id > -1)
		{
			$sql .= " AND parent_id='$folder_id'";
		}
		$sql .= " ORDER BY sort_order ASC, name ASC";

		$this->query($sql);
		return $this->num_rows();
	}

	function get_folders($account_id, $folder_id=-1)
	{
		$sql = "SELECT * FROM emFolders WHERE account_id='$account_id'";

		if ($folder_id > -1)
		{
			$sql .= " AND parent_id='$folder_id' ";
		}
		$sql .= "ORDER BY sort_order ASC, name ASC";

		$this->query($sql);
		return $this->num_rows();
	}
	/*
	function get_all_folders($account_id, $subscribed_only=false)
	{
	if ($subscribed_only)
	{
	$sql = "SELECT * FROM emFolders WHERE account_id='$account_id' AND ".
	"subscribed='1' ORDER BY NAME ASC";
	}else
	{
	$sql = "SELECT * FROM emFolders WHERE account_id='$account_id' ORDER ".
	"BY NAME ASC";
	}
	$this->query($sql);
	return $this->num_rows();
	}

	*/
	function add_folder($account_id, $name, $parent_id=0, $subscribed=1,
	$delimiter='/', $attributes=0, $sort_order=10)
	{
		$next_id = $this->nextid("emFolders");
		if ($next_id > 0)
		{
			$sql = "INSERT INTO emFolders (id, parent_id, account_id, subscribed, ".
			"name, attributes, delimiter, sort_order) VALUES ('$next_id', '$parent_id', ".
			"'$account_id', '$subscribed', '$name', ".
			"'$attributes', '$delimiter', '$sort_order')";
			if ($this->query($sql))
			{
				return $next_id;
			}
		}
		return false;
	}
	function rename_folder($account_id, $old_name, $new_name)
	{
		$sql = "UPDATE emFolders SET name='$new_name' WHERE".
		" name='".smart_addslashes($old_name)."' AND ".
		"account_id='$account_id'";

		$this->query($sql);
		$sql = "UPDATE emFilters SET folder='$new_name' ".
		"WHERE folder='$old_name' AND ".
		"account_id='$account_id'";
		$this->query($sql);

	}

	function update_folder($folder_id, $parent_id, $subscribed, $attributes, $sort_order=10)
	{
		$sql = "UPDATE emFolders SET subscribed='$subscribed', ".
		"parent_id='$parent_id', attributes='$attributes', sort_order='$sort_order' ".
		"WHERE id='$folder_id'";

		$this->query($sql);
	}

	function __update_folder($folder)
	{
		return $this->update_row('emFolders','id', $folder);
	}

	function __add_folder($folder)
	{
		$folder['id'] = $this->nextid("emFolders");
		if ($folder['id'] > 0)
		{
			if ($this->insert_row('emFolders', $folder))
			{
				return $folder['id'];
			}
		}
		return false;
	}

	function delete_folder($account_id, $name)
	{
		$sql = "DELETE FROM emFolders WHERE account_id='$account_id' ".
		"AND name='$name'";
		$this->query($sql);

		$sql = "DELETE FROM emFilters WHERE account_id='$account_id' ".
		"AND folder='$name'";
		$this->query($sql);
	}
	function folder_exists($account_id, $name)
	{
		$sql = "SELECT id FROM emFolders WHERE name='$name' AND ">
		"account_id='$account_id'";
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
		$sql = "SELECT * FROM emFolders WHERE name='$name' AND ".
		"account_id='$account_id'";
		$this->query($sql);
		if ($this->next_record(MYSQL_ASSOC))
		{
			return $this->Record;
		}else
		{
			return false;
		}
	}

	function get_folder_by_id($folder_id)
	{
		$sql = "SELECT * FROM emFolders WHERE id=$folder_id";
		$this->query($sql);
		if($this->next_record(MYSQL_ASSOC))
		{
			return $this->Record;
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
		return $this->query("UPDATE emFolders SET subscribed='1' ".
		"WHERE account_id='$account_id' AND name='$name'");
	}

	function unsubscribe($account_id, $name)
	{
		return $this->query("UPDATE emFolders SET subscribed='0' ".
		"WHERE account_id='$account_id' AND name='$name'");
	}

	/*
	Gets the parent_id from a folder path
	*/
	function get_parent_id($account_id, $path, $delimiter)
	{
		if ($pos = strrpos($path, $delimiter))
		{
			$parent_name = substr($path, 0, $pos);
			if ($parent_folder = $this->get_folder($account_id, $parent_name))
			{
				return $parent_folder['id'];
			}
		}else
		{
			return 0;
		}
		return false;

	}

	function delete_folders($account_id)
	{
		$sql = "DELETE FROM emFolders WHERE account_id='$account_id'";
		return $this->query($sql);
	}

	function cache_accounts($user_id)
	{

		if($this->get_accounts($user_id))
		{
			$email = new email();

			while($this->next_record())
			{
				$email->cache_account_status($this->Record);
			}
		}
	}

	function cache_account_status($account)
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

		$this->get_folders($account['id']);

		while($this->next_record())
		{
			$folder['id'] = $this->f('id');
			if($status = $mail->status($this->f('name'), SA_ALL))
			{
				$folder['msgcount'] = $status->messages;
				$folder['unseen'] = $status->unseen;
			}
			$email->__update_folder($folder);
		}
		$mail->close();
	}


	function synchronize_folders($account)
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

		if(!$subscribed =  $mail->get_subscribed('', true))
		{
			return false;
		}


		if(!$mailboxes =  $mail->get_mailboxes(''))
		{
			return false;
		}

		$mailbox_names = array();

		foreach($mailboxes as $mailbox)
		{
			$mailbox_names[] = $mailbox['name'];
			$folder['account_id'] = $account['id'];
			$folder['parent_id'] = $this->get_parent_id($account['id'], addslashes($mailbox['name']), 	$mailbox['delimiter']);
			$folder['attributes'] = $mailbox['attributes'];
			$folder['name'] = $mailbox['name'];

			$folder['subscribed']=in_array($mailbox['name'], $subscribed);
			$folder['delimiter'] = $mailbox['delimiter'];
			if($status = $mail->status($mailbox['name'], SA_ALL))
			{
				$folder['msgcount'] = $status->messages;
				$folder['unseen'] = $status->unseen;
			}

			if($mailbox['name'] == 'INBOX')
			{
				$folder['sort_order'] = 0;
			}elseif($mailbox['name'] == $account['sent'])
			{
				$folder['sort_order'] = 1;
			}elseif($mailbox['name'] == $account['drafts'])
			{
				$folder['sort_order'] = 2;
			}elseif($mailbox['name'] == $account['trash'])
			{
				$folder['sort_order'] = 3;
			}elseif($mailbox['name'] == $account['spam'])
			{
				$folder['sort_order'] = 4;
			}else
			{
				$folder['sort_order'] = 10;
			}

			$folder = array_map('addslashes', $folder);

			if ($existing_folder = $this->get_folder($account['id'],addslashes($mailbox['name'])))
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
			if($status = $mail->status('INBOX', SA_ALL))
			{
				$folder['msgcount'] = $status->messages;
				$folder['unseen'] = $status->unseen;
			}

			if ($existing_folder = $this->get_folder($account['id'],addslashes('INBOX')))
			{
				$folder['id'] = $existing_folder['id'];
				$this->__update_folder($folder);
			}else
			{
				$folder['id'] = $this->__add_folder($folder);
			}
		}

		$mail->close();

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
				$emailobj->delete_folder($account['id'], addslashes($this->f('name')));
			}
		}
	}



	function get_filters($account_id)
	{
		$sql = "SELECT * FROM emFilters WHERE account_id='$account_id' ".
		"ORDER BY priority DESC";
		$this->query($sql);
		return $this->num_rows();
	}

	function add_filter($account_id, $field, $keyword, $folder, $mark_as_read)
	{
		$next_id = $this->nextid("emFilters");
		if ($next_id > 0)
		{
			$sql = "INSERT INTO emFilters (id, account_id, field, keyword, ".
			"folder, priority, mark_as_read) VALUES ('$next_id','$account_id','$field',".
			"'$keyword','$folder','$next_id', '$mark_as_read')";

			return $this->query($sql);
		}else
		{
			return false;
		}
	}

	function get_filter($filter_id)
	{
		$sql = "SELECT * FROM emFilters WHERE id='$filter_id'";
		$this->query($sql);
		if ($this->next_record())
		{
			return $this->Record;
		}else
		{
			return false;
		}
	}

	function update_filter($filter_id, $field, $keyword, $folder, $mark_as_read)
	{
		$sql = "UPDATE emFilters SET field='$field', keyword='$keyword', folder='$folder', mark_as_read='$mark_as_read' WHERE id='$filter_id'";
		$this->query($sql);
	}

	function delete_filter($id)
	{
		$sql = "DELETE FROM emFilters WHERE id='$id'";
		$this->query($sql);
	}

	function move_up($move_up_id, $move_dn_id, $move_up_pr, $move_dn_pr)
	{
		if ($move_up_pr == $move_dn_pr)
		$move_up_pr++;

		$sql = "UPDATE emFilters SET priority='$move_up_pr' WHERE id='$move_up_id'";
		$this->query($sql);

		$sql = "UPDATE emFilters SET priority='$move_dn_pr' WHERE id='$move_dn_id'";
		$this->query($sql);
	}



	function register_attachment($tmp_file, $filename, $filesize, $filemime='',
	$disposition='attachment', $content_id='')
	{
		global $GO_CONFIG;

		$filename = smart_addslashes($filename);
		$tmp_file = smart_addslashes($tmp_file);


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
		0,
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
			if ((eregi("ATTACHMENT", $message['parts'][$i]["disposition"])  ||
			eregi("INLINE", $message['parts'][$i]["disposition"]) &&
			!empty($message['parts'][$i]["name"])) ||
			!empty($message['parts'][$i]["name"])){

				$filename = $tmpdir.'/'.$message['parts'][$i]["name"];
				$x=0;
				while(file_exists($filename))
				{
					$x++;
					$filename .= strip_extension($filename).' ('.$x.').'.get_extension($filename);
				}

				if(!$fp = fopen($tmpdir.'/'.$message['parts'][$i]["name"],'w+'))
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
		$sql = "SELECT id FROM emAccounts WHERE user_id='$user_id' AND standard=1";

		$this->query($sql);

		if ($this->next_record()) {

			return $this->f("id");

		} else {
			return false;
		}
	}

	function __on_user_delete($user_id)
	{
		$del = new email;
		$this->get_accounts($user_id);
		while ($this->next_record())
		{
			$del->delete_account($user_id,$this->f("id"));
		}
		$this->query("DELETE FROM em_settings WHERE user_id='$user_id'");
	}
}

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

class mailings extends db
{	
	public function __on_load_listeners($events){
		$events->add_listener('user_delete', __FILE__, 'mailings', 'user_delete');
	}


	public function get_message_for_client($id, $path, $part_number, $create_temporary_attachment_files=false,$create_temporary_inline_attachment_files=false){
		global $GO_CONFIG, $GO_MODULES;
		require_once ($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
		$email = new email();

		if(!empty($path))
		{
			$data = file_get_contents($GO_CONFIG->file_storage_path.$path);
			$inline_url = $GO_MODULES->modules['mailings']['url'].'mimepart.php?path='.urlencode($path);
			$response['path']=$path;
		}elseif($id>0)
		{
			$linked_message = $email->get_linked_message($id);
			$data = file_get_contents($GO_CONFIG->file_storage_path.$linked_message['path']);

			$inline_url = $GO_MODULES->modules['mailings']['url'].'mimepart.php?path='.urlencode($linked_message['path']);

			$response['path']=$linked_message['path'];

		}

		require_once($GO_CONFIG->class_path.'mail/Go2Mime.class.inc.php');
		$go2mime = new Go2Mime();

		$response['blocked_images']=0;

		$response= array_merge($response, $go2mime->mime2GO($data, $inline_url,$create_temporary_attachment_files, $create_temporary_inline_attachment_files, $part_number));

		$response['attachments']=$go2mime->remove_inline_images($response['attachments']);
		return $response;
	}
	
	function get_authorized_mailing_groups($auth_type='read', $user_id, $start=0, $offset=0, $sort='name', $dir='ASC')
	{
		$sql = "SELECT DISTINCT g.* FROM ml_mailing_groups g ".
		"INNER JOIN go_acl a ON ";
		
		if($auth_type=='write')
		{
		 $sql .= "(g.acl_id = a.acl_id AND a.level>1) ";
		}else
		{
			$sql .= "g.acl_id = a.acl_id ";
		}
				
		$sql .= "LEFT JOIN go_users_groups ug ON a.group_id = ug.group_id ".
			"WHERE a.user_id=".intval($user_id)." OR ug.user_id=".intval($user_id)." ".						
			" ORDER BY g.".$this->escape($sort." ".$dir);
		
		if($offset > 0 )
		{
			$sql .= " LIMIT ".intval($start).",".intval($offset);
			$this->query($sql);
			return $this->num_rows();
		}else {
			$this->query($sql);
			return $this->num_rows();
		}
	}
	

	
	function get_mailing_group($mailing_group_id)
	{		
		$sql = "SELECT * FROM ml_mailing_groups WHERE id='".$this->escape($mailing_group_id)."'";
		$this->query($sql);
		if($this->next_record())
		{
			return $this->record;
		}
		return false;
	}
	
	function add_mailing_group($mailing)
	{
		$mailing['id'] = $this->nextid("ml_mailing_groups");

		if ($mailing['id'] > 0)
		{		

			if($this->insert_row("ml_mailing_groups", $mailing))
			{
				return $mailing['id'];
			}
		}
		return false;
	}		

	function delete_mailing_group($mailing_group_id)
	{
		$mailing_group_id = $this->escape($mailing_group_id);
		
		if($mailing_group = $this->get_mailing_group($mailing_group_id))
		{
			global $GO_SECURITY;
			$GO_SECURITY->delete_acl($mailing_group['acl_id']);
				
			$this->query("DELETE FROM ml_mailing_contacts WHERE group_id='$mailing_group_id'");
			$this->query("DELETE FROM ml_mailing_companies WHERE group_id='$mailing_group_id'");
			return $this->query("DELETE FROM ml_mailing_groups WHERE id='$mailing_group_id'");
		}
		return false;
	}

	function update_mailing_group($mailing)
	{		
		$this->update_row('ml_mailing_groups','id', $mailing);		
	}

	function get_mailing_group_by_name($name)
	{
		$this->query("SELECT * FROM ml_mailing_groups WHERE name='".$this->escape($name)."'");
		if ($this->next_record())
		{
			return $this->record;
		}
		return false;
	}
	
	function add_addressbook_contacts_to_mailing_group($addressbook_id, $mailing_group_id)
	{
		$sql = "INSERT IGNORE INTO ml_mailing_contacts (group_id, contact_id) (SELECT '".$this->escape($mailing_group_id)."', id FROM ab_contacts WHERE addressbook_id=".intval($addressbook_id).")";
		return $this->query($sql);
	}
	
	function add_addressbook_companies_to_mailing_group($addressbook_id, $mailing_group_id)
	{
		$sql = "INSERT IGNORE INTO ml_mailing_companies (group_id, company_id) (SELECT '".$this->escape($mailing_group_id)."', id FROM ab_companies WHERE addressbook_id=".intval($addressbook_id).")";
		return $this->query($sql);
	}

	function add_contact_to_mailing_group($contact_id, $mailing_group_id)
	{
		$sql = "INSERT INTO ml_mailing_contacts (group_id, contact_id) VALUES ('".$this->escape($mailing_group_id)."', '$contact_id')";
		return $this->query($sql);
	}

	function remove_contact_from_group($contact_id, $mailing_group_id)
	{
		$sql = "DELETE FROM ml_mailing_contacts WHERE group_id='".$this->escape($mailing_group_id)."' AND contact_id='$contact_id'";
		return $this->query($sql);
	}

	function contact_is_in_group($contact_id, $mailing_group_id)
	{
		$sql = "SELECT * FROM ml_mailing_contacts WHERE contact_id='".$this->escape($contact_id)."' AND group_id='".$this->escape($mailing_group_id)."'";
		$this->query($sql);
		return $this->next_record();
	}

	function add_user_to_mailing_group($user_id, $mailing_group_id)
	{
		$sql = "INSERT INTO ml_mailing_users (group_id, user_id) VALUES ('".$this->escape($mailing_group_id)."', '".intval($user_id)."')";
		return $this->query($sql);
	}

	function remove_user_from_group($user_id, $mailing_group_id)
	{
		$sql = "DELETE FROM ml_mailing_users WHERE group_id='".$this->escape($mailing_group_id)."' AND user_id='".intval($user_id)."'";
		return $this->query($sql);
	}

	function user_is_in_group($user_id, $mailing_group_id)
	{
		$sql = "SELECT * FROM ml_mailing_users WHERE user_id='".intval($user_id)."' AND group_id='".$this->escape($mailing_group_id)."'";
		$this->query($sql);
		return $this->next_record();
	}

	function remove_user_from_mailing_groups($user_id)
	{
		$sql = "DELETE FROM ml_mailing_users WHERE user_id='".intval($user_id)."'";
		$this->query($sql);
	}
	
	function remove_contact_from_mailing_groups($contact_id)
	{
		$sql = "DELETE FROM ml_mailing_contacts WHERE contact_id='".$this->escape($contact_id)."'";
		$this->query($sql);
	}

	function remove_company_from_mailing_groups($company_id)
	{
		$sql = "DELETE FROM ml_mailing_companies WHERE company_id='".$this->escape($company_id)."'";
		$this->query($sql);
	}

	function get_users_from_mailing_group($mailing_group_id, $start=0, $offset=0, $name='name', $dir='ASC')
	{
		$sql = "SELECT DISTINCT go_users.first_name, go_users.middle_name, go_users.last_name,go_users.id, go_users.email FROM ml_mailing_users ".
		"INNER JOIN go_users ON (ml_mailing_users.user_id=go_users.id) ".
		"WHERE ml_mailing_users.group_id='".$this->escape($mailing_group_id)."'";
		
		
		$sql .= " ORDER BY last_name ".$this->escape($dir).", first_name ".$this->escape($dir);

		if ($offset != 0)
		{
			$this->query($sql);
			$count = $this->num_rows();
			
			$sql .= " LIMIT ".intval($start).",".intval($offset);
			
			if ($count > 0)
			{
				$this->query($sql);				
			}
			return $count;
		}else
		{
			$this->query($sql);
			return $this->num_rows();
		}
	}

	function get_contacts_from_mailing_group($mailing_group_id, $start=0, $offset=0, $name='name', $dir='ASC')
	{
		$sql = "SELECT DISTINCT ab_contacts.first_name, ab_contacts.middle_name, ab_contacts.last_name,ab_contacts.id, ab_contacts.email FROM ml_mailing_contacts ".
		"INNER JOIN ab_contacts ON (ml_mailing_contacts.contact_id=ab_contacts.id) ".
		"WHERE ml_mailing_contacts.group_id='".$this->escape($mailing_group_id)."'";
		

		$sql .= " ORDER BY last_name ".$this->escape($dir).", first_name ".$this->escape($dir);

		if ($offset != 0)
		{			
			$this->query($sql);
			$count = $this->num_rows();
			
			$sql .= " LIMIT ".intval($start).",".intval($offset);


			if ($count > 0)
			{
				$this->query($sql);				
			}
		
			return $count;
		}else
		{
			$this->query($sql);
			return $this->num_rows();
		}
	}

	function add_company_to_mailing_group($company_id, $mailing_group_id)
	{
		$sql = "INSERT INTO ml_mailing_companies (group_id, company_id) VALUES ('".$this->escape($mailing_group_id)."', '".$this->escape($company_id)."')";
		return $this->query($sql);
	}

	function remove_company_from_group($company_id, $mailing_group_id)
	{
		$sql = "DELETE FROM ml_mailing_companies WHERE group_id='".$this->escape($mailing_group_id)."' AND company_id='".$this->escape($company_id)."'";
		return $this->query($sql);
	}

	function company_is_in_group($company_id, $mailing_group_id)
	{
		$sql = "SELECT * FROM ml_mailing_companies WHERE company_id='".$this->escape($company_id)."' AND group_id='".$this->escape($mailing_group_id)."'";
		$this->query($sql);
		return $this->next_record();
	}

	function get_companies_from_mailing_group($mailing_group_id, $start=0, $offset=0, $name='name', $dir='ASC')
	{
		$sql = "SELECT DISTINCT ab_companies.name, ab_companies.id, ab_companies.email FROM ml_mailing_companies ".
		"INNER JOIN ab_companies ON (ml_mailing_companies.company_id=ab_companies.id) ".
		"WHERE ml_mailing_companies.group_id='".$this->escape($mailing_group_id)."'";
		
		$sql .= " ORDER BY ".$this->escape($name." ".$dir);

		if ($offset != 0)
		{
			$this->query($sql);
			$count = $this->num_rows();
			
			$sql .= " LIMIT ".intval($start).",".intval($offset);
			
			if ($count > 0)
			{
				$this->query($sql);				
			}
			return $count;
		}else
		{
			$this->query($sql);
			return $this->num_rows();
		}
	}
	
	
	
	
	
	function add_mailing($mailing)
	{
		$mailing['id']=$this->nextid('ml_mailings');
		$this->insert_row('ml_mailings', $mailing);

		$this->start_mailing($mailing);
		return $mailing['id'];
	}

	function launch_mailing($mailing_id){
		global $GO_CONFIG, $GO_MODULES;

		$mailing_id=intval($mailing_id);

		$log = $GO_CONFIG->file_storage_path.'log/mailings/';
		if(!is_dir($log))
			mkdir($log, 0755,true);
		
		$log .= $mailing_id.'.log';

		$cmd = $GO_CONFIG->cmd_php.' '.$GO_MODULES->modules['mailings']['path'].'sendmailing.php '.$GO_CONFIG->get_config_file().' '.$mailing_id.' >> '.$log;

		if (!is_windows()) {
		 $cmd .= ' 2>&1 &';
		}

		file_put_contents($log, Date::get_timestamp(time())."\r\n".$cmd."\r\n\r\n", FILE_APPEND);

		if(is_windows())
		{
			pclose(popen("start /B ". $cmd, "r"));
		}else
		{
			exec($cmd);
		}
	}

	function update_mailing($mailing){
		return $this->update_row('ml_mailings', 'id', $mailing);
	}
	
	function get_first_active_mailing()
	{
		$sql = "SELECT * FROM ml_mailings WHERE status<2 LIMIT 0,1;";
		$this->query($sql);
		
		if($this->next_record(DB_ASSOC))
		{
			return $this->record;
		}
	}
	
	function get_mailing($mailing_id)
	{
		$sql = "SELECT * FROM ml_mailings WHERE id=$mailing_id;";
		$this->query($sql);		
		return $this->next_record(DB_ASSOC);
	}
	
	function start_mailing($mailing)
	{
		$sql = "DELETE FROM ml_sendmailing_contacts WHERE mailing_id=?";
		$this->query($sql, 'i', array($mailing['id']));

		$sql = "INSERT INTO ml_sendmailing_contacts SELECT DISTINCT ?, contact_id FROM ml_mailing_contacts c ".
			"INNER JOIN ab_contacts a ON (c.contact_id=a.id) ".
			"WHERE group_id=? AND email_allowed='1' AND email!=''";
		$types='ii';
		$params = array($mailing['id'], $mailing['mailing_group_id']);		
		$this->query($sql, $types, $params);

		$sql = "DELETE FROM ml_sendmailing_companies WHERE mailing_id=?";
		$this->query($sql, 'i', array($mailing['id']));

		$sql = "INSERT INTO ml_sendmailing_companies SELECT DISTINCT ?, company_id FROM ml_mailing_companies c ".
			"INNER JOIN ab_companies a ON (c.company_id=a.id) ".
			"WHERE group_id=? AND email_allowed='1' AND email!=''";
		$types='ii';
		$params = array($mailing['id'], $mailing['mailing_group_id']);
		$this->query($sql, $types, $params);

		$sql = "DELETE FROM ml_sendmailing_users WHERE mailing_id=?";
		$this->query($sql, 'i', array($mailing['id']));

		$sql = "INSERT INTO ml_sendmailing_users SELECT DISTINCT ?, user_id FROM ml_mailing_users c WHERE group_id=?";
		$types='ii';
		$params = array($mailing['id'], $mailing['mailing_group_id']);
		$this->query($sql, $types, $params);
		
		$count = $this->get_contacts_for_send($mailing['id']);
		$count += $this->get_companies_for_send($mailing['id']);
		$count += $this->get_users_for_send($mailing['id']);
				
		$sql = "UPDATE ml_mailings SET status='1', total=$count WHERE id=".intval($mailing['id']);
		$this->query($sql);
	}

	function get_users_for_send($mailing_id)
	{
		$sql = "SELECT u.first_name, u.middle_name, u.last_name,u.id, u.email FROM ml_sendmailing_users s ".
		"INNER JOIN go_users u ON (s.user_id=u.id) ".
		"WHERE s.mailing_id='".$this->escape($mailing_id)."'";

		$this->query($sql);
		return $this->num_rows();
	}

	function user_sent($mailing_id, $user_id){
		$sql = "DELETE FROM ml_sendmailing_users WHERE mailing_id=? AND user_id=?";
		$this->query($sql, 'ii', array($mailing_id, $user_id));
	}

	function get_contacts_for_send($mailing_id)
	{
		$sql = "SELECT c.first_name, c.middle_name, c.last_name,c.id, c.email FROM ml_sendmailing_contacts s ".
		"INNER JOIN ab_contacts c ON (s.contact_id=c.id) ".
		"WHERE s.mailing_id='".$this->escape($mailing_id)."'";

		$this->query($sql);
		return $this->num_rows();
	}
	function contact_sent($mailing_id, $contact_id){
		$sql = "DELETE FROM ml_sendmailing_contacts WHERE mailing_id=? AND contact_id=?";
		$this->query($sql, 'ii', array($mailing_id, $contact_id));
	}

	function get_companies_for_send($mailing_id)
	{
		$sql = "SELECT c.name, c.id, c.email FROM ml_sendmailing_companies s ".
		"INNER JOIN ab_companies c ON (s.company_id=c.id) ".
		"WHERE s.mailing_id='".$this->escape($mailing_id)."'";
		$this->query($sql);
		return $this->num_rows();
	}
	function company_sent($mailing_id, $company_id){
		$sql = "DELETE FROM ml_sendmailing_companies WHERE mailing_id=? AND company_id=?";
		$this->query($sql, 'ii', array($mailing_id, $company_id));
	}
	
	
	function end_mailing($mailing)
	{
		$sql = "UPDATE ml_mailings SET status='2' WHERE id=".intval($mailing['id']);
		$this->query($sql);
	}
	
	function update_status($mailing_id, $error_count, $success_count)
	{
		$sql = "UPDATE ml_mailings SET ";
		if($success_count)
		{
			$updates[] = "sent=sent+$success_count";
		}
		if($error_count)
		{
			$updates[] = "errors=errors+$error_count";
		}
		
		if(isset($updates))
		{
			$sql .= implode(',', $updates)." WHERE id=".intval($mailing_id);
			$this->query($sql);
		}		
	}	
	
	function get_mailings($mailing_group_id, $user_id=0, $start=0, $offset=0, $sort='ctime', $dir='DESC')
	{
		$sql = "SELECT m.*,mg.name AS mailing_group FROM ml_mailings m INNER JOIN ml_mailing_groups mg ON mg.id=m.mailing_group_id ";
		
		if(!empty($mailing_group_id)){
			$sql .= " WHERE mailing_group_id=".intval($mailing_group_id);
		}
		
		if($user_id)
		{
			$sql .= " WHERE m.user_id=".intval($user_id);
		}

		$sql .= " ORDER BY ".$this->escape($sort.' '.$dir);

		$this->query($sql);
		$count = $this->num_rows();

		if($offset>0)
		{
			$sql .= " LIMIT ".intval($start).",".intval($offset);
			$this->query($sql);
		}
		return $count;
	}
	
	
	
	function user_delete($user)
	{
		global $GO_MODULES;
		
		require_once($GO_MODULES->modules['mailings']['class_path'].'templates.class.inc.php');
		$mailings = new mailings();
		
		$tp = new templates();
		
		$sql = "SELECT id FROM ml_mailing_groups WHERE user_id='".$mailings->escape($user['id'])."'";
		$mailings->query($sql);
		while($mailings->next_record())
		{
			$tp->delete_mailing_group($mailings->f('id'));
		}
		
		$sql = "SELECT id FROM ml_templates WHERE user_id='".$mailings->escape($user['id'])."'";
		$mailings->query($sql);
		while($mailings->next_record())
		{
			$tp->delete_template($mailings->f('id'));
		}
	}
}
?>
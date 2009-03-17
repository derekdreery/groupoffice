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

class addressbook extends db {

	public function __on_load_listeners($events){
		$events->add_listener('user_delete', __FILE__, 'addressbook', 'user_delete');
		//$events->add_listener('add_user', __FILE__, 'addressbook', 'add_user');
		$events->add_listener('build_search_index', __FILE__, 'addressbook', 'build_search_index');
	}

	function is_duplicate_contact($contact)
	{
		$contact = $contact;

		$contact['email']=isset($contact['email']) ? $contact['email'] : '';
		$contact['first_name']=isset($contact['first_name']) ? $contact['first_name'] : '';
		$contact['middle_name']=isset($contact['middle_name']) ? $contact['middle_name'] : '';
		$contact['last_name']=isset($contact['last_name']) ? $contact['last_name'] : '';

		$sql = "SELECT id FROM ab_contacts WHERE ".
 		"addressbook_id='".$this->escape($contact['addressbook_id'])."' AND ".
 		"first_name='".$this->escape($contact['first_name'])."' AND ".
 		"middle_name='".$this->escape($contact['middle_name'])."' AND ".
 		"last_name='".$this->escape($contact['last_name'])."' AND ".
 		"email='".$this->escape($contact['email'])."'";

		$this->query($sql);
		if($this->next_record())
		{
			return $this->f('id');
		}
		return false;
	}

	function parse_address($address) {
		$address = trim($address);

		$address_arr['housenumber'] = '';
		$address_arr['street'] = $address;

		if ($address != '') {
			$last_space = strrpos($address, ' ');

			if ($last_space !== false) {
				$address_arr['housenumber'] = substr($address, $last_space +1);
				$address_arr['street'] = substr($address, 0, $last_space);

			}
		}
		return $address_arr;
	}

	function get_addressbook($addressbook_id=0) {
		if($addressbook_id == 0)
		{
			global $GO_SECURITY, $GO_USERS;

			//$sql = "SELECT * FROM ab_addressbooks WHERE user_id=".$GO_SECURITY->user_id;
			//$this->query($sql);

			$this->get_writable_addressbooks($GO_SECURITY->user_id);

			if($this->next_record())
			{
				$addressbook_id = $this->f('id');
			}else
			{
				$user = $GO_USERS->get_user($GO_SECURITY->user_id);
				$name = String::format_name($user['last_name'], $user['first_name'], $user['middle_name'], 'last_name');
				$new_ab_name = $name;
				$x = 1;
				while ($this->get_addressbook_by_name($new_ab_name)) {
					$new_ab_name = $name.' ('.$x.')';
					$x ++;
				}
				$addressbook = $this->add_addressbook($GO_SECURITY->user_id, $new_ab_name);
				$addressbook=$addressbook['id'];
			}
		}
		$sql = "SELECT * FROM ab_addressbooks WHERE id='".$this->escape($addressbook_id)."'";
		$this->query($sql);
		if ($this->next_record()) {
			return $this->record;
		}else
		{
			return false;
		}
	}

	function get_user_addressbooks($user_id, $start=0, $offset=0, $sort='name', $dir='ASC') {
		$sql = "SELECT DISTINCT ab_addressbooks.* ".
		"FROM ab_addressbooks ".
		"	INNER JOIN go_acl ON (ab_addressbooks.acl_read = go_acl.acl_id ".
		"OR ab_addressbooks.acl_write = go_acl.acl_id) ".
		"LEFT JOIN go_users_groups ON go_acl.group_id = go_users_groups.group_id ".
		"WHERE go_acl.user_id=".$this->escape($user_id)." ".
		"OR go_users_groups.user_id=".$this->escape($user_id)." ".
		" ORDER BY ab_addressbooks.".$sort." ".$dir;

		$this->query($sql);
		$count= $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}

	function get_contacts_for_export($addressbook_id, $user_id = 0) {
		global $GO_SECURITY;

		if ($user_id == 0) {
			$user_id = $GO_SECURITY->user_id;
		}
		$sql = "SELECT ab_contacts.*,".
 		"ab_companies.name AS company FROM ab_contacts ".
 		"LEFT JOIN ab_companies ON (ab_contacts.company_id=ab_companies.id) ".
 		" WHERE ab_contacts.addressbook_id='".$this->escape($addressbook_id)."' ".
 		" ORDER BY ab_contacts.first_name, ab_contacts.last_name ASC";

		$this->query($sql);
		return $this->num_rows();
	}

	function get_contacts($addressbook_id=0, $sort = "name", $direction = "ASC", $start=0, $offset=0) {
		global $GO_SECURITY;

		if ($sort == 'name') {
			if ($_SESSION['GO_SESSION']['sort_name'] == 'first_name') {
				$sort = 'first_name '.$direction.', last_name';
			} else {
				$sort = 'last_name '.$direction.', first_name';
			}
		}
		$sql = "SELECT * FROM ab_contacts ";
		if($addressbook_id>0)
		{
			$sql .= " WHERE ab_contacts.addressbook_id='".$this->escape($addressbook_id)."'";
		}

		$sql .= 	" ORDER BY $sort $direction";

		$this->query($sql);
		$count =  $this->num_rows();
		if ($offset != 0 && $count > $offset) {
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}

		return $count;
	}

	function get_user_addressbook_ids($user_id)
	{
		/*if(!isset($_SESSION['GO_SESSION'][$user_id]['authorized_addressbooks']))
		 {
			$_SESSION['GO_SESSION'][$user_id]['authorized_addressbooks'] = array();
			$this->get_user_addressbooks($user_id);
			while($this->next_record())
			{
			$_SESSION['GO_SESSION'][$user_id]['authorized_addressbooks'][] = $this->f('id');
			}
			}
			return $_SESSION['GO_SESSION'][$user_id]['authorized_addressbooks'];*/

		$addressbooks=array();
		$this->get_user_addressbooks($user_id);
		while($this->next_record())
		{
			$addressbooks[] = $this->f('id');
		}

		return $addressbooks;
	}

	function get_writable_addressbooks($user_id, $start=0, $offset=0, $sort='name', $dir='ASC') {
		$sql = "SELECT DISTINCT ab_addressbooks.* ".
		"FROM ab_addressbooks ".
		"	INNER JOIN go_acl ON ab_addressbooks.acl_write = go_acl.acl_id ".
		"LEFT JOIN go_users_groups ON go_acl.group_id = go_users_groups.group_id ".
		"WHERE go_acl.user_id=".$this->escape($user_id)." ".
		"OR go_users_groups.user_id=".$this->escape($user_id)." ".
		" ORDER BY ab_addressbooks.".$sort." ".$dir;
		$this->query($sql);
		$count= $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}

	function add_company($company) {

		if (!isset($company['user_id']) || $company['user_id'] == 0) {
			global $GO_SECURITY;
			$company['user_id'] = $GO_SECURITY->user_id;
		}

		if (!isset($company['ctime']) || $company['ctime'] == 0) {
			$company['ctime'] = time();
		}
		if (!isset($company['mtime']) || $company['mtime'] == 0) {
			$company['mtime'] = $company['ctime'];
		}

		$company['id'] = $this->nextid("ab_companies");
		$this->insert_row('ab_companies', $company);
		$this->cache_company($company['id']);

		return $company['id'];
	}

	function update_company($company)
	{
		if (!isset($company['mtime']) || $company['mtime'] == 0) {
			$company['mtime'] = time();
		}
		$r = $this->update_row('ab_companies', 'id', $company);
		$this->cache_company($company['id']);
		return $r;
	}

	function get_companies($addressbook_id=0, $sort = 'name', $direction = 'ASC', $start = 0, $offset = 0) {
		global $GO_SECURITY;

		$sql = "SELECT ab_companies.* FROM ab_companies";

		if($addressbook_id > 0)
		{
			$sql .= " WHERE addressbook_id='$addressbook_id'";
		}

		$sql .= " ORDER BY $sort $direction";
		$this->query($sql);
		$count = $this->num_rows();

		if ($offset != 0 && $count > $offset) {
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}
		return $count;
	}

	function get_company($company_id) {
		$sql = "SELECT ab_companies.*, ab_addressbooks.acl_read, ".
		"ab_addressbooks.acl_write FROM ab_companies ".
		"INNER JOIN ab_addressbooks ON ".
		"(ab_addressbooks.id=ab_companies.addressbook_id) ".
		"WHERE ab_companies.id='".$this->escape($company_id)."'";
		$this->query($sql);
		if ($this->next_record(DB_ASSOC)) {
			return $this->record;
		}
		return false;
	}

	function get_company_by_name($addressbook_id, $name) {
		$sql = "SELECT * FROM ab_companies WHERE addressbook_id='".$this->escape($addressbook_id)."' AND name='".$this->escape($name)."'";
		$this->query($sql);
		if ($this->next_record()) {
			return $this->record;
		}
		return false;
	}

	function get_company_id_by_name($name, $addressbook_id) {
		$sql = "SELECT id FROM ab_companies WHERE addressbook_id='$addressbook_id' AND name='".$this->escape($name)."'";
		$this->query($sql);
		if ($this->next_record()) {
			return $this->f('id');
		}
		return false;
	}

	function get_company_contacts($company_id, $sort = "name", $direction = "ASC", $start=0, $offset=0) {
		if ($sort == 'name') {
			if ($_SESSION['GO_SESSION']['sort_name'] == 'first_name') {
				$sort = 'first_name '.$direction.', last_name';
			} else {
				$sort = 'last_name '.$direction.', first_name';
			}

			//	  $sort = 'first_name '.$direction.', last_name';
		}
		$sql = "SELECT * FROM ab_contacts WHERE company_id='".$this->escape($company_id)."' ORDER BY $sort $direction";

		if ($offset != 0) {
			$sql .= " LIMIT ".$this->escape($start.",".$offset);

			$sql2 = "SELECT * FROM ab_contacts WHERE company_id='".$this->escape($company_id)."'";

			$this->query($sql2);
			$count = $this->num_rows();

			if ($count > 0) {
				$this->query($sql);
				return $count;
			}
			return 0;

		} else {
			$this->query($sql);
			return $this->num_rows();
		}
	}

	function delete_company($company_id) {
		global $GO_CONFIG, $GO_LINKS;

		//$company=$this->get_company($company_id);

		#$GO_LINKS->delete_link($company['link_id']);

		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem();
		if(file_exists($GO_CONFIG->file_storage_path.'companies/'.$company_id.'/'))
		{
			$fs->delete($GO_CONFIG->file_storage_path.'companies/'.$company_id.'/');
		}

		$sql = "UPDATE ab_contacts SET company_id=0 WHERE company_id=$company_id";
		$this->query($sql);
			
		require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
		$search = new search();
		$search->delete_search_result($company_id, 3);

		$sql = "DELETE FROM ab_companies WHERE id='$company_id'";
		if ($this->query($sql)) {
			return true;
		}


	}

	function add_contact($contact) {

		if (!isset($contact['user_id']) || $contact['user_id'] == 0) {
			global $GO_SECURITY;
			$contact['user_id'] = $GO_SECURITY->user_id;
		}

		if (!isset($contact['ctime']) || $contact['ctime'] == 0) {
			$contact['ctime'] = time();
		}
		if (!isset($contact['mtime']) || $contact['mtime'] == 0) {
			$contact['mtime'] = $contact['ctime'];
		}

		if (isset($contact['sex']) && $contact['sex'] == '') {
			$contact['sex'] = 'M';
		}

		$contact['id'] = $this->nextid("ab_contacts");

		$this->insert_row('ab_contacts', $contact);

		$this->cache_contact($contact['id']);

		return $contact['id'];
	}

	function update_contact($contact)
	{
		if (!isset($contact['mtime']) || $contact['mtime'] == 0) {
			$contact['mtime'] = time();
		}

		if (isset($contact['sex']) && $contact['sex'] == '') {
			$contact['sex'] = 'M';
		}

		$r = $this->update_row('ab_contacts', 'id', $contact);

		$this->cache_contact($contact['id']);
		return $r;
	}

	function get_contact($contact_id) {
		$this->query("SELECT ab_addressbooks.acl_read, ab_addressbooks.acl_write, ab_contacts.*, ".
		"ab_companies.address AS work_address, ab_companies.address_no AS ".
		"work_address_no, ab_companies.zip AS work_zip, ".
		"ab_companies.city AS work_city, ab_companies.state AS work_state, ".
		"ab_companies.country AS work_country, ab_companies.homepage, ".
		"ab_companies.bank_no, ab_companies.email AS company_email, ".
		"ab_companies.phone AS company_phone, ab_companies.fax AS company_fax, ".
		"ab_companies.name AS company_name, ".
		"ab_companies.post_address AS work_post_address, ab_companies.post_address_no AS work_post_address_no, ".
		"ab_companies.post_zip AS work_post_zip, ab_companies.post_city AS work_post_city, ab_companies.post_state AS work_post_state, ".
		"ab_companies.post_country AS work_post_country ".
		"FROM ab_contacts LEFT JOIN ab_companies ON (ab_contacts.company_id=ab_companies.id) ".
		"INNER JOIN ab_addressbooks ON (ab_contacts.addressbook_id=ab_addressbooks.id) ".
		"WHERE ab_contacts.id='".$this->escape($contact_id)."'");


		if ($this->next_record(DB_ASSOC)) {
			return $this->record;
		}else
		{
			throw new DatabaseSelectException();
		}
		return false;
	}

	function delete_contact($contact_id) {

		global $GO_CONFIG,$GO_LINKS, $GO_MODULES;

		$contact=$this->get_contact($contact_id);

		#$GO_LINKS->delete_link($contact['link_id']);

		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem(true);
		if(file_exists($GO_CONFIG->file_storage_path.'contacts/'.$contact_id.'/'))
		{
			$fs->delete($GO_CONFIG->file_storage_path.'contacts/'.$contact_id.'/');
		}
			
		if(isset($GO_MODULES->modules['mailings']))
		{
			$sql1 = "DELETE FROM ml_mailing_contacts WHERE contact_id='".$this->escape($contact_id)."'";
			$this->query($sql1);
		}

		require_once($GO_CONFIG->class_path.'base/search.class.inc.php');
		$search = new search();
		$search->delete_search_result($contact_id, 2);

		return $this->query("DELETE FROM ab_contacts WHERE id='".$this->escape($contact_id)."'");

	}

	function search_contacts($user_id, $query, $field = 'last_name', $addressbook_id = 0, $start=0, $offset=0, $require_email=false, $sort_index='name', $sort_order='ASC', $writable_only=false, $query_type='LIKE', $mailings_filter=array(), $advanced_query='') {
		global $GO_MODULES;
		//$query = str_replace('*', '%', $query);

		if($sort_index=='name')
		{
			if ($_SESSION['GO_SESSION']['sort_name'] == 'first_name') {
				$sort_index = 'ab_contacts.first_name '.$sort_order.', ab_contacts.last_name';
			} else {
				$sort_index = 'ab_contacts.last_name '.$sort_order.', ab_contacts.first_name';
			}
		}

		if(count($mailings_filter))
		{
			$sql = "SELECT DISTINCT ";
		}else
		{
			$sql = "SELECT ";
		}

		$sql .= "ab_contacts.*, ab_companies.name AS company_name FROM ab_contacts ".
		"LEFT JOIN ab_companies ON ab_contacts.company_id=ab_companies.id ";

		if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
		{
			$sql .= "LEFT JOIN cf_2 ON cf_2.link_id=ab_contacts.id ";
		}

		if(count($mailings_filter))
		{
			$sql .= "INNER JOIN ml_mailing_contacts mc ON mc.contact_id=ab_contacts.id ";
		}


		if ($addressbook_id > 0) {
			$sql .= "WHERE ab_contacts.addressbook_id='$addressbook_id' ";
		} else {

			if($writable_only)
			{
				$user_ab = $this->get_writable_addressbook_ids($user_id);
			}else {
				$user_ab = $this->get_user_addressbook_ids($user_id);
			}
			if(count($user_ab) > 1)
			{
				$sql .= "WHERE ab_contacts.addressbook_id IN (".implode(",",$user_ab).") ";
			}elseif(count($user_ab)==1)
			{
				$sql .= "WHERE ab_contacts.addressbook_id=".$user_ab[0]." ";
			}else
			{
				return false;
			}
		}

		if(!empty($query))
		{
			$sql .= " AND ";

			if(!is_array($field))
			{
				if($field == '')
				{
					$fields=array('name');
					$fields_sql = "SHOW FIELDS FROM ab_contacts";
					$this->query($fields_sql);
					while($this->next_record())
					{
						if(eregi('varchar', $this->f('Type')))
						{
							$fields[]='ab_contacts.'.$this->f('Field');
						}
					}
					if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
					{
						$fields_sql = "SHOW FIELDS FROM cf_2";
						$this->query($fields_sql);
						while ($this->next_record()) {
							$fields[]='cf_2.'.$this->f('Field');
						}
					}
				}else {
					$fields[]=$field;
				}
			}else {
				$fields=$field;
			}

			foreach($fields as $field)
			{
				if(count($fields)>1)
				{
					if(isset($first))
					{
						$sql .= ' OR ';
					}else
					{
						$first = true;
						$sql .= '(';
					}
				}

				if($field=='name')
				{
					$sql .= "CONCAT(first_name,middle_name,last_name) $query_type '".$this->escape(str_replace(' ','%', $query))."' ";
				}else
				{
					$sql .= "$field $query_type '".$this->escape($query)."' ";
				}
			}
			if(count($fields)>1)
			{
				$sql .= ')';
			}
		}


		if($require_email)
		{
			$sql .= " AND ab_contacts.email != ''";
		}

		if(count($mailings_filter))
		{
			$sql .= " AND mc.group_id IN (".implode(',', $mailings_filter).")";
		}

		if(!empty($advanced_query))
		{
			$sql .= $advanced_query;
		}

		$sql .= " ORDER BY $sort_index $sort_order";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset > 0)
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			$this->query($sql);
		}

		//debug($sql);
		return $count;
	}

	function search_companies($user_id, $query, $field = 'name', $addressbook_id = 0, $start=0, $offset=0, $require_email=false, $sort_index='name', $sort_order='ASC', $query_type='LIKE', $mailings_filter=array(), $advanced_query='') {
		global $GO_MODULES;

		//$query = str_replace('*', '%', $query);

		if(count($mailings_filter))
		{
			$sql = "SELECT DISTINCT ";
		}else
		{
			$sql = "SELECT ";
		}

		if(isset($GO_MODULES->modules['customfields']))
		{
			$sql .= "ab_companies.*, cf_3.* FROM ab_companies ".
				"LEFT JOIN cf_3 ON cf_3.link_id=ab_companies.id ";
		}else {
			$sql .= "ab_companies.* FROM ab_companies ";
		}

		if(count($mailings_filter))
		{
			$sql .= "INNER JOIN ml_mailing_companies mc ON mc.company_id=ab_companies.id ";
		}

		if ($addressbook_id > 0) {
			$sql .= "WHERE ab_companies.addressbook_id='$addressbook_id'";
		} else {

			$user_ab = $this->get_user_addressbook_ids($user_id);
			if(count($user_ab) > 1)
			{
				$sql .= "WHERE ab_companies.addressbook_id IN (".implode(",",$user_ab).")";
			}elseif(count($user_ab)==1)
			{
				$sql .= "WHERE ab_companies.addressbook_id=".$user_ab[0];
			}else
			{
				return false;
			}
		}

		if(!empty($query))
		{
			$query = $this->escape($query);
			$sql .= ' AND ';
			if ($field == '') {
				$fields_sql = "SHOW FIELDS FROM ab_companies";
				$this->query($fields_sql);
				while ($this->next_record()) {
					if (eregi('varchar', $this->f('Type'))) {
						if (isset ($first)) {
							$sql .= ' OR ';
						} else {
							$first = true;
							$sql .= '(';
						}
						$sql .= "ab_companies.".$this->f('Field')." LIKE '".$this->escape($query)."'";
					}
				}
				if(isset($GO_MODULES->modules['customfields']) && $GO_MODULES->modules['customfields']['read_permission'])
				{
					$fields_sql = "SHOW FIELDS FROM cf_3";
					$this->query($fields_sql);
					while ($this->next_record()) {
						//if (eregi('varchar', $this->f('Type')) || $this->f('Field')=='id') {
						if (isset ($first)) {
							$sql .= ' OR ';
						} else {
							$first = true;
							$sql .= '(';
						}
						$sql .= "cf_3.".$this->f('Field')." $query_type '$query'";
						//}
					}

				}
				$sql .= ')';
			} else {
				$sql .= "$field $query_type '$query'";
			}
		}

		if($require_email)
		{
			$sql .= " AND ab_companies.email != ''";
		}

		if(count($mailings_filter))
		{
			$sql .= " AND mc.group_id IN (".implode(',', $mailings_filter).")";
		}

		if(!empty($advanced_query))
		{
			$sql .= $advanced_query;
		}

		$sql .= " ORDER BY $sort_index $sort_order";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset > 0 )
		{
			$sql .= " LIMIT ".$this->escape($start.",".$offset);
			//echo $sql;
			$this->query($sql);
			return $count;
		}else
		{
			return $count;
		}
	}

	function add_addressbook($user_id, $name) {
		global $GO_SECURITY;

		$result['id'] = $this->nextid('ab_addressbooks');
		$result['acl_read'] = $GO_SECURITY->get_new_acl('addressbook', $user_id);
		$result['acl_write'] = $GO_SECURITY->get_new_acl('addressbook', $user_id);
		$result['user_id']=$user_id;
		$result['name']=$name;

		$this->insert_row('ab_addressbooks', $result);
		$result['addressbook_id']=$result['id'];
		return $result;
	}

	function update_addressbook($addressbook_id, $user_id, $name) {
		$sql = "UPDATE ab_addressbooks SET name='".$this->escape($name)."', user_id='".$this->escape($user_id)."' WHERE id='".$this->escape($addressbook_id)."'";
		return $this->query($sql);
	}

	function get_addressbook_by_name($name) {
		$sql = "SELECT * FROM ab_addressbooks WHERE name='".$this->escape($name)."'";
		$this->query($sql);
		if ($this->next_record()) {
			return $this->record;
		} else {
			return false;
		}
	}

	function delete_addressbook($addressbook_id) {

		$addressbook = $this->get_addressbook($addressbook_id);

		global $GO_SECURITY;

		$GO_SECURITY->delete_acl($addressbook['acl_read']);
		$GO_SECURITY->delete_acl($addressbook['acl_write']);

		$ab = new addressbook();

		$this->get_contacts($addressbook_id);
		while($this->next_record())
		{
			$ab->delete_contact($this->f('id'));
		}

		$this->get_companies($addressbook_id);
		while($this->next_record())
		{
			$ab->delete_company($this->f('id'));
		}

		$sql = "DELETE FROM ab_addressbooks WHERE id='".$this->escape($addressbook_id)."'";
		$this->query($sql);

	}

	function search_email($user_id, $query)
	{

		$query = $this->escape(str_replace(' ','%', $query));

		$sql = "SELECT DISTINCT ab_contacts.first_name, ab_contacts.middle_name, ab_contacts.last_name, ab_contacts.email, ab_contacts.email2, ab_contacts.email3 FROM ab_contacts WHERE ";

		$user_ab = $this->get_user_addressbook_ids($user_id);
		if(count($user_ab) > 1)
		{
			$sql .= "ab_contacts.addressbook_id IN (".implode(",",$user_ab).") AND ";
		}elseif(count($user_ab)==1)
		{
			$sql .= "ab_contacts.addressbook_id=".$user_ab[0]." AND ";
		}else
		{
			return false;
		}
		$sql .= "(CONCAT(first_name,middle_name,last_name) LIKE '".$query."' OR email LIKE '".$this->escape($query)."' OR email2 LIKE '".$this->escape($query)."' OR email3 LIKE '".$this->escape($query)."')";

		if ($_SESSION['GO_SESSION']['sort_name'] == 'first_name') {
			$sort_index = 'ab_contacts.first_name ASC, ab_contacts.last_name';
		} else {
			$sort_index = 'ab_contacts.last_name ASC, ab_contacts.first_name';
		}

		$sql .= " ORDER BY $sort_index ASC LIMIT 0,10";

		$this->query($sql);
	}

	/**
	 * When a an item gets deleted in a panel with links. Group-Office attempts
	 * to delete the item by finding the associated module class and this function
	 *
	 * @param int $id The id of the linked item
	 * @param int $link_type The link type of the item. See /classes/base/links.class.inc
	 */

	function __on_delete_link($id, $link_type)
	{
		//echo $id.':'.$link_type;
		if($link_type==3)
		{
			$this->delete_company($id);
		}elseif($link_type==2)
		{
			$this->delete_contact($id);
		}
	}


	/**
	 * Adds or updates a note in the search cache table
	 *
	 * @param int $note_id
	 */
	private function cache_contact($contact_id)
	{
		global $GO_CONFIG, $GO_LANGUAGE;
		require_once($GO_CONFIG->class_path.'/base/search.class.inc.php');
		$search = new search();

		require($GO_LANGUAGE->get_language_file('addressbook'));

		$sql = "SELECT c.*,a.acl_read,a.acl_write FROM ab_contacts c INNER JOIN ab_addressbooks a ON a.id=c.addressbook_id WHERE c.id=?";
		$this->query($sql, 'i', $contact_id);
		$record = $this->next_record();
		if($record)
		{
			$cache['id']=$this->f('id');
			$cache['user_id']=$this->f('user_id');
			$cache['module']='addressbook';
			$cache['name'] = String::format_name($this->f('last_name'),$this->f('first_name'),$this->f('middle_name'));
			$cache['link_type']=2;
			$cache['description']='';
			$cache['type']=$lang['addressbook']['contact'];
			$cache['keywords']=$search->record_to_keywords($this->record).','.$lang['addressbook']['contact'];
			$cache['mtime']=$this->f('mtime');
			$cache['acl_read']=$this->f('acl_read');
			$cache['acl_write']=$this->f('acl_write');
				
			$search->cache_search_result($cache);
		}
	}

	/**
	 * Adds or updates a note in the search cache table
	 *
	 * @param int $note_id
	 */
	private function cache_company($company_id)
	{
		global $GO_CONFIG, $GO_LANGUAGE;
		require_once($GO_CONFIG->class_path.'/base/search.class.inc.php');
		$search = new search();
		require($GO_LANGUAGE->get_language_file('addressbook'));
		$sql = "SELECT c.*, a.acl_read, a.acl_write FROM ab_companies c INNER JOIN ab_addressbooks a ON a.id=c.addressbook_id WHERE c.id=?";
		$this->query($sql, 'i', $company_id);
		$record = $this->next_record();
		if($record)
		{
			$cache['id']=$this->f('id');
			$cache['user_id']=$this->f('user_id');
			$cache['name'] = htmlspecialchars($this->f('name'), ENT_QUOTES, 'utf-8');
			$cache['link_type']=3;
			$cache['module']='addressbook';
			$cache['description']='';
			$cache['type']=$lang['addressbook']['company'];
			$cache['keywords']=$search->record_to_keywords($this->record).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');
			$cache['acl_read']=$this->f('acl_read');
			$cache['acl_write']=$this->f('acl_write');
				
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
		$ab = new addressbook();
		$ab2 = new addressbook();

		$sql = "SELECT id FROM ab_contacts";
		$ab2->query($sql);

		while($record = $ab2->next_record())
		{
			$ab->cache_contact($record['id']);
		}

		$sql = "SELECT id FROM ab_companies";
		$ab2->query($sql);
		while($record = $ab2->next_record())
		{
			$ab->cache_company($record['id']);
		}
	}

	/**
	 * This function is called when a user is deleted
	 *
	 * @param int $user_id
	 */

	public static function user_delete($user) {

		$ab2 = new addressbook();

		$sql = "UPDATE ab_contacts SET source_id='0' WHERE source_id='".$ab2->escape($user['id'])."'";
		$ab2->query($sql);

		$ab = new addressbook();

		$sql = "SELECT id FROM ab_addressbooks WHERE user_id='".$ab2->escape($user['id'])."'";
		$ab2->query($sql);
		while ($ab2->next_record()) {
			$ab->delete_addressbook($ab2->f('id'));
		}
	}

	function move_contacts_company($company_id, $old_addressbook_id, $addressbook_id, $update_company=true)
	{
		if($company_id>0)
		{
			$this->query('UPDATE ab_contacts SET addressbook_id="'.$this->escape($addressbook_id).'" WHERE company_id="'.$this->escape($company_id).'" AND addressbook_id="'.$this->escape($old_addressbook_id).'"');
			if($update_company)
			{
				$this->query('UPDATE ab_companies SET addressbook_id="'.$this->escape($addressbook_id).'" WHERE id="'.$this->escape($company_id).'"');
			}
		}
	}

	function get_contact_by_email($email, $user_id, $addressbook_id=0) {
		$email = $this->escape(String::get_email_from_string($email));
		$sql = "SELECT * FROM ab_contacts ";

		if($addressbook_id>0)
		{
			$sql .= "WHERE addressbook_id=".$addressbook_id." AND ";
		}else
		{
			$user_ab = $this->get_user_addressbook_ids($user_id);
			if(count($user_ab) > 1)
			{
				$sql .= "WHERE addressbook_id IN (".implode(",",$user_ab).") AND ";
			}elseif(count($user_ab)==1)
			{
				$sql .= "WHERE addressbook_id=".$user_ab[0]." AND ";
			}else
			{
				return false;
			}
		}
		$sql .= " (email='$email' OR email2='$email' OR email3='$email')";

		$this->query($sql);
		if ($this->next_record())
		return $this->record;
		else
		return false;
	}
}
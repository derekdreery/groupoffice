<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.91 $ $Date: 2006/12/05 11:37:30 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

class addressbook extends db {
	var $selector_is_enabled = false;

	function addressbook() {
		$this->db();
	}

	function is_duplicate_contact($contact)
	{

		$contact = array_map('addslashes', $contact);

		$contact['email']=isset($contact['email']) ? $contact['email'] : '';
		$contact['first_name']=isset($contact['first_name']) ? $contact['first_name'] : '';
		$contact['middle_name']=isset($contact['middle_name']) ? $contact['middle_name'] : '';
		$contact['last_name']=isset($contact['last_name']) ? $contact['last_name'] : '';

		$sql = "SELECT id FROM ab_contacts WHERE ".
 		"addressbook_id='".$contact['addressbook_id']."' AND ".
 		"first_name='".$contact['first_name']."' AND ".
 		"middle_name='".$contact['middle_name']."' AND ".
 		"last_name='".$contact['last_name']."' AND ".
 		"email='".$contact['email']."'";

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

	function get_addressinfo($zip) {
		$zip = str_replace(' ', '', $zip);
		$sql = "SELECT * FROM ab_zipcodes WHERE zip LIKE '$zip%'";
		$this->query($sql);
		if ($this->next_record()) {
			return $this->Record;
		}
		return false;
	}



	function address_format($contact_id, $crlf='<br />') {
		if ($contact = $this->get_contact($contact_id)) {

			$name = format_name($contact['last_name'],$contact['first_name'],$contact['middle_name'], 'first_name');
			$address = $name.$crlf;

			if ($contact['address'] != '') {
				$address .= $contact['address'].' '.$contact['address_no'].$crlf;
			}
			if ($contact['zip'] != '') {
				$address .= $contact['zip'].' ';
			}
			if ($contact['city'] != '') {
				$address .= $contact['city'].$crlf;
			}
			if ($contact['country'] != '') {
				$address .= $contact['country'].$crlf;
			}
			return $address;
		}
		return false;
	}

	function company_post_address_format($company_id, $crlf='<br />') {
		if ($company = $this->get_company($company_id)) {

			$address = $company['name'].$crlf;

			if ($company['post_address'] != '') {
				$address .= $company['post_address'].' '.$company['post_address_no'].$crlf;
			}
			if ($company['post_zip'] != '') {
				$address .= $company['post_zip'].' ';
			}
			if ($company['post_city'] != '') {
				$address .= $company['post_city'].$crlf;
			}
			if ($company['post_country'] != '') {
				$address .= $company['post_country'].$crlf;
			}
			return $address;
		}
		return false;
	}


	function update_settings($settings)
	{
		if(!isset($settings['user_id']))
		{
			global $GO_SECURITY;
			$settings['user_id'] = $GO_SECURITY->user_id;
		}
		return $this->update_row('ab_settings', 'user_id', $settings);
	}

	function get_settings($user_id) {
		$this->query("SELECT * FROM ab_settings WHERE user_id='$user_id'");
		if ($this->next_record(MYSQL_ASSOC)) {
			return $this->Record;
		} else {
			$this->query("INSERT INTO ab_settings ".
 			"(user_id, search_type, search_contacts_field, "."search_companies_field, search_users_field) VALUES ".
 			"('$user_id', 'contact','', '', '')");
			return $this->get_settings($user_id);
		}
	}

	function select_contacts($target_field, $handler_file, $multiselect = 'true', $require_email_address = 'true', $show_users = 'true', $show_contacts = 'true', $show_companies = 'true', $show_mailings='true', $pass_value = 'email') {
		/*if (!$this->selector_is_enabled) {
		 die('FATAL ERROR: called select contacts while contact_selector is not'.'enabled. Call enable_contact_selector() first outside a form!');
		 } else {*/
		$link = "javascript:open_addressbook('$target_field',";
		if ($target_field != '') {
			$link .= $target_field.'.value';
		} else {
			$link .= "''";
		}
		$link .= ", '$handler_file', '$multiselect', '$require_email_address', ".
 		"'$show_users', '$show_contacts', '$show_companies', '$show_mailings', ".
 		"'$pass_value');";

		return $link;
		//}
	}

	function enable_contact_selector() {
		global $GO_CONFIG;

		return '<form name="ab_form" action="'.$GO_CONFIG->control_url.'select/select.php" method="post" target="ab_select">
<input type="hidden" name="address_string" />
<input type="hidden" name="GO_HANDLER" />
<input type="hidden" name="GO_FIELD" />
<input type="hidden" name="multiselect" />
<input type="hidden" name="require_email_address" />
<input type="hidden" name="show_users" />
<input type="hidden" name="show_contacts" />
<input type="hidden" name="show_companies" />
<input type="hidden" name="show_mailings" />
<input type="hidden" name="pass_value" />
</form>		
<script type="text/javascript">
function open_addressbook(field, addresses, handler_file, multiselect, require_email_address, show_users, show_contacts, show_companies, show_mailings, pass_value)
{
var width = 620;
var height = 400;
var centered;
x = (screen.availWidth - width) / 2;
y = (screen.availHeight - height) / 2;
centered = ",width=" + width + ",height=" + height + ",left=" + x + ",top=" + y + ",scrollbars=yes,resizable=yes,status=no";
var popup = window.open("about:blank", "ab_select", centered);

document.ab_form.address_string.value = addresses;
document.ab_form.GO_HANDLER.value = handler_file;
document.ab_form.GO_FIELD.value = field;
document.ab_form.multiselect.value = multiselect;
document.ab_form.require_email_address.value = require_email_address;
document.ab_form.show_users.value = show_users;
document.ab_form.show_contacts.value = show_contacts;
document.ab_form.show_companies.value = show_companies;
document.ab_form.show_mailings.value = show_mailings;
document.ab_form.pass_value.value = pass_value;
document.ab_form.submit();

if (!popup.opener) popup.opener = self;
popup.focus();
}
</script>';
		$this->selector_is_enabled = true;
	}




	function get_addressbooks() {
		$sql = "SELECT * FROM ab_addressbooks ORDER BY name ASC";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_user_addressbooks($user_id, $start=0, $offset=0) {
		$sql = "SELECT DISTINCT ab_addressbooks.* ".
 		"FROM ab_addressbooks ".
 		"	INNER JOIN acl ON (ab_addressbooks.acl_read = acl.acl_id ".
 		"OR ab_addressbooks.acl_write = acl.acl_id) ".
 		"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id ".
 		"WHERE acl.user_id=$user_id ".
 		"OR users_groups.user_id=$user_id ".
 		"ORDER BY ab_addressbooks.name ASC";
		$this->query($sql);
		$count= $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT $start, $offset";
			$this->query($sql);
		}
		return $count;
	}

	function get_writable_addressbooks($user_id, $start=0, $offset=0) {
		$sql = "SELECT DISTINCT ab_addressbooks.* ".
 		"FROM ab_addressbooks ".
 		"	INNER JOIN acl ON ab_addressbooks.acl_write = acl.acl_id ".
 		"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id ".
 		"WHERE acl.user_id=$user_id ".
 		"OR users_groups.user_id=$user_id ".
 		"ORDER BY ab_addressbooks.name ASC";
		$this->query($sql);
		$count= $this->num_rows();
		if($offset>0)
		{
			$sql .= " LIMIT $start, $offset";
			$this->query($sql);
		}
		return $count;
	}

	function get_first_writable_addressbook($user_id)
	{
		global $GO_SECURITY;

		$sql = "SELECT DISTINCT ab_addressbooks.* ".
 		"FROM ab_addressbooks ".
 		"	INNER JOIN acl ON ab_addressbooks.acl_write = acl.acl_id ".
 		"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id ".
 		"WHERE acl.user_id=".$GO_SECURITY->user_id." ".
 		"OR users_groups.user_id=".$GO_SECURITY->user_id." ".
 		"ORDER BY ab_addressbooks.name ASC";
		$this->query($sql);

		if($this->next_record())
		{
			return $this->Record;
		}
		return $this->get_addressbook();
	}

	function add_addressbook($user_id, $name) {
		global $GO_SECURITY;

		$addressbook_id = $this->nextid('ab_addressbooks');
		$acl_read = $GO_SECURITY->get_new_acl('acl_read addressbook_id: '.$addressbook_id);
		$acl_write = $GO_SECURITY->get_new_acl('acl_write addressbook_id: '.$addressbook_id);

		if ($addressbook_id > 0 && $acl_read > 0 && $acl_write > 0) {
			$sql = "INSERT INTO ab_addressbooks (id, user_id, name, acl_read, acl_write) VALUES ('$addressbook_id', '$user_id', '$name', '$acl_read', '$acl_write')";
			if ($this->query($sql)) {
				$GO_SECURITY->add_user_to_acl($user_id, $acl_write);

				return $addressbook_id;
			}
		} else {
			$GO_SECURITY->delete_acl($acl_read);
			$GO_SECURITY->delete_acl($acl_write);
		}
		return false;
	}

	function update_addressbook($addressbook_id, $user_id, $name) {
		$sql = "UPDATE ab_addressbooks SET name='$name', user_id='$user_id' WHERE id='$addressbook_id'";
		return $this->query($sql);
	}

	function get_addressbook_by_name($name) {
		$sql = "SELECT * FROM ab_addressbooks WHERE name='$name'";
		$this->query($sql);
		if ($this->next_record()) {
			return $this->Record;
		} else {
			return false;
		}
	}

	function delete_addressbook($addressbook_id) {

		if($addressbook = $this->get_addressbook($addressbook_id))
		{
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

			$sql = "DELETE FROM ab_addressbooks WHERE id='$addressbook_id'";
			return $this->query($sql);
		}
		return false;
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
				$new_ab_name = format_name($user['last_name'], $user['first_name'], $user['middle_name'], 'last_name');
				$x = 1;
				while ($this->get_addressbook_by_name(addslashes($new_ab_name))) {
					$new_ab_name = $_SESSION['GO_SESSION']['name'].' ('.$x.')';
					$x ++;
				}
				if(!$addressbook_id = $this->add_addressbook($GO_SECURITY->user_id, addslashes($new_ab_name)))
				{
					return false;
				}
			}
		}
		$sql = "SELECT * FROM ab_addressbooks WHERE id='$addressbook_id'";
		$this->query($sql);
		if ($this->next_record()) {
			return $this->Record;
		}else
		{
			return $this->get_addressbook();
		}
	}

	function get_user_addressbook_ids($user_id)
	{
		if(!isset($_SESSION['GO_SESSION'][$user_id]['authorized_addressbooks']))
		{
			$_SESSION['GO_SESSION'][$user_id]['authorized_addressbooks'] = array();
			$this->get_user_addressbooks($user_id);
			while($this->next_record())
			{
				$_SESSION['GO_SESSION'][$user_id]['authorized_addressbooks'][] = $this->f('id');
			}
		}
		return $_SESSION['GO_SESSION'][$user_id]['authorized_addressbooks'];
	}

	function get_writable_addressbook_ids($user_id)
	{
		if(!isset($_SESSION['GO_SESSION'][$user_id]['writable_addressbooks']))
		{
			$_SESSION['GO_SESSION'][$user_id]['writable_addressbooks'] = array();
			$this->get_writable_addressbooks($user_id);
			while($this->next_record())
			{
				$_SESSION['GO_SESSION'][$user_id]['writable_addressbooks'][] = $this->f('id');
			}
		}
		return $_SESSION['GO_SESSION'][$user_id]['writable_addressbooks'];
	}


	function check_contact($user_id, $contact) {

		$sql = "SELECT id FROM ab_contacts ";

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
		$sql .=	"((first_name='".$contact['first_name']."' AND middle_name='".$contact['middle_name']."' AND last_name='".$contact['last_name']."')";

		if ($contact['email'] != '') {
			$sql .= " OR email='".$contact['email']."' OR email2='".$contact['email']."' OR email3='".$contact['email']."'";
		}
		if ($contact['zip'] != '' && $contact['address_no'] != '') {
			$sql .= " OR (zip='".$contact['zip']."' AND address_no='".$contact['address_no']."')";
		}
		$sql .= ");";
		$this->query($sql);

		if ($this->next_record()) {
			return $this->f('id');
		}
		return false;
	}

	function check_company($user_id, $company) {
		$sql = "SELECT id FROM ab_companies ";

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

		$sql .= "(name='".$company['name']."'";

		if ($company['email'] != '') {
			$sql .= " OR email='".$company['email']."' ";
		}

		if ($company['zip'] != '' && $company['address_no'] != '') {
			$sql .= " OR (zip='".$company['zip']."' AND address_no='".$company['address_no']."')";
		}

		$sql .= ");";
		$this->query($sql);

		if ($this->next_record()) {
			return $this->f('id');
		}
		return false;
	}

	function search_email($user_id, $query)
	{

		$query = str_replace(' ','%', $query);

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
		$sql .= "(CONCAT(first_name,middle_name,last_name) LIKE '".$query."' OR email LIKE '$query' OR email2 LIKE '$query' OR email3 LIKE '$query')";

		if ($_SESSION['GO_SESSION']['sort_name'] == 'first_name') {
			$sort_index = 'ab_contacts.first_name ASC, ab_contacts.last_name';
		} else {
			$sort_index = 'ab_contacts.last_name ASC, ab_contacts.first_name';
		}

		$sql .= " ORDER BY $sort_index ASC LIMIT 0,10";

		$this->query($sql);
	}






	function search_contacts($user_id, $query, $field = 'last_name', $addressbook_id = 0, $mtime = 0, $start=0, $offset=0, $require_email=false, $sort_index='name', $sort_order='ASC', $writable_only=false) {
		global $GO_MODULES;
		$query = str_replace('*', '%', $query);

		if($sort_index=='name')
		{
			if ($_SESSION['GO_SESSION']['sort_name'] == 'first_name') {
				$sort_index = 'ab_contacts.first_name ASC, ab_contacts.last_name';
			} else {
				$sort_index = 'ab_contacts.last_name ASC, ab_contacts.first_name';
			}
		}

		$sql = "SELECT ab_contacts.*, ab_companies.name AS company_name FROM ab_contacts ".
 		"LEFT JOIN ab_companies ON ab_contacts.company_id=ab_companies.id ";

		if(isset($GO_MODULES->modules['custom_fields']) && $GO_MODULES->modules['custom_fields']['read_permission'])
		{
			$sql .= "LEFT JOIN cf_2 ON cf_2.link_id=ab_contacts.link_id ";
		}


		if ($addressbook_id > 0) {
			$sql .= "WHERE ab_contacts.addressbook_id='$addressbook_id' AND ";
		} else {

			if($writable_only)
			{
				$user_ab = $this->get_writable_addressbook_ids($user_id);
			}else {
				$user_ab = $this->get_user_addressbook_ids($user_id);
			}
			if(count($user_ab) > 1)
			{
				$sql .= "WHERE ab_contacts.addressbook_id IN (".implode(",",$user_ab).") AND ";
			}elseif(count($user_ab)==1)
			{
				$sql .= "WHERE ab_contacts.addressbook_id=".$user_ab[0]." AND ";
			}else
			{
				return false;
			}
		}

		if($query!='')
		{
			if(!is_array($field))
			{
				$fields=array();
				if($field == '')
				{
					$fields_sql = "SHOW FIELDS FROM ab_contacts";
					$this->query($fields_sql);
					while($this->next_record())
					{
						if(eregi('varchar', $this->f('Type')))
						{
							$fields[]='ab_contacts.'.$this->f('Field');
						}
					}
					if(isset($GO_MODULES->modules['custom_fields']) && $GO_MODULES->modules['custom_fields']['read_permission'])
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
					$sql .= "CONCAT(first_name,middle_name,last_name) LIKE '".str_replace(' ','%', $query)."' ";
				}else
				{
					$sql .= "$field LIKE '$query' ";
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

		$sql .= " ORDER BY $sort_index $sort_order";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset > 0)
		{
			$sql .= " LIMIT $start, $offset";
			$this->query($sql);
			return $count;
		}else
		{
			return $count;
		}
	}

	function get_contacts_by_birthday($interval_start, $interval_end) {

	}

	function search_companies($user_id, $query, $field = 'name', $addressbook_id = 0, $start=0, $offset=0, $require_email=false, $sort_index='name', $sort_order='ASC') {
		global $GO_MODULES;

		$query = str_replace('*', '%', $query);



		if(isset($GO_MODULES->modules['custom_fields']))
		{
			$sql = "SELECT ab_companies.*, cf_3.* FROM ab_companies ".
 			"LEFT JOIN cf_3 ON cf_3.link_id=ab_companies.link_id ";
		}else {
			$sql = "SELECT ab_companies.* FROM ab_companies ";
		}

		if ($addressbook_id > 0) {
			$sql .= "WHERE ab_companies.addressbook_id='$addressbook_id' AND ";
		} else {

			$user_ab = $this->get_user_addressbook_ids($user_id);
			if(count($user_ab) > 1)
			{
				$sql .= "WHERE ab_companies.addressbook_id IN (".implode(",",$user_ab).") AND ";
			}elseif(count($user_ab)==1)
			{
				$sql .= "WHERE ab_companies.addressbook_id=".$user_ab[0]." AND ";
			}else
			{
				return false;
			}
		}

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
					$sql .= "ab_companies.".$this->f('Field')." LIKE '$query'";
				}
			}
			if(isset($GO_MODULES->modules['custom_fields']) && $GO_MODULES->modules['custom_fields']['read_permission'])
			{
				$fields_sql = "SHOW FIELDS FROM cf_3";
				$this->query($fields_sql);
				while ($this->next_record()) {
					if (eregi('varchar', $this->f('Type')) || $this->f('Field')=='id') {
						if (isset ($first)) {
							$sql .= ' OR ';
						} else {
							$first = true;
							$sql .= '(';
						}
						$sql .= "cf_3.".$this->f('Field')." LIKE '$query'";
					}
				}

			}
			$sql .= ')';
		} else {
			$sql .= "$field LIKE '$query' ";
		}

		if($require_email)
		{
			$sql .= " AND ab_companies.email != ''";
		}

		$sql .= "ORDER BY $sort_index $sort_order";

		$this->query($sql);
		$count = $this->num_rows();

		if($offset > 0 )
		{
			$sql .= " LIMIT $start, $offset";
			$this->query($sql);
			return $count;
		}else
		{
			return $count;
		}
	}

	function get_contacts($addressbook_id=0, $sort = "name", $direction = "ASC", $start=0, $offset=0, $links=false) {
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
			$sql .= " WHERE ab_contacts.addressbook_id='$addressbook_id'";
		}

		if(is_array($links))
		{
			if(!count($links))
			{
				return 0;
			}
			if($addressbook_id>0)
			{
				$sql .= " AND ";
			}else
			{
				$sql .= " WHERE ";
			}
			$sql .= "link_id IN (".implode(',', $links).")";
		}
		$sql .= 	" ORDER BY $sort $direction";

		$this->query($sql);
		$count =  $this->num_rows();
		if ($offset != 0 && $count > $offset) {
			$sql .= " LIMIT $start, $offset";
			$this->query($sql);
		}
		return $count;
	}

	function get_user_contacts($user_id, $addressbook_id) {
		$this->query("SELECT * FROM ab_contacts WHERE addressbook_id='$addressbook_id' AND user_id='$user_id'");
		return $this->num_rows();
	}

	function get_user_companies($user_id, $addressbook_id) {
		$this->query("SELECT * FROM ab_companies WHERE addressbook_id='$addressbook_id' AND user_id='$user_id'");
		return $this->num_rows();
	}

	function get_company_contacts($company_id, $sort = "name", $direction = "ASC", $start, $offset) {
		if ($sort == 'name') {
			if ($_SESSION['GO_SESSION']['sort_name'] == 'first_name') {
				$sort = 'first_name '.$direction.', last_name';
			} else {
				$sort = 'last_name '.$direction.', first_name';
			}

			//	  $sort = 'first_name '.$direction.', last_name';
		}
		$sql = "SELECT * FROM ab_contacts WHERE company_id='$company_id' ORDER BY $sort $direction";

		if ($offset != 0) {
			$sql .= " LIMIT $start, $offset";

			$sql2 = "SELECT * FROM ab_contacts WHERE company_id='$company_id'";

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

	function get_contacts_for_export($addressbook_id, $user_id = 0) {
		global $GO_SECURITY;

		if ($user_id == 0) {
			$user_id = $GO_SECURITY->user_id;
		}
		$sql = "SELECT ab_contacts.*,".
 		"ab_companies.name AS company FROM ab_contacts ".
 		"LEFT JOIN ab_companies ON (ab_contacts.company_id=ab_companies.id) ".
 		" WHERE ab_contacts.addressbook_id='$addressbook_id' ".
 		"ORDER BY ab_contacts.first_name, ab_contacts.last_name ASC";

		$this->query($sql);
		return $this->num_rows();
	}

	function get_contacts_group($addressbook_id, $group_id, $sort = "name", $direction = "ASC", $user_id = 0) {
		global $GO_SECURITY;
		if ($user_id == 0) {
			$user_id = $GO_SECURITY->user_id;
		}
		if ($sort == 'name') {
			if ($_SESSION['GO_SESSION']['sort_name'] == 'first_name') {
				$sort = 'first_name '.$direction.', middle_name, last_name';
			} else {
				$sort = 'last_name '.$direction.', first_name, middle_name';
			}

			//      $sort = 'first_name '.$direction.', last_name';
		}
		$sql = "SELECT ab_contacts.* FROM ab_contacts "."WHERE ab_contacts.addressbook_id='$addressbook_id' "."AND ab_contacts.group_id='$group_id' "."ORDER BY $sort $direction";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_contact_by_name($addressbook_id, $name) {
		$sql = "SELECT * FROM ab_contacts WHERE addressbook_id='$addressbook_id' AND name='$name'";
		$this->query($sql);
		if ($this->next_record()) {
			return $this->Record;
		}
		return false;
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
 		"WHERE ab_contacts.id='$contact_id'");

		if ($this->next_record(MYSQL_ASSOC)) {
			return $this->Record;
		}
		return false;
	}

	function get_empty_contact()
	{
		$sql = "SHOW FIELDS FROM ab_contacts";
		$this->query($sql);
		while($this->next_record())
		{
			$contact[$this->Field] = '';
		}
		return $contact;
	}

	function get_empty_company()
	{
		$sql = "SHOW FIELDS FROM ab_companies";
		$this->query($sql);
		while($this->next_record())
		{
			$company[$this->Field] = '';
		}
		return $company;
	}

	function add_contact($contact) {

		if (!isset($contact['user_id']) || $contact['user_id'] == 0) {
			global $GO_SECURITY;
			$contact['user_id'] = $GO_SECURITY->user_id;
		}

		if (!isset($contact['ctime']) || $contact['ctime'] == 0) {
			$contact['ctime'] = get_gmt_time();
		}
		if (!isset($contact['mtime']) || $contact['mtime'] == 0) {
			$contact['mtime'] = $contact['ctime'];
		}

		if (isset($contact['sex']) && $contact['sex'] == '') {
			$contact['sex'] = 'M';
		}

		if(empty($contact['link_id']))
		{
			$contact['link_id']=$GLOBALS['GO_LINKS']->get_link_id();
		}
		$contact['id'] = $this->nextid("ab_contacts");

		if ($contact['id']  > 0) {

			if ($this->insert_row('ab_contacts', $contact)) {
				return $contact['id'];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function update_contact($contact)
	{
		if (!isset($contact['mtime']) || $contact['mtime'] == 0) {
			$contact['mtime'] = get_gmt_time();
		}

		if (isset($contact['sex']) && $contact['sex'] == '') {
			$contact['sex'] = 'M';
		}

		return $this->update_row('ab_contacts', 'id', $contact);
	}

	function delete_contact($contact_id) {

		global $GO_CONFIG,$GO_LINKS;

		$contact=$this->get_contact($contact_id);

		$GO_LINKS->delete_link($contact['link_id']);

		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem(true);
		$fs->delete($GO_CONFIG->file_storage_path.'contacts/'.$contact_id.'/');

		$sql1 = "DELETE FROM tp_mailing_contacts WHERE contact_id='$contact_id'";

		if ($this->query($sql1)) {
			return $this->query("DELETE FROM ab_contacts WHERE id='$contact_id'");
		}
	}

	function user_is_contact($user_id, $id) {

		$sql = "SELECT ab_contacts.* FROM ab_contacts ";

		$count = $this->get_user_addressbooks($user_id);
		if($count > 1)
		{
			while($this->next_record())
			{
				$user_ab[] = $this->f('id');
			}
			$sql .= "WHERE ab_contacts.addressbook_id IN (".implode(",",$user_ab).") AND ";
		}elseif(	$this->next_record())
		{
			$sql .= "WHERE ab_contacts.addressbook_id=".$this->f('id')." AND ";
		}else
		{
			return false;
		}
		$sql .= 	"ab_contacts.source_id='$id'";

		$this->query($sql);
		if ($this->next_record()) {
			return $this->Record;
		} else {
			return false;
		}
	}

	function user_is_in_addressbook($user_id, $addressbook_id) {
		$sql = "SELECT id FROM ab_contacts WHERE source_id='$user_id' AND addressbook_id='$addressbook_id'";
		$this->query($sql);
		return $this->next_record();
	}


	function get_contact_by_email($email, $user_id) {
		$email = get_email_from_string($email);
		$sql = "SELECT * FROM ab_contacts ";

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
		$sql .= " (email='$email' OR email2='$email' OR email3='$email')";

		$this->query($sql);
		if ($this->next_record())
		return $this->Record;
		else
		return false;
	}






	function move_company_to_addressbook($company_id, $addressbook_id) {
		$sql = "UPDATE ab_companies SET mtime='".get_gmt_time()."', addressbook_id='$addressbook_id' WHERE id='$company_id'";
		$this->query($sql);
	}

	function move_contact_to_addressbook($contact_id, $addressbook_id) {
		$sql = "UPDATE ab_contacts SET mtime='".get_gmt_time()."', addressbook_id='$addressbook_id' WHERE id='$contact_id'";
		$this->query($sql);
	}






	function add_contact_to_company($contact_id, $company_id) {
		return $this->query("UPDATE ab_contacts SET company_id='$company_id' WHERE id='$contact_id'");
	}

	function add_company($company) {

		if (!isset($company['user_id']) || $company['user_id'] == 0) {
			global $GO_SECURITY;
			$company['user_id'] = $GO_SECURITY->user_id;
		}

		if (!isset($company['ctime']) || $company['ctime'] == 0) {
			$company['ctime'] = get_gmt_time();
		}
		if (!isset($company['mtime']) || $company['mtime'] == 0) {
			$company['mtime'] = $company['ctime'];
		}

		if(!isset($company['link_id']))
		{
			global $GO_LINKS;
			$company['link_id']=$GO_LINKS->get_link_id();
		}

		$company['id'] = $this->nextid("ab_companies");
		if ($company['id']  > 0) {

			if ($this->insert_row('ab_companies', $company)) {
				return $company['id'];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function update_company($company)
	{
		if (!isset($company['mtime']) || $company['mtime'] == 0) {
			$company['mtime'] = get_gmt_time();
		}
		return $this->update_row('ab_companies', 'id', $company);
	}

	function get_company_by_name($addressbook_id, $name) {
		$sql = "SELECT * FROM ab_companies WHERE addressbook_id='$addressbook_id' AND name='$name'";
		$this->query($sql);
		if ($this->next_record()) {
			return $this->Record;
		}
		return false;
	}

	function get_company($company_id) {
		$sql = "SELECT ab_companies.*, ab_addressbooks.acl_read, ".
 		"ab_addressbooks.acl_write FROM ab_companies ".
 		"INNER JOIN ab_addressbooks ON ".
 		"(ab_addressbooks.id=ab_companies.addressbook_id) ".
 		"WHERE ab_companies.id='$company_id'";
		$this->query($sql);
		if ($this->next_record()) {
			return $this->Record;
		}
		return false;
	}

	function get_companies($addressbook_id=0, $sort = 'name', $direction = 'ASC', $start = 0, $offset = 0, $links=false) {
		global $GO_SECURITY;

		$sql = "SELECT ab_companies.* FROM ab_companies";

		if($addressbook_id > 0)
		{
			$sql .= " WHERE addressbook_id='$addressbook_id'";
		}

		if(is_array($links))
		{
			if(!count($links))
			{
				return 0;
			}
			if($addressbook_id>0)
			{
				$sql .= " AND ";
			}else
			{
				$sql .= " WHERE ";
			}
			$sql .= "link_id IN (".implode(',', $links).")";
		}
		$sql .= " ORDER BY $sort $direction";
		$this->query($sql);
		$count = $this->num_rows();

		if ($offset != 0 && $count > $offset) {
			$sql .= " LIMIT $start, $offset";
			$this->query($sql);
		}
		return $count;
	}

	function search_company($name, $addressbook_id) {
		$sql = "SELECT * FROM ab_companies WHERE addressbook_id='$addressbook_id' AND name LIKE '%$name%'";
		$this->query($sql);
		return $this->num_rows();
	}

	function get_company_id_by_name($name, $addressbook_id) {
		$sql = "SELECT id FROM ab_companies WHERE addressbook_id='$addressbook_id' AND name='$name'";
		$this->query($sql);
		if ($this->next_record()) {
			return $this->f('id');
		}
		return false;
	}

	function delete_company($company_id) {
		global $GO_CONFIG, $GO_LINKS;

		$company=$this->get_contact($company_id);

		$GO_LINKS->delete_link($company['link_id']);

		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem(true);
		$fs->delete($GO_CONFIG->file_storage_path.'companies/'.$company_id.'/');

		$sql = "DELETE FROM ab_companies WHERE id='$company_id'";
		if ($this->query($sql)) {
			return true;
		}
	}



	function consistencycheck($fixit = false) {
		global $GO_USERS, $GO_SECURITY;

		echo "<h2>checking ab_addressbooks</h2>";
		$sql = "SELECT * FROM ab_addressbooks";
		$this->query($sql);
		while ($this->next_record()) {
			if (!($GO_USERS->get_user($this->f('user_id')))) {
				echo "owner of ".$this->f('id')." does not exist (".$this->f('user_id').")<br>";
			}
			if (!($GO_SECURITY->acl_exists($this->f('acl_read')))) {
				echo "aclid(read) ".$this->f('acl_read')." of ".$this->f('id')." does not exist<br>";
			}
			if (!($GO_SECURITY->acl_exists($this->f('acl_write')))) {
				echo "aclid(write) ".$this->f('acl_write')." of ".$this->f('id')." does not exist<br>";
			}
		}

		echo "<h2>checking ab_companies</h2>";
		$sql = "SELECT * FROM ab_companies";
		$this->query($sql);
		while ($this->next_record()) {
			if (!($GO_USERS->get_user($this->f('user_id')))) {
				echo "owner of ".$this->f('id')."does not exist (".$this->f('user_id').")<br>";
			}
		}

		echo "<h2>checking ab_contacts</h2>";
		$sql = "SELECT * FROM ab_contacts";
		$this->query($sql);
		while ($this->next_record()) {
			if (!($GO_USERS->get_user($this->f('user_id')))) {
				echo "owner of ".$this->f('id')."does not exist (".$this->f('user_id').")<br>";
			}
		}



		echo "<h2>checking ab_settings</h2>";
		$sql = "SELECT * FROM ab_settings";
		$this->query($sql);
		while ($this->next_record()) {
			if (!($GO_USERS->get_user($this->f('user_id')))) {
				echo "owner of ".$this->f('id')."does not exist (".$this->f('user_id').")<br>";
			}
		}
	}




	function __on_search($user_id,$last_sync_time=0)
	{
		global $GO_MODULES, $GO_LANGUAGE;

		require($GO_LANGUAGE->get_language_file('addressbook'));

		$sql = "SELECT * FROM ab_contacts ";
		$user_ab = $this->get_user_addressbook_ids($user_id);
		if(count($user_ab) > 1)
		{
			$sql .= "WHERE ab_contacts.addressbook_id IN (".implode(",",$user_ab).") AND ";
		}elseif(count($user_ab)==1)
		{
			$sql .= "WHERE ab_contacts.addressbook_id=".$user_ab[0]." AND ";
		}else
		{
			return false;
		}

		$sql .= "mtime>$last_sync_time";

		$this->query($sql);
			
		$search = new search();

		$db = new db();
		while($this->next_record())
		{
			$cache['table']='ab_contacts';
			$cache['id']=$this->f('id');
			$cache['user_id']=$user_id;
			$cache['name'] = addslashes(format_name($this->f('last_name'),$this->f('first_name'),$this->f('middle_name')));
			$cache['link_id'] = $this->f('link_id');
			$cache['link_type']=2;
			$cache['description']='';
			$cache['url']=$GO_MODULES->modules['addressbook']['url'].'contact.php?contact_id='.$this->f('id');
			$cache['type']=$ab_contact;
			$cache['keywords']=addslashes(record_to_keywords($this->Record)).','.$ab_contact;
			$cache['mtime']=$this->f('mtime');
			if($search->get_search_result($user_id, $this->f('link_id')))
			{
				$db->update_row('se_cache',array('user_id','link_id'), $cache);
			}else {
				$db->insert_row('se_cache',$cache);
			}
		}


		$sql = "SELECT * FROM ab_companies ";

		if(count($user_ab) > 1)
		{
			$sql .= "WHERE ab_companies.addressbook_id IN (".implode(",",$user_ab).") AND ";
		}elseif(count($user_ab)==1)
		{
			$sql .= "WHERE ab_companies.addressbook_id=".$user_ab[0]." AND ";
		}else
		{
			return false;
		}

		$sql .= "mtime>$last_sync_time";

		$this->query($sql);

		$db = new db();
		while($this->next_record())
		{
			$cache['table']='ab_companies';
			$cache['id']=$this->f('id');
			$cache['user_id']=$user_id;
			$cache['name'] = addslashes($this->f('name'));
			$cache['link_id'] = $this->f('link_id');
			$cache['link_type']=3;
			$cache['description']='';
			$cache['url']=$GO_MODULES->modules['addressbook']['url'].'company.php?company_id='.$this->f('id');
			$cache['type']=$ab_company;
			$cache['keywords']=addslashes(record_to_keywords($this->Record)).','.$cache['type'];
			$cache['mtime']=$this->f('mtime');
				
			if($search->get_search_result($user_id, $this->f('link_id')))
			{
				$db->update_row('se_cache',array('user_id','link_id'), $cache);
			}else {
				$db->insert_row('se_cache',$cache);
			}
		}
	}


	function __on_user_delete($user_id) {
		$sql = "UPDATE ab_contacts SET source_id='0' WHERE source_id='$user_id'";
		$this->query($sql);

		$ab = new addressbook();

		$sql = "SELECT id FROM ab_addressbooks WHERE user_id='$user_id'";
		$this->query($sql);
		while ($this->next_record()) {
			$ab->delete_addressbook($this->f('id'));
		}

		$this->query("DELETE FROM ab_settings WHERE user_id='$user_id'");

		global $GO_MODULES;

		if($plugin = $GO_MODULES->get_plugin('templates','addressbook'))
		{
			require_once($plugin['class_path'].'templates.class.inc');
			$tp = new templates();
			$tp->delete_user($user_id);
		}
	}
}

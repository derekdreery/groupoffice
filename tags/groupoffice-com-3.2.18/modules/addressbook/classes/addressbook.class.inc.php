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
        $events->add_listener('add_user', __FILE__, 'addressbook', 'add_user');
        $events->add_listener('build_search_index', __FILE__, 'addressbook', 'build_search_index');
        $events->add_listener('check_database', __FILE__, 'addressbook', 'check_database');
    }
    public static function check_database(){
        global $GO_CONFIG, $GO_MODULES, $GO_LANGUAGE;

        $line_break=php_sapi_name() != 'cli' ? '<br />' : "\n";

        echo 'Addressbook folders'.$line_break;

        if(isset($GO_MODULES->modules['files']))
        {
            $ab = new addressbook();
            $db = new db();

            require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
            $files = new files();

            $sql = "SELECT * FROM ab_addressbooks";
            $db->query($sql);
            while($addressbook = $db->next_record())
            {
                try{
                    $files->check_share('contacts/'.File::strip_invalid_chars($addressbook['name']), $addressbook['user_id'], $addressbook['acl_read'], $addressbook['acl_write'], false);
                    $files->check_share('companies/'.File::strip_invalid_chars($addressbook['name']), $addressbook['user_id'], $addressbook['acl_read'], $addressbook['acl_write'], false);
                }
                catch(Exception $e){
                    echo $e->getMessage().$line_break;
                }
            }
            flush();

            $db->query("SELECT c.*,a.name AS addressbook_name,a.acl_read,a.acl_write FROM ab_contacts c INNER JOIN ab_addressbooks a ON a.id=c.addressbook_id");
            while($contact = $db->next_record())
            {
                try{
                    $path = $ab->build_contact_files_path($contact, array('name'=>$contact['addressbook_name']));
                    echo $path.$line_break;
                    $up_contact['files_folder_id']=$files->check_folder_location($contact['files_folder_id'], $path);

                    if($up_contact['files_folder_id']!=$contact['files_folder_id']){
                        $up_contact['id']=$contact['id'];
                        $ab->update_row('ab_contacts', 'id', $up_contact);
                    }

                    $files->set_readonly($up_contact['files_folder_id']);
                }
                catch(Exception $e){
                    echo $e->getMessage().$line_break;
                }
                flush();
            }

            $db->query("SELECT c.*,a.name AS addressbook_name,a.acl_read,a.acl_write FROM ab_companies c INNER JOIN ab_addressbooks a ON a.id=c.addressbook_id");
            while($company = $db->next_record())
            {
                try{
                    $path = $ab->build_company_files_path($company, array('name'=>$company['addressbook_name']));
                    $up_company['files_folder_id']=$files->check_folder_location($company['files_folder_id'], $path);

                    if($up_company['files_folder_id']!=$company['files_folder_id']){
                        $up_company['id']=$company['id'];
                        $ab->update_row('ab_companies', 'id', $up_company);
                    }
                    $files->set_readonly($up_company['files_folder_id']);
                }
                catch(Exception $e){
                    echo $e->getMessage().$line_break;
                }
                flush();
            }

        }
        echo 'Done with addressbook'.$line_break.$line_break;

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

    public static function add_user($user){
        $ab = new addressbook();
        $ab->create_default_addressbook($user);
    }

    function create_default_addressbook($user){
        $name = String::format_name($user);
        $new_ab_name = $name;
        $x = 1;
        while ($this->get_addressbook_by_name($new_ab_name)) {
            $new_ab_name = $name.' ('.$x.')';
            $x ++;
        }
        $addressbook = $this->add_addressbook($user['id'], $new_ab_name);
        $addressbook=$addressbook['id'];
        return $addressbook;
    }

    function get_addressbook($addressbook_id=0, $user_addressbook=false) {
        if($addressbook_id == 0)
        {
            global $GO_SECURITY, $GO_USERS;

            if($user_addressbook)
            {
                $sql = "SELECT * FROM ab_addressbooks WHERE user_id=".$GO_SECURITY->user_id." ORDER BY id ASC";
                $this->query($sql);
            }else
            {
                $this->get_writable_addressbooks($GO_SECURITY->user_id);
            }

            if($this->next_record())
            {
                $addressbook_id = $this->f('id');
            }else
            {
                $user = $GO_USERS->get_user($GO_SECURITY->user_id);
                $addressbook = $this->create_default_addressbook($user);
                $addressbook_id=$addressbook['id'];
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

    function add_company($company, $addressbook=false) {

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

        global $GO_MODULES;
        if(!isset($company['files_folder_id']) && isset($GO_MODULES->modules['files']))
        {
            global $GO_CONFIG;

            if(!$addressbook)
            {
                $addressbook = $this->get_addressbook($company['addressbook_id']);
            }
            require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
            $files = new files();

            $new_path = $this->build_company_files_path($company, $addressbook);
            if($folder=$files->create_unique_folder($new_path))
            {
                $company['files_folder_id']=$folder['id'];
            }
        }

        $company['id'] = $this->nextid("ab_companies");
        $this->insert_row('ab_companies', $company);
        $this->cache_company($company['id']);

        return $company['id'];
    }

    function update_company($company, $addressbook=false, $old_company=false)
    {
    	
        if (!isset($company['mtime']) || $company['mtime'] == 0) {
            $company['mtime'] = time();
        }

        if(!$old_company)
        {
            $old_company = $this->get_company($company['id']);
        }

        global $GO_MODULES;

        if(isset($GO_MODULES->modules['files']) && isset($company['addressbook_id']))
        {
            if(!$addressbook)
            {
                $addressbook = $this->get_addressbook($company['addressbook_id']);
            }
            require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
            $files = new files();



            $new_path = $this->build_company_files_path($company, $addressbook);
            $company['files_folder_id']=$files->check_folder_location($old_company['files_folder_id'], $new_path);
        }

        $r = $this->update_row('ab_companies', 'id', $company);

        if(isset($company['addressbook_id']) && $old_company['addressbook_id'] != $company['addressbook_id'])
        {
            $this->move_contacts_company($company['id'], $old_company['addressbook_id'], $company['addressbook_id']);
        }

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
        global $GO_CONFIG, $GO_LINKS,$GO_MODULES;

        if(isset($GO_MODULES->modules['files']))
        {
            $company=$this->get_company($company_id);
            require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
            $files = new files();
            try{
                $files->delete_folder($company['files_folder_id']);
            }catch(Exception $e){}
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

    function add_contact(&$contact, $addressbook=false) {

        global $GO_MODULES;

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

        if(!isset($contact['files_folder_id']) && isset($GO_MODULES->modules['files']))
        {
            global $GO_CONFIG;

            if(!$addressbook)
            {
                $addressbook = $this->get_addressbook($contact['addressbook_id']);
            }
            require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
            $files = new files();

            $new_path = $this->build_contact_files_path($contact, $addressbook);
            if($folder=$files->create_unique_folder($new_path))
            {
							$contact['files_folder_id']=$folder['id'];
            }
        }

        $contact['id'] = $this->nextid("ab_contacts");
        $this->insert_row('ab_contacts', $contact);
        $this->cache_contact($contact['id']);
        return $contact['id'];
    }

    function build_contact_files_path($contact, $addressbook)
    {
        $new_folder_name = File::strip_invalid_chars(String::format_name($contact));
        $last_part = $this->get_index_char($contact['last_name']);
        $new_path = 'contacts/'.File::strip_invalid_chars($addressbook['name']);
        if(!empty($last_part))
        {
            $new_path .= '/'.$last_part;
        }else
				{
					$new_path .= '/0 no last name';
				}
        $new_path .= '/'.$new_folder_name;
        return $new_path;
    }

    function build_company_files_path($company, $addressbook)
    {
        $new_folder_name = File::strip_invalid_chars($company['name']);
        $last_part = $this->get_index_char($company['name']);
        $new_path = 'companies/'.File::strip_invalid_chars($addressbook['name']);
        if(!empty($last_part))
        {
            $new_path .= '/'.$last_part;
        }
        $new_path .= '/'.$new_folder_name;
        return $new_path;
    }

    function update_contact($contact, $addressbook=false, $old_contact=false)
    {
        if (!isset($contact['mtime']) || $contact['mtime'] == 0) {
            $contact['mtime'] = time();
        }

        if (isset($contact['sex']) && $contact['sex'] == '') {
            $contact['sex'] = 'M';
        }

        if(!$old_contact)
        {
            $old_contact = $this->get_contact($contact['id']);
        }

        global $GO_MODULES;
        if(isset($GO_MODULES->modules['files']) && isset($contact['addressbook_id']))
        {
            if(!$addressbook)
            {
                $addressbook = $this->get_addressbook($contact['addressbook_id']);
            }
            require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
            $files = new files();

            $new_path = $this->build_contact_files_path($contact, $addressbook);
            $contact['files_folder_id']=$files->check_folder_location($old_contact['files_folder_id'], $new_path);
        }

        $r = $this->update_row('ab_contacts', 'id', $contact);

        if(isset($contact['addressbook_id']) && $old_contact['addressbook_id']!=$contact['addressbook_id'])
        {
            $this->move_contacts_company($contact['company_id'], $old_contact['addressbook_id'], $contact['addressbook_id']);
        }

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

        if(isset($GO_MODULES->modules['files']))
        {
            require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
            $files = new files();
            try{
                $files->delete_folder($contact['files_folder_id']);
            }
            catch(Exception $e){}
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

        if($offset>0)
        {
            $sql .= "SQL_CALC_FOUND_ROWS ";
        }

        $sql .= "ab_contacts.*, ab_companies.name AS company_name";

        if($GO_MODULES->has_module('customfields'))
        {
            $sql .= ",cf_2.*";
        }

        $sql .= " FROM ab_contacts LEFT JOIN ab_companies ON ab_contacts.company_id=ab_companies.id ";

        if($GO_MODULES->has_module('customfields'))
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

        if(!empty($query)) {
					$sql .= " AND ";

					if(!is_array($field)) {
						if($field == '') {
							if(!isset($_SESSION['GO_SESSION']['addressbook_search_contact_fields']))
							{
								$fields=array('name');
								$fields_sql = "SHOW FIELDS FROM ab_contacts";
								$this->query($fields_sql);
								while($this->next_record()) {
									if(eregi('varchar', $this->f('Type'))) {
										$fields[]='ab_contacts.'.$this->f('Field');
									}
								}
								if($GO_MODULES->has_module('customfields')) {
									$fields_sql = "SHOW FIELDS FROM cf_2";
									$this->query($fields_sql);
									while ($this->next_record()) {
										$fields[]='cf_2.'.$this->f('Field');
									}
								}
								$_SESSION['GO_SESSION']['addressbook_search_contact_fields']=$fields;
							}else
							{
								$fields=$_SESSION['GO_SESSION']['addressbook_search_contact_fields'];
							}
						}else {
							$fields[]=$field;
						}
					}else {
						$fields=$field;
					}

					foreach($fields as $field) {
						if(count($fields)>1) {
							if(isset($first)) {
								$sql .= ' OR ';
							}else {
								$first = true;
								$sql .= '(';
							}
						}

						if($field=='name') {
							$sql .= "CONCAT(first_name,middle_name,last_name) $query_type '".$this->escape(str_replace(' ','%', $query))."' ";
						}else {
							$sql .= "$field $query_type '".$this->escape($query)."' ";
						}
					}
					if(count($fields)>1) {
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


        $_SESSION['GO_SESSION']['export_queries']['search_contacts']=array(
            'query'=>$sql,
            'method'=>'format_contact_record',
            'class'=>'addressbook',
            'require'=>__FILE__);

        if($offset > 0)
        {
            $sql .= " LIMIT ".$this->escape($start.",".$offset);
        }

        //debug($sql);
        return $this->query($sql);
    }

    function format_contact_record(&$record){
        $record['name'] = String::format_name($record['last_name'], $record['first_name'], $record['middle_name']);
        $record['ctime']=Date::get_timestamp($record['ctime']);
        $record['mtime']=Date::get_timestamp($record['mtime']);
    }


    function format_company_record(&$record){
        $record['ctime']=Date::get_timestamp($record['ctime']);
        $record['mtime']=Date::get_timestamp($record['mtime']);
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

        if($offset>0)
        {
            $sql .= "SQL_CALC_FOUND_ROWS ";
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


				if(!empty($query)) {
					$sql .= " AND ";

					if(!is_array($field)) {
						if($field == '') {
							if(!isset($_SESSION['GO_SESSION']['addressbook_search_company_fields']))
							{
								$fields=array();
								$fields_sql = "SHOW FIELDS FROM ab_companies";
								$this->query($fields_sql);
								while($this->next_record()) {
									if(eregi('varchar', $this->f('Type'))) {
										$fields[]='ab_companies.'.$this->f('Field');
									}
								}
								if($GO_MODULES->has_module('customfields')) {
									$fields_sql = "SHOW FIELDS FROM cf_3";
									$this->query($fields_sql);
									while ($this->next_record()) {
										$fields[]='cf_3.'.$this->f('Field');
									}
								}
								$_SESSION['GO_SESSION']['addressbook_search_company_fields']=$fields;
							}else
							{
								$fields=$_SESSION['GO_SESSION']['addressbook_search_company_fields'];
							}
						}else {
							$fields[]=$field;
						}
					}else {
						$fields=$field;
					}

					foreach($fields as $field) {
						if(count($fields)>1) {
							if(isset($first)) {
								$sql .= ' OR ';
							}else {
								$first = true;
								$sql .= '(';
							}
						}

						$sql .= "$field $query_type '".$this->escape($query)."' ";
					}
					if(count($fields)>1) {
						$sql .= ')';
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

        $_SESSION['GO_SESSION']['export_queries']['search_companies']=array(
            'query'=>$sql,
            'method'=>'format_company_record',
            'class'=>'addressbook',
            'require'=>__FILE__);

        $sql .= " ORDER BY $sort_index $sort_order";

        if($offset > 0 )
        {
            $sql .= " LIMIT ".$this->escape($start.",".$offset);
        }
        $this->query($sql);
    }

    function add_addressbook($user_id, $name) {
        global $GO_SECURITY, $GO_MODULES;
        
        $result['acl_read'] = $GO_SECURITY->get_new_acl('addressbook', $user_id);
        $result['acl_write'] = $GO_SECURITY->get_new_acl('addressbook', $user_id);
        $result['user_id']=$user_id;
        $result['name']=$name;

				$this->_add_addressbook($result);       
        $result['addressbook_id']=$result['id'];
        return $result;
    }

		function _add_addressbook(&$addressbook)
		{
			$addressbook['id'] = $this->nextid('ab_addressbooks');
			if(isset($GO_MODULES->modules['files']))
			{
				require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
				$files = new files();

				$files->check_share('contacts/'.File::strip_invalid_chars($addressbook['name']),$addressbook['user_id'], $addressbook['acl_read'], $addressbook['acl_write']);
				$files->check_share('companies/'.File::strip_invalid_chars($addressbook['name']),$addressbook['user_id'], $addressbook['acl_read'], $addressbook['acl_write']);
			}

			$this->insert_row('ab_addressbooks', $addressbook);
			return $addressbook['id'];
		}


    function update_addressbook($addressbook, $old_addressbook=false) {

        if(!$old_addressbook)$old_addressbook=$this->get_addressbook($addressbook['id']);

        global $GO_MODULES;
        if(isset($GO_MODULES->modules['files']) && $old_addressbook &&  $addressbook['name']!=$old_addressbook['name'])
        {
            require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
            $files = new files();
            $files->move_by_paths('contacts/'.File::strip_invalid_chars($old_addressbook['name']), 'contacts/'.File::strip_invalid_chars($addressbook['name']));
            $files->move_by_paths('companies/'.File::strip_invalid_chars($old_addressbook['name']), 'companies/'.File::strip_invalid_chars($addressbook['name']));
        }

        global $GO_SECURITY;
        //user id of the addressbook changed. Change the owner of the ACL as well
        if(isset($addressbook['user_id']) && $old_addressbook['user_id'] != $addressbook['user_id'])
        {
            $GO_SECURITY->chown_acl($old_addressbook['acl_read'], $addressbook['user_id']);
            $GO_SECURITY->chown_acl($old_addressbook['acl_write'], $addressbook['user_id']);
        }

        return $this->update_row('ab_addressbooks', 'id', $addressbook);

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

        global $GO_SECURITY, $GO_MODULES;

        if(isset($GO_MODULES->modules['files']))
        {
            require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
            $files = new files();

            $folder = $files->resolve_path('contacts/'.File::strip_invalid_chars($addressbook['name']));

            if($folder){
                $files->delete_folder($folder);
            }

            $folder = $files->resolve_path('companies/'.File::strip_invalid_chars($addressbook['name']));
            if($folder){
                $files->delete_folder($folder);
            }
        }

				if(empty($addressbook['shared_acl'])){
					$GO_SECURITY->delete_acl($addressbook['acl_read']);
					$GO_SECURITY->delete_acl($addressbook['acl_write']);
				}

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

        $sql = "SELECT c.*,a.acl_read,a.acl_write, a.name AS addressbook_name FROM ab_contacts c INNER JOIN ab_addressbooks a ON a.id=c.addressbook_id WHERE c.id=?";
        $this->query($sql, 'i', $contact_id);
        $record = $this->next_record();
        if($record)
        {
            $cache['id']=$this->f('id');
            $cache['user_id']=$this->f('user_id');
            $cache['module']='addressbook';
            $cache['name'] = htmlspecialchars(String::format_name($this->f('last_name'),$this->f('first_name'),$this->f('middle_name')).' ('.$this->f('addressbook_name').')', ENT_QUOTES,'UTF-8');
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
        $sql = "SELECT c.*, a.acl_read, a.acl_write, a.name AS addressbook_name FROM ab_companies c INNER JOIN ab_addressbooks a ON a.id=c.addressbook_id WHERE c.id=?";
        $this->query($sql, 'i', $company_id);
        $record = $this->next_record();
        if($record)
        {
            $cache['id']=$this->f('id');
            $cache['user_id']=$this->f('user_id');
            $cache['name'] = htmlspecialchars($this->f('name').' ('.$this->f('addressbook_name').')', ENT_QUOTES, 'utf-8');
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
        	$this->query("SELECT * FROM ab_contacts WHERE company_id=? AND addressbook_id=?", 'ii', array($company_id, $old_addressbook_id));
        	while($contact = $this->next_record())
        	{
        		$contact['addressbook_id'] = $addressbook_id;
        		$this->update_contact($contact);
        	}
                    
            if($update_company)
            {
                $this->query('UPDATE ab_companies SET addressbook_id=? WHERE id=?', 'ii', array($addressbook_id, $company_id));
            }
        }
    }

    function get_contact_by_email($email, $user_id, $addressbook_id=0){
        $this->get_contacts_by_email($email, $user_id, $addressbook_id,0,1);
        return $this->next_record();
    }

    function get_index_char($string)
    {
        $char = '';
        if (!empty($string)) {
            if (function_exists('mb_substr')) {
                $char = strtoupper(mb_substr(File::strip_invalid_chars($string),0,1,'UTF-8'));
            } else {
                $char = strtoupper(substr(File::strip_invalid_chars($string),0,1));
            }
        }

        return $char;
    }

    function get_contacts_by_email($email, $user_id, $addressbook_id=0, $start=0, $offset=0, $count=false) {
        $email = $this->escape(String::get_email_from_string($email));
        $sql = "SELECT";

        if($count && $offset>0)
        {
            $sql .= " SQL_CALC_FOUND_ROWS";
        }

        $sql .= " * FROM ab_contacts ";

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

        if($offset > 0)
        {
            $sql .= " LIMIT ".$this->escape($start.",".$offset);
        }

        return $this->query($sql);
    }
}
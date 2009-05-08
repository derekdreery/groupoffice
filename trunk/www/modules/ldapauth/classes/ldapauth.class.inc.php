<?php
require_once($GO_MODULES->modules['ldapauth']['class_path'].'ldap.class.inc.php');
require_once($GLOBALS['GO_CONFIG']->root_path.'modules/imapauth/classes/imapauth.class.inc.php');

class ldapauth extends imapauth
{
	/**
	 * This variable defines a mapping between a column of the SQL users table,
	 * and an attribute in an LDAP user account entry. The KEYs contain the names
	 * of the SQL column names, and the values the LDAP attribute names.
	 * This mapping defines a mapping to the standard posixAccount objectclass,
	 * which may be extended with our own groupofficeperson objectclass.
	 */
	var $mapping = array();

	public function __construct(){
		$this->mapping = array(
		'username'	=> 'uid',
		'password'	=> 'userpassword',
		'first_name'	=> 'givenname',
		'middle_name'	=> 'middlename',
		'last_name'	=> 'sn',
		'initials'	=> 'initials',
		'title'	=> 'title',
		'sex'		=> 'gender',
		'birthday'	=> 'birthday',
		'email'	=> 'mail',
		'company'	=> 'o',
		'department'	=> 'ou',
		'function'	=> 'businessrole',	// TODO
		'home_phone'	=> 'homephone',
		'work_phone'	=> 'telephonenumber',
		'fax'		=> 'homefacsimiletelephonenumber',
		'cellular'	=> 'mobile',
		'country'	=> 'homecountryname',
		'state'	=> 'homestate',
		'city'	=> 'homelocalityname',
		'zip'		=> 'homepostalcode',
		'address'	=> 'homepostaladdress',
		'homepage'	=> 'homeurl',	// TODO: homeurl, workurl, labeledURI
		'work_address'=> 'postaladdress',
		'work_zip'	=> 'postalcode',
		'work_country'=> 'c',
		'work_state'	=> 'st',
		'work_city'	=> 'l',
		'work_fax'	=> 'facsimiletelephonenumber',
		'currency'	=> 'gocurrency',
		'max_rows_list'	=> 'gomaxrowslist',
		'timezone'	=> 'gotimezone',
		'start_module'=> 'gostartmodule',
		'theme'	=> 'gotheme',
		'language'	=> 'golanguage',
		);

	}

	public function __on_load_listeners($events){
		$events->add_listener('before_login', __FILE__, 'ldapauth', 'before_login');
	}
	

	public static function before_login($username, $password)
	{
		global $GO_CONFIG, $GO_USERS, $GO_MODULES;
		
		if(!isset($GO_CONFIG->ldap_host))
		{
			//trigger_error('ldapauth module is installed but not configured', E_USER_NOTICE);
			return false;
		}

		$ldap = new ldap(
		$GO_CONFIG->ldap_host,
		$GO_CONFIG->ldap_user,
		$GO_CONFIG->ldap_pass,
		$GO_CONFIG->ldap_basedn,
		$GO_CONFIG->ldap_peopledn,
		$GO_CONFIG->ldap_groupsdn);

		$ldap->search('uid='.$username, $ldap->PeopleDN);

		$entry = $ldap->get_entries();
		if(!isset($entry[0]))
		{
			return false;
		}
		$la = new ldapauth();
		$user = $la->convert_ldap_entry_to_groupoffice_record($entry[0]);
		
		$authenticated = $ldap->bind($entry[0]['dn'], $password);

		if(!$authenticated)
		{
			debug('LDAP authentication failed for '.$username);
			throw new Exception($GLOBALS['lang']['common']['badLogin']);
		}else
		{
			$gouser = $GO_USERS->get_user_by_username($username);
			
			if ($gouser) {
				$user['id']=$gouser['id'];
				$GO_USERS->update_profile($user);

				//user exists. See if the password is accurate
				if(md5($password) != $gouser['password'])
				{
					$GO_USERS->update_password($gouser['id'], $password);
					if(isset($GO_MODULES->modules['email']))
					{
						require_once($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
						$email_client = new email();
						//$email_client->update_password($config['host'], $mail_username, $arguments['password']);
					}
				}
			} else {
				$user['username'] = $username;
				$user['password'] = $password;
				
				global $GO_GROUPS;

				if (!$user_id = $GO_USERS->add_user($user,
					$GO_GROUPS->groupnames_to_ids(explode(',',$GO_CONFIG->register_user_groups)),
					$GO_GROUPS->groupnames_to_ids(explode(',',$GO_CONFIG->register_visible_user_groups)),
					explode(',',$GO_CONFIG->register_modules_read),
					explode(',',$GO_CONFIG->register_modules_write)))
				{
					trigger_error('Failed creating user '.$username.' and e-mail '.$email.' with ldapauth.', E_USER_WARNING);
				} else {
					
					if(!empty($user['email']))
					{
						$arr = explode('@', $user['email']);						
						$mailbox = trim($arr[0]);
						$domain = isset($arr[1]) ? trim($arr[1]) : '';
			
						$config = $la->get_domain_config($domain);
						if($config)
						{
							$mail_username = empty($config['ldap_use_email_as_imap_username']) ? $username : $user['email'];
							
							$la->create_email_account($config, $user_id, $mail_username, $password,$user['email']);
						}
					}
				}
			}				
		}
	}


	/**
	 * Convert an LDAP entry to an SQL record.
	 *
	 * This function takes an LDAP entry, as you get from ldap_fetch_entries()
	 * and converts this entry to an SQL result record. It is used to convert
	 * the account data that is stored in the directory server to an SQL style
	 * result as is expected from the framework.
	 * The mapping of table-columns to ldap-attributes is included from the
	 * users.ldap.mapping file (which is located in the lib/ldap directory),
	 * which is loaded from the constructor in this class. The name of this
	 * file can be overridden in the configuration.
	 *
	 * @access private
	 *
	 * @param $entry is the LDAP entry that should be converted.
	 *
	 * @return Array is the converted entry.
	 */
	function convert_ldap_entry_to_groupoffice_record( $entry ) {

		$row = array();
		/*
		 * Process each SQL/LDAP key pair of the mapping array, so that we can
		 * fetch all values that are needed for each SQL key.
		 */
		foreach ( $this->mapping as $key => $ldapkey ) {
			/*
			 * If the ldapkey is undefined, we don't know any attributes that
			 * match the specifiy SQL column, so we can leave it empty.
			 */
			if ( $ldapkey == '' ) {
				$row[$key] = '';
				continue;
			}

			/*
			 * Check if this is already a new mapping - if the data type is not
			 * a string, we can savely assume that it is a ldap_user_mapping
			 * object, so we can directly execute the generic method.
			 */
			if ( !is_string( $ldapkey ) ) {

				$value = $ldapkey->get_value($entry, $key);
			}elseif ( isset( $entry[$ldapkey] ) ) {
				$value = $entry[$ldapkey][0];
			} else {
				continue;
			}
				
			$row[$key] = $value;
				
		}

		/*
		 * We have processed all mapping fields and created our SQL result
		 * array. So we can return it.
		 */
		return $row;
	}
}



class ldap_mapping_type {
	var $type;
	var $value;

	function mapping_type( $type, $value ) {
		$this->type = $type;
		$this->value = $value;
	}

	function get_value($entry, $key){
		switch($this->type)
		{
			case 'function':
				$my_method = $this->value;
				return $my_method( $entry );
				break;
			case 'constant':
				return $this->value;
				break;
					
			default:
				return false;
				break;
		}
	}
}

function ldap_mapping_username( $entry ) {
	if ( $entry['uid']['count'] > 1 ) {
		$dn = $entry['dn'];
		$dn = substr( $dn, 0, strpos( $dn, ',' ) );
		$value = substr( $dn, strpos( $dn, '=' ) + 1 );
	} else {
		$value = $entry['uid'][0];
	}
	if ( !$value ) {
		$value = '';
	}
	return $value;
}


function ldap_mapping_enabled( $entry ) {
	return ( $entry['accountstatus'][0] == 'active' ) ? 1 : 0;
}



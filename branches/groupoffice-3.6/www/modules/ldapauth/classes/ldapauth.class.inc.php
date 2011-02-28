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

require_once($GLOBALS['GO_CONFIG']->class_path.'base/ldap.class.inc.php');
require_once($GLOBALS['GO_CONFIG']->root_path.'modules/imapauth/classes/imapauth.class.inc.php');

class ldapauth extends imapauth {
	/**
	 * This variable defines a mapping between a column of the SQL users table,
	 * and an attribute in an LDAP user account entry. The KEYs contain the names
	 * of the SQL column names, and the values the LDAP attribute names.
	 * This mapping defines a mapping to the standard posixAccount objectclass,
	 * which may be extended with our own groupofficeperson objectclass.
	 */
	var $mapping = array();

	public function __construct() {
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

	public function __on_load_listeners($events) {
		$events->add_listener('before_login', __FILE__, 'ldapauth', 'before_login');
	}


	public static function before_login($username, $password) {
		global $GO_CONFIG, $GO_MODULES;

		if(!isset($GO_CONFIG->ldap_host)) {
			go_debug('LDAPAUTH: module is installed but not configured');
			return false;
		}

		$ldap = new ldap();
		if(!$ldap->connect()){
			go_debug('LDAPAUTH: Could not connect to server');
			throw new Exception('LDAPAUTH: Could not connect to server');
		}


		if(!empty($GO_CONFIG->ldap_user) && !empty($GO_CONFIG->ldap_pass)){
			if(!$ldap->bind()){
				go_debug('LDAPAUTH: Could not bind to server');
				throw new Exception('Could not bind to LDAP server');
			}
		}

		$ldap->search('uid='.$username, $ldap->PeopleDN);

		$entry = $ldap->get_entries();
		if(!isset($entry[0])) {
			go_debug('LDAPAUTH: No LDAP user found');
			return false;
		}
		$la = new ldapauth();
		$user = $la->convert_ldap_entry_to_groupoffice_record($entry[0]);

		$authenticated = @$ldap->bind($entry[0]['dn'], $password);

		if(!$authenticated) {
			go_debug('LDAPAUTH: LDAP authentication failed for '.$username);
			//throw new Exception($GLOBALS['lang']['common']['badLogin']);
			return false;
		}else {
			go_debug('LDAPAUTH: LDAP Authentication successfull');

			require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$mail_username=false;
			$gouser = $GO_USERS->get_user_by_username($username);

			if(!empty($user['email']) && isset($GO_MODULES->modules['email'])) {
				$arr = explode('@', $user['email']);
				$mailbox = trim($arr[0]);
				$domain = isset($arr[1]) ? trim($arr[1]) : '';

				$config = $la->get_domain_config($domain);
				if($config) {
					go_debug('LDAPAUTH: E-mail configuration found. Creating e-mail account');
					$mail_username = empty($config['ldap_use_email_as_imap_username']) ? $username : $user['email'];
				}
			}else {
				go_debug('LDAPAUTH: Warning! no E-mail address found in profile.');
			}

			if ($gouser) {
				go_debug('LDAPAUTH: Group-Office user was found');

				if(!empty($GO_CONFIG->ldap_auth_dont_update_profiles))
					$user=array('email'=>$gouser['email'], 'username'=>$user['username']);


				$user['id']=$gouser['id'];
				$user['password']=$password;

				//user exists. See if the password is accurate
				if(crypt($password, $gouser['password']) != $gouser['password']) {
					go_debug('LDAPAUTH: password on LDAP server has changed. Updating Group-Office database');


					if($mail_username) {
						require_once($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
						$email_client = new email();
						$email_client->update_password($config['host'], $mail_username, $password);
					}
				}

				if(!empty($GO_CONFIG->ldap_create_mailboxes_for_email_domain)){
					$mail_username=false;

					$_POST['serverclient_no_halt']=true;//Don't stop when mailbox wasn't created. Perhaps it already exists

					$arr = explode('@', $user['email']);
					if(!empty($arr[1])){
						require_once($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
						$email_client = new email();

						$account = $email_client->get_account_by_username($username, $gouser['id']);
						if($account){
							if(crypt($password, $gouser['password']) != $gouser['password'])
								$email_client->update_password($account['host'], $username, $password);
						}else
						{
							require_once($GO_MODULES->modules['serverclient']['class_path']."serverclient.class.inc.php");
							//$sc = new serverclient();
							go_debug('LDAPAUTH: Could not find e-mail account for LDAP user. It will be created now.');
							$_POST['serverclient_domains']=array($arr[1]);
							try{
								serverclient::add_user($user);
							}catch(Exception $e){
								go_debug('LDAPAUTH: Failed adding e-mail account: '.$e->getMessage());
							}
						}

					}
				}


				$GO_USERS->update_profile($user);

			} else {
				$user['username'] = $username;
				$user['password'] = $password;

				require_once($GO_CONFIG->class_path.'base/groups.class.inc.php');
				$GO_GROUPS = new GO_GROUPS();

				go_debug('LDAPAUTH: Group-Office user not found. Creating new user from LDAP profile');

				if(!empty($GO_CONFIG->ldap_create_mailboxes_for_email_domain)){

					$mail_username=false;

					$_POST['serverclient_no_halt']=true;//Don't stop when mailbox wasn't created. Perhaps it already exists

					$arr = explode('@', $user['email']);
					if(!empty($arr[1])){
						go_debug("LDAPAUTH: Sending ".$arr[1].
						" to serverclient module to create mailboxes");

						$_POST['serverclient_domains']=array($arr[1]);
					}
				}

				if (!$user_id = $GO_USERS->add_user($user,
				$GO_GROUPS->groupnames_to_ids(explode(',',$GO_CONFIG->register_user_groups)),
				$GO_GROUPS->groupnames_to_ids(explode(',',$GO_CONFIG->register_visible_user_groups)),
				explode(',',$GO_CONFIG->register_modules_read),
				explode(',',$GO_CONFIG->register_modules_write))) {
					go_debug('LDAPAUTH: Failed creating user '.$username.' and e-mail '.$email.' with ldapauth.');
					trigger_error('Failed creating user '.$username.' and e-mail '.$email.' with ldapauth.', E_USER_WARNING);
				} else {

					if($mail_username) {
						$la->create_email_account($config, $user_id, $mail_username, $password,$user['email']);
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
			}elseif ( !empty( $entry[$ldapkey] ) ) {
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

	function get_value($entry, $key) {
		switch($this->type) {
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


<?php
/**
 * @copyright Copyright &copy; Intermesh 2003
 * @version $Revision: 1.4 $ $Date: 2006/05/02 13:54:26 $
 * 
 * @author Markus Schabel <markus.schabel@tgm.ac.at>

   This file is part of Group-Office.

   Group-Office is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Group-Office is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Group-Office; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 * @package Framework
 * @subpackage Usermanagement
 * @category Accounts
 */

class mapping_type {
	var $type;
	var $value;

	function mapping_type( $type, $value ) {
		$this->type = $type;
		$this->value = $value;
	}

	function isFunction() {
		if ( $this->type == "function" ) {
			return true;
		}
		return false;
	}

	function isConstant() {
		if ( $this->type == "constant" ) {
			return true;
		}
		return false;
	}

	function isAttribute() {
		if ( $this->type == "attribute" ) {
			return true;
		}
		return false;
	}
}

function ldap_mapping_username( $entry ) {
	if ( $entry['uid']['count'] > 1 ) {
		$dn = $entry['dn'];
		$dn = substr( $dn, 0, strpos( $dn, ',' ) );
		$value = substr( $dn, strpos( $dn, '=' ) + 1 );
	} else {
		$value = utf8_decode( $entry['uid'][0] );
	}
	if ( !$value ) {
		$value = '';
	}
	return $value;
}

function ldap_mapping_acl_id( $entry ) {
	$GO_LDAP_USERS = new ldap_users();

	$mail = isset( $entry['mail'][0] ) ?
		$entry['mail'][0] : null;
	$uidnumber = isset( $entry['uidnumber'][0] ) ?
		$entry['uidnumber'][0] : null;

	// Fetch the acl_id of this email address.
	return $GO_LDAP_USERS->getOrCreateAclId( $mail, $uidnumber );
}

function ldap_mapping_enabled( $entry ) {
	return ( $entry['accountstatus'][0] == 'active' ) ? 1 : 0;
}

/**
 * This variable defines a mapping between a column of the SQL users table,
 * and an attribute in an LDAP user account entry. The KEYs contain the names
 * of the SQL column names, and the values the LDAP attribute names.
 * This mapping defines a mapping to the standard posixAccount objectclass,
 * which may be extended with our own groupofficeperson objectclass.
 */
$users_ldap_mapping = array(
		'username'	=> new mapping_type( 'function', 'ldap_mapping_username' ),
		'acl_id'	=> new mapping_type( 'function', 'ldap_mapping_acl_id' ),
		'enabled'	=> new mapping_type( 'function', 'ldap_mapping_enabled' ),
		'date_format'	=> new mapping_type( 'constant', 'd-m-Y' ),
		'time_format'	=> new mapping_type( 'constant', 'G:i' ),
		'date_seperator'	=> new mapping_type( 'constant', '-' ),
		'thousands_seperator'	=> new mapping_type( 'constant', '.' ),
		'decimal_seperator'	=> new mapping_type( 'constant', ',' ),
		'first_weekday'	=> new mapping_type( 'constant', '1' ),
		'sort_name'	=> new mapping_type( 'constant', 'last_name' ),
		'password'	=> 'userpassword',
		'id'		=> 'uidnumber',
		'authcode'	=> 'goauthcode',
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
		'mail_client'	=> 'gomailclient',
		'logins'	=> 'gologins',
		'lastlogin'	=> 'golastlogin',
		'registration_time'	=> 'goregistrationtime',
		'max_rows_list'	=> 'gomaxrowslist',
		'timezone'	=> 'gotimezone',
		'DST'		=> 'goDST',
		'start_module'=> 'gostartmodule',
		'theme'	=> 'gotheme',
		'language'	=> 'golanguage',
		);

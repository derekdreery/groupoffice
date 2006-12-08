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

/**
 * This variable defines a mapping between a column of the SQL users table,
 * and an attribute in an LDAP user account entry. The KEYs contain the names
 * of the SQL column names, and the values the LDAP attribute names.
 * This mapping defines a mapping to the standard posixAccount objectclass,
 * which may be extended with our own groupofficeperson objectclass.
 */
$users_ldap_mapping = array(
	  'id'		=> 'uidnumber',
	  'username'	=> 'uid',
	  'password'	=> 'userpassword',
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
	  'photo'	=> 'jpegphoto',
	  'acl_id'	=> 'goAclID',
	  'date_format'	=> 'godateformat',
	  'time_format'	=> 'gotimeformat',
	  'date_seperator'	=> 'godateseperator',
	  'thousands_seperator'	=> 'gothousandsseperator',
	  'decimal_seperator'	=> 'godecimalseperator',
	  'currency'	=> 'gocurrency',
	  'mail_client'	=> 'gomailclient',
	  'logins'	=> 'gologins',
	  'lastlogin'	=> 'golastlogin',
	  'registration_time'	=> 'goregistrationtime',
	  'samba_user'	=> '',		// TODO
	  'max_rows_list'	=> 'gomaxrowslist',
	  'timezone'	=> 'gotimezone',
	  'DST'		=> 'goDST',
	  'start_module'=> 'gostartmodule',
	  'theme'	=> 'gotheme',
	  'language'	=> 'golanguage',
	  'first_weekday'	=> 'gofirstweekday',
	  'sort_name'	=> 'gosortname',
	  'enabled'	=> 'accountstatus'
);
?>
<?php
/*
   Copyright Intermesh 2003
   Author: Markus Schabel <markus.schabel@tgm.ac.at>
   Author: Michael Borko <michael.borko@tgm.ac.at>
   Version: 1.0 Release date: 22 August 2003
   Version: 1.5 Release date: 27 February 2004

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.
 */

class profiles
{
  var $db;

  var $mapping;
  var $group_mapping;

  function profiles()
  {
    global $GO_CONFIG, $GO_LDAP;

    $this->db = new db();
    $this->db->connect();

    if ($_SESSION['auth_source']['type'] == "ldap" )
    {
      $this->mapping['ldap'] = array(
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
	  'sort_name'	=> 'gosortname'
	    );

      $this->group_mapping['ldap'] = array(
          'id'		=> 'gidnumber',
	  'ident'	=> 'dn',
          'name'        => 'sn',
          'user_id'     => 'memberuid'
      );
    }
  }

  function get_profile_ldap( $userid )
  {
    global $GO_SECURITY, $GO_CONFIG, $GO_GROUPS, $GO_LDAP;
    
    $GO_LDAP->search("uidNumber=$userid", $GO_LDAP->PeopleDN );
    if ( $GO_LDAP->num_entries() == 1 ) {
      $entry = $GO_LDAP->get_entries();
      return $this->convert_profile_ldap( $entry[0] );
    }
    return false;
  }

  function convert_profile_ldap( $entry )
  {
    global $GO_SECURITY, $GO_CONFIG, $GO_LDAP, $GO_GROUPS;
    
    /*
    if ( !$GO_GROUPS->is_in_group( $entry["uidnumber"][0], 
      $GO_CONFIG->group_everyone ) )
    {
      $GO_GROUPS->add_user_to_group( $entry["uidnumber"][0],
	$GO_CONFIG->group_everyone );
    }
    */

    $row = array();
    foreach ( $this->mapping['ldap'] as $key => $ldapkey )
    {
      if ( $ldapkey == '' )
      {
	$row[$key] = '';
      } else if ( $ldapkey == 'goAclID' )
      {
	if ( isset( $entry["mail"][0] ) )
	{
	  $this->db->query( "SELECT * FROM acl_items WHERE description=\"".
	    $entry["mail"][0]."\"" );
	  if ( $this->db->next_record())
	  {
	    $row[$key] = $this->db->f("id");
	  } else
	  {
	    $acl_id = $GO_SECURITY->get_new_acl( $entry["mail"][0] );
	    $GO_SECURITY->add_group_to_acl( $GO_CONFIG->group_root, $acl_id );
	    $row[$key] = $acl_id;
	  }
	}
      } else if ( $ldapkey == "uid" )
      {
	if ( $entry["uid"]["count"] > 1 )
	{
	  $dn = $entry["dn"];
	  $dn = substr( $dn, 0, strpos( $dn, "," ) );
	  $value = substr( $dn, strpos( $dn, "=" ) + 1 );
	} else
	{
	  $value = utf8_decode( $entry["uid"][0] );
	}
	if ( !$value )
	{
	  $value = '';
	}
	$row[$key] = $value;
      } else if ( $ldapkey == "gofirstweekday" ) { $row[$key] = '1';
      } else if ( $ldapkey == "godateformat"   ) { $row[$key] = 'd-m-Y';
      } else if ( $ldapkey == "gotimeformat"   ) { $row[$key] = 'G:i';
      } else if ( $ldapkey == "godecimalseperator" ) { $row[$key] = ',';
      } else if ( $ldapkey == "gothousandsseperator" ) { $row[$key] = '.';
      } else if ( $ldapkey == "gosortname" ) { $row[$key] = '';
      } else
      {
	if ( isset( $entry[$ldapkey] ) )
	{
	  $value = utf8_decode( $entry[$ldapkey][0] );
	} else
	{
	  $value = '';
	}
	if ( !$value )
	{
	  $value = '';
	}
	$row[$key] = $value;
      }
    }
    return $row;
  }

  function get_profile_sql( $userid )
  {
    $sql = "SELECT * FROM users WHERE id='$userid'";
    $this->db->query( $sql );
    if ($this->db->next_record())
    {
      return $this->db->Record;
    }
    return false;
  }

  function get_profile( $userid )
  {
    global $GO_LDAP;

    $sqlrecord = $this->get_profile_sql( $userid );
    // echo "<pre>";
    // var_dump( $sqlrecord );
    //echo "</pre>";
    if ( $GO_LDAP && $ldaprecord = $this->get_profile_ldap( $userid )) {
      if ( $sqlrecord ) {
	foreach ( $sqlrecord as $key => $value ) {
	  if ( !$value && isset($ldaprecord[$key])) {
	    $sqlrecord[$key] = $ldaprecord[$key];
	  }
	}
      } else {
	return $ldaprecord;
      }
    }
    return $sqlrecord;
  }

  function get_group_profile_ldap( $groupid )
  {
    global $GO_SECURITY, $GO_CONFIG, $GO_LDAP;
    
    $GO_LDAP->search( "(&(gidNumber=$groupid)(mail=*))",
      $GO_LDAP->GroupDN );
    if ( $GO_LDAP->num_entries() == 1 ) {
      $entry = $GO_LDAP->get_entries();
      return $this->convert_group_profile_ldap( $entry[0] );
    }
    return false;
  }

  function convert_group_profile_ldap( $entry )
  {
    global $GO_SECURITY, $GO_CONFIG;
    
    $row = array();
    foreach ( $this->group_mapping['ldap'] as $key => $ldapkey ) {
      if ( $ldapkey == '' ) {
	$row[$key] = '';
      } if ( $ldapkey == 'dn' ) {
	$value = utf8_decode( $entry[$ldapkey] );
	$row[$key] = $value;
      } else {
	if ( isset( $entry[$ldapkey] ) ) {
	  $value = utf8_decode( $entry[$ldapkey][0] );
	} else {
	  $value = '';
	}
	if ( !$value ) {
	  $value = '';
	}
	$row[$key] = $value;
      }
    }
    return $row;
  }

  function get_group_profile_sql( $groupid )
  {
    $sql = "SELECT * FROM groups WHERE id='$groupid'";
    $this->db->query( $sql );
    if ($this->db->next_record())
    {
      return $this->db->Record;
    }
    return false;
  }

  function get_group_profile( $groupid )
  {
    global $GO_LDAP;

    $sqlrecord = $this->get_group_profile_sql( $groupid );
    if ( $GO_LDAP )
    {
      $ldaprecord = $this->get_group_profile_ldap( $groupid );
      if ( $sqlrecord )
      {
	foreach ( $sqlrecord as $key => $value )
	{
	  if ( !$value )
	  {
	    $sqlrecord[$key] = $ldaprecord[$key];
	  }
	}
      } else
      {
	return $ldaprecord;
      }
    }
    return $sqlrecord;
  }

}

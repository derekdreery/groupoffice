<?php
/*
   DAV-Addings
   Copyright .tgm 2003
   Author: Michael Borko <michael.borko@tgm.ac.at>

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.
 */

class dav extends db
{
  var $search_results = array();

  var $dav_home = "";
  var $sites_dir = "";
  var $drafts_dir = "";
  var $dav_auth = "MYSQL";
  var $dav_ldap = "";
  var $alias = "";
  var $accessfile = "";
  var $sharefile = "";
  var $userfile = "";
  var $nsharedir = "";
  var $nnsharedir = "";

  function dav()
  {
    $this->db();

    //Getting all the information from the Configfile
    global $GO_CONFIG;
    $this->dav_home = $GO_CONFIG->file_storage_path;
    $this->sites_dir = $this->dav_home."sites-enabled/";
    $this->drafts_dir = $GO_CONFIG->dav_drafts;
    //Check what kind of authentication is wanted in the accessfiles
    if ( eregi("ldap", $GO_CONFIG->dav_auth) ) {
      $this->dav_auth = "LDAP";
      $this->dav_ldap = $GO_CONFIG->dav_auth;
    }
    $this->alias = $GO_CONFIG->dav_alias;
    $this->accessfile = $GO_CONFIG->dav_accessfilename;
    $this->sharefile = "ShareAccess".$this->dav_auth;
    $this->userfile = "UserDefault".$this->dav_auth;
    $this->nsharedir = $GO_CONFIG->name_of_sharedir."/";
    $this->nnsharedir = $GO_CONFIG->name_of_sharedir;
  }

  function file_get_contents ($filename)
  {
    $opendf = fopen("$filename", "rb");
    $content = fread($opendf, filesize($filename));
    fclose($opendf);
    return $content;
  }

  function file_write_string ($filename, $input)
  {
    $opendf = fopen("$filename", "wb");
    if( is_writeable($filename) ) {
      fwrite($opendf,$input);
      fclose($opendf);
    }
  }

  function make_dirs($strPath, $mode="755")
  {
    if (is_dir($strPath)) return true;
    $pStrPath = dirname($strPath);
    if (!$this->make_dirs($pStrPath, $mode)) return false;
    return mkdir($strPath);
  }

  function delete_dirs($dir)
  {
    $current_dir = opendir($dir);
    while($entryname = readdir($current_dir)) {
      if(is_dir("$dir/$entryname") and ($entryname != "." and $entryname!=".."))
      {
	$this->delete_dirs("${dir}/${entryname}");
      }
      elseif($entryname != "." and $entryname!="..") {
	unlink("${dir}/${entryname}");
      }
    }
    closedir($current_dir);
    @rmdir($dir);
  }

  function multi_strpos($pattern, $sequence)
  {
    $n = -1;
    while (ereg($pattern, $sequence)) {
      $n++;
      $fragment = split($pattern, $sequence);
      $trimsize = (strlen($fragment[0]))+1;
      $sequence = "*".substr($sequence, $trimsize);
      @$position[$n] = (strlen($fragment[0]) + $position[($n-1)]);
    }
    return $position;
  }

  function add_access($term, $path, $mode, $type)
  {
    switch($type) {
      case "linker":
	$req = "user";
      $check = "Linkers";
      break;
      case "group":
	$req = "group";
      $check = "Groups";
      break;
      default:
      echo "<pre>User/Group not added!</pre>";
      return false;
    }

    //Open the accessfile
    $string = $this->file_get_contents($path."/".$this->accessfile);

    //If the User/Group isn't present in the whole accessfile, 
    //add him/it to the choosen directive and to the linkers/groups...
    if( !strstr($string, $term) ) {
      $string = str_replace ("##$mode-$type-Access\nRequire $req", 
	"##$mode-$type-Access\nRequire $req $term", $string);
      $string = str_replace ("##$check:", "##$check: $term", $string);
      $this->file_write_string ($path."/".$this->accessfile, "$string");
      return true;
    }

    //The User/Group is present in the .htaccess! Checking the positions...
    $position = $this->multi_strpos($term, $string); $p = 0;

    while(@$position[$p] < strlen($string) && @$position[$p] != "") {
      $pos = $position[$p];

      //If the User/Group is listed already in the choosen directive...
      if( strpos($string, "##$mode-$type-Access") < $pos 
	&& $pos < strpos($string, "##END $mode-$type-Access") ) {
	return true;
      }

      //If the User/Group is not listed in the R/W-directive, so he/it 
      //isn't listed in the choosen directive, if he/it is present in 
      //accessfile...
      $termRW = strchr($string, "R/W-$type-Access"); 
      $termRW = substr($termRW,0,strpos($termRW,"#")-1);
      if( strpos($termRW, $term) ) {
	return true;
      } else {
	$string = str_replace ("##$mode-$type-Access\nRequire $req", 
	  "##$mode-$type-Access\nRequire $req $term", $string);
	$string = str_replace ("##R/W-$type-Access\nRequire $req", 
	  "##R/W-$type-Access\nRequire $req $term", $string);
	$this->file_write_string ($path."/".$this->accessfile, "$string");
	return true;
      }
      $p++;
    }
    $this->file_write_string ($path."/".$this->accessfile, "$string");
    return false;
  }

  function remove_access($term, $path, $mode, $type)
  {
    switch($type) {
      case "linker":
	$check = "Linkers";
      break;
      case "group":
	$check = "Groups";
      break;
      default:
      echo "<pre>User/Group not removed!</pre>";
      return false;
    }

    $string = $this->file_get_contents($path."/".$this->accessfile);

    $position = $this->multi_strpos($term, $string); $p = 0;
    $removed = false; $clear = true;

    while(@$position[$p] < strlen($string) && @$position[$p] != "") {
      $pos = $position[$p];
      if( strpos($string, "##$mode-$type-Access") < $pos 
	&& $pos < strpos($string, "##END $mode-$type-Access") ) {
	//...delete the user/group from the selected directive...
	$string = substr_replace($string, "", $pos, strlen($term)+1);
	//CAUTION: Stringposition changed!!!
	$position = $this->multi_strpos($term, $string); $p = -1;
	$removed = true;
      }
      if( $removed && strpos($string, "##R/W-$type-Access") < $pos 
	&& $pos < strpos($string, "##END R/W-$type-Access") ) {
	//...delete the user/group from the R/W-directive...
	$string = substr_replace($string, "", $pos, strlen($term)+1);
	//CAUTION: Stringposition changed!!!
	$position = $this->multi_strpos($term, $string); $p = -1;
	$clear = false;
      }
      if( $removed && $clear && strpos($string, "##$check:") < $pos ) {
	$string = substr_replace($string, "", $pos, strlen($term)+1);
	$this->file_write_string ($path."/".$this->accessfile, "$string");
	return true;
      }
      $p++;
    }
    $this->file_write_string ($path."/".$this->accessfile, "$string");
  }

  function first_login($username)
  {
    if( !is_dir($this->sites_dir) )
      $this->make_dirs($this->sites_dir);

    //Now add the UserContainer and reload the apache...
    $this->check_login($username);
  }

  function check_login($username)
  {
    $added = false;

    //User has no UserContainer!
    if ( !is_file($this->sites_dir.$username) ) {
    
      if( !is_dir($this->sites_dir) )
	$this->make_dirs($this->sites_dir);
	
      $string = $this->file_get_contents($this->drafts_dir.$this->userfile);
      
      $string = str_replace("%USERNAME%", $username, $string);
      $string = str_replace("%ALIAS_USERNAME%",
	$this->alias.$username, $string);
      $string = str_replace("%REALPATH_USERNAME%",
	$this->dav_home.$username, $string);
      $string = str_replace("%SHAREFOLDER%", $this->nnsharedir, $string);
      $string = str_replace("%DAV_LDAP%", $this->dav_ldap, $string);
      
      $this->file_write_string($this->sites_dir.$username,"$string");
      $added = true;
    }
    
    //A new UserContainer was created - Reload apache!
    //Make it with cron, every 5mins or so ... is better!
    //if ( $added )
    //  exec("sudo /etc/init.d/apache reload", $error_msg);
  }
  
  function add_share($user_id, $path)
  {
    global $GO_USERS;
    $temp = $GO_USERS->get_user($user_id); $owner = $temp['username'];
    //Load the access file into the new ShareDirectory...
    $string = $this->file_get_contents($this->drafts_dir.$this->sharefile);
    $string = str_replace("%USERNAME%", $owner, $string);
    if($this->dav_auth == "LDAP")
      $string = str_replace("%DAV_AUTH%", $this->dav_ldap, $string);
    //Put the modified draft file into the defined accessfile 
    $this->file_write_string($path."/".$this->accessfile,"$string");
  }

  function delete_share($path)
  {
    //Getting the proper accessfile.
    $shareControl = $path."/".$this->accessfile;
    //Getting all linkers
    $list = $this->get_all_linkers($path); $i=0;

    //Here we are deleting all the established sharelinks in the sharedir of
    //every linker who has access to the share. To achieve this we have to
    //modify the path (the original share) so, that we add the linker and the
    //sharedir-name in the userdir.
    while(@$list[$i] != "") {
      $linker = $list[$i];
      $dest_dav = str_replace($this->dav_home, 
        $this->dav_home.$linker."/".$this->nsharedir, $path);

      $this->remove_sharelink($dest_dav);

      $i++;
    }
    //deleting the share-access and all granted permissions...
    if (is_file($shareControl))
      unlink($shareControl);
  }

  function update_share($old_path, $new_path)
  {
    //Need it for WebDAV.php to find the accessfile, because the old path isnt
    //existend anymore!
    if( is_file($old_path."/".$this->accessfile) )
      $lookat = $old_path;
    elseif( is_file($new_path."/".$this->accessfile) )
      $lookat = $new_path;

    $list = $this->get_all_linkers($lookat); $l=0;

    while(@$list[$l] != "") {
      $linker = $list[$l];
      $old_dest_dav = str_replace($this->dav_home, 
        $this->dav_home.$linker."/".$this->nsharedir, $old_path);
      $new_dest_dav = str_replace($this->dav_home, 
        $this->dav_home.$linker."/".$this->nsharedir, $new_path);

      //deleting the old link...
      $this->remove_sharelink($old_dest_dav);
      //If everything went fine, the linker has the new symlink...
      $this->add_sharelink($new_path, $new_dest_dav);

      $l++;
    }
  }

  function init_sharing($term_id, $acl_id, $type)
  {
    $db = new db();
    $sql_query = "SELECT * FROM fsShares 
      WHERE fsShares.acl_read='$acl_id' OR fsShares.acl_write='$acl_id'";
    $db->query($sql_query);
    $db->next_record();
    $result = $db->Record;

    global $GO_USERS;
    $temp = $GO_USERS->get_user($result['user_id']);
    $owner = $temp['username'];
    $array['owner'] = $owner;

    $path = $result['path'];
    $array['path'] = $path."/";
    $share = strchr($path,"$owner/");

    //Check the acl_id permission with the current acl_id. If it is the 
    //acl_read, check for the acl_write and vice versa...
    $array['read'] = false; 
    $array['write'] = false; 
    $array['readwrite'] = false;
    if ($result['acl_read'] == $acl_id) {
      $array['read'] = true;
      $acl_id_new = $result['acl_write'];
    }
    if ($result['acl_write'] == $acl_id) {
      $array['write'] = true;
      $acl_id_new = $result['acl_read'];
    }

    switch($type) {
      case "user":
	$temp = $GO_USERS->get_user($term_id);
      $linker = $temp['username'];
      $array['linker'] = $linker;
      //Linkers-Share-Folder
      $array['share'] = $this->dav_home.$linker."/".$this->nsharedir.$share;
      $sql_permission = "SELECT acl.user_id AS id FROM acl 
	WHERE acl.acl_id = '$acl_id_new'";
      $db->query($sql_permission);
      $db->next_record();
      $user_array = $db->Record;
      if (@in_array($linker, $user_array)) $array['readwrite'] = true;
      $array['group'] = "";
      return $array;
      break;

      case "group":
	global $GO_GROUPS;
      $temp = $GO_GROUPS->get_group($term_id);
      //var_dump($temp);
      $group = $temp['ident'];
      $array['group'] = $group;
      $array['share'] = "";
      $sql_permission = "SELECT acl.group_id AS id FROM acl 
	WHERE acl.acl_id = '$acl_id_new'";
      $db->query($sql_permission);
      $db->next_record();
      $group_array = $db->Record;
      if (@in_array($group, $group_array)) $array['readwrite'] = true;
      $array['linker'] = "";
      return $array;
      break;
      
      default:
      echo "<pre>Fehler in der Initialisierung des Shares...</pre>";
      return false;
    }
  }

  function add_user($linker_id, $acl_id)
  {
    $result = $this->init_sharing($linker_id, $acl_id, "user");
    $owner = $result['owner'];
    $path = $result['path'];
    $linker = $result['linker'];
    $dest_dav = $result['share'];
    $r_access = $result['read'];
    $w_access = $result['write'];
    $rw_access = $result['readwrite'];

    //Do nothing...
    if( $owner == $linker )
      return false;

    //If the share is activated...
    $return = false;
    if (is_file($path.$this->accessfile)) {
      //Add granted permissions...
      if ($r_access) $return = $this->add_access($linker,$path,"R","linker");
      if ($w_access) $return = $this->add_access($linker,$path,"W","linker");
      if ($rw_access) $return = $this->add_access($linker,$path,"R/W","linker");
    }
    //Generate the "Link" to the share into the Linkers-Share-Folder.
    //It's necessary to check if the parent directories exist.
    if( $return )
      $this->add_sharelink($path,$dest_dav);
  }

  function delete_user($linker_id, $acl_id)
  {
    $result = $this->init_sharing($linker_id, $acl_id, "user");
    $owner = $result['owner'];
    $path = $result['path'];
    $notlinker = $result['linker'];
    $dest_dav = $result['share'];
    $r_access = $result['read'];
    $w_access = $result['write'];
    $rw_access = $result['readwrite'];

    //Do nothing...
    if( $owner == $notlinker )
      return true;

    if ( $r_access )
      $remove = $this->remove_access($notlinker, $path, "R", "linker");
    if ( $w_access )
      $remove = $this->remove_access($notlinker, $path, "W", "linker");

    if ( $remove ) {
      //We have to check if the user is in any granted group. In that case
      //we must not delete the sharelink!
      //$shareControl = $path."/".$this->accessfile;
      //$temp = $this->file_get_contents($shareControl);
      //$groups =  strchr($temp, "##Groups:"); $g = 1;
      //$groups = explode (" ", $groups);
      $list = $this->get_all_linkers($path);
      // Checking groups, one by one... if the linker is in one of the group we
      // can stop the check, and return without deleting the sharelink.
      //while(@$groups[$g] != "" && !stristr($groups[$g], "#") ) {
	//global $GO_GROUPS;
	//$listed_group = $GO_GROUPS->get_group_by_name($groups[$g]);
	//if ($GO_GROUPS->is_in_group($linker_id, $listed_group['id']))
	  //return true;
	//$g++;
      //}
      if( in_array($notlinker, $list) )
	return true;

      $this->remove_sharelink($dest_dav);
    }
  }

  function grouping($group_id, $acl_id, $switch)
  {
    $result = $this->init_sharing($group_id, $acl_id, "group");
    $path = $result['path'];
    $groupname = $result['group'];
    $r_access = $result['read'];
    $w_access = $result['write'];
    $rw_access = $result['readwrite'];
    $owner = $result['owner'];
    $added = false;
    $removed = false;

    switch($switch) {
      case "add":
	if ($r_access)
	  $added = $this->add_access($groupname,$path,"R","group");
	if ($w_access)
	  $added = $this->add_access($groupname,$path,"W","group");
	if ($rw_access)
	  $added = $this->add_access($groupname,$path,"R/W","group");
      break;
      case "delete":
	if ($r_access)
	  $removed = $this->remove_access($groupname,$path,"R","group");
	if ($w_access)
	  $removed = $this->remove_access($groupname,$path,"W","group");
      break;
      default:
      echo "<pre>Failure in checking the group(s)...</pre>";
      return false;
    }

    global $GO_GROUPS;
    if( $added ) {
      //We have to add all the sharelinks for every user of the group!
      $share = strchr($path,"$owner/");
      $count = $GO_GROUPS->get_users_in_group($group_id);
      
      while ( $count ) {
	$linker = $GO_GROUPS->next_record();
	$linker = $linker['username'];

	if( $owner != $linker ) {
	  $dest_dav = $this->dav_home.$linker."/".$this->nsharedir.$share;
	  //Now add the sharelink...
	  $this->add_sharelink($path,$dest_dav);
	}
	
	$count--;
      }
    }

    if( $removed ) {
      // Have to check, if the groupusers are present in the access file as
      // a) stand-alone or b) in another group of the accessfile. So, let us
      // get all the linkers from the share...
      $list = $this->get_all_linkers($path);

      // Now we look if the users are in the array, if not, we are deleting
      // the sharelink...
      $share = strchr($path,"$owner/");
      $count = $GO_GROUPS->get_users_in_group($group_id);
      while ( $count ) {
	$linker = $GO_GROUPS->next_record();
	$linker = $linker['username'];

	if( $owner != $linker ) {
	  $dest_dav = $this->dav_home.$linker."/".$this->nsharedir.$share;
	  if ( in_array($linker, $list) ) {
	    //He has luck! Nothing will happen!
	  } else {
	    // Remove the linked directory of the Groupuser...
	    $this->remove_sharelink($dest_dav);
	  }
	}
	$count--;
      }
    }
  }
  function check_acl( $acl_id )
  {
    global $GO_CONFIG;
    $sql = "SELECT modules.id FROM modules WHERE modules.id = 'filesystem'";
    $this->query($sql);
    $this->next_record();
    if($this->Record != "")
    {
      $sql = "SELECT acl_items.description FROM acl_items
	WHERE acl_items.id = '$acl_id'";
      $this->query($sql);
      $this->next_record();
      if ($this->Record != "")
      {
	$result = $this->Record;
	if ( stristr( $result['description'], $GO_CONFIG->file_storage_path ) )
	  return true;
      }
    }
    return false;
  }
  function get_all_linkers($path)
  {
    //Getting the proper accessfile.
    $shareControl = $path."/".$this->accessfile;

    // Here we get all the Linkers from the accessfile
    $temp = $this->file_get_contents($shareControl);
    //Searching at the end of the file for the linkers...
    $start = strpos($temp, "##Linkers:") + 11;
    $length = strpos($temp, "##Groups:") - $start;
    $linkers = substr($temp, $start, $length );
    $list = explode (" ", $linkers); $l = 1;
    //Little cosmetics for the array: Removing the endmark of the list!
    array_pop($list);

    $groups =  strchr($temp, "##Groups:"); $g = 1;
    $groups = explode (" ", $groups);

    while(@$groups[$g] != "" && !stristr($groups[$g], "#") ) {

      if( $this->dav_auth == "LDAP" ) {
	global $GO_LDAP;
	$GO_LDAP->search("cn=*",$groups[$g],array("cn"));
	$selected_group = $GO_LDAP->get_entries();
	$selected_group = $selected_group[0]['cn'][0];
      } else {
	$selected_group = $groups[$g];
      }

      global $GO_GROUPS;
      $listed_group = $GO_GROUPS->get_group_by_name($selected_group);
      $count = $GO_GROUPS->get_users_in_group($listed_group['id']);
      while ( $count ) {
	array_push($list, $GO_GROUPS->next_record() );
	$count--;
      }
      $g++;
    }
    //Remove multiple linkers...
    $list = array_unique($list);
    //Remove the owner from the list...
    $start = strpos($temp, "##Owner:") + 9;
    $length = strpos($temp, "##Linkers:") - $start;
    $owner = rtrim(substr($temp, $start, $length));
    $key = array_search($owner, $list);
    if( $key )
      array_splice( $list, $key, 1);

    return $list;
  }
  function remove_sharelink($dest_dav)
  {
    //remove the last slash from dest_dav, if its there...
    if( strlen($dest_dav)-1 == strrpos($dest_dav, "/") ) {
      $dest_dav = substr_replace($dest_dav, "", strlen($dest_dav)-1 );
    }

    //Dont ask me why we have to stop here, but there were troubles without it!
    sleep(0.001);
    
    if(is_link($dest_dav)) {
      unlink($dest_dav);
      //Delete the parent directory of the link
      //@rmdir(dirname($dest_dav));
      $this->delete_dirs(dirname($dest_dav));
    }
  }
  function add_sharelink($path, $dest_dav)
  {
    //remove the last slash from dest_dav, if its there...
    if( strlen($dest_dav)-1 == strrpos($dest_dav, "/") ) {
      $dest_dav = substr_replace($dest_dav, "", strlen($dest_dav)-1 );
    }

    if(!is_dir(dirname($dest_dav))) {
      $this->make_dirs(dirname($dest_dav),0755);
      symlink($path,$dest_dav);
    } elseif(!is_link($dest_dav) && !is_dir($dest_dav)) {
      symlink($path,$dest_dav);
    }
  }
  function is_share($path)
  {
    $sql_query = "SELECT * FROM fsShares WHERE fsShares.path='$path'";
    $this->query($sql_query);
    $this->next_record();
    $result = $this->Record;
    return $result;
  }
  function find_new_name($path, $time)
  {
    //Its possible to get here troubles, if the infos will de/increase ...
    exec("stat ".dirname($path)."/*", $output);
    //find a corresponding date and time at Change: to be able to get the
    //new name...
    $Identify = "Change: ".date("Y-m-d")." ".$time.".000000000 ".date("O");
      
    if( $key = array_search($Identify, $output) ) {
      //Cutting off anything which is garbage...
      $newname = substr($output[$key-6], strstr($output[$key-6], "File:")+8);
      $newname = str_replace("'","",$newname);
      $newname = str_replace("`","",$newname);
      return $newname;
    } else {
      return false;
    }
  }
  function alias_to_path($user, $alias)
  {
    return str_replace( $this->alias.$user, $this->dav_home.$user, $alias);
  }
  function linkpath_to_orig($user, $path)
  {
    return str_replace( $this->dav_home.$user."/".$this->nsharedir."/",
      $this->dav_home, $path);
  }
}

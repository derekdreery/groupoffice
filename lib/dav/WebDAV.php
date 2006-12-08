#!/usr/bin/php
<?php
/*
   Copyright Intermesh 2003
   Author: Michael Borko <michael.borko@tgm.ac.at>
   Version: 1.0 Release date: 28 April 2004

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.
 */

//Apache-Log-Input recognition from STDINPUT
$input = fread(STDIN, 256);
$temp = explode(" ", $input);

//Nothing to do ... let the log go ...
if( $temp[2] == "-" ) {
  return true;
}

//Init of all necessary globals
require_once("/var/www/groupoffice/Group-Office.php");
global $GO_DAV, $GO_CONFIG;
$db = new db();

//Logfileinit
define_syslog_variables();
openlog("WebDAV", LOG_NDELAY, LOG_LOCAL3);

$data['time'] = substr($temp[0],strpos($temp[0],":")+1);
//$data['date'] = substr($temp[0],1,strpos($temp[0],":")-1);
//$data['date'] = date("Y-m-d");
$data['user'] = $temp[2];
$data['action'] = $temp[3];
$data['dir'] = $temp[4];

//
//Checking the input only if its a useraction!
//
if( $data['action']=="MOVE" ) {
  //If the Sharedir was renamed: We will find a MOVE under the action, but we
  //wont know in which new Folder the Sharedir was moved. 
  //We have to check the alternation table of the parent directory to be able
  //to determine the new name of the renamed folder!
  //DONT FORGET TO LOG SUCH ACTIONS!!!
  
  //Here we create the searchpattern /Alias/User/Sharedir which ensures that
  //there is no permission to change directories
  $Share = $GO_CONFIG->dav_alias.$data['user']."/".$GO_CONFIG->name_of_sharedir;
  //Look if the dir is a linked share! In this case it is a must to rename the
  //new empty directory and make the link to the sharesource!
  if( strstr($data['dir'],$Share) ) {
    //Create the real-dirname. Use the first occurance of the username as 
    //proof thats the beginning of the dirname, and replace it with the
    //file storage path on the harddisk + username.
    $oldName = $GO_DAV->alias_to_path($data['user'], $data['dir']);
    $newName = $GO_DAV->find_new_name($oldName, $data['time']);
  
    if( $newName == "" ) {
      syslog( LOG_NOTICE, "UNABLE TO RETRIEVE NEWNAME FOR ".$oldName.
	" NO_CHANGE" );
      closelog();
      return true;
    }
    
    $origPath = $GO_DAV->linkpath_to_orig($data['user'], $oldName);
    $result = $GO_DAV->is_share($origPath);

    if( $result[0] != "" ) {
      syslog( LOG_NOTICE, "RENAME_THIS_BACK ".$data['dir'] );

      $cmd = "rm -rf $newName";
      `$cmd`;
      $GO_DAV->add_sharelink($origPath, $oldName);
      syslog( LOG_NOTICE, "LINKED ".$origPath." BACK_TO ".$oldName );
    } else {
      //TODO ONLY IF ITS A DIRECTORY, BUT CHECK IF ITS PERMITTED WITH A 
      //PARENT SHARE!
      if( is_dir($newName) ) {
	syslog( LOG_NOTICE, "RENAME_THIS_BACK ".$data['dir'] );
	$cmd = "mv $newName $oldName";
	`$cmd`;
	syslog( LOG_NOTICE, "RENAMED ".$newName." BACK_TO ".$oldName );
	//Finished! Dont use more time ...
	closelog();
	return true;
      }
      syslog( LOG_NOTICE, "SHARE_FILE ".$oldName." MOVED_TO ".$newName );
    }
    //Finished! Dont use more time ...
    closelog();
    return true;
  }
  
  //Look if the original folder was a share. It should be the owner!
  $oldName = $GO_DAV->alias_to_path($data['user'], $data['dir']);
  $result = $GO_DAV->is_share($oldName);

  if( $result[0] != "" ) {
    //The directory is a share. The next thing to check are the accessrights
    //of the user, which changed this directory. We have to consider, that
    //the owner himself changed the directory.

    //Checking if the username is showing up in the sharename immediately after
    //the dav_home_dir; thats a proof that the owner is renameing a share
    if( strlen($GO_DAV->dav_home) == strpos($oldName, $data['user']) ) {
      syslog( LOG_NOTICE, "UPDATE_THIS_SHARE ".$data['dir']);

      $newName = $GO_DAV->find_new_name($oldName, $data['time']);
      $GO_DAV->update_share($oldName, $newName);
      //Update the Database
      $sql = "UPDATE fsShares SET path='$newName' WHERE path='$oldName'";
      $db->query($sql);	

      syslog( LOG_NOTICE, "SHARE $oldName UPDATED_TO $newName");
      //Finished for now! Dont use more CPU-Time ...
      closelog();
      return true;
    }
  }
  
  syslog( LOG_NOTICE, "WAS_MOVED ".$data['dir']." BY ".$data['user']);
  //Finished for now! Dont use more CPU-Time ...
  closelog();
  return true;
}

if( $data['action']=="DELETE" ) {

  //Here we create the searchpattern /Alias/User/Sharedir which ensures that
  //there is no permission to change directories
  $Share = $GO_CONFIG->dav_alias.$data['user']."/".$GO_CONFIG->name_of_sharedir;
  //Look if the dir is a linked share! In this case it is a must to rename the
  //new empty directory and make the link to the sharesource!
  if( strstr($data['dir'],$Share) ) {
    syslog( LOG_NOTICE, "DELETED OR TRIED TO DELETE ".$data['dir'] );
  }
   
  //Look if the original folder was a share. It should be the owner!
  $oldName = $GO_DAV->alias_to_path($data['user'], $data['dir']);
  $result = $GO_DAV->is_share($oldName);

  if( $result[0] != "" ) {
    //The directory is a share. The next thing to check are the accessrights
    //of the user, which changed this directory. We have to consider, that
    //the owner himself changed the directory.

    //Checking if the username is showing up in the sharename immediately after
    //the dav_home_dir; thats a proof that the owner is renameing a share
    if( strlen($GO_DAV->dav_home) == strpos($oldName, $data['user']) ) {
      syslog( LOG_NOTICE, "DELETE_THIS_SHARE ".$data['dir']);

      $GO_DAV->delete_share($oldName);
      //Update the Database
      $sql = "DELETE FROM fsShares WHERE path='$oldName'";
      $db->query($sql);	

      syslog( LOG_NOTICE, "SHARE $oldName WAS DELETED ENTIRELY");
      //Finished for now! Dont use more CPU-Time ...
      closelog();
      return true;
    }
  }
}

//Closing logentry...
closelog();
?>

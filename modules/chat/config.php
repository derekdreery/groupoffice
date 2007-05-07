<?php
include_once( "../../Group-Office.php" );
//------------------------------------------------------------------------
// This is the MAIN justchat configuration file. From here you can change
// the database connection options, and configure the user and moderator
// management options. Please, MODIFY this file in ANY case, to adapt
// it to your requirements, especially the moderator login options.
//------------------------------------------------------------------------

// choose the connectino type, depending ont the type of database
// that you have decided to use - options are "mysql", "pgsql".
$CONNECTION = 'mysql'; // $GO_CONFIG->db_type;

// enter the connection data, as follows, corresponding to your DB enging
// type and configuration
$_host = $GO_CONFIG->db->Host;
$_user = $GO_CONFIG->db->User;
$_pass = $GO_CONFIG->db->Password;
$_db   = $GO_CONFIG->db->Database;

//------------------------------------------------------------------------
// This function should return 0 or 1. It MUST return 1 if the username
// passed is allowed by the system. This function is called whenever
// a user types in a login usernaame from the applet. If you want to make
// additional database checks to your database, or some any other means
// of checking, here is the place. The example givven makes the username
// "ignisfire" reserved, so that noone can use it via the normal login of
// the applet. This function IS NOT CALLED, if the username is called via
// the <param name="username" value="...."> in the applet HTML tag.
function isUsernameAllowed($username) {
  if (strlen($username) < 2) return 0; //username is too short
  if (strtolower($username) == "") return 0; //ignisfire not allowed
  return 1;
}

//------------------------------------------------------------------------
// The following function checks if a password is valid for specific 
// chatroom moderation. If you have created 10 initial chatrooms, you may
// want to give moderation for them to different people, with different
// passwords. Thus, you may add the checks here. This function receives
// $password and $chatroomid as parameter, and is expected to return 
// 1 on success and 0 if not. In this example, sys moderators (see below)
// always get rights, and for the default chatroom (id 1), the password
// "defaultmod2002" is accepted. This function is called on \modlogin
// command in the applet.
function isModerator($password, $chatroomid) {
  if (isSystemModerator($password)) return 1; // if sys mod - give rights
  return 0;
}


//------------------------------------------------------------------------
// This function checks if the password is correct for a sys moderator.
// The sys moderator has moderating rights for all chatrooms, and also
// has the possibility to disable users, so this should be hard to get.
// Add here any database checks if you wish. The example givven accepts
// the string "mYMod2002t" as sys mod passoword only. This function is 
// called when the user uses the \syslogin command in the applet. It must
// return 1 on success, 0 elseways.
function isSystemModerator($password) {
 session_start();
 if ($_SESSION['chat_admin'] == 1) return 1;
 return 0; 
}

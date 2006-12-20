<?php
//----------------------------------------------------------
//-- Configuration data:
include_once("./config.php");

//----------------------------------------------------------
//-- Establishing connections
if ($CONNECTION == "pgsql") {
  if ($_host) $_host1 = "host='$_host'";
  if ($_db)   $_db1   = "dbname='$_db'";
  if ($_user) $_user1 = "user='$_user'";
  if ($_pass) $_pass1 = "password='$_pass'";

  $conn_id = pg_connect("$_host1 $_db1 $_user1 $_pass1");
} else {
  $conn_id = mysql_connect($_host, $_user, $_pass);
}

//----------------------------------------------------------
//-- DB functions
$_global_counter = 0;
function query($SQL) {
  global $CONNECTION, $_db, $conn_id, $_global_counter;
  if ($CONNECTION == "pgsql") {
    $_global_counter = 0;
    return pg_exec($conn_id, $SQL);
  } else {
    return mysql_db_query($_db, $SQL, $conn_id);
  }
}
function fetch_object($resulthandle) {
  global $CONNECTION, $_global_counter;
  if ($CONNECTION == "pgsql") {
    if ($_global_counter >= pg_numrows($resulthandle)) return 0;
    return pg_fetch_object ($resulthandle, $_global_counter++);
  } else {
    return mysql_fetch_object($resulthandle);
  }
}

//----------------------------------------------------------
//-- required action check
$a = $_GET["a"];

while (strlen($a) && $conn_id) {
  switch (substr($a, -1)) {
    case "1" : //login
      include("chat_login.php");
    break;
    case "2" : //getmessages
      include("chat_getmessages.php");
    break;
    case "3" : //getusers
      include("chat_getusers.php");
    break;
    case "4" : //createchatroom
      include("chat_createchatroom.php");
    break;
    case "5" : //getchatrooms
      include("chat_getchatrooms.php");
    break;
    case "6" : //deletechatroom
      include("chat_deletechatroom.php");
    break;
    case "7" : //adduser
      include("chat_adduser.php");
    break;
    case "8" : //addmessage
      include("chat_addmessage.php");
    break;
    case "9" : //disconnect
      include("chat_disconnect.php");
    break;
    case "a" : //modlogin
      if ($_GET["password"] && $_GET["chatroomid"] && isModerator($_GET["password"], $_GET["chatroomid"])) echo "1\n"; else echo "0\n";
    break;
    case "b" : //syslogin
      if ($_GET["password"] && isSystemModerator($_GET["password"])) echo "1\n"; else echo "0\n";
    break;
  }
  $a = substr($a, 0, strlen($a)-1);
//  echo "\n";
}

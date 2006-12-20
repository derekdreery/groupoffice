<?php
// $Id: init.php,v 1.4 2005/02/16 11:42:54 mschering Exp $

// General initialization code.

require_once('lib/defaults.php');
require_once('config.php');
require_once('lib/url.php');
require_once('lib/pagestore.php');
require_once('lib/rate.php');

$PgTbl = $DBTablePrefix . 'pages';
$IwTbl = $DBTablePrefix . 'interwiki';
$SwTbl = $DBTablePrefix . 'sisterwiki';
$LkTbl = $DBTablePrefix . 'links';
$RtTbl = $DBTablePrefix . 'rate';
$RemTbl = $DBTablePrefix . 'remote_pages';

$FlgChr = chr(255);                     // Flag character for parse engine.

$pagestore = new PageStore();
$db = $pagestore->dbh;

$Entity = array();                      // Global parser entity list.

$RefList = array(); // Array of referenced links, see view_macro_reflist
// Strip slashes from incoming variables.

if(get_magic_quotes_gpc())
{
  $document = stripslashes($document);
  $categories = stripslashes($categories);
  $comment = stripslashes($comment);
  $page = stripslashes($page);
}

// Read user preferences from cookie.

$prefstr = isset($HTTP_COOKIE_VARS[$CookieName])
           ? $HTTP_COOKIE_VARS[$CookieName] : '';

// Choose a textual language for this wiki
#if (defined($LANGUAGE_CODE)) {
#  require_once('lang/lang_'. $LANGUAGE_CODE . '.php');
#} else {
  require_once('lang/default.php');
#}

if(!empty($prefstr))
{
  if(ereg("rows=([[:digit:]]+)", $prefstr, $result))
    { $EditRows = $result[1]; }
  if(ereg("cols=([[:digit:]]+)", $prefstr, $result))
    { $EditCols = $result[1]; }
  if(ereg("user=([^&]*)", $prefstr, $result))
    { $UserName = urldecode($result[1]); }
  if(ereg("days=([[:digit:]]+)", $prefstr, $result))
    { $DayLimit = $result[1]; }
  if(ereg("auth=([[:digit:]]+)", $prefstr, $result))
    { $AuthorDiff = $result[1]; }
  if(ereg("min=([[:digit:]]+)", $prefstr, $result))
    { $MinEntries = $result[1]; }
  if(ereg("hist=([[:digit:]]+)", $prefstr, $result))
    { $HistMax = $result[1]; }
  if(ereg("tzoff=(-?[[:digit:]]+)", $prefstr, $result))
    { $TimeZoneOff = $result[1]; }
}
$user = $GO_USERS->get_user($GO_SECURITY->user_id);
$UserName = $user['first_name']." ".$user['last_name'];

// Commented since headers are written from Group-Office framework
//if($Charset != '')
//  { header("Content-Type: text/html; charset=$Charset"); }

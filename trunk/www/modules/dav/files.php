<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: calendar.php 5573 2010-08-13 14:38:32Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

// settings
require('../../GO.php');

//session_write_close();

// If you want to run the SabreDAV server in a custom location (using mod_rewrite for instance)
// You can override the baseUri here.
// $baseUri = '/';



if(!GO::modules()->dav)
	die('DAV module not installed. Install it at Start menu -> Modules');

// Files we need
require_once GO::config()->root_path.'go/vendor/SabreDAV/lib/Sabre/autoload.php';
//require('autoload.php');


//ini_set('memory_limit','100M');

//$_SESSION['GO_SESSION']['username']='admin';
//$GLOBALS['GO_SECURITY']->user_id=1;
//
//
// Create the root node
//$root = new GO_DAV_Root_Directory('/');

// Authentication backend
$authBackend = new GO_Dav_Auth_Backend();
$userpass = $authBackend->getUserPass();


$children = array();
//if($GLOBALS['GO_SECURITY']->logged_in()){
$children[] = new GO_Dav_Fs_Directory('users/' . $userpass[0]);
$children[] = new GO_Dav_Fs_SharedDirectory();

//}

$root = new Sabre_DAV_SimpleCollection('root',$children);

$tree = new GO_Dav_ObjectTree($root);

// The rootnode needs in turn to be passed to the server class
$server = new Sabre_DAV_Server($tree);

//baseUri can also be /webdav/ with:
//Alias /webdav/ /path/to/files.php
$baseUri = strpos($_SERVER['REQUEST_URI'],'files.php') ? GO::config()->host.'modules/dav/files.php/' : '/webdav/';
$server->setBaseUri($baseUri);

// Support for LOCK and UNLOCK
$lockBackend = new Sabre_DAV_Locks_Backend_FS(GO::config()->tmpdir);
$lockPlugin = new Sabre_DAV_Locks_Plugin($lockBackend);
$server->addPlugin($lockPlugin);

// Support for html frontend
$browser = new Sabre_DAV_Browser_Plugin();
$server->addPlugin($browser);

$auth = new Sabre_DAV_Auth_Plugin($authBackend,'Group-Office WebDAV server');
$server->addPlugin($auth);

// Temporary file filter
$tempFF = new Sabre_DAV_TemporaryFileFilterPlugin(GO::config()->tmpdir);
$server->addPlugin($tempFF);

// And off we go!
$server->exec();

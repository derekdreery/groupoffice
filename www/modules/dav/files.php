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
require('../../Group-Office.php');

// If you want to run the SabreDAV server in a custom location (using mod_rewrite for instance)
// You can override the baseUri here.
// $baseUri = '/';

if(!isset($GO_MODULES->modules['dav']))
	die('dav module not installed');


// Files we need
require_once 'SabreDAV/lib/Sabre/autoload.php';
require('GO_DAV_Auth_Backend.class.inc.php');
require('GO_DAV_FS_Directory.class.inc.php');
//require('GO_DAV_Root_Directory.class.inc.php');
require('GO_DAV_Shared_Directory.class.inc.php');
require('GO_DAV_FS_File.class.inc.php');

require_once ($GO_MODULES->modules['files']['class_path']."files.class.inc.php");


// Create the root node
//$root = new GO_DAV_Root_Directory('/');

$children = array();
if($GO_SECURITY->logged_in()){
	$children[] = new GO_DAV_FS_Directory('users/' . $_SESSION['GO_SESSION']['username']);
	$children[] = new GO_DAV_Shared_Directory();
}

$root = new Sabre_DAV_SimpleDirectory('root',$children);


// The rootnode needs in turn to be passed to the server class
$server = new Sabre_DAV_Server($root);
$server->setBaseUri($GO_MODULES->modules['dav']['url'].'files.php/');

// Support for LOCK and UNLOCK
$lockBackend = new Sabre_DAV_Locks_Backend_FS($GO_CONFIG->tmpdir);
$lockPlugin = new Sabre_DAV_Locks_Plugin($lockBackend);
$server->addPlugin($lockPlugin);

// Support for html frontend
$browser = new Sabre_DAV_Browser_Plugin();
$server->addPlugin($browser);

// Authentication backend
$authBackend = new GO_DAV_Auth_Backend();
$auth = new Sabre_DAV_Auth_Plugin($authBackend,'Group-Office');
$server->addPlugin($auth);

// Temporary file filter
$tempFF = new Sabre_DAV_TemporaryFileFilterPlugin($GO_CONFIG->tmpdir);
$server->addPlugin($tempFF);

// And off we go!
$server->exec();

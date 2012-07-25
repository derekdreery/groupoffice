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

//session writing doesn't make any sense because
define("GO_NO_SESSION", true);

//require_once GO::config()->root_path.'go/vendor/SabreDAV/lib/Sabre/autoload.php';


// Authentication backend
$authBackend = new GO_Dav_Auth_Backend();

if(!GO::modules()->isInstalled("dav"))
	trigger_error('DAV module not installed. Install it at Start menu -> Modules', E_USER_ERROR);


$root = new GO_Dav_Fs_RootDirectory();

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

$auth = new Sabre_DAV_Auth_Plugin($authBackend,GO::config()->product_name);
$server->addPlugin($auth);

// Temporary file filter
$tempFF = new Sabre_DAV_TemporaryFileFilterPlugin(GO::config()->tmpdir);
$server->addPlugin($tempFF);

// And off we go!
$server->exec();

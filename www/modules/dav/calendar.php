<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

// settings
require('../../Group-Office.php');

session_write_close();

if(!isset($GO_MODULES->modules['dav']))
	die('dav module not installed');

require_once($GO_MODULES->modules['calendar']['class_path'].'calendar.class.inc.php');
require_once($GO_MODULES->modules['calendar']['class_path'].'go_ical.class.inc');
require_once($GO_CONFIG->class_path.'ical2array.class.inc');

require_once($GO_MODULES->modules['tasks']['class_path'].'tasks.class.inc.php');
require_once($GO_MODULES->modules['tasks']['class_path'].'export_tasks.class.inc.php');

// Files we need
require_once 'SabreDAV/lib/Sabre/autoload.php';
require('autoload.php');


// The object tree needs in turn to be passed to the server class
$server = new GO_CalDAV_Server();

//baseUri can also be /caldav/ with:
//Alias /caldav/ /path/to/calendar.php
$baseUri = strpos($_SERVER['REQUEST_URI'],'calendar.php') ? $GO_MODULES->modules['dav']['url'].'calendar.php/' : '/caldav/';
$server->setBaseUri($baseUri);


// Support for html frontend
$browser = new Sabre_DAV_Browser_Plugin(false);
$server->addPlugin($browser);

// And off we go!
$server->exec();

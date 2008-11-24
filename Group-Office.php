<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * This class holds the main configuration options of Group-Office
 * Don't modify this file. The values defined here are just default values.
 * They are overwritten by the configuration options in local/config.php.
 * To edit these options use install.php.
 *
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @since Group-Office 1.0
 * @package go.basic
 * @access public
 */

require(dirname(__FILE__).'/classes/base/config.class.inc.php');

//load configuration
$GO_CONFIG = new GO_CONFIG();

if(!$GO_CONFIG->enabled)
{
	die('<h1>Disabled</h1>This Group-Office installation has been disabled');
}

//preload classes before session so they can be stored in the session
if ( isset( $GO_INCLUDES ) ) {
	while ( $include = array_shift( $GO_INCLUDES ) ) {
		require_once( $include );
	}
}

//start session
session_name('groupoffice');
session_start();

//ini_set('display_errors', 'off');
//ini_set('log_errors', 'on');

require_once($GO_CONFIG->root_path.'functions.inc.php');
if(!isset($_SESSION['DIR_CHECK']))
{
	$_SESSION['DIR_CHECK'] = $GO_CONFIG->root_path;
}elseif($_SESSION['DIR_CHECK'] != $GO_CONFIG->root_path)
{
	go_log(LOG_DEBUG, 'Session root path check failed. Stored root path in session: '.
	$_SESSION['DIR_CHECK'].' doesn\'t match the configured one: '.$GO_CONFIG->root_path);

	session_destroy();
	unset($_SESSION);
}

if($GO_CONFIG->debug)
{
	$_SESSION['query_count']=0;
}

require_once($GO_CONFIG->class_path.'base/exceptions.class.inc.php');
require_once($GO_CONFIG->class_path.'base/auth.class.inc.php');
require_once($GO_CONFIG->class_path.'base/security.class.inc.php');
require_once($GO_CONFIG->class_path.'base/groups.class.inc.php');
require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
require_once($GO_CONFIG->class_path.'base/modules.class.inc.php');
require_once($GO_CONFIG->class_path.'base/language.class.inc.php');
require_once($GO_CONFIG->class_path.'base/theme.class.inc.php');
require_once($GO_CONFIG->class_path.'base/links.class.inc.php');

$GO_THEME = new GO_THEME();
$GO_AUTH = new GO_AUTH();
$GO_USERS = new GO_USERS();

if(strlen($_SESSION['GO_SESSION']['timezone'])>3)
{
	//set user timezone setting after user class is loaded
	date_default_timezone_set($_SESSION['GO_SESSION']['timezone']);
}
$GO_GROUPS = new GO_GROUPS();
$GO_LANGUAGE = new GO_LANGUAGE();
$GO_MODULES = new GO_MODULES();
$GO_SECURITY = new GO_SECURITY();
$GO_LINKS = new GO_LINKS();

if (isset($_REQUEST['SET_LANGUAGE'])){
	$GO_LANGUAGE->set_language($_REQUEST['SET_LANGUAGE']);
}

require($GO_LANGUAGE->get_base_language_file('common'));
//require($GO_LANGUAGE->get_base_language_file('filetypes'));

if ( $GO_CONFIG->log ) {
	$username = isset($_SESSION['GO_SESSION']['username']) ? $_SESSION['GO_SESSION']['username'] : 'notloggedin';
	define_syslog_variables();
	openlog('[Group-Office]['.date('Ymd G:i').']['.$username.']', LOG_PERROR, LOG_LOCAL0);
}


require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();

if($GO_SECURITY->user_id>0)
{
	$GO_CONFIG->tmpdir=$GO_CONFIG->tmpdir.$GO_SECURITY->user_id.'/';
}

if(!is_dir($GO_CONFIG->tmpdir))
{
	mkdir($GO_CONFIG->tmpdir,0755, true);
}

unset($type);

define('GO_LOADED', true);

//undo magic quotes
if (get_magic_quotes_gpc())
{
	function stripslashes_array($data) {
		if (is_array($data)){
			foreach ($data as $key => $value){
				$data[$key] = stripslashes_array($value);
			}
			return $data;
		}else{
			return stripslashes($data);
		}
	}

	$_REQUEST=stripslashes_array($_REQUEST);
	$_GET=stripslashes_array($_GET);
	$_POST=stripslashes_array($_POST);
	$_COOKIE=stripslashes_array($_COOKIE);
}


umask(0);
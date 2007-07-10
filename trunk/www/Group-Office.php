<?php
/**
 * @copyright Copyright &copy; Intermesh 2004
 * @version $Revision: 1.68 $ $Date: 2006/11/22 10:59:50 $
 * 
 * @author Markus Schabel <markus.schabel@tgm.ac.at>
 * @author Merijn Schering <mschering@intermesh.nl>

   This file is part of Group-Office.

   Group-Office is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Group-Office is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Group-Office; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 * @package Framework
 * @subpackage Configuration
 */

/**
 * This class holds the main configuration options of Group-Office
 * Don't modify this file. The values defined here are just default values.
 * They are overwritten by the configuration options in local/config.php. 
 * To edit these options use install.php.
 *
 * @author   Merijn Schering <mschering@intermesh.nl>
 * @since    Group-Office 1.0
 * 
 * @package Framework
 * @subpackage Configuration
 * 
 * @access public
 */

require(dirname(__FILE__).'/classes/base/config.class.inc');

//load configuration
$GO_CONFIG = new GO_CONFIG();


if(!empty($GO_CONFIG->default_timezone_string))
{
	date_default_timezone_set($GO_CONFIG->default_timezone_string);
}

if($GO_CONFIG->debug)
{
	list ($usec, $sec) = explode(" ", microtime());
	$load_start = ((float) $usec + (float) $sec);
}

//preload classes before session so they can be stored in the session
if ( isset( $GO_INCLUDES ) ) {
	while ( $include = array_shift( $GO_INCLUDES ) ) {
		require_once( $include );
	}
}

//require_once($GO_CONFIG->class_path.'base/controls.class.inc');

//setting session save path is required for some server configuration
//session_save_path($GO_CONFIG->tmpdir);

//start session
session_start();
if($GO_CONFIG->debug)
{
	$_SESSION['query_count']=0;
}


//require external auth_sources file
if ( $GO_CONFIG->auth_sources != '' ) {
	require_once($GO_CONFIG->auth_sources);
} else {
	$auth_sources = array();
}

if(isset($_REQUEST['auth_source_key']) && isset( $auth_sources[$_REQUEST['auth_source_key']]))
{
	SetCookie("GO_AUTH_SOURCE_KEY", $_REQUEST['auth_source_key'], time()+3600*24*30,"/",'',0);
	$_COOKIE['GO_AUTH_SOURCE_KEY'] = $_REQUEST['auth_source_key'];
	$_SESSION['auth_source'] = $auth_sources[$_REQUEST['auth_source_key']];
}elseif(!isset($_SESSION['auth_source']))
{
	if(isset($GO_SYNC))
	{
		$_SESSION['auth_source'] = isset($auth_sources[$GO_CONFIG->sync_auth_source_key]) ? $auth_sources[$GO_CONFIG->sync_auth_source_key] : null;
	}else
	{
		$_SESSION['auth_source'] = (isset($_COOKIE['GO_AUTH_SOURCE_KEY']) && isset( $auth_sources[$_COOKIE['GO_AUTH_SOURCE_KEY']]  )) ? $auth_sources[$_COOKIE['GO_AUTH_SOURCE_KEY']]  : null;
	}
}

$user_manager = $type = 'sql';

if (isset($_SESSION['auth_source'])) {
	if ( ( $_SESSION['auth_source']['type'] == "ldap" ) |
	( $_SESSION['auth_source']['user_manager'] == "ldap" ) )
	{
		require_once($GO_CONFIG->root_path.'database/ldap.class.inc');
		$GO_LDAP = new ldap();
	}

	$user_manager = $_SESSION['auth_source']['user_manager'];
	$type = $_SESSION['auth_source']['type'];
}

require_once($GO_CONFIG->class_path.'base/'.$type.'.auth.class.inc');
require_once($GO_CONFIG->class_path.'base/'.$user_manager.'.security.class.inc');
require_once($GO_CONFIG->class_path.'base/'.$user_manager.'.groups.class.inc');
require_once($GO_CONFIG->class_path.'base/'.$user_manager.'.users.class.inc');

if ( $type == 'ldap' && $user_manager == 'sql' ) {
	require_once($GO_CONFIG->class_path.'base/ldap.users.class.inc');
}

require_once($GO_CONFIG->class_path.'base/modules.class.inc');

require_once($GO_CONFIG->class_path.'/date/Date.php');

require_once($GO_CONFIG->root_path.'functions.inc');
require_once($GO_CONFIG->root_path.'adodb-time.inc.php');
require_once($GO_CONFIG->class_path."base/language.class.inc");
require_once($GO_CONFIG->class_path.'base/theme.class.inc');
require_once($GO_CONFIG->class_path.'base/links.class.inc');

/*
 * Maybe these should be defined in the files where the class is?
 */
$go_users_class = $user_manager.'_users';
$go_groups_class = $user_manager.'_groups';
$go_security_class = $user_manager.'_security';
$go_auth_class = $type.'_auth';

$GO_LANGUAGE = new GO_LANGUAGE();
$GO_THEME = new GO_THEME();
$GO_AUTH = new $go_auth_class();
$GO_SECURITY = new $go_security_class();
$GO_USERS = new $go_users_class();
$GO_GROUPS = new $go_groups_class();
//Important that GO_SECURITY is loaded before GO_MODULES
$GO_MODULES = new GO_MODULES();
$GO_LINKS = new GO_LINKS();

if ( $GO_CONFIG->dav_switch ) {
	require_once($GO_CONFIG->class_path.'dav.class.inc');
	$GO_DAV = new dav();
}
if ( isset( $_REQUEST['SET_LANGUAGE'] ) ) {
	$GO_LANGUAGE->set_language( $_REQUEST['SET_LANGUAGE'] );
}
require_once($GO_LANGUAGE->get_base_language_file('common'));
require_once($GO_LANGUAGE->get_base_language_file('filetypes'));

if ( $GO_CONFIG->log ) {

	$username = isset($_SESSION['GO_SESSION']['username']) ? $_SESSION['GO_SESSION']['username'] : 'notloggedin';
	define_syslog_variables();
	openlog('[Group-Office]['.date('Ymd G:i').']['.$username.']', LOG_PERROR, LOG_LOCAL0);
}


define('GO_LOADED', true);

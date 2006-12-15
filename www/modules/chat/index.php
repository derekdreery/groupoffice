<?php
/*
 * Displays the daily garfield strip from ucomics.com
 *
 * Author: Markus Schabel <markus.schabel@tgm.ac.at>
 *
 * TODO add support for multiple comics
 */

// Require main configuration file
require_once( "../../Group-Office.php" );

// Check if a user is logged in. If not try to login via cookies. If that
// also fails then show the login-screen.
$GO_SECURITY->authenticate();

// Check if the user is allowed to access this module.
$GO_MODULES->authenticate( 'chat' );

$page_title = "Chat";

$module = $GO_MODULES->get_module('chat');
$admin  = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $module['acl_write']);

$_SESSION['chat_admin'] = $admin;

require_once( $GO_THEME->theme_path."header.inc" );

$username = $_SESSION['GO_SESSION']['username'];
if (strpos($username,'@')) { 
 $username = explode('@',$username);
 $username = $username[0];
}

echo '<table><td>'."\n";
echo '<applet code="justchat.class" archive="'.$GO_CONFIG->host.'modules/chat/justchat.jar" width=700 height=400>'."\n".
     '<param name="username" value="'.$username.'">'."\n".
     '<param name="mediaconfig" value="'.$GO_CONFIG->host.'modules/chat/media.cfg">'."\n".
     '</applet>'."\n";
echo '</td></table>'."\n";


require_once( $GO_THEME->theme_path."footer.inc" );
?>

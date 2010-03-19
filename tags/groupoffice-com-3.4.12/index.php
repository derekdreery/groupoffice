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

header('Content-Type: text/html; charset=UTF-8');
require_once("Group-Office.php");

//$config_file = $GO_CONFIG->get_config_file();
if(empty($GO_CONFIG->title))
{
	header('Location: install/');
	exit();
}

//Redirect to correct login url if a force_login_url is set. Useful to foce ssl
if($GO_CONFIG->force_login_url && strpos($GO_CONFIG->full_url,$GO_CONFIG->force_login_url)===false) {
	header('Location: '.$GO_CONFIG->force_login_url);
	exit();
}

$mtime = $GO_CONFIG->get_setting('upgrade_mtime');

if($mtime!=$GO_CONFIG->mtime)
{
	if($GO_SECURITY->logged_in())
		$GO_SECURITY->logout();
	
	echo '<html><head><style>body{font-family:arial;}</style></head><body>';
	echo '<h1>'.$lang['common']['running_sys_upgrade'].'</h1><p>'.$lang['common']['sys_upgrade_text'].'</p>';
	require($GO_CONFIG->root_path.'install/upgrade.php');
	echo '<a href="#" onclick="document.location.reload();">'.$lang['common']['click_here_to_contine'].'</a>';
	echo '</body></html>';
	exit();
}


//will do autologin here before theme is loaded.
$GO_SECURITY->logged_in();


if(isset($_REQUEST['task']) && $_REQUEST['task']=='logout')
{
	$GO_SECURITY->logout();	
	if(isset($_COOKIE['GO_FULLSCREEN']) && $_COOKIE['GO_FULLSCREEN']=='1')
	{
		?>
		<script type="text/javascript">
		window.close();
		</script>
		<?php
		exit();
	}
}

require_once($GO_THEME->theme_path."layout.inc.php");
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

require_once("Group-Office.php");

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
//$config_file = $GO_CONFIG->get_config_file();
if(empty($GO_CONFIG->installed))
{
	header('Location: install/');
	exit();
}

if(!is_dir($GO_CONFIG->tmpdir))
{
	mkdir($GO_CONFIG->tmpdir,0755, true);
}


require_once($GO_THEME->theme_path."layout.inc.php");

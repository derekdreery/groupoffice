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
 * @author Boy Wijnmaalen <bwijnmaalen@intermesh.nl>
 */

require_once("../../Group-Office.php");
$GO_SECURITY->html_authenticate('modules');
require_once($GO_LANGUAGE->get_language_file('modules'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

	<title>
		<?php echo $GO_CONFIG->title.' - '.$modules['modules']['name']; ?>
	</title>
	<?php	
		function get_stylesheet($module_id)
		{
			global $GO_CONFIG, $GO_THEME;
	
			$file = $GO_CONFIG->root_path.'modules/'.$module_id.'/themes/'.$GO_THEME->theme.'/style.css';
			$url = $GO_CONFIG->host.'modules/'.$module_id.'/themes/'.$GO_THEME->theme.'/style.css';
			if(!file_exists($file))
			{
				$file = $GO_CONFIG->root_path.'modules/'.$module_id.'/themes/Default/style.css';
				$url = $GO_CONFIG->host.$module_id.'/themes/Default/style.css';
				if(!file_exists($file))
				{
					return '';
				}
			}
	
			return '<link href="'.$url.'" type="text/css" rel="stylesheet" />';
		}
	
		require($GO_CONFIG->root_path.'default_head.inc');
		require($GO_CONFIG->root_path.'default_scripts.inc');
		
		require_once ($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem();
		$folders = $fs->get_folders($GO_CONFIG->module_path);
 		while($module = array_shift($folders))
 		{
			echo get_stylesheet($module['name']);
		}
	?>
	<script type="text/javascript" src="language/<?php echo $modules_js_lang; ?>"></script>
	<script type="text/javascript" src="../../javascript/grids/RowExpander.js"></script>
	<script type="text/javascript" src="../../javascript/grids/CheckColumn.js"></script>
	<script type="text/javascript" src="rowexpander.js"></script>
	<script type="text/javascript" src="modules.js"></script>
</head>
<body>
	<div id="grid1"></div>
	<div id="grid2"></div>
</body>
</html>             
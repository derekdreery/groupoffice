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
require('Group-Office.php');

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
require($GO_THEME->theme_path.'default_head.inc.php');
?>
</head>
<body>
<?php
require($GO_CONFIG->root_path.'default_scripts.inc.php');

/*
$load_modules=isset($_REQUEST['load_modules']) ? explode(',', $_REQUEST['load_modules']) : array();


$load_modules[]=$_REQUEST['module'];

foreach($load_modules as $module_id)
{
	$module = $GO_MODULES->modules[$module_id];
	
	echo '<script type="text/javascript" src="'.$module['url'].'language/en.js"></script>';
	echo "\n";
	
	if(file_exists($module['path'].'language/'.$GO_LANGUAGE->language.'.js'))
	{
		echo '<script type="text/javascript" src="'.$module['url'].'language/'.$GO_LANGUAGE->language.'.js"></script>';
		echo "\n";
	}
	
	if(file_exists($module['path'].'scripts.txt') && $GO_CONFIG->debug)
	{					
		$data = file_get_contents($module['path'].'scripts.txt');
		$lines = explode("\n", $data);
		foreach($lines as $line)
		{
			if(!empty($line))
			{
				echo '<script type="text/javascript" src="'.$GO_CONFIG->host.$line.'"></script>';
				echo "\n";
			}
		}
	}else if(file_exists($module['path'].'all-module-scripts-min.js'))
	{
		echo '<script type="text/javascript" src="'.$module['url'].'all-module-scripts-min.js"></script>';
		echo "\n";
	}
		
	if(file_exists($module['path'].'scripts.inc.php'))
	{
		require($module['path'].'scripts.inc.php');
	}	
}*/
?>
<script type="text/javascript">

Ext.onReady(function(){

	GO.mainLayout.fireReady();
	
	var panel = GO.moduleManager.getPanel("<?php echo $_REQUEST['module']; ?>");
	
	if(!panel)
		panel = GO.moduleManager.getAdminPanel("<?php echo $_REQUEST['module']; ?>");

	var viewport = new Ext.Viewport({
		layout:'fit',				
		items: panel
	});
});

</script>
</body>
</html>

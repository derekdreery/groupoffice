<?php
require('../../Group-Office.php');

if(!$GO_SECURITY->logged_in())
{
	header('Location: '.$GO_CONFIG->host.'index.php?after_login_url='.$_SERVER['PHP_SELF']);
	exit();
}
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
?>
<script src="<?php echo $GO_CONFIG->host; ?>javascript/tiny_mce/tiny_mce.js" type="text/javascript"></script>
<script src="<?php echo $GO_CONFIG->host; ?>javascript/form/TinyMCE.js" type="text/javascript"></script>

<script type="text/javascript">
var init = function(){
	var	panel = new GO.cms.MainPanel(); 

	var viewport = new Ext.Viewport({
		layout:'fit',				
		items: panel
	});
};

Ext.onReady(init);
</script>
</body>
</html>
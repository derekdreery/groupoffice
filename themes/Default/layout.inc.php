<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
require($GO_THEME->theme_path.'default_head.inc.php');
?>
</head>
<body>
<div id="loading-mask" style="width:100%;height:100%;background:#f1f1f1;position:absolute;z-index:20000;left:0;top:0;">&#160;</div>
<div id="loading">
	<div class="loading-indicator">
	<img src="<?php echo $GO_THEME->theme_url; ?>images/groupoffice.gif" style="display:block;margin-bottom:15px;" />
	<img src="<?php echo $GO_CONFIG->host; ?>ext/resources/images/default/grid/loading.gif" style="width:16px;height:16px;vertical-align:middle" />&#160;<span id="load-status"><?php echo $lang['common']['loadingCore']; ?></span>
	<div style="font-size:10px; font-weight:normal;margin-top:15px;">Copyright &copy; Intermesh 2003-2008</div>
	</div>
</div>
<!-- include everything after the loading indicator -->


<?php
require($GO_CONFIG->root_path.'default_scripts.inc.php');

if($GO_SECURITY->logged_in())
{
	?>
	<div id="mainNorthPanel">
		<div id="headerLeft">
			<img src="<?php echo $GO_CONFIG->host; ?>themes/Default/images/go_logo.gif" style="text-align:middle;vertical-align:middle;border:0px;padding:6px;" />
			<?php echo $lang['common']['loggedInAs'].' '.htmlspecialchars($_SESSION['GO_SESSION']['name']); ?>
		</div>
		<div id="headerRight">
			
			<span id="notification-area">				
			</span>			
			
			<img id="reminder-icon" src="<?php echo $GO_THEME->theme_url; ?>images/16x16/reminders.png" style="border:0;vertical-align:middle;cursor:pointer" />
			<img id="checker-icon" src="<?php echo $GO_CONFIG->host; ?>ext/resources/images/default/grid/loading.gif" style="border:0;vertical-align:middle" />
			
				
			<img src="<?php echo $GO_CONFIG->host; ?>themes/Default/images/16x16/icon-search.png" style="border:0px;margin-left:10px;margin-right:1px;vertical-align:middle" />
			<input type="text" name="search_query" value="<?php echo $lang['common']['search']; ?>..." id="search_query" onfocus="javascript:this.value='';" onblur="javascript:this.value='<?php echo $lang['common']['search']; ?>...';" onkeypress="return GO.mainLayout.search(event);" class="textbox" />
	
			
			&nbsp;&nbsp;<a id="admin-menu-link" href="#"><?php echo $lang['common']['adminMenu']; ?></a>		
			
			<a href="#" id="configuration-link">
				|&nbsp;&nbsp;<?php echo $lang['common']['settings']; ?></a>
			<a href="#" id="help-link">
				|&nbsp;&nbsp;<?php echo $lang['common']['help']; ?></a>
			<a href="<?php echo $GO_CONFIG->host; ?>index.php?task=logout" target="_top">
				|&nbsp;&nbsp;<?php echo $lang['common']['logout']; ?></a>
		</div>
	</div>
	
	<?php	
	
	foreach($GO_MODULES->modules as $module)
	{
		if($module['read_permission'])
		{
			//if(!$module['legacy'])
			//{
				
					
				
				echo '<script type="text/javascript">Ext.get("load-status").update("'.$lang['common']['loadingModule'].' '.$module['humanName'].'");</script>';
				
				
				if(file_exists($module['path'].'language/en.js'))
				{
					echo '<script type="text/javascript" src="'.$module['url'].'language/en.js"></script>';
					echo "\n";
				}
				
				if($GO_LANGUAGE->language!='en' && file_exists($module['path'].'language/'.$GO_LANGUAGE->language.'.js'))
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
					
				
			/*}else
			{
				if($module['admin_menu']=='1')
				{
					?>
					<script type="text/javascript">
					
					GO.moduleManager.addAdminModule('<?php echo $module['id']; ?>', Ext.Panel, {
						title : '<?php echo $module['humanName']; ?>',
						iconCls : 'go-module-icon-<?php echo $module['id']; ?>',
						items: [ new GO.panel.IFrameComponent({ 'id': '<?php echo $module['id']; ?>', 'url': '<?php echo $module['url']; ?>' }) ],
						layout: 'fit',
						closable: true
					});
					
					</script>
					
					
					<?php
				}else
				{
					?>
					<script type="text/javascript">
					
					GO.moduleManager.addModule('<?php echo $module['id']; ?>', Ext.Panel, {
						title : '<?php echo $module['humanName']; ?>',
						iconCls : 'go-module-icon-<?php echo $module['id']; ?>',
						items: [ new GO.panel.IFrameComponent({ 'id': '<?php echo $module['id']; ?>', 'url': '<?php echo $module['url']; ?>' }) ],
						layout: 'fit'
					});
					
					</script>
					
					
					<?php
				}
				
			}*/
		}
	}
	?>
	<script src="<?php echo $GO_CONFIG->host; ?>javascript/panels/SearchPanel.js" type="text/javascript"></script>
	<script type="text/javascript">Ext.get("load-status").update("<?php echo $lang['common']['renderInterface']; ?>");</script>	
	<script type="text/javascript">
	Ext.onReady(GO.mainLayout.init, GO.mainLayout);
	</script>
<?php
}else
{
	?>
	<script type="text/javascript">Ext.get("load-status").update("<?php echo $lang['common']['loadingLogin']; ?>");</script>
	<script type="text/javascript">	
	Ext.onReady(GO.mainLayout.login, GO.mainLayout);
	</script>
	<?php	
}
?>
<?php
/*
<object width="1" height="1" id="alarmSound">
<param name="movie" value="<?php echo $GO_THEME->theme_url; ?>reminder.swf">
<param name="loop" value="false">
<param name="autostart" value="false">
<embed src="<?php echo $GO_THEME->theme_url; ?>reminder.swf" autostart=false loop="false" width="1" height="1" name="alarmSound">
</embed>
</object>
*/
 ?>
</body>
</html>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
require($GO_THEME->theme_path.'default_head.inc.php');
?>
</head>
<body>
<div id="loading-mask" style="width:100%;height:100%;background:#c3daf9;position:absolute;z-index:20000;left:0;top:0;">&#160;</div>
<div id="loading">
	<div class="loading-indicator">
	<img src="<?php echo $GO_THEME->theme_url; ?>images/groupoffice.gif" style="display:block;margin-bottom:15px;" />
	<img src="<?php echo $GO_CONFIG->host; ?>ext/resources/images/default/grid/loading.gif" style="width:16px;height:16px;vertical-align:middle" />&#160;<span id="load-status"><?php echo $lang['common']['loadingCore']; ?></span>
	<div style="font-size:10px;margin-top:15px;">Copyright &copy; Intermesh 2003-2008</div>
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
			<img src="<?php echo $GO_CONFIG->host; ?>themes/Default/images/16x16/go-icon.png" style="text-align:middle;vertical-align:middle;border:0px;padding:2px;" />
			<?php echo $lang['common']['loggedInAs'].' '.htmlspecialchars($_SESSION['GO_SESSION']['name']); ?>
		</div>
		<div id="headerRight">
			
			<span id="notification-area">				
			</span>			
			
			<img id="reminder-icon" src="<?php echo $GO_THEME->theme_url; ?>images/16x16/reminders.png" style="border:0;vertical-align:middle;cursor:pointer" />
			<img id="checker-icon" src="<?php echo $GO_CONFIG->host; ?>ext/resources/images/default/grid/loading.gif" style="border:0;vertical-align:middle" />
			
				
			<img src="<?php echo $GO_CONFIG->host; ?>themes/Default/images/16x16/icon-search.png" style="border:0px;margin-left:10px;margin-right:1px;vertical-align:middle" />
			<input type="text" name="search_query" value="<?php echo $lang['common']['search']; ?>..." id="search_query" onfocus="javascript:this.value='';" onblur="javascript:this.value='<?php echo $lang['common']['search']; ?>...';" onkeypress="return GO.mainLayout.search(event);" class="textbox" />
	
			
			<a id="admin-menu-link" href="#">
			<img src="<?php echo $GO_CONFIG->host; ?>themes/Default/images/16x16/admin.png" style="border:0px;margin-right:3px;width:16px;height:16px;vertical-align:middle;" /><?php echo $lang['common']['adminMenu']; ?></a>		
			
			<a href="#" id="configuration-link">
				<img src="<?php echo $GO_CONFIG->host; ?>themes/Default/images/16x16/administration.png" style="border:0px;margin-right:3px;vertical-align:middle;" /><?php echo $lang['common']['settings']; ?></a>
			<a href="#" id="help-link">
				<img src="<?php echo $GO_CONFIG->host; ?>themes/Default/images/16x16/icon-help.png" style="border:0px;margin-right:3px;vertical-align:middle;" /><?php echo $lang['common']['help']; ?></a>
			<a href="<?php echo $GO_CONFIG->host; ?>index.php?task=logout" target="_top">
				<img src="<?php echo $GO_CONFIG->host; ?>themes/Default/images/16x16/gnome-logout.png" style="border:0px;margin-right:3px;vertical-align:middle;" /><?php echo $lang['common']['logout']; ?></a>
		</div>
	</div>
	<script type="text/javascript">Ext.get("load-status").update("<?php echo $lang['common']['renderInterface']; ?>");</script>	
	<script type="text/javascript">
	Ext.onReady(GO.mainLayout.init, GO.mainLayout);
	</script>
<?php
}else
{
	?>
	<div id="checker-icon"></div>
	<script type="text/javascript">Ext.get("load-status").update("<?php echo $lang['common']['loadingLogin']; ?>");</script>
	<script type="text/javascript">	
	Ext.onReady(GO.mainLayout.login, GO.mainLayout);
	</script>
	<?php	
}
?>
<object width="1" height="1" id="alarmSound">
<param name="movie" value="<?php echo $GO_THEME->theme_url; ?>reminder.swf">
<param name="loop" value="false">
<param name="autostart" value="false">
<embed src="<?php echo $GO_THEME->theme_url; ?>reminder.swf" autostart=false loop="false" width="1" height="1" name="alarmSound">
</embed>
</object>
</body>
</html>

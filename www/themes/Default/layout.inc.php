<?php
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
<div id="loading-mask" style="width:100%;height:100%;background:#f1f1f1;position:absolute;z-index:20000;left:0;top:0;">&#160;</div>
<div id="loading">
	<div class="loading-indicator">
	<img class="loading-logo" src="<?php echo $GO_THEME->theme_url; ?>images/groupoffice.gif" />
	<img src="<?php echo $GO_CONFIG->host; ?>ext/resources/images/default/grid/loading.gif" style="width:16px;height:16px;vertical-align:middle" />&#160;<span id="load-status"><?php echo $lang['common']['loadingCore']; ?></span>
	<div style="font-size:10px; font-weight:normal;margin-top:15px;">Copyright &copy; Intermesh 2003-2008</div>
	</div>
</div>
<!-- include everything after the loading indicator -->


<?php
require($GO_CONFIG->root_path.'default_scripts.inc.php');

/*
 * If we don't have a name of the user then we don't open Group-Office yet. The login dialog will ask to complete the 
 * profile. This typically happens when IMAP authentication is used. A user without a name is added.
 * 
 * When $popup_groupoffice is set in /default_scripts.inc.php we need to display the login dialog and launch GO in a popup.
 */

if($GO_SECURITY->logged_in() && trim($_SESSION['GO_SESSION']['name']) != '' && !isset($popup_groupoffice))
{
	?>
	<div id="mainNorthPanel">
		<div id="headerLeft">
			<img class="go_logo" src="<?php echo $GO_THEME->theme_url; ?>images/go_logo.gif" />
			<?php echo $lang['common']['loggedInAs'].' '.htmlspecialchars($_SESSION['GO_SESSION']['name']); ?>
		</div>
		<div id="headerRight">
			
			<span id="notification-area">				
			</span>			
			
			<img id="reminder-icon" src="<?php echo $GO_THEME->theme_url; ?>images/16x16/reminders.png" style="border:0;vertical-align:middle;cursor:pointer" />
			<img id="checker-icon" src="<?php echo $GO_CONFIG->host; ?>ext/resources/images/default/grid/loading.gif" style="border:0;vertical-align:middle" />
			
				
			<img src="<?php echo $GO_CONFIG->host; ?>themes/Default/images/16x16/icon-search.png" style="border:0px;margin-left:10px;margin-right:1px;vertical-align:middle" />
			<input type="text" name="search_query" value="<?php echo $lang['common']['search']; ?>..." id="search_query" onfocus="javascript:this.value='';" onblur="javascript:this.value='<?php echo $lang['common']['search']; ?>...';" onkeypress="return GO.mainLayout.search(event);" class="textbox" />
	
			
			&nbsp;&nbsp;<a id="admin-menu-link" href="#"><?php echo $lang['common']['adminMenu']; ?>&nbsp;&nbsp;|</a>		
			
			<a href="#" id="configuration-link">
				<?php echo $lang['common']['settings']; ?>&nbsp;&nbsp;|</a>
			<a href="#" id="help-link">
				<?php echo $lang['common']['help']; ?>&nbsp;&nbsp;|</a>
			<a href="<?php echo $GO_CONFIG->host; ?>index.php?task=logout" target="_top">
				<?php echo $lang['common']['logout']; ?></a>
		</div>
	</div>
	
	<script type="text/javascript">Ext.get("load-status").update("<?php echo $lang['common']['renderInterface']; ?>");</script>	
	<script type="text/javascript">
	Ext.onReady(GO.mainLayout.init, GO.mainLayout);
	</script>
<?php
}else
{
	if(isset($popup_groupoffice))
	{
		?>
		<script type="text/javascript">
		
		</script>
		<?php
	}
	?>
	<div id="checker-icon"></div>
	<script type="text/javascript">Ext.get("load-status").update("<?php echo $lang['common']['loadingLogin']; ?>");</script>
	<script type="text/javascript">	
	<?php
	if(isset($popup_groupoffice))
	{
		echo 'Ext.onReady(function(){
			//GO.mainLayout.login.defer(10000, GO.mainLayout);
			GO.mainLayout.login();
			GO.mainLayout.launchFullscreen("'.$popup_groupoffice.'");
		}, GO.mainLayout.login);';
	}else
	{
		echo 'Ext.onReady(GO.mainLayout.login, GO.mainLayout);';
	}
	?>
	</script>
	
	<div style="position:absolute;right:10px;bottom:10px">
	Powered by Group-Office: <a target="_blank" class="normal-link" href="http://www.group-office.com">http://www.group-office.com</a>	
	</div>
	<?php	
}
?>
<object width="0" height="0" id="alarmSound">
<param name="movie" value="<?php echo $GO_THEME->theme_url; ?>reminder.swf">
<param name="loop" value="false">
<param name="autostart" value="false">
<embed src="<?php echo $GO_THEME->theme_url; ?>reminder.swf" autostart=false loop="false" width="0" height="0" name="alarmSound">
</embed>
</object>
</body>
</html>

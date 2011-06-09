<?php
$webCache = new GO_Web_Cache();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="x-ua-compatible" content="IE=8">
<meta name="robots" content="noindex" />
<link href="<?php echo GO::config()->theme_url; ?>Default/images/groupoffice.ico?" rel="shortcut icon" type="image/x-icon">
<title><?php echo GO::config()->title; ?></title>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
<meta name="description" content="Take your office online. Share projects, calendars, files and e-mail online with co-workers and clients. Easy to use and fully customizable, Group-Office takes online colaboration to the next level." />
<?php
$webCache->addStylesheet(GO::config()->root_path.'ext/resources/css/ext-all.css', GO::config()->host.'ext/resources/css/');
$webCache->addStylesheet(GO::config()->root_path.'themes/Default/xtheme-groupoffice.css', GO::config()->host.'themes/Default/');
$webCache->addStylesheet(GO::config()->root_path.'themes/Default/style.css', GO::config()->host.'themes/Default/');
//$webCache->load_module_stylesheets();
				

$webCache->getCachedCss();

GO::events()->fire_event('head');

if(isset(GO::modules()->modules['customcss']) && file_exists(GO::config()->file_storage_path.'customcss/style.css'))
	echo '<style>'.file_get_contents(GO::config()->file_storage_path.'customcss/style.css').'</style>'."\n";

?>

</head>
<body>
<div id="loading-mask" style="width:100%;height:100%;background:#f1f1f1;position:absolute;z-index:20000;left:0;top:0;">&#160;</div>
<div id="loading">
	<div class="loading-indicator">
	<img src="<?php echo GO::config()->host; ?>ext/resources/images/default/grid/loading.gif" style="width:16px;height:16px;vertical-align:middle" />&#160;<span id="load-status"><?php echo $lang['common']['loadingCore']; ?></span>
	<div id="copyright" style="font-size:10px; font-weight:normal;margin-top:15px;">Copyright &copy; <?php
	if(GO::config()->product_name!='Group-Office'){
		echo GO::config()->product_name;
	}else{ echo 'Intermesh BV';

	} ?> 2003-<?php echo date('Y'); ?></div>
	</div>
</div>
<!-- include everything after the loading indicator -->


<?php
require(GO::config()->root_path.'default_scripts.inc.php');

/*
 * If we don't have a name of the user then we don't open Group-Office yet. The login dialog will ask to complete the 
 * profile. This typically happens when IMAP authentication is used. A user without a name is added.
 * 
 * When $popup_groupoffice is set in /default_scripts.inc.php we need to display the login dialog and launch GO in a popup.
 */

if(GO::security()->logged_in() && trim($_SESSION['GO_SESSION']['name']) != '' && !isset($popup_groupoffice))
{
	?>
	<div id="mainNorthPanel">
		<div id="headerLeft">
			<?php echo $lang['common']['loggedInAs'].' '.htmlspecialchars($_SESSION['GO_SESSION']['name']); ?>
		</div>
		<div id="headerRight">
			
			<span id="notification-area">				
			</span>			
			
			<img id="reminder-icon" src="<?php echo GO::config()->host; ?>themes/Default/images/16x16/reminders.png" style="border:0;vertical-align:middle;cursor:pointer" />
			<!-- <img id="checker-icon" src="<?php echo GO::config()->host; ?>ext/resources/images/default/grid/loading.gif" style="border:0;vertical-align:middle" /> -->
			
			<?php if (isset(GO::modules()->modules['search']) && GO::modules()->modules['search']['read_permission']) {
			//echo '<img src="'.GO::config()->host.'themes/Default/images/16x16/icon-search.png" style="border:0px;margin-left:10px;margin-right:1px;vertical-align:middle" />';
			}
			?>
			
			<span id="search_query"></span>
			
			<a id="start-menu-link" href="#"><?php echo $lang['common']['startMenu']; ?></a>

												<span class="top-menu-separator">|</span>
			
			<a href="#" id="configuration-link">
				<?php echo $lang['common']['settings']; ?></a>

												<span class="top-menu-separator">|</span>
                        
			<a href="#" id="help-link">
				<?php echo $lang['common']['help']; ?></a>

			<?php
			if(!GO::security()->http_authenticated_session){?>
			<span class="top-menu-separator">|</span>
			<a href="javascript:GO.mainLayout.logout();">
				<?php echo $lang['common']['logout']; ?></a>
            <?php } ?>
		</div>
	</div>
	
	<script type="text/javascript">Ext.get("load-status").update("<?php echo $lang['common']['loadingModules']; ?>");</script>	
	<script type="text/javascript">
	/*window.onbeforeunload=function(){
		return "<?php echo addslashes($lang['common']['confirm_leave']); ?>";
	};*/

	Ext.onReady(GO.mainLayout.init, GO.mainLayout);
	</script>
<?php
}else
{

	?>

	<div id="go-powered-by" style="position:absolute;right:10px;bottom:10px">
	Powered by Group-Office: <a target="_blank" class="normal-link" href="http://www.group-office.com">http://www.group-office.com</a>
	</div>

	<div id="checker-icon"></div>
	<script type="text/javascript">Ext.get("load-status").update("<?php echo $lang['common']['loadingLogin']; ?>");</script>
	<script type="text/javascript">	
	<?php
	//set in /default_scripts.inc.php
	if(isset($popup_groupoffice))
	{
		echo 'Ext.onReady(function(){
			GO.mainLayout.login();
			GO.mainLayout.launchFullscreen("'.$popup_groupoffice.'");
		}, GO.mainLayout.login);';
	}else
	{
		echo 'Ext.onReady(GO.mainLayout.login, GO.mainLayout);';
	}
	?>
	</script>
	
	
	<?php	
}

if(GO::security()->logged_in() && empty($_SESSION['GO_SESSION']['mute_sound']))
{
?>
	<object width="0" height="0" id="alarmSound">
	<param name="movie" value="<?php echo $GO_THEME->theme_url; ?>reminder.swf" />
	<param name="loop" value="false" />
	<param name="autostart" value="false" />
	<embed src="<?php echo $GO_THEME->theme_url; ?>reminder.swf" autostart=false loop="false" width="0" height="0" name="alarmSound"></embed>
	</object>
<?php
} 
?>
</body>
</html>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
<meta name="robots" content="noindex" />
<meta http-equiv="x-ua-compatible" content="IE=8">
<?php
$favicon = !empty(GO::config()->favicon) ? GO::config()->favicon : $view->getTheme()->getUrl()."images/groupoffice.ico?";
?>
<link href="<?php echo $favicon; ?>" rel="shortcut icon" type="image/x-icon">
<title><?php echo GO::config()->title; ?></title>
<?php
$view->addStylesheet($view->getPath().'ext/resources/css/ext-all.css', $view->getUrl().'ext/resources/css/');
$view->addStylesheet($view->getPath().'themes/Default/style.css', $view->getUrl().'themes/Default/');
$view->addStylesheet($view->getPath().'themes/ExtJS/style.css', $view->getUrl().'themes/ExtJS/');
$view->loadModuleStylesheets();
?>
<link href="<?php echo $view->getCachedStylesheet(); ?>" type="text/css" rel="stylesheet" />
<?php
if(!empty(GO::config()->custom_css_url))
	echo '<link href="'.GO::config()->custom_css_url.'" type="text/css" rel="stylesheet" />';

//$this is GO_Core_Controller_Auth
$this->fireEvent('head');

?>


</head>
<body>
<div id="loading-mask" style="width:100%;height:100%;background:#f1f1f1;position:absolute;z-index:20000;left:0;top:0;">&#160;</div>
<div id="loading">
	<div class="loading-indicator">
	<img src="<?php echo GO::config()->host; ?>views/Extjs3/ext/resources/images/default/grid/loading.gif" style="width:16px;height:16px;vertical-align:middle" />&#160;<span id="load-status"><?php echo GO::t('loadingCore'); ?></span>
	<div id="copyright" style="font-size:10px; font-weight:normal;margin-top:15px;">Copyright &copy; <?php
	if(GO::config()->product_name!='Group-Office'){
		echo GO::config()->product_name;
	}else{ echo 'Intermesh BV';

	} ?> 2003-<?php echo date('Y'); ?></div>
	</div>
</div>
<!-- include everything after the loading indicator -->


<?php
require(GO::config()->root_path.'views/Extjs3/default_scripts.inc.php');

/*
 * If we don't have a name of the user then we don't open Group-Office yet. The login dialog will ask to complete the 
 * profile. This typically happens when IMAP authentication is used. A user without a name is added.
 * 
 * When $popup_groupoffice is set in /default_scripts.inc.php we need to display the login dialog and launch GO in a popup.
 */
if(GO::user())
{
	?>
	<div id="mainNorthPanel">
<!--		<div id="beta">BETA</div>-->
		<div id="headerLeft">
			<?php echo GO::t('loggedInAs').' '.htmlspecialchars(GO::user()->name); ?>
		</div>
		<div id="headerRight">
			
			<span id="notification-area">				
			</span>			
			
			<img id="reminder-icon" src="<?php echo GO::config()->host; ?>views/Extjs3/themes/Default/images/16x16/reminders.png" style="border:0;vertical-align:middle;cursor:pointer" />
			
			<span id="search_query"></span>
			
			<a id="start-menu-link" href="#"><?php echo GO::t('startMenu'); ?></a>

												<span class="top-menu-separator">|</span>
			
			<a href="#" id="configuration-link">
				<?php echo GO::t('settings'); ?></a>

												<span class="top-menu-separator">|</span>
                        
			<a href="#" id="help-link">
				<?php echo GO::t('help'); ?></a>

			<span class="top-menu-separator">|</span>
			<a href="javascript:GO.mainLayout.logout();">
			<?php echo GO::t('logout'); ?></a>
  	</div>
	</div>
	
	<script type="text/javascript">Ext.get("load-status").update("<?php echo GO::t('loadingModules'); ?>");</script>	
	<script type="text/javascript">
	Ext.onReady(GO.mainLayout.init, GO.mainLayout);
	</script>
<?php
}else
{

	?>

	<div id="go-powered-by" style="position:absolute;right:10px;bottom:10px">
	Powered by <?php echo GO::config()->product_name; ?>
	<?php if(GO::config()->product_name=='Group-Office'){ ?>
	: <a target="_blank" class="normal-link" href="http://www.group-office.com">http://www.group-office.com</a>
	<?php } ?>
	</div>

	<div id="checker-icon"></div>
	<script type="text/javascript">Ext.get("load-status").update("<?php echo GO::t('loadingLogin'); ?>");</script>
	<script type="text/javascript">	
	Ext.onReady(GO.mainLayout.login, GO.mainLayout);
	</script>
	
	
	<?php	
}

if(GO::user() && !GO::user()->mute_sound)
{
?>
	<object width="0" height="0" id="alarmSound">
	<param name="movie" value="<?php echo $view->getTheme()->getUrl(); ?>reminder.swf" />
	<param name="loop" value="false" />
	<param name="autostart" value="false" />
	<embed src="<?php echo $view->getTheme()->getUrl(); ?>reminder.swf" autostart=false loop="false" width="0" height="0" name="alarmSound"></embed>
	</object>
<?php
} 
?>
</body>
</html>

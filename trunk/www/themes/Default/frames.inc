<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php echo $GO_CONFIG->title; ?></title>
<?php
require($GO_CONFIG->root_path.'default_head.inc');
?>
<link href="<?php echo $GO_THEME->theme_url; ?>css/framework.css" type="text/css" rel="stylesheet"></link>
</head>
<body>
<div id="loading-mask" style="width:100%;height:100%;background:#c3daf9;position:absolute;z-index:20000;left:0;top:0;">&#160;</div>
<div id="loading">
	<div class="loading-indicator">
	<img src="<?php echo $GO_THEME->theme_url; ?>images/GOCOM.gif" style="display:block;margin-bottom:15px;">
	<img src="<?php echo $GO_CONFIG->host; ?>ext/resources/images/default/grid/loading.gif" style="width:16px;height:16px;" align="absmiddle">&#160;Loading...
	<div style="font-size:10px;margin-top:15px;">Copyright &copy Intermesh 2003-2007</div>
	</div>
</div>
<!-- include everything after the loading indicator -->

<?php
require($GO_CONFIG->root_path.'default_scripts.inc');
?>
<script src="<?php echo $GO_CONFIG->host; ?>groupoffice.js" type="text/javascript"></script>
  


<div id="header">
	<div id="headerLeft">
		<img src="<?php echo $GO_CONFIG->host; ?>lib/go-icon.png" style="text-align:middle;vertical-align:middle;border:0px;padding:2px;" />
		<?php echo $strLoggedInAs.' '.htmlspecialchars($_SESSION['GO_SESSION']['name']); ?>
	</div>
	<div id="headerRight">
		
		<img src="<?php echo $GO_CONFIG->host; ?>themes/Default/images/16x16/icon-search.png" style="border:0px;margin-left:10px;margin-right:1px;" align="absmiddle"></img>
		<input type="text" name="search_query" value="<?php echo $cmdSearch; ?>..." id="search_query" onfocus="javascript:this.value='';" onblur="javascript:this.value='<?php echo $cmdSearch; ?>...';" onkeypress="return search(event);" class="textbox" />
		<a href="<?php echo $GO_CONFIG->host; ?>configuration/" target="mainframe">
			<img src="<?php echo $GO_CONFIG->host; ?>themes/Default/images/16x16/administration.png" style="border:0px;margin-right:3px;" align="absmiddle" />Configuration</a>
		<a href="<?php echo $GO_CONFIG->host; ?>help.php" target="_blank">
			<img src="<?php echo $GO_CONFIG->host; ?>themes/Default/images/16x16/icon-help.png" style="border:0px;margin-right:3px;" align="absmiddle" />Help</a>
		<a href="<?php echo $GO_CONFIG->host; ?>index.php?task=logout" target="_top">
			<img src="<?php echo $GO_CONFIG->host; ?>themes/Default/images/16x16/gnome-logout.png" style="border:0px;margin-right:3px;" align="absmiddle" />Logout</a>
	</div>
</div>

<div id="center"></div>




<script type="text/javascript">


function search(e)
{
	var keynum;	
	var input;
	input = Ext.get("search_query");
	
	if(window.event) // IE
	{
		keynum = e.keyCode
	}else if(e.which) // Netscape/Firefox/Opera
	{
		keynum = e.which
	}
	
	if(keynum==13)
	{
		GroupOffice.search(input.getValue());
	}
	return true;
}


</script>

</body>
</html>
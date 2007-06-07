<html>
<head>
<title><?php echo $GO_CONFIG->title; ?></title>
<?php
require($GO_CONFIG->root_path.'default_head.inc');
?>
<script src="<?php echo $GO_CONFIG->host; ?>groupoffice.js" type="text/javascript"></script>
<script src="<?php echo $GO_CONFIG->host; ?>links.js" type="text/javascript"></script>
</head>
<body>
<div id="container">
	<div id="north">
	<table id="headerTable">
	<tbody>
		<tr>
		<td style="width:15%">
			<img src="<?php echo $GO_CONFIG->host; ?>lib/go-icon.png" style="text-align:middle;vertical-align:middle;border:0px;padding:2px;" />
			<?php echo $strLoggedInAs.' '.htmlspecialchars($_SESSION['GO_SESSION']['name']); ?>
		</td>
		<td style="text-align:right;width:70%">
			<iframe style="height:20px;width:100%;border:0;" frameborder="0" scrolling="no" name="checker" ALLOWTRANSPARENCY="true" src="<?php echo $GO_CONFIG->host; ?>checker.php"> </iframe></td>
		<td style="text-align:right;width:15%">
			<img src="<?php echo $GO_CONFIG->host; ?>themes/Default/images/16x16/icon-search.png" style="border:0px;margin-left:10px;margin-right:3px;" align="absmiddle"></img>
			<input type="text" name="search_query" value="Search..." id="search_query" onfocus="javascript:this.value='';" onblur="javascript:this.value='Search...';" onkeypress="return search(event);" class="textbox" />
			<a href="<?php echo $GO_CONFIG->host; ?>configuration/" target="mainframe">
				<img src="<?php echo $GO_CONFIG->host; ?>themes/Default/images/16x16/administration.png" style="border:0px;margin-right:3px;" align="absmiddle" />Configuration</a>
			<a href="<?php echo $GO_CONFIG->host; ?>help.php" target="_blank">
				<img src="<?php echo $GO_CONFIG->host; ?>themes/Default/images/16x16/icon-help.png" style="border:0px;margin-right:3px;" align="absmiddle" />Help</a>
			<a href="<?php echo $GO_CONFIG->host; ?>index.php?task=logout" target="_top">
				<img src="<?php echo $GO_CONFIG->host; ?>themes/Default/images/16x16/gnome-logout.png" style="border:0px;margin-right:3px;" align="absmiddle" />Logout</a>
		</td>
	</tr>
	</tbody>
	</table>
	</div>
	
	<div id="east">
	<div id="searchtoolbar"></div>
	<div id="searchgrid"></div>
	</div>
	
	<div id="center">
	<iframe id="mainframe" name="mainframe" frameborder="0" src="<?php echo $start_url; ?>" style="width:100%;height:100%"></iframe>
	</div>
</div>


<div id="dialog" style="visibility:hidden;height:100%;width:100%;"></div>

<div id="search_links_dialog" style="visibility:hidden;">
	<div class="x-dlg-hd">Link to items</div>

	<div class="x-dlg-bd">
		<div id="search_links_input" style="height:20px;">
			<input class="x-form-text x-form-field" type="text" id="links_query" name="links_query" onkeypress="return GroupOffice.searchLinksKeyEvent(event);" />
		</div>
		<div id="search_links_grid" style="height:300px;padding-top:10px;"></div>
	</div>
</div>
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
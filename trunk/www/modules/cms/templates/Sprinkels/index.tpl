<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="nl" lang="nl">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<meta name="robots" content="all,index,follow" />
<meta name="keywords" content="{$file.keywords}" />
<meta name="description" content="{$file.description}" />
<title>{$site.name} - {$file.title}</title>
<link href="{$template_url}css/editor.css" rel="stylesheet" type="text/css" />
<link href="{$template_url}css/stylesheet.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{$cms_url}plugins/ext-core.js"></script>
{literal}
<script type="text/javascript" >
// Copyright 2006-2007 javascript-array.com

var timeout	= 500;
var closetimer	= 0;
var ddmenuitem	= 0;

// open hidden layer
function mopen(id)
{
	// cancel close timer
	mcancelclosetime();

	// close old layer
	if(ddmenuitem) ddmenuitem.style.visibility = 'hidden';

	// get new layer and show it
	ddmenuitem = document.getElementById(id);
	ddmenuitem.style.visibility = 'visible';

}
// close showed layer
function mclose()
{
	if(ddmenuitem) ddmenuitem.style.visibility = 'hidden';
}

// go close timer
function mclosetime()
{
	closetimer = window.setTimeout(mclose, timeout);
}

// cancel close timer
function mcancelclosetime()
{
	if(closetimer)
	{
		window.clearTimeout(closetimer);
		closetimer = null;
	}
}

// close layer when click-out
document.onclick = mclose;
</script>
{/literal}

</head>

<body>

{php}
var_dump($_SESSION['sprinkels']);
{/php}

<div id="main">

	<div id="header-logo">
	</div>
	<div id="header-menu">
	</div>

	<div id="menu-bar">
		<div id="menu-panel">
			<ul id="menu">
				{items root_path="Hoofdmenu" item_template="menu/menu_item.tpl" }
			</ul>
		</div>
		<div id="rss">
		</div>
	</div>


	<div id="right-panel">
		<div id="right-panel-shadow">
		</div>
		<div id="right-panel-container">
			<div id="right-panel-title">
				{if $file.type eq "column"}
					{$file.option_values.column_title}
				{else}
					{if $smarty.session.sprinkels.last_column_path neq ''}
						{php}
							global $co;

							$path = $_SESSION['sprinkels']['last_column_path'];
							{
								$file = $co->resolve_url($path, $co->site['root_folder_id']);
								$file = $co->get_file($file['id']);

								if(!$file)
									echo 'Path not found: '.$path;
								else {
									echo $file['option_values']['column_title'];
								}
							}
						{/php}
					{else}
						{items max_items=1 root_path="columns" item_template="pages/column_item_title.tpl"}
					{/if}
				{/if}
			</div>
			<div id="right-panel-date">
				{if $file.type eq "column"}
					{$file.option_values.column_date}
				{else}
					{if $smarty.session.sprinkels.last_column_path neq ''}
						{php}
							global $co;

							$path = $_SESSION['sprinkels']['last_column_path'];
							{
								$file = $co->resolve_url($path, $co->site['root_folder_id']);
								$file = $co->get_file($file['id']);

								if(!$file)
									echo 'Path not found: '.$path;
								else {
									echo $file['option_values']['column_date'];
								}
							}
						{/php}
					{else}
						{items max_items=1 root_path="columns" item_template="pages/column_item_date.tpl"}
					{/if}
				{/if}
			</div>
			<div id="right-panel-content">
				{if $file.type eq "column"}
					{$file.content}
				{else}
					{if $smarty.session.sprinkels.last_column_path neq ''}
						{include_file path="`$smarty.session.sprinkels.last_column_path`"}
					{else}
						{items max_items=1 root_path="columns" item_template="pages/column_item.tpl"}
					{/if}
				{/if}
			</div>
		</div>
		<div id="right-panel-bottom">
			<div class="right-menu">
					<a href="#" onmouseover="javascript:mopen('{$item.name}-div');" onmouseout="javascript:mclosetime();"><span class="button_up"><img src="{$template_url}images/green_button_up.bmp" /></span>Meer columns...</a>
					<div class="right-menu-panel" id="{$item.name}-div" onmouseover="javascript:mcancelclosetime();" onmouseout="javascript:mclosetime();" >
						<div class="right-menu-top"></div>
						<div class="right-menu-mid">
							{items root_path="columns" item_template="menu/right_menu_item.tpl" }
						</div>
						<div class="right-menu-bottom"></div>
					</div>
			</div>
		</div>
		<div id="right-panel-img">
		</div>
	</div>

	<div id="left-panel">
		<div id="left-panel-content">
			{if $file.type eq "nieuws_voorpagina"}
				{items max_items=3 root_path="nieuws" item_template="pages/nieuws_item.tpl"}
			{elseif $file.type eq "nieuws_item"}
				{items max_items=3 root_path="nieuws" item_template="pages/nieuws_item.tpl" active_item_template="pages/nieuws_item_active.tpl"}
			{elseif $file.type eq "column"}
				{if $smarty.session.sprinkels.last_page_path neq ''}
					{if $smarty.session.sprinkels.last_page_path eq 'Hoofdmenu/home'}
						{items max_items=3 root_path="nieuws" item_template="pages/nieuws_item.tpl" active_item_template="pages/nieuws_item_active.tpl"}
					{else}
						{include_file path="`$smarty.session.sprinkels.last_page_path`"}
					{/if}
				{else}
					{items max_items=3 root_path="nieuws" item_template="pages/nieuws_item.tpl"}
				{/if}
			{else}
				{$file.content}
			{/if}
		</div>
	</div>

</div>
</body>
</html>

{php}
	if (substr($_GET['path'],0,7)=='columns') {
		$_SESSION['sprinkels']['last_column_path'] = $_GET['path'];
	} else {
		$_SESSION['sprinkels']['last_page_path'] = $_GET['path'];
	}
{/php}
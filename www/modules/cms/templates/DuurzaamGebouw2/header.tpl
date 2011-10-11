<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<meta name="robots" content="all,index,follow" />
<meta name="keywords" content="{$file.keywords}" />
<meta name="description" content="{$file.description}" />
<title>{$file.title}</title>
<link href="{$template_url}css/editor.css" rel="stylesheet" type="text/css" />
<link href="{$template_url}css/stylesheet.css" rel="stylesheet" type="text/css" />
<link href="{$template_url}css/buttons.css" rel="stylesheet" type="text/css" />
<link href="{$template_url}css/tabs.css" rel="stylesheet" type="text/css" />
<link href="{$template_url}css/file_frontend.css" rel="stylesheet" type="text/css" />
<link rel="icon" href="{$template_url}favicon.ico" />
{if $site.domain=='eisolutions.nl'}
	<link href="{$template_url}css/eisolutions.css" rel="stylesheet" type="text/css" />
<link rel="icon" href="{$template_url}images/eisolutions.ico" />
{else}
	<link rel="icon" href="{$template_url}favicon.ico" />
{/if}

<script type="text/javascript" src="{$cms_url}templates/DuurzaamGebouw2/scripts/jquery.innerfade/js/jquery.js"></script>
<script type="text/javascript" src="{$cms_url}templates/DuurzaamGebouw2/scripts/jquery.innerfade/js/jquery.innerfade.js"></script>

<script type="text/javascript">
	  {literal} $(document).ready(
				function(){
					$('#news').innerfade({
						animationtype: 'slide',
						speed: 750,
						timeout: 2000,
						type: 'random',
						containerheight: '1em'
					});
					
					$('ul#portfolio').innerfade({
						speed: 1000,
						timeout: 5000,
						type: 'sequence',
						containerheight: '220px'
					});
					
					$('.fade').innerfade({
						speed: 1000,
						timeout: 6000,
						type: 'random_start',
						containerheight: '170px'
					});
					
					$('.adi').innerfade({
						speed: 'slow',
						timeout: 5000,
						type: 'random',
						containerheight: '150px'
					});

			});{/literal}
  	</script>

{$head}
</head>
<body>
<div id="main-container">
	<div id="top-bar">
		<div id="menu1-panel">
			{items root_path="MENU1" wrap_div="false" class="topmenu-item-center" item_template="menu/menu1_item.tpl" active_item_template="menu/menu1_item_active.tpl"}
		</div>
		<a id="logo" href="{$site_url}"><img src="{$template_url}/images/logo.jpg" /></a>
		<div id="menu2-panel">
			{items root_path="MENU2" wrap_div="false" class="topmenu-item-center" item_template="menu/menu2_item.tpl" active_item_template="menu/menu2_item_active.tpl"}
		</div>
	</div>

	{literal}
	<script type="text/javascript">
		var header_images = new Array();
		{/literal}
			{if $file.option_values.headers_dir neq ''}
				{files path="`$file_storage_path``$file.option_values.headers_dir`" template="parts/topbar_img.tpl"}
			{/if}

		{if $file.option_values.image neq ''}
			var default_header = '{thumbnail_url path="`$file.option_values.image`" zc=1 w=700 h=180"}';
		{else}
			var default_header = '{$template_url}images/nieuws.jpg';
		{/if}

		{literal}
	</script>
	{/literal}

	<div id="header-images-panel">
		<img id="topbar_img1" />
		<img id="topbar_img2" />
	</div>

{php}
global $co;
	$path_array = explode('/',$this->_tpl_vars['file']['path']);

	$path = '';
	$path_links = '';
	foreach ($path_array as $path_part) {
		if (substr($path_part,0,4)!='MENU') {
			$path .= '/'.$path_part;
			$path_links .= '<a href="'.$co->create_href_by_path(urlencode($path)).'">'.urldecode($path_part).'</a>&nbsp;/&nbsp;';
		} else {
			$path .= '/'.$path_part;
		}
	}

	if (count($path_array)>2)
		$this->assign('file_path',$path_links);
	else
		$this->assign('file_path','');

{/php}

	<div id="middle-bar">
		<a name="the-anchor"></a>
		<div id="path-panel">
			{$file_path}
		</div>
		<div id="menu3-panel">
			{items root_path="MENU3" wrap_div="false" item_template="menu/menu3_item.tpl" active_item_template="menu/menu3_item_active.tpl"}
		</div>
	</div>

	<div class="center-container">		
		{literal}
		<script type="text/javascript">
			if (header_images.length<1)
				document.getElementById('topbar_img1').src=default_header;
			else
				document.getElementById('topbar_img1').src=header_images[0];
			var currently = 1;
			if (header_images.length>1) {
				var topbarTimerObject = {
					timeoutId : setTimeout("switchTopBar(topbarTimerObject)",6000),
					counter : '0'
				};
				function switchTopBar(object) {
					clearTimeout(object.timeoutId);
					if (currently == 1)
						var id = "topbar_img1";
					else
						var id = "topbar_img2";
					$('#'+id).fadeOut('slow');
					//document.getElementById(id).style.display="none";
					if (object.counter<header_images.length-1) {
						object.counter ++;
					} else {
						object.counter=0;
					}
					if (currently == 1) {
						var id = "topbar_img2";
						currently = 2;
					} else {
						var id = "topbar_img1";
						currently = 1;
					}
					document.getElementById(id).src=header_images[object.counter];
					$('#'+id).fadeIn('slow');
					object.timeoutId = setTimeout("switchTopBar(topbarTimerObject)",6000);
				}
			}
		</script>
		{/literal}
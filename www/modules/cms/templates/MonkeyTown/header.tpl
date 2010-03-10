<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>
Monkeytown
</title>

<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<meta name="robots" content="all,index,follow" />
<meta name="keywords" content="{$file.keywords}" />
<meta name="description" content="{$file.description}" />
<title>{$file.title} - {$site.name}</title>
<link href="{$template_url}css/editor.css" rel="stylesheet" type="text/css" />
<link href="{$template_url}css/stylesheet.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="{$template_url}images/favicon.ico" />
<script type="text/javascript" src="{$template_url}lib/form.js"></script>
<script language="javascript" type="text/javascript" src="{$template_url}lib/calendar/calendar.core.js"></script>

{if substr($file.type,0,5)=='photo'}
	<link rel="stylesheet" type="text/css" href="{$cms_url}plugins/shadowbox/shadowbox.css" />
	<script type="text/javascript" src="{$cms_url}plugins/shadowbox/shadowbox.js"></script>

	{literal}
	<script type="text/javascript">
	Shadowbox.init({
			language:   "nl",
			players:    ["img"]
	});
	</script>
	{/literal}
{/if}
</head>
<body>
<div id="mainwrapper">

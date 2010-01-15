<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<meta name="robots" content="all,index,follow" />
<meta name="keywords" content="{$file.keywords}" />
<meta name="description" content="{$file.description}" />
<title>{$file.title} - {$site.name}</title>
<link href="{$template_url}css/editor.css" rel="stylesheet" type="text/css" />
<link href="{$template_url}css/stylesheet.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" type="text/css" href="{$cms_url}plugins/shadowbox/shadowbox.css" />
<script type="text/javascript" src="{$cms_url}plugins/shadowbox/shadowbox.js"></script>

{literal}

<!--[if (IE 7)]>
	<style type="text/css">
		.main-container {
			left : 15px;
		}
		.afbeeldingen-container {
			position : relative;
			left: 9px;
		}
	</style>
<![endif]-->
<!--[if (IE 6)]>
	<style type="text/css">
		.homebuttons-container {
			position : relative;
			left : 20px;
		}
		.afbeeldingen-container {
			position : relative;
			left : 0px;
		}
		.menu-container {
				position : absolute;
				left : 79px;
		}
		.buttons-container {
			position : relative;
				left : 69px;
		}
		.buttons {
			position : absolute;
		}
		.content_wit {
			height : 460px;
			position : relative;
			left : 240px;
		}
		.content_rood {
			position : relative;
			left : 240px;
		}
		.schaduw-container {
			position : relative;
			top : 440px;
			left : -66px;
		}
		.album-thumb {
			position : absolute;
		}
	</style>
<![endif]-->

<script type="text/javascript">
Shadowbox.init({
    language:   "nl",
    players:    ["img","iframe","flv"]
});
</script>

{/literal}

</head>
<body>
<title><?php echo $GO_CONFIG->title; ?></title>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
<link href="<?php echo $GO_CONFIG->host; ?>ext/resources/css/ext-all.css" type="text/css" rel="stylesheet" />
<?php
foreach($GO_MODULES->modules as $module)
{
	echo $GO_THEME->get_stylesheet($module['id']);	
}?>
<link href="<?php echo $GO_CONFIG->theme_url; ?>Default/images/favicon.ico" rel="shotcut icon" />
<link href="<?php echo $GO_CONFIG->theme_url; ?>Default/style.css" type="text/css" rel="stylesheet" />
<link href="<?php echo $GO_THEME->theme_url; ?>style.css" type="text/css" rel="stylesheet" />
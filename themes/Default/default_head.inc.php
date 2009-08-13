<title><?php echo $GO_CONFIG->title; ?></title>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
<meta name="description" content="Take your office online. Share projects, calendars, files and e-mail online with co-workers and clients. Easy to use and fully customizable, Group-Office takes online colaboration to the next level." />

<link href="<?php echo $GO_CONFIG->host; ?>ext/resources/css/ext-all.css" type="text/css" rel="stylesheet" />
<link href="<?php echo $GO_CONFIG->theme_url; ?>Default/xtheme-groupoffice.css" type="text/css" rel="stylesheet" />

<link href="<?php echo $GO_CONFIG->theme_url; ?>Default/images/favicon.ico" rel="shotcut icon" />

<link href="<?php echo $GO_CONFIG->theme_url; ?>Default/style.css" type="text/css" rel="stylesheet" />

<?php
foreach($GO_MODULES->modules as $module)
{
	echo $GO_THEME->get_stylesheet($module['id']);	
}
?>


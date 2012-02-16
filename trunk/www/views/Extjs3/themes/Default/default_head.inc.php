<meta name="robots" content="noindex" />
<link href="<?php echo GO::config()->host; ?>views/Extjs3/themes/Default/images/groupoffice.ico?" rel="shortcut icon" type="image/x-icon">
<title><?php echo GO::config()->title; ?> - BETA</title>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
<meta name="description" content="Take your office online. Share projects, calendars, files and e-mail online with co-workers and clients. Easy to use and fully customizable, Group-Office takes online colaboration to the next level." />
<?php

$root_path = $GLOBALS['GO_CONFIG']->root_path.'views/Extjs3/';
$root_url = $GLOBALS['GO_CONFIG']->host.'views/Extjs3/';


$GLOBALS['GO_THEME']->add_stylesheet($root_path.'ext/resources/css/ext-all.css', $root_url.'ext/resources/css/');
$GLOBALS['GO_THEME']->add_stylesheet($root_path.'themes/Default/xtheme-groupoffice.css', $root_url.'themes/Default/');
$GLOBALS['GO_THEME']->add_stylesheet($root_path.'themes/Default/style.css', $root_url.'themes/Default/');
//$GLOBALS['GO_THEME']->add_stylesheet($root_path.'javascript/plupload/ext.ux.plupload.css', $root_url.'ext/resources/css/');
$GLOBALS['GO_THEME']->load_module_stylesheets();
$GLOBALS['GO_THEME']->get_cached_css();

$GLOBALS['GO_EVENTS']->fire_event('head');
?>

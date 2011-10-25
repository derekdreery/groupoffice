<link href="<?php echo $GLOBALS['GO_CONFIG']->theme_url; ?>Default/images/groupoffice.ico?" rel="shortcut icon" type="image/x-icon">
<title><?php echo $GLOBALS['GO_CONFIG']->title; ?></title>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
<meta name="description" content="Take your office online. Share projects, calendars, files and e-mail online with co-workers and clients. Easy to use and fully customizable, Group-Office takes online colaboration to the next level." />
<?php
$GLOBALS['GO_THEME']->add_stylesheet($GLOBALS['GO_CONFIG']->root_path.'ext/resources/css/ext-all.css');
$GLOBALS['GO_THEME']->add_stylesheet($GLOBALS['GO_CONFIG']->root_path.'themes/Default/style.css');
$GLOBALS['GO_THEME']->add_stylesheet($GLOBALS['GO_CONFIG']->root_path.'themes/ExtJS/style.css');
$GLOBALS['GO_THEME']->load_module_stylesheets();

$GLOBALS['GO_THEME']->get_cached_css();

$GLOBALS['GO_EVENTS']->fire_event('head');
?>
<?php
$root_path = $GLOBALS['GO_CONFIG']->root_path.'views/Extjs3/';
$root_url = $GLOBALS['GO_CONFIG']->host.'views/Extjs3/';
?>
<link href="<?php echo $root_url; ?>themes/Default/images/groupoffice.ico?" rel="shortcut icon" type="image/x-icon">
<title><?php echo GO::config()->title; ?></title>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
<?php
$GLOBALS['GO_THEME']->add_stylesheet($root_path.'ext/resources/css/ext-all.css', $root_url.'ext/resources/css/');
$GLOBALS['GO_THEME']->add_stylesheet($root_path.'themes/Default/style.css', $root_url.'themes/Default/');
$GLOBALS['GO_THEME']->add_stylesheet($root_path.'themes/ExtJS/style.css', $root_url.'themes/ExtJS/');
//$GLOBALS['GO_THEME']->add_stylesheet($root_path.'javascript/plupload/ext.ux.plupload.css', $root_url.'ext/resources/css/');
$GLOBALS['GO_THEME']->load_module_stylesheets();
$GLOBALS['GO_THEME']->get_cached_css();

$GLOBALS['GO_EVENTS']->fire_event('head');

$this->fireEvent('head');
?>
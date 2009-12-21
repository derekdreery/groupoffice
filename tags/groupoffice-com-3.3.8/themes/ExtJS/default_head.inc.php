<title><?php echo $GO_CONFIG->title; ?></title>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
<?php
$GO_THEME->add_stylesheet($GO_CONFIG->root_path.'ext/resources/css/ext-all.css');
$GO_THEME->add_stylesheet($GO_CONFIG->root_path.'themes/Default/style.css');
$GO_THEME->add_stylesheet($GO_CONFIG->root_path.'themes/ExtJS/style.css');
$GO_THEME->load_module_stylesheets();

$GO_THEME->get_cached_css();
?>
<link href="<?php echo $GO_CONFIG->theme_url; ?>Default/images/favicon.ico" rel="shotcut icon" />

<meta name="robots" content="noindex" />
<link href="<?php echo GO::config()->theme_url; ?>Default/images/groupoffice.ico?" rel="shortcut icon" type="image/x-icon">
<title><?php echo GO::config()->title; ?></title>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
<meta name="description" content="Take your office online. Share projects, calendars, files and e-mail online with co-workers and clients. Easy to use and fully customizable, Group-Office takes online colaboration to the next level." />
<?php
$GO_THEME->add_stylesheet(GO::config()->root_path.'ext/resources/css/ext-all.css', GO::config()->host.'ext/resources/css/');
$GO_THEME->add_stylesheet(GO::config()->root_path.'themes/Default/xtheme-groupoffice.css', GO::config()->host.'themes/Default/');
$GO_THEME->add_stylesheet(GO::config()->root_path.'themes/Default/style.css', GO::config()->host.'themes/Default/');
$GO_THEME->load_module_stylesheets();
$GO_THEME->get_cached_css();

GO::events()->fire_event('head');
?>

<title><?php echo $GO_CONFIG->title; ?></title>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
<meta name="description" content="Take your office online. Share projects, calendars, files and e-mail online with co-workers and clients. Easy to use and fully customizable, Group-Office takes online colaboration to the next level." />

<?php
$GO_THEME->add_stylesheet($GO_CONFIG->root_path.'ext/resources/css/ext-all.css', $GO_CONFIG->host.'ext/resources/css/');
$GO_THEME->add_stylesheet($GO_CONFIG->root_path.'themes/Default/xtheme-groupoffice.css', $GO_CONFIG->host.'themes/Default/');
$GO_THEME->add_stylesheet($GO_CONFIG->root_path.'themes/Default/style.css', $GO_CONFIG->host.'themes/Default/');
$GO_THEME->load_module_stylesheets();

$cssfile = $GO_CONFIG->local_path.'/cache/'.$GO_SECURITY->user_id.'-style.css';
$cssurl = $GO_CONFIG->local_url.'/cache/'.$GO_SECURITY->user_id.'-style.css';
$GO_THEME->get_cached_css();
?>
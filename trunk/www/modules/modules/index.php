<?php
/*
   Copyright Intermesh 2003
   Author: Merijn Schering <mschering@intermesh.nl>
   Version: 1.0 Release date: 08 July 2003

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.
 */

require_once ("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('modules');

require_once ($GO_LANGUAGE->get_language_file('modules'));

$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';
require_once ($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();

load_basic_controls();
load_control('tooltip');

$GO_HEADER['head'] = tooltip::get_header();



require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('users');
load_basic_controls();
require_once($GO_LANGUAGE->get_language_file('users'));
?>
<html>
<head>
<title><?php echo $GO_CONFIG->title.' - '.$lang_modules['users']; ?></title>
<?php
require($GO_CONFIG->root_path.'default_head.inc');
//$GO_THEME->load_module_theme('modules');
//echo $GO_THEME->get_stylesheet('users');

echo tooltip::get_header();
?>
<script type="text/javascript" src="language/en.js"></script>
<script type="text/javascript" src="users.js"></script>
<script type="text/javascript" src="../../links.js"></script>
</head>
<body>
<?php


switch ($task) {
	case 'install' :
		$module_id = $_POST['module_id'];

		$acl_read = $GO_SECURITY->get_new_acl('Module read: '.$module_id, 0);
		$acl_write = $GO_SECURITY->get_new_acl('Module write: '.$module_id, 0);

		if ($acl_read > 0 && $acl_write > 0) {
			if ($GO_SECURITY->add_user_to_acl($GO_SECURITY->user_id, $acl_write) && $GO_SECURITY->add_user_to_acl($GO_SECURITY->user_id, $acl_read)) {
				if($_REQUEST['admin_menu'] == '1')
				{
					$admin_menu = '1';
				}else
				{
					$admin_menu = '0';
				}
				if (!$GO_MODULES->add_module($module_id, $_REQUEST['version'], $acl_read, $acl_write, $_REQUEST['sort_order'], $admin_menu)) {
					$feedback = '<p class="Error">'.$strSaveError.'</p>';
				}else {
					if(file_exists($GO_CONFIG->root_path.'modules'.$GO_CONFIG->slash.$module_id.$GO_CONFIG->slash.'sql/install.inc'))
					{
						require($GO_CONFIG->root_path.'modules'.$GO_CONFIG->slash.$module_id.$GO_CONFIG->slash.'sql/install.inc');
					}
				}
				$GO_MODULES->load_modules();
			} else {
				$GO_SECURITY->delete_acl($acl_read);
				$GO_SECURITY->delete_acl($acl_write);
				$feedback = '<p class="Error">'.$strAclError.'</p>';
			}
		} else {
			$GO_SECURITY->delete_acl($acl_read);
			$GO_SECURITY->delete_acl($acl_write);
			$feedback = '<p class="Error">'.$strAclError.'</p>';
		}
		break;

	case 'uninstall' :
		$module_id = $_POST['module_id'];

		if ($module = $GO_MODULES->get_module($_POST['module_id'])) {
			$GO_MODULES->delete_module($module_id);
		}
		$GO_MODULES->load_modules();
		break;

	case 'sort_order' :
		if (isset ($_POST['modules'])) {
			foreach ($_POST['modules'] as $key => $value) {
				$value['admin_menu'] = isset($value['admin_menu']) ? '1' : '0';
				$GO_MODULES->update_module($key, $value['sort_order'], $value['admin_menu']);
			}
		}
		$GO_MODULES->load_modules();
		break;

	case 'consistencycheck' :
		if ($module = $GO_MODULES->get_module($_POST['module_id'])) {
			require_once ($GO_MODULES->modules[$_POST['module_id']]['class_path'].$_POST['module_id'].".class.inc");
			$mod = new $_POST['module_id'];
			$mod->consistencycheck();
		}
}

if ($task == 'install' || $task == 'uninstall' || $task == 'sort_order') {
	echo '<script type="text/javascript">';
	echo 'parent.location="'.$GO_CONFIG->host.'index.php?return_to='.urlencode($_SERVER['PHP_SELF']).'";';
	echo '</script>';
}

echo '<form class="x-form" method="post" name="modules" action="'.$_SERVER['PHP_SELF'].'">';
echo '<input type="hidden" name="task" />';
echo '<input type="hidden" name="version" />';
echo '<input type="hidden" name="sort_order" />';
echo '<input type="hidden" name="module_id" />';
echo '<input type="hidden" name="admin_menu" />';
echo '<input type="hidden" name="close" value="false" />';
require_once ('modules.inc');
echo '</form>';
echo '</body></html>';


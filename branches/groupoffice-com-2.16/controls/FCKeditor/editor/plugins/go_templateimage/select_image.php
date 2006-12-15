<?php
/*
Copyright Intermesh 2005
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 22 December 2005

Part of the Group-Office Professional license
*/

require_once('../../../../../Group-Office.php');
require_once($GO_LANGUAGE->get_language_file('filesystem'));
$GO_HANDLER = $GO_MODULES->modules['addressbook']['url'].'templates/attach_inline.php';
$GO_FILTER_TYPES = array('jpg','bmp','gif','png','jpeg');
$GO_CONFIG->window_mode = 'popup';
$target_frame = '_self';
$GO_MULTI_SELECT = false;
require_once($GO_MODULES->modules['filesystem']['path'].'index.php');
?>

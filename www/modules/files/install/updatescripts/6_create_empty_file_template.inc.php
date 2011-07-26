<?php
$module = $GLOBALS['GO_MODULES']->get_module('files');

global $GO_LANGUAGE, $lang, $GO_SECURITY, $GO_CONFIG;

require($GLOBALS['GO_LANGUAGE']->get_language_file('files'));

require_once($module['class_path'].'files.class.inc.php');
$files = new files();

$template['name']=$lang['files']['emptyFile'];
$template['user_id']=1;
$template['extension']='';
$template['acl_id']=$GLOBALS['GO_SECURITY']->get_new_acl('files');

$GLOBALS['GO_SECURITY']->add_group_to_acl($GLOBALS['GO_CONFIG']->group_internal, $template['acl_id']);

$files->add_template($template);

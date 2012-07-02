<?php
$module = $GO_MODULES->get_module('files');

global $GO_LANGUAGE, $lang, $GO_SECURITY, $GO_CONFIG;

require($GO_LANGUAGE->get_language_file('files'));

require_once($module['class_path'].'files.class.inc.php');
$files = new files();

$template['name']=$lang['files']['emptyFile'];
$template['user_id']=1;
$template['extension']='';
$template['acl_id']=$GO_SECURITY->get_new_acl('files');

$GO_SECURITY->add_group_to_acl($GO_CONFIG->group_internal, $template['acl_id']);

$files->add_template($template);

<?php
$module = $this->get_module('files');

global $GO_LANGUAGE, $lang, $GO_SECURITY;

require($GO_LANGUAGE->get_language_file('files'));

require_once($module['class_path'].'files.class.inc');
$files = new files();

$template['name']=addslashes($lang['files']['ootextdoc']);
$template['user_id']=1;
$template['extension']='odt';
$template['content']=addslashes(file_get_contents($module['path'].'install/templates/empty.odt'));
$template['acl_read']=$GO_SECURITY->get_new_acl('files');
$template['acl_write']=$GO_SECURITY->get_new_acl('files');

$GO_SECURITY->add_group_to_acl($GO_CONFIG->group_internal, $template['acl_read']);

$files->add_template($template);


$template['name']=addslashes($lang['files']['wordtextdoc']);
$template['user_id']=1;
$template['extension']='doc';
$template['content']=addslashes(file_get_contents($module['path'].'install/templates/empty.doc'));
$template['acl_read']=$GO_SECURITY->get_new_acl('files');
$template['acl_write']=$GO_SECURITY->get_new_acl('files');

$GO_SECURITY->add_group_to_acl($GO_CONFIG->group_internal, $template['acl_read']);

$files->add_template($template);
?>
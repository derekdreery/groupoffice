<?php
$module = $this->get_module('mailings');
global $GO_LANGUAGE, $lang;
require($GLOBALS['GO_LANGUAGE']->get_language_file('addressbook'));
require($GLOBALS['GO_LANGUAGE']->get_language_file('mailings'));

require_once($module['class_path'].'../../addressbook/classes/addressbook.class.inc.php');
$ab = new addressbook();
require_once($module['class_path'].'templates.class.inc.php');
$tp = new templates();

require_once($GO_CONFIG->class_path.'mail/Go2Mime.class.inc.php');


$template_body = '&lt;gotpl if="salutation"&gt;{salutation},<br />&lt;/gotpl&gt;
<br />
{body}<br />
<br />
'.$lang['mailings']['greet'].'<br />
<br />
<br />
{my_name}
&lt;gotpl if="my_company"&gt;{my_company}<br />&lt;/gotpl&gt;
&lt;gotpl if="my_work_address"&gt;{my_work_address} {my_work_address_no}<br />&lt;/gotpl&gt;
&lt;gotpl if="my_work_zip"&gt;{my_work_zip} {my_work_city}<br />&lt;/gotpl&gt;
&lt;gotpl if="my_work_phone"&gt;T: {my_work_phone}<br />&lt;/gotpl&gt;';

$go2mime = new Go2Mime();
$go2mime->set_body($template_body);

$template['content']=$go2mime->build_mime();
$template['name']=$lang['common']['default'];
$template['type']='0';
$template['user_id']=1;
$template['acl_id']=$GO_SECURITY->get_new_acl('addressbook', 1);
$tp->add_template($template);

$GO_SECURITY->add_group_to_acl($GO_CONFIG->group_internal, $template['acl_id']);


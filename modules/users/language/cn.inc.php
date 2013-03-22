<?php
//This is a translation by hodrag. If you have questions please e-mail to hodrag@gmail.com
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));
$lang['users']['name'] = '用户名';
$lang['users']['description'] = '管理模块; 管理系统用户';

$lang['users']['deletePrimaryAdmin'] = '你不能删除主管理员';
$lang['users']['deleteYourself'] = '不能删除自己';

$lang['link_type'][8]=$us_user = '用户';

$lang['users']['error_username']='用户名中含有无效字符';
$lang['users']['error_username_exists']='用户名已存在';
$lang['users']['error_email_exists']='此 e-mail 已经被注册。 您可以用“忘记密码”功能找回';
$lang['users']['error_match_pass']='密码不匹配';
$lang['users']['error_email']='输入的 e-mail 地址无效';

<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='任务';
$lang['tasks']['description']='在这里写上说明';

$lang['link_type'][12]=$lang['tasks']['task']='任务';
$lang['tasks']['status']='状态';


$lang['tasks']['statuses']['NEEDS-ACTION'] = '需要做的';
$lang['tasks']['statuses']['ACCEPTED'] = '接受';
$lang['tasks']['statuses']['DECLINED'] = '拒绝';
$lang['tasks']['statuses']['TENTATIVE'] = '未确定';
$lang['tasks']['statuses']['DELEGATED'] = '委派';
$lang['tasks']['statuses']['COMPLETED'] = '已完成';
$lang['tasks']['statuses']['IN-PROCESS'] = '排队';
?>

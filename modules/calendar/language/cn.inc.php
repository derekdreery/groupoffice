<?php
//This is a translation by hodrag. If you have questions please e-mail to hodrag@gmail.com
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = '日程表';
$lang['calendar']['description'] = '日程模块; 所有人都可以添加、编辑和删除日程，也可以查看其他用户的日程安装并在必要时进行修改。';

$lang['link_type'][1]='日程';

$lang['calendar']['groupView'] = '组视图';
$lang['calendar']['event']='事件';
$lang['calendar']['startsAt']='开始于';
$lang['calendar']['endsAt']='结束于';

$lang['calendar']['exceptionNoCalendarID'] = '错误: 没有日程 ID!';
$lang['calendar']['appointment'] = '日程: ';
$lang['calendar']['allTogether'] = '一起';

$lang['calendar']['location']='位置';

$lang['calendar']['invited']='邀请您参加下列活动';
$lang['calendar']['acccept_question']='你要接受此安排？';

$lang['calendar']['accept']='接受';
$lang['calendar']['decline']='拒绝';

$lang['calendar']['bad_event']='该活动已不存在';

$lang['calendar']['subject']='主题';
$lang['calendar']['status']='状态';



$lang['calendar']['statuses']['NEEDS-ACTION'] = '需要做的';
$lang['calendar']['statuses']['ACCEPTED'] = '接受';
$lang['calendar']['statuses']['DECLINED'] = '拒绝';
$lang['calendar']['statuses']['TENTATIVE'] = '未确定';
$lang['calendar']['statuses']['DELEGATED'] = '委派';
$lang['calendar']['statuses']['COMPLETED'] = '完成';
$lang['calendar']['statuses']['IN-PROCESS'] = '排队';


$lang['calendar']['accept_mail_subject'] = '邀请 \'%s\' 接受';
$lang['calendar']['accept_mail_body'] = '%s 已接受邀请:';

$lang['calendar']['decline_mail_subject'] = '邀请 \'%s\' 被拒绝';
$lang['calendar']['decline_mail_body'] = '%s 已拒绝邀请:';

$lang['calendar']['location']='位置';
$lang['calendar']['and']='并且and';

$lang['calendar']['repeats'] = '重复 %s';
$lang['calendar']['repeats_at'] = '重复 %s at %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = '重复 %s %s at %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['until']='直到'; 

$lang['calendar']['not_invited']='您没有被邀请参加此活动。您可能需要使用另一个用户登录';


$lang['calendar']['accept_title']='接受';
$lang['calendar']['accept_confirm']='你接受了此活动，发起人将得到通知';

$lang['calendar']['decline_title']='拒绝';
$lang['calendar']['decline_confirm']='你拒绝了此活动，发起人将得到通知';

$lang['calendar']['cumulative']='重复规则无效，下一次可能在未发生前就已经结束。';

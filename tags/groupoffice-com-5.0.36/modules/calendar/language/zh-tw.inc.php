<?php
//This is a translation by hodrag. If you have questions please e-mail to hodrag@gmail.com , modify chinese traditional by quincy
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = '行事曆';
$lang['calendar']['description'] = '行事曆模組; 所有人都可以增加、編輯和刪除行程，也可以查看其他使用者的行事曆並在必要時進行修改。';

$lang['link_type'][1]='行程';

$lang['calendar']['groupView'] = '群組顯示';
$lang['calendar']['event']='事件';
$lang['calendar']['startsAt']='開始於';
$lang['calendar']['endsAt']='結束於';

$lang['calendar']['exceptionNoCalendarID'] = '錯誤: 沒有行程 ID!';
$lang['calendar']['appointment'] = '行程: ';
$lang['calendar']['allTogether'] = '一起';

$lang['calendar']['location']='位置';

$lang['calendar']['invited']='邀請您參加下列活動';
$lang['calendar']['acccept_question']='您要接受此安排？';

$lang['calendar']['accept']='接受';
$lang['calendar']['decline']='拒絕';

$lang['calendar']['bad_event']='該活動已不存在';

$lang['calendar']['subject']='主題';
$lang['calendar']['status']='狀態';



$lang['calendar']['statuses']['NEEDS-ACTION'] = '需要做的';
$lang['calendar']['statuses']['ACCEPTED'] = '接受';
$lang['calendar']['statuses']['DECLINED'] = '拒絕';
$lang['calendar']['statuses']['TENTATIVE'] = '未確定';
$lang['calendar']['statuses']['DELEGATED'] = '委派';
$lang['calendar']['statuses']['COMPLETED'] = '完成';
$lang['calendar']['statuses']['IN-PROCESS'] = '排隊';


$lang['calendar']['accept_mail_subject'] = '邀請 \'%s\' 接受';
$lang['calendar']['accept_mail_body'] = '%s 已接受邀請:';

$lang['calendar']['decline_mail_subject'] = '邀請 \'%s\' 被拒絕';
$lang['calendar']['decline_mail_body'] = '%s 已拒絕邀請:';

$lang['calendar']['location']='位置';
$lang['calendar']['and']='並且and';

$lang['calendar']['repeats'] = '重複 %s';
$lang['calendar']['repeats_at'] = '重複 %s at %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = '重複 %s %s at %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['until']='直到'; 

$lang['calendar']['not_invited']='您沒有被邀請參加此活動。您可能需要使用另一個使用者登錄';


$lang['calendar']['accept_title']='接受';
$lang['calendar']['accept_confirm']='你接受了此活動，發起人將得到通知';

$lang['calendar']['decline_title']='拒絕';
$lang['calendar']['decline_confirm']='你拒絕了此活動，發起人將得到通知';

$lang['calendar']['cumulative']='重複規則無效，下一次可能在未發生前就已經結束。';

<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='작업';
$lang['tasks']['description']='Put a description here';

$lang['link_type'][12]=$lang['tasks']['task']='작업';
$lang['tasks']['status']='상태';


$lang['tasks']['scheduled_call']='Scheduled call at %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = '설정 필요';
$lang['tasks']['statuses']['ACCEPTED'] = '수락됨';
$lang['tasks']['statuses']['DECLINED'] = '거절됨';
$lang['tasks']['statuses']['TENTATIVE'] = '잠정적';
$lang['tasks']['statuses']['DELEGATED'] = '위임됨';
$lang['tasks']['statuses']['COMPLETED'] = '완료됨';
$lang['tasks']['statuses']['IN-PROCESS'] = '진행중';

$lang['tasks']['import_success']='%s tasks were imported';

$lang['tasks']['call']='Call';

$lang['tasks']['dueAtdate']='예정 : %s';

$lang['tasks']['list']='작업 리스트';
$lang['tasks']['tasklistChanged']="* 작업 리스트가 '%s'에서 '%s'로 바뀌었습니다";
$lang['tasks']['statusChanged']="* 상태가 '%s'에서 '%s'로 바뀌었습니다";
$lang['tasks']['multipleSelected']='여러 작업 리스트가 선택되었습니다';
$lang['tasks']['incomplete_delete']='선택된 모든 작업을 지울 권한이 없습니다';

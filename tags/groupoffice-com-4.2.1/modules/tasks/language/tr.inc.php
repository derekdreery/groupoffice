<?php
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Görevler';
$lang['tasks']['description']='Buraya bir açıklama koyunuz';

$lang['link_type'][12]=$lang['tasks']['task']='Görev';
$lang['tasks']['status']='Durum';


$lang['tasks']['scheduled_call']='%s için zamanlanmış çağrı';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'yapılması gerekli';
$lang['tasks']['statuses']['ACCEPTED'] = 'Kabul edilmiş';
$lang['tasks']['statuses']['DECLINED'] = 'Red edilmiş';
$lang['tasks']['statuses']['TENTATIVE'] = 'Duruma göre';
$lang['tasks']['statuses']['DELEGATED'] = 'Delegasyon yapılmış';
$lang['tasks']['statuses']['COMPLETED'] = 'Bitmiş';
$lang['tasks']['statuses']['IN-PROCESS'] = 'Sürüyor';

$lang['tasks']['import_success']='%s görev içeri aktarılmıştır';

$lang['tasks']['call']='Çağrı';

$lang['tasks']['dueAtdate']='saat %s göre';
?>
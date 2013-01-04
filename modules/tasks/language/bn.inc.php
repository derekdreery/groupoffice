<?php
//Uncomment this line in new translations!
//Bangla translation by Shubhra Prakash Paul <shuvro.paul@gmail.com>
require($GO_LANGUAGE->get_fallback_language_file('tasks'));

$lang['tasks']['name']='কর্মসমূহ';
$lang['tasks']['description']='এখানে বিবরণ লিখুন ';

$lang['link_type'][12]=$lang['tasks']['task']=' কর্মসমূহ';
$lang['tasks']['status']='অবস্থা ';


$lang['tasks']['scheduled_call']='Scheduled call at %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'Needs action';
$lang['tasks']['statuses']['ACCEPTED'] = 'Accepted';
$lang['tasks']['statuses']['DECLINED'] = 'Declined';
$lang['tasks']['statuses']['TENTATIVE'] = 'Tentative';
$lang['tasks']['statuses']['DELEGATED'] = 'Delegated';
$lang['tasks']['statuses']['COMPLETED'] = 'Completed';
$lang['tasks']['statuses']['IN-PROCESS'] = 'In process';

$lang['tasks']['import_success']='%s tasks were imported';

$lang['tasks']['call']='Call';

$lang['tasks']['dueAtdate']='Due at %s';

$lang['tasks']['list']='Tasklist';
$lang['tasks']['tasklistChanged']="* Tasklist changed from '%s' to '%s'";
$lang['tasks']['statusChanged']="* Status changed from '%s' to '%s'";
$lang['tasks']['multipleSelected']='Multiple tasklists selected';
$lang['tasks']['incomplete_delete']='You don\'t have permission to delete all selected tasks';
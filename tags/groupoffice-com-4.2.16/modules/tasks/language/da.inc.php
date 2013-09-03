<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Opgaver';
$lang['tasks']['description']='Angiv beskrivelse her';

$lang['link_type'][12]=$lang['opgaver']['opgave']='Opgave';
$lang['tasks']['status']='Status';


$lang['tasks']['scheduled_call']='Planlagt opringning  %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'Afventer reaktion';
$lang['tasks']['statuses']['ACCEPTED'] = 'Accepteret';
$lang['tasks']['statuses']['DECLINED'] = 'Afvist';
$lang['tasks']['statuses']['TENTATIVE'] = 'Foreløbig';
$lang['tasks']['statuses']['DELEGATED'] = 'Delegeret';
$lang['tasks']['statuses']['COMPLETED'] = 'Fuldført';
$lang['tasks']['statuses']['IN-PROCESS'] = 'Igangværende';

$lang['tasks']['import_success']='%s opgaver importeret';

$lang['tasks']['call']='Ring';
?>
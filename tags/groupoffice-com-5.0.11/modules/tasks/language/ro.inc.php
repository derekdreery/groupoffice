<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Activitate';
$lang['tasks']['description']='Intrudu aici o descriere';

$lang['link_type'][12]=$lang['tasks']['task']='Activitate';
$lang['tasks']['status']='Stato';

$lang['tasks']['statuses']['NEEDS-ACTION']= 'Cerere Acţiune';
$lang['tasks']['statuses']['ACCEPTED']= 'Acceptat';
$lang['tasks']['statuses']['DECLINED']= 'Refuzat';
$lang['tasks']['statuses']['TENTATIVE']= 'Tentativ';
$lang['tasks']['statuses']['DELEGATED']= 'Delegat';
$lang['tasks']['statuses']['COMPLETED']= 'Complectat';
$lang['tasks']['statuses']['IN-PROCESS']= 'In curs';
?>
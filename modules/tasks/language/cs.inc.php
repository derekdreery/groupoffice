<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Úkoly';
$lang['tasks']['description']='Můžete zaznamenávat důležité akce.';

$lang['link_type'][12]=$lang['tasks']['task']='Úkol';
$lang['tasks']['status']='Stav';


$lang['tasks']['scheduled_call']='Plánované připomenutí %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'Vyžaduje akci';
$lang['tasks']['statuses']['ACCEPTED'] = 'Přijat';
$lang['tasks']['statuses']['DECLINED'] = 'Odmítnutý';
$lang['tasks']['statuses']['TENTATIVE'] = 'Nezávazný';
$lang['tasks']['statuses']['DELEGATED'] = 'Delegovaný';
$lang['tasks']['statuses']['COMPLETED'] = 'Dokončený';
$lang['tasks']['statuses']['IN-PROCESS'] = 'V procesu';

$lang['tasks']['import_success']='%s úkolů bylo importováno';

$lang['tasks']['call']='Připomenutí';

$lang['tasks']['dueAtdate']='Vzhledem k %s';
?>

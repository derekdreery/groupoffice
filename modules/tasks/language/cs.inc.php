<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Úkoly';
$lang['tasks']['description']='Můžete zaznamenávat důležité akce.';

$lang['link_type'][12]=$lang['tasks']['task']='Úkol';
$lang['tasks']['status']='Stav';


$lang['tasks']['scheduled_call']='Plánované připomenutí %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'Vyžaduje akci';
$lang['tasks']['statuses']['ACCEPTED'] = 'Přijatý';
$lang['tasks']['statuses']['DECLINED'] = 'Odmítnutý';
$lang['tasks']['statuses']['TENTATIVE'] = 'Nezávazný';
$lang['tasks']['statuses']['DELEGATED'] = 'Delegovaný';
$lang['tasks']['statuses']['COMPLETED'] = 'Dokončený';
$lang['tasks']['statuses']['IN-PROCESS'] = 'V procesu';

$lang['tasks']['import_success']='%s úkolů bylo importováno';

$lang['tasks']['call']='Připomenutí';

$lang['tasks']['dueAtdate']='Vzhledem k %s';

$lang['tasks']['list']='Přehled úkolů';

$lang['tasks']['tasklistChanged']="* Seznam úkolů byl změněn z '%s' na '%s'";
$lang['tasks']['statusChanged']="* Stav byl změněn z '%s' na '%s'";
$lang['tasks']['multipleSelected']='Vybráno více přehledů úkolů';
$lang['tasks']['incomplete_delete']='Nemáte oprávnění pro smazání všech vybraných úkolů';
?>

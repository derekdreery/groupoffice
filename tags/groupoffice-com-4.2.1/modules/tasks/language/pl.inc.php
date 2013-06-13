<?php

//Polish Translation v1.0
//Author : Robert GOLIAT info@robertgoliat.com  info@it-administrator.org
//Date : January, 20 2009
//Polish Translation v1.1
//Author : Paweł Dmitruk pawel.dmitruk@gmail.com
//Date : September, 05 2010
//Polish Translation v1.2
//Author : rajmund
//Date : January, 26 2011
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Zadania';
$lang['tasks']['description']='Moduł do zarządzania zadaniami';

$lang['link_type'][12]=$lang['tasks']['task']='Zadanie';
$lang['tasks']['status']='Status';


$lang['tasks']['scheduled_call']='Zaplanowane wywołania o %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'Wymaga akcji';
$lang['tasks']['statuses']['ACCEPTED'] = 'Zaakceptowane';
$lang['tasks']['statuses']['DECLINED'] = 'Odrzucone';
$lang['tasks']['statuses']['TENTATIVE'] = 'Niepewne';
$lang['tasks']['statuses']['DELEGATED'] = 'Delegowane';
$lang['tasks']['statuses']['COMPLETED'] = 'Zakończone';
$lang['tasks']['statuses']['IN-PROCESS'] = 'W trakcie realizacji';

$lang['tasks']['import_success']='%s zadań zostało zaimportowanych';

$lang['tasks']['call']='Wywołanie';
$lang['tasks']['list']='Lista zadań';
$lang['tasks']['tasklistChanged']="* Lista zadań zmieniona z '%s' na '%s'";
$lang['tasks']['statusChanged']="* Status zmieniony z '%s' na '%s'";

$lang['tasks']['dueAtdate']='Due at %s';
$lang['tasks']['multipleSelected']='Wybrano wiele list zadań';
$lang['tasks']['incomplete_delete']='Nie masz uprawnień do usunięcia wszystkich zaznaczonych zadań';
?>
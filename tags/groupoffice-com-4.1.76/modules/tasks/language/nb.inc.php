<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Arbeidsoppgaver';
$lang['tasks']['description']='Angi en beskrivelse';

$lang['link_type'][12]=$lang['tasks']['task']='Oppgave';
$lang['tasks']['status']='Status';


$lang['tasks']['scheduled_call']='Avtalt samtale den %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'Trenger oppfølging';
$lang['tasks']['statuses']['ACCEPTED'] = 'Akseptert';
$lang['tasks']['statuses']['DECLINED'] = 'Avvist';
$lang['tasks']['statuses']['TENTATIVE'] = 'Tentativ';
$lang['tasks']['statuses']['DELEGATED'] = 'Delegert';
$lang['tasks']['statuses']['COMPLETED'] = 'Fullført';
$lang['tasks']['statuses']['IN-PROCESS'] = 'Under behandling';

$lang['tasks']['import_success']='%s oppgaver er importert';

$lang['tasks']['call']='Telefon';

$lang['tasks']['dueAtdate']='Frist %s';
$lang['tasks']['list']='Oppgaveliste';
$lang['tasks']['tasklistChanged']="* Oppgaveliste endret fra '%s' til '%s'";
$lang['tasks']['statusChanged']="* Status endret fra '%s' til '%s'";
$lang['tasks']['multipleSelected']='Flere oppgavelister er valgt';
$lang['tasks']['incomplete_delete']='Du har ikke rettigheter til å slette alle valgte oppgaver';
?>

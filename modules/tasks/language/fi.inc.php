<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Tehtävät';
$lang['tasks']['description']='Laita kuvaus tähän';

$lang['link_type'][12]=$lang['tasks']['task']='Tehtävä';
$lang['tasks']['status']='Tila';


$lang['tasks']['scheduled_call']='Ajastettu soitto %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'Toimenpiteitä tarvitaan';
$lang['tasks']['statuses']['ACCEPTED'] = 'Hyväksytty';
$lang['tasks']['statuses']['DECLINED'] = 'Hylätty';
$lang['tasks']['statuses']['TENTATIVE'] = 'Alustava';
$lang['tasks']['statuses']['DELEGATED'] = 'Delegoitu';
$lang['tasks']['statuses']['COMPLETED'] = 'Valmis';
$lang['tasks']['statuses']['IN-PROCESS'] = 'Keskeneräinen';

$lang['tasks']['import_success']='%s tehtävät tuotiin';

$lang['tasks']['call']='Soitto';
?>
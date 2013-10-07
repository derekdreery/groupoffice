<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Ülesanded';
$lang['tasks']['description']='Lisa siia kirjeldus';

$lang['link_type'][12]=$lang['tasks']['task']='Ülesanne';
$lang['tasks']['status']='Staatus';


$lang['tasks']['scheduled_call']='Planeeritud telefonikõne %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'Vajab tegevust';
$lang['tasks']['statuses']['ACCEPTED'] = 'Vastu võetud';
$lang['tasks']['statuses']['DECLINED'] = 'Tagasi lükatud';
$lang['tasks']['statuses']['TENTATIVE'] = 'Esialgne';
$lang['tasks']['statuses']['DELEGATED'] = 'Delegeeritud';
$lang['tasks']['statuses']['COMPLETED'] = 'Lõpetatud';
$lang['tasks']['statuses']['IN-PROCESS'] = 'Pooleli';

$lang['tasks']['import_success']='%s ülesannet imporditud';

$lang['tasks']['call']='Telefonikõne';

$lang['tasks']['dueAtdate']='Tähtaeg %s';

$lang['tasks']['list']='Ülesannete nimekiri';

$lang['tasks']['tasklistChanged']="* Nimekiri muudetud. Enne oli '%s', nüüd on '%s'";
$lang['tasks']['statusChanged']="* Staatus muudetud. Enne oli '%s', nüüd on '%s'";
$lang['tasks']['multipleSelected']='Mitu nimekirja valitud';
$lang['tasks']['incomplete_delete']='Valitud ülesannete kustutamiseks puuduvad õigused';
?>
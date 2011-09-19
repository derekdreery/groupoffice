<?php
	/** 
		* @copyright Copyright Boso d.o.o.
		* @author Mihovil Stanić <mihovil.stanic@boso.hr>
	*/
 
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Zadaci';
$lang['tasks']['description']='Upravljanje zadacima i obvezama.';

$lang['link_type'][12]=$lang['tasks']['task']='Zadatak';
$lang['tasks']['status']='Status';


$lang['tasks']['scheduled_call']='Zakazani poziv u %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'Potrebno djelovati';
$lang['tasks']['statuses']['ACCEPTED'] = 'Prihvaćeno';
$lang['tasks']['statuses']['DECLINED'] = 'Odbijeno';
$lang['tasks']['statuses']['TENTATIVE'] = 'Privremeno';
$lang['tasks']['statuses']['DELEGATED'] = 'Delegirano';
$lang['tasks']['statuses']['COMPLETED'] = 'Završeno';
$lang['tasks']['statuses']['IN-PROCESS'] = 'U procesu';

$lang['tasks']['import_success']='%s zadataka je uvezeno';

$lang['tasks']['call']='Poziv';

$lang['tasks']['dueAtdate']='Dospijeva %s';

$lang['tasks']['list']='Lista zadataka';
$lang['tasks']['tasklistChanged']="* Lista zadataka promjenjena iz '%s' u '%s'";
$lang['tasks']['statusChanged']="* Status promjenjen iz '%s' u '%s'";
$lang['tasks']['multipleSelected']='Više listi zadataka odabrano';
$lang['tasks']['incomplete_delete']='Nemate dopuštenje za brisanje svih odabranih zadataka';
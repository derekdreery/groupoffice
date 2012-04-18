<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Tasques';
$lang['tasks']['description']='Per favor, introduïu una descripció';

$lang['link_type'][12]=$lang['tasks']['task']='Tasca';
$lang['tasks']['status']='Estat';

$lang['tasks']['statuses']['NEEDS-ACTION']= 'Sol·licitud d\'Acció';
$lang['tasks']['statuses']['ACCEPTED']= 'Acceptada';
$lang['tasks']['statuses']['DECLINED']= 'Rebutjada';
$lang['tasks']['statuses']['TENTATIVE']= 'Temptativa';
$lang['tasks']['statuses']['DELEGATED']= 'Delegat';
$lang['tasks']['statuses']['COMPLETED']= 'Complert';
$lang['tasks']['statuses']['IN-PROCESS']= 'En curs';

$lang['tasks']['scheduled_call']='Trucada agendada per a %s';
$lang['tasks']['call']='Trucada';

$lang['tasks']['import_success']='%s tasques han estat importades';

$lang['tasks']['dueAtdate']='Venç en %s';

$lang['tasks']['list']='Llistat de tasques';
$lang['tasks']['tasklistChanged']="* Llistat de tasques ha canviat de '%s' a '%s'";
$lang['tasks']['statusChanged']="* Estat ha canviat de '%s' a '%s'";

$lang['tasks']['multipleSelected']='Selecció múltiple de llistats de tasques';
$lang['tasks']['incomplete_delete']='No teniu permís per eliminar totes les tasques seleccionades';

?>

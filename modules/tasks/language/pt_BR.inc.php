<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Tarefas';
$lang['tasks']['description']='Coloque uma descrição aqui';

$lang['link_type'][12]=$lang['tasks']['task']='Tarefas';
$lang['tasks']['status']='Status';


$lang['tasks']['scheduled_call']='Agendar chamada em %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'Necessita ação';
$lang['tasks']['statuses']['ACCEPTED'] = 'Aceito';
$lang['tasks']['statuses']['DECLINED'] = 'Negado';
$lang['tasks']['statuses']['TENTATIVE'] = 'Tentativas';
$lang['tasks']['statuses']['DELEGATED'] = 'Delegado';
$lang['tasks']['statuses']['COMPLETED'] = 'Concluído';
$lang['tasks']['statuses']['IN-PROCESS'] = 'Em processo';

$lang['tasks']['import_success']='%s tarefas foram importadas';

$lang['tasks']['call']='Chamada';

$lang['tasks']['dueAtdate']='Fazer até %s';

$lang['tasks']['list']='Lista de Tarefas';
$lang['tasks']['tasklistChanged']="* Lista de Tarefas alterada de '%s' para '%s'";
$lang['tasks']['statusChanged']="* Status alterado de '%s' para '%s'";


?>

<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Calendário';
$lang['calendar']['description'] = 'Módulo calendário; Todo usuário pode adicionar, editar or apagar compromissos. Os compromissos de outros usuários podem ser vistos e modificados se necessário.';

$lang['link_type'][1]='Compromisso';

$lang['calendar']['groupView'] = 'Vista do grupo';
$lang['calendar']['event']='Evento';
$lang['calendar']['startsAt']='Inicia em';
$lang['calendar']['endsAt']='Termina em';

$lang['calendar']['exceptionNoCalendarID'] = 'FATAL: Sem identifição do calendário!';
$lang['calendar']['appointment'] = 'Compromisso: ';
$lang['calendar']['allTogether'] = 'Todo junto';

$lang['calendar']['location']='Localização';

$lang['calendar']['invited']='Você foi convidado para o seguinte evento';
$lang['calendar']['acccept_question']='Você aceita esse evento?';

$lang['calendar']['accept']='Aceita';
$lang['calendar']['decline']='Rejeita';

$lang['calendar']['bad_event']='Esse evento não existe mais';

$lang['calendar']['subject']='Assunto';
$lang['calendar']['status']='Status';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Necessita de ação';
$lang['calendar']['statuses']['ACCEPTED'] = 'Aceito';
$lang['calendar']['statuses']['DECLINED'] = 'Negado';
$lang['calendar']['statuses']['TENTATIVE'] = 'Tentativa';
$lang['calendar']['statuses']['DELEGATED'] = 'Delegado';
$lang['calendar']['statuses']['COMPLETED'] = 'Completado';
$lang['calendar']['statuses']['IN-PROCESS'] = 'Em processo';


$lang['calendar']['accept_mail_subject'] = 'Convite para \'%s\' aceito';
$lang['calendar']['accept_mail_body'] = '%s aceitou seu convite para:';

$lang['calendar']['decline_mail_subject'] = 'Convite para \'%s\' rejeitado';
$lang['calendar']['decline_mail_body'] = '%s rejeitou seu convite para:';

$lang['calendar']['location']='Localização';
$lang['calendar']['and']='e';

$lang['calendar']['repeats'] = 'Repetir cada %s';
$lang['calendar']['repeats_at'] = 'Repetir cada %s em %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Repetir cada %s %s em %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['until']='até'; 

$lang['calendar']['not_invited']='Você foi convidado para este evento. Você pode precisar se logar como um usuário diferente.';


$lang['calendar']['accept_title']='Aceito';
$lang['calendar']['accept_confirm']='O dono será notificado que você aceitou o evento';

$lang['calendar']['decline_title']='Negado';
$lang['calendar']['decline_confirm']='O dono será notificado que você rejeitou o evento';

$lang['calendar']['cumulative']='Regra inválida. A próxima ocorrencia não pode começar anter do final da anterior.';

$lang['calendar']['already_accepted']='Você já aceitou esse evento.';

$lang['calendar']['private']='Privado';
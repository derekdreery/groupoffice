<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Calendário';
$lang['calendar']['description'] = 'Módulo calendário; Todo usuário pode adicionar, editar or apagar compromissos. Os compromissos de outros usuários podem ser vistos e modificados se necessário.';

$lang['link_type'][1]='Compromisso';

$lang['calendar']['groupView'] = 'Visualização do grupo';
$lang['calendar']['event']='Evento';
$lang['calendar']['startsAt']='Inicia em';
$lang['calendar']['endsAt']='Termina em';

$lang['calendar']['exceptionNoCalendarID'] = 'FATAL: Sem identificação do calendário!';
$lang['calendar']['appointment'] = 'Compromisso: ';
$lang['calendar']['allTogether'] = 'Tudo junto';

$lang['calendar']['location']='Localização';

$lang['calendar']['invited']='Você foi convidado para o seguinte evento';
$lang['calendar']['acccept_question']='Você aceita esse evento?';

$lang['calendar']['accept']='Aceitar';
$lang['calendar']['decline']='Rejeitar';

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

$lang['calendar']['repeats'] = 'Repetir a cada %s';
$lang['calendar']['repeats_at'] = 'Repetir a cada %s no %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Repetir a cada %s %s no %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['until']='até';

$lang['calendar']['not_invited']='Você foi convidado para este evento. Você pode precisar se logar como um usuário diferente.';


$lang['calendar']['accept_title']='Aceito';
$lang['calendar']['accept_confirm']='O proprietário será notificado que você aceitou o evento';

$lang['calendar']['decline_title']='Negado';
$lang['calendar']['decline_confirm']='O proprietário será notificado que você rejeitou o evento';

$lang['calendar']['cumulative']='Regra inválida. A próxima ocorrencia não pode começar antes do final da anterior.';

$lang['calendar']['already_accepted']='Você já aceitou esse evento.';

$lang['calendar']['private']='Privado';

$lang['calendar']['import_success']='%s eventos foram importados';

$lang['calendar']['printTimeFormat']='De %s até %s';
$lang['calendar']['printLocationFormat']=' na localização "%s"';
$lang['calendar']['printPage']='Página %s de %s';
$lang['calendar']['printList']='Lista de compromissos';

$lang['calendar']['printAllDaySingle']='Todo o dia';
$lang['calendar']['printAllDayMultiple']='Todos os dias de %s até %s';

$lang['calendar']['calendars']='Calendários';

$lang['calendar']['open_resource']='Recurso disponível';

$lang['calendar']['resource_mail_subject']='Recurso \'%s\' agendado para \'%s\' em \'%s\'';//%s is resource name, %s is event name, %s is start date

$lang['calendar']['resource_mail_body']='%s fez um agendamento para o recurso \'%s\'. Você é o mantenedor do recurso. Por favor abra o agendamento para aprovar ou reprová-lo.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['resource_modified_mail_subject']='Recurso \'%s\' agendado por \'%s\' para \'%s\'';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_modified_mail_body']='%s modificou o agendamento para o recurso \'%s\'. Você é o mantenedor deste recurso. Por favor abra o agendamento para aprovar ou reprová-lo.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['your_resource_modified_mail_subject']='Seu agendamento para \'%s\' em \'%s\' com status \'%s\' foi modificado';//is resource name, %s is event name, %s is start date
$lang['calendar']['your_resource_modified_mail_body']='%s alterou seu agendamento para o recurso \'%s\'.';
$lang['calendar']['your_resource_accepted_mail_subject']='Seu agendamento para \'%s\' em \'%s\' foi aceito';//%s is resource name, %s is start date
$lang['calendar']['your_resource_accepted_mail_body']='%s aceitou seu agendamento para o recurso \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['your_resource_declined_mail_subject']='Seu agendamento para \'%s\' em \'%s\' foi recusado';//%s is resource name, %s is start date
$lang['calendar']['your_resource_declined_mail_body']='%s recusou seu agendamento para o recurso \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['birthday_name']='Aniversário: {NAME}';
$lang['calendar']['birthday_desc']='{NAME} está fazendo {AGE} anos hoje';
$lang['calendar']['unauthorized_participants_write']='Você não possui permissões suficientes para agentar um compromisso para os seguintes usuários:<br /><br />{NAMES}<br /><br />Você pode enviar um convite para eles, dessa forma eles poderão escolher se querem aceitar ou recusar o convite.';
$lang['calendar']['noCalSelected']= 'Nenhum calendário foi selecionado para esta visualização. Selecione pelo menos um calendário na Administração.';
$lang['calendar']['month_times'][1]='o primeiro';
$lang['calendar']['month_times'][2]='o segundo';
$lang['calendar']['month_times'][3]='o terceiro';
$lang['calendar']['month_times'][4]='o quarto';
$lang['calendar']['month_times'][5]='o quinto';

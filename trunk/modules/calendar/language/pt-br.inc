<?php
require_once($GO_LANGUAGE->get_fallback_language_file('calendar'));

$sc_title = "Título";
$sc_description = "Descrição";
$sc_participants = "Convidados";

$sc_location = "Local";

$month_times[0] = "Primeiro";
$month_times[1] = "Segundo";
$month_times[2] = "Terceiro";
$month_times[3] = "Quarto";

$sc_new_app = "Novo Evento";
$sc_calendars = "Calendários";
$sc_invited = 'Você foi convidado para participar do evento descrito abaixo.';

$sc_types1[0] = 'Sem repetição';
$sc_types1[1] = 'dias';
$sc_types1[2] = 'semanas';
$sc_types1[3] = 'data do mês';
$sc_types1[4] = 'dia do mês';
$sc_types1[5] = 'anos';

$sc_types[0] = 'Uma vez';
$sc_types[1] = 'Diariamente';
$sc_types[2] = 'Semanalmente';
$sc_types[3] = 'Mensalmente por data';
$sc_types[4] = 'Mensalmente por dia';
$sc_types[5] = 'Anualmente';


$sc_start_at = 'Início';
$sc_end_at = 'Término';
$sc_start_time = 'Hora inicial';
$sc_end_time = 'Hora de Término';

$sc_at_days = 'Nos dias';
$sc_of_month = 'do mês.';

$sc_cycle_start = 'Período iniciado em';
$sc_cycle_end = 'Retornar até';
$sc_accept_question = 'Você deseja aceitar e agendar este evento?';

$sc_accept = 'aceitar';
$sc_decline = 'recusar';

$sc_notime = 'Tempo não aplicável';
$sc_noend = 'Sem data para Término.';

$sc_never_happens = 'Dados incorretos! Este evento nunca ocorreu.';
$sc_start_later = 'Dados incorretos! A hora inicial está maior que a hora final.';

$sc_add_self = 'Entrada manual';

$sc_select_calendar = 'Selecione o calendário para agendar este evento';
$sc_accept_confirm = 'O evento foi aceito e agendado.';
$sc_no_calendars = 'Nenhum calendário válido foi encontrado para agendar este evento.';
$sc_bad_event = 'Nenhum evento válido encontrado. O evento provavelmente foi apagado.';
$sc_accept_title = 'Aceitar evento';

$sc_decline_confirm = 'O evento foi recusado.';
$sc_decline_title = 'Recusar evento';

$sc_status = 'Situação';
$sc_not_responded = 'Sem resposta';
$sc_accepted = 'Aceito';
$sc_declined = 'Recusado';
$sc_no_participants = 'Sem participantes';

$sc_person = 'Pessoa';
$sc_owner = 'Propietário';
$sc_type = 'Tipo';
$sc_subscribed = 'Inscrito';

$sc_subscribe = 'Inscrever';
$sc_new_calendar = 'Novo calendário';
$sc_no_calendar = 'Nenhum calendário encontrado . Por favor, crie um agora ou permita uma criação automática.';
$sc_day_view = 'Listar por dia';
$sc_week_view = 'Listar por semana';
$sc_list_view = 'Listar eventos';
$sc_put_in = 'Colocar evento em';
$sc_inherit_permissions ='Permissões herdadas de';
$sc_no_inherit = 'não herdar';

$sc_select_calendar_please = 'Selecione apenas um calendário.';
$sc_events = 'Eventos';

$sc_from_calendar = 'Deste calendário';
$sc_enitrely = 'Completamente';
$sc_delete_event = 'Apagar evento';
$sc_delete_pre = 'Você deseja apagar o evento';
$sc_delete_suf = 'Completamente ou apenas deste calendário?';

$sc_refresh = 'Atualizar';
$sc_reminder = 'Lembrete';
$sc_mins = 'minutos';
$sc_hour = 'hora';
$sc_hours = 'horas';
$sc_day = 'dia';
$sc_days = 'dias';
$sc_week = 'semana';
$sc_weeks = 'semanas';
$sc_reminder_set_to_early = "O lembrete não poderá aparecer antes de uma ocorrência prevista. Por favor, reduza o tempo do lembrete ou aumente o Período de repetição.";
$sc_cycle_start_later = 'A data inicial do Período está maior que a data final.';


$cmdGenerate = 'Gerar';
$sc_preferences = 'Preferências';
$sc_holidays = 'Feriados';

$strNoHolidays = 'Sem feriados no banco de dados';
$strUnknown = 'Desconhecido';
$strYear = 'Ano';
$strDeleteHolidaysPrefix = 'Você tem certeza que deseja apagar todos os feriados do ano ';
$strDeleteHolidaysSuffix = '?';
$strReplaceHolidays = 'Você tem certeza que deseja sobrescrever todos os feriados existentes?';
$sc_holidays_text1 = 'Por favor clique em \''.$cmdGenerate.'\' para processar os feriados para o ano selecionado.';
$sc_holidays_text2 = 'Por favor clique em \''.$cmdApply.'\' para armazenar os feriados para o ano selecionado.';

//translation required below this
$sc_client = 'Cliente';
$sc_no_contact = 'Nenhum';

$sc_background = 'Fundo';

$sc_colors['red'] = 'Vermelho';
$sc_colors['green'] = 'Verde';
$sc_colors['blue'] = 'Azul';
$sc_colors['pink'] = 'Rosa';
$sc_colors['orange'] = 'Laranja';
$sc_colors['brown'] = 'Marrom';
$sc_colors['yellow'] = 'Amarelo';
$sc_colors['grey'] = 'Cinza';
$sc_colors['purple'] = 'Violeta';
$sc_colors['white']= 'Branco';
$sc_status = 'Situação';

$sc_show_hours = 'Exibir horas de';
$sc_to = 'até';

$sc_today = 'Hoje';

$sc_recur_every = 'Repetir a cada';
$sc_months = 'meses';

$sc_private_event = 'Evento particular';
$sc_calendar_exists = 'Um calendário com esse nome já existe';
//translate
$sc_month = 'mês';
$sc_view = 'Exibir';
$sc_calendar = 'calendário';
$sc_default = 'padrão';
$sc_month_view = 'Listar por mês';

//text changed, needs new translation
$sc_holidays_no_file_prefix = 'Os feriados não puderam ser gerados pois o arquivo contendo as informações dos feriados para o ';
$sc_holidays_no_file_suffix = ' idioma estÃ£o vazios ou não existe.';

$sc_no_events = 'Nenhum evento encontrado .';

//2.03
$cal_export = 'Exportar';
$cal_import = 'Importar';
$cal_please_wait = 'Por favor, aguarde...';
$cal_import_text = 'Selecione um arquivo iCal (*.ics) e clique em \'Ok\'';
$cal_filename = 'Arquivo';
$cal_import_success = 'Data importada';

//2.04
$cal_add_todo = 'Nova pendência';
$cal_todos = 'pendências';

$cal_hide_completed = 'Ocultar concluidas';
$cal_show_all = 'Exibir todas';
$cal_show_own = 'Exibir as minhas';
$cal_show_responsible = 'Exibir por responsável';

$cal_priority = 'Prioridade';
$cal_priority_values[0] = 'Baixa';
$cal_priority_values[1] = 'Mestádia';
$cal_priority_values[2] = 'Alta';

$cal_status = 'Situação';
$cal_canceled = 'Cancelada';
$cal_completed = 'Concluida em';
$cal_percent_completed = '% Concluida';

$cal_before_task_start = 'Antes da pendência iniciar';
$cal_before_task_due = 'Antes da pendência ser finalizada';
$cal_responsible = 'responsável';

$cal_due_at = 'até';
$cal_no_todos= 'Nenhuma pendência encontrada';
$cal_resend_invitation = 'Reenviar convite';

//2.05
$cal_everybody_write = 'Todos podem alterar este evento';
$cal_everybody_read = 'Apenas eu posso alterar este evento';

$cal_view = 'Interface';
$cal_views = 'Interface';
$sc_view_exists = 'Uma interface com este nome já existe';
$cal_new_view = 'Nova interface';
$cal_event_calendars = 'calendário(s)';


//2.06
$cal_view_merged = 'combinado';
$cal_view_emerged = 'surgido';

//2.07
$cal_display = 'Vista';
$sc_new_task = "Nova tarefa";
$sc_all_day_events = 'eventos o dia inteiro';
$sc_all_day_event = 'evento o dia inteiro';
$sc_usual_events = 'eventos';
$sc_usual_event = 'evento';

//2.08
$sc_recur_section = 'Periodicidade';
$sc_options_section = 'Opções';
$sc_close = 'Fechar';

$cal_count_single = 'evento';
$cal_count_multiple = 'eventos';

$cal_until = 'até';
$cal_forever = 'para sempre';

//2.09
$cal_merged_overview = 'Fundir visão geral';
$cal_own_availability = 'Disponibilidade própria';

//2.11
$cal_no_file = 'Nenhum arquivo recebido';
$cms_no_calendars_in_view = 'Esta vista não pode ser exibida pois não tem nenhum calendário nesta vista. clique em \'Vistas\' para gerenciar as vistas.';
$cal_no_reminder = 'Nenhum lembrete';

//2.13
$cal_refresh_rate = 'Taxa de atualização';
$sc_seconds = 'segundos (Deixe vazio para não atualizar)';
$cal_default_reminder = 'Lembrete padrão';


//2.14
$cal_event = 'Evento';
$cal_todo = 'A fazer';
$cal_participants_write = 'Os Participantes podem modificar o evento';

$cal_everybody_write = 'Todos podem modificar o evento'; //modified string from 2.05 this became the
$cal_everybody_read = 'Somente eu posso modificar o evento';//modified string from 2.05 

$cal_default_permissions = 'Permissão padrão';
$cal_show_todos = 'Mostrar tarefas no calendário';

$cal_edit_series_or_single = 'Editar somente este caso ou a Série inteira?';
$cal_single = 'Caso';
$cal_series = 'Série inteira';

$cal_legend = 'Legenda';
$cal_multiple_calendars = 'Calendários múltiplos';

$cal_cumulative = 'Entrada inválida! O evento é maior que o intervalo recorrente.';

$cal_weekview = 'A vista semanal mostra';
$cal_mo_to_fr = 'Sengunda a Sexta';
$cal_mo_to_su = 'Segunda a Domingo';

$cal_batchcalendars = 'Criar Calendários para todos usuários';
$cal_create_all_calendars = 'Criar Calendários para todos usuários ou garanta Permissões para grupos de usuários';

$cal_no_views = 'não foi encontrada nenhuma vista';

$cal_my_cals = 'Meus Calendários';

$cal_event_colors_override = 'Usar o ajuste de cores manual para eventos';

//2.15
$cal_groups = 'Grupos';
$cal_new_group = 'Novo grupo';
$cal_group_exists = 'Este nome de grupo já existe. Favor escolher outro nome.';
$cal_no_calendars = 'Nenhum calendário encontrado  neste grupo';
$cal_group = 'Grupo';
$cal_resources = 'Recursos';

$cal_event_mail_subject = 'Evento \'%s\' criado em seu calendário';//%s is event name
$cal_event_mail_body = '%s fÃªz o compromisso \'%s\' chamado em seu calendário \'%s\'.'; //irst %s is the name of the person who created the event. Second is the event name, third is the calendar name
$cal_event_mail_open = 'Compromisso aberto';
$cal_add_custom_field = 'Adicionar campo customizado';

$cal_event_modified_mail_subject = 'Evento \'%s\' foi modificado em seu calendário';//%s is event name
$cal_event_modified_mail_body = '%s modificou seu compromisso \'%s\' chamado em seu calendário \'%s\'.'; //First %s is the name of the person who created the event. Second is the event name, third is the calendar name

$cal_your_event_modified_mail_subject = 'O evento \'%s\' criado por Você foi modificado';//%s is event name
$cal_your_event_modified_mail_body = '%s modificou o compromisso \'%s\' criado por Você.'; //First %s is the name of the person who created the event. Second is the event name.
$cal_no_custom_fields = 'Nenhum campo customizado está definido';
$cal_add_field = 'Adicionar campo';


$cal_resource_mail_subject = 'Recurso \'%s\' reservado'; //%s is resource name
$cal_resource_mail_body = '%s fez reservas para o recurso\'%s\'. Você é o administrador deste recurso. Favor abra as reservas para aceitar ou rejeitar isto.'; //First %s is the name of the person who created the event. Second is the calendar name
$cal_open_resource = 'Abrir reservas';

$cal_resource_modified_mail_subject = 'Reservas do recurso \'%s\' modificado';//%s is resource name
$cal_resource_modified_mail_body = '%s modificou a reserva para o recurso \'%s\'. Você é o administador deste recurso. Favor abra a reserva para aceitar ou rejeitar isto.'; //First %s is the name of the person who created the event. Second is the calendar name

$cal_your_resource_modified_mail_subject = 'Seu registro para \'%s\' no status %s está modificado';//%s is resource name, status
$cal_your_resource_modified_mail_body = '%s foi modificado seu registro para o recurso \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name


$cal_accept_mail_subject = 'Convite para \'%s\' aceito';
$cal_accept_mail_body = '%s aceitou seu convite para \'%s\'';

$cal_decline_mail_subject = 'Convite para \'%s\' recusado';
$cal_decline_mail_body = '%s recusou seu convite para \'%s\'';

$cal_statuses['NEEDS-ACTION'] = 'Precisa da aÃ§Ã£o';
$cal_statuses['ACCEPTED'] = 'Aceito';
$cal_statuses['DECLINED'] = 'Recusado';
$cal_statuses['TENTATIVE'] = 'Tentativa';
$cal_statuses['DELEGATED'] = 'Delegado';
$cal_statuses['COMPLETED'] = 'Completado';
$cal_statuses['IN-PROCESS'] = 'Em processo';

$cal_booking = 'Registro para \'%s\'';//%s is resource name

$cal_group_admins = 'Administradores';
$cal_no_admins = 'Nenhum adminstrador definido';
$cal_add_admin = 'Adicionar administrador';

$cal_scale='Escala';

$cal_show_completed = 'Mostrar tarefas completadas';

$cal_resource = 'Recurso';
$cal_grid_view = 'Vista em grade';

$cal_field_types['checkbox'] = 'Caixa de checagem';
$cal_field_types['text'] = 'Texto';
$cal_field_types['textarea'] = 'Area do texto';
$cal_field_types['date'] = 'Campo da data';

$cal_conflict = 'Este evento está conflitando com outro evento ou reserva de recurso . Clique em \'Ok\' ou \'Aplicar\' novamente para ignorar ou clique em \'Checar disponibilidade\' para encontrar uma hora apropiada.';
$cal_select_calendar = 'Selecionar calendário';
$cal_check_conflicts = 'Avise-me sobre conflitos quando eu criar um evento';
$cal_email_changes = 'Enviar e-mailquando alguém fizer mudanÃ§as no meu calendário';
$cal_shared_calendars = 'Calendários compartilhados';

$cal_admin = 'Administração';
$cal_resource_groups = 'Grupos de recursos';
$cal_open_calendar = 'Abrir outro calendário';

$cal_email_reminders='Me enviar lembretes por e-mail';

$cal_favourites = 'Favoritos';

$cal_your_resource_accepted_mail_subject = 'Reserva para \'%s\' foi aceita';//%s is resource name, status
$cal_your_resource_accepted_mail_body = '%s foi aceita sua reserva para o recurso \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

$cal_your_resource_declined_mail_subject = 'Reserva para \'%s\' está recusada';//%s is resource name
$cal_your_resource_declined_mail_body = '%s foi recusada sua reserva para o recurso \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

$cal_resource_deleted_mail_subject = 'Registro para \'%s\' está apagada';//%s is resource name, status

$cal_repeats_at = 'Repetir todo %s na %s';//eg. Repeats every month at the first monday
$cal_repeats_at_not_every = 'Repetir a cada %s %s na %s';//eg. Repeats every 2 weeks at monday 

$cal_repeats = 'Repetir todo %s';

$cal_and = 'e';
$sc_months = 'meses';
$sc_month = 'mês';
$sc_year = 'ano';
$sc_years = 'anos';

$cal_another_resource = 'O recurso foi mudado para %s';

$cal_background_colors = 'Cores de fundo';

$cal_weekday = 'Dia da semana';
$cal_background_color = 'Cor de fundo';
$cal_background = 'Fundo';
?>


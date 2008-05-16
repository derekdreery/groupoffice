<?php
require_once($GO_LANGUAGE->get_fallback_language_file('calendar'));

$sc_title = "Título";
$sc_description = "Descrição";
$sc_participants = "Convidados";

$sc_location = "Local";

$month_times[0] = "Primeira(o)";
$month_times[1] = "Segunda(o)";
$month_times[2] = "Terceira(o)";
$month_times[3] = "Quarta(o)";

$sc_new_app = "Novo Compromisso";
$sc_calendars = "Calendários";
$sc_invited = 'Foi convidado para participar no compromisso descrito abaixo.';

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
$sc_end_at = 'Fim';
$sc_start_time = 'Hora inicial';
$sc_end_time = 'Hora final';

$sc_at_days = 'Nos dias';
$sc_of_month = 'do mês.';

$sc_cycle_start = 'Com início em';
$sc_cycle_end = 'Repetir até';
$sc_accept_question = 'Deseja aceitar e agendar este compromisso?';

$sc_accept = 'aceitar';
$sc_decline = 'recusar';

$sc_notime = 'Tempo não aplicável';
$sc_noend = 'Sem data final';

$sc_never_happens = 'Entrada inválida! Este compromisso nunca ocorreu.';
$sc_start_later = 'Entrada inválida! A hora inicial é posterior à hora final.';

$sc_add_self = 'Entrada manual';

$sc_select_calendar = 'Seleccione o calendário para agendar este compromisso';
$sc_accept_confirm = 'O compromisso foi aceite e agendado.';
$sc_no_calendars = 'Não foi encontrado nenhum calendário válido para agendar este compromisso.';
$sc_bad_event = 'Não foi encontrado nenhum compromisso válido. O compromisso provavelmente foi apagado.';
$sc_accept_title = 'Aceitar compromisso';

$sc_decline_confirm = 'O compromisso foi recusado.';
$sc_decline_title = 'Recusar compromisso';

$sc_status = 'Estado';
$sc_not_responded = 'Sem resposta';
$sc_accepted = 'Aceite';
$sc_declined = 'Recusado';
$sc_no_participants = 'Sem participantes';

$sc_person = 'Pessoa';
$sc_owner = 'Próprio';
$sc_type = 'Tipo';
$sc_subscribed = 'Inscrito';

$sc_subscribe = 'Inscrever';
$sc_new_calendar = 'Novo calendário';
$sc_no_calendar = 'Não há calendários. Por favor, crie um agora ou permita que se crie um automaticamente.';
$sc_day_view = 'Mostrar por dia';
$sc_week_view = 'Mostrar por semana';
$sc_list_view = 'Mostrar compromissos';
$sc_put_in = 'Colocar compromisso em';
$sc_inherit_permissions ='Permissões herdadas de';
$sc_no_inherit = 'não herdar';

$sc_select_calendar_please = 'Seleccione apenas um calendário.';
$sc_events = 'Compromissos';

$sc_from_calendar = 'Deste calendário';
$sc_enitrely = 'Completamente';
$sc_delete_event = 'Apagar compromisso';
$sc_delete_pre = 'Deseja apagar o compromisso';
$sc_delete_suf = 'completamente ou apenas deste calendário?';

$sc_refresh = 'Actualizar';
$sc_reminder = 'Aviso';
$sc_mins = 'minutos';
$sc_hour = 'hora';
$sc_hours = 'horas';
$sc_day = 'dia';
$sc_days = 'dias';
$sc_week = 'semana';
$sc_weeks = 'semanas';
$sc_reminder_set_to_early = "O aviso não poderá aparecer antes de uma ocorrência prevista. Por favor, reduza o tempo do aviso ou aumente o período de repetição.";
$sc_cycle_start_later = 'A data inicial do período é posterior à data final.';


$cmdGenerate = 'Gerar';
$sc_preferences = 'Preferências';
$sc_holidays = 'Feriados';

$strNoHolidays = 'Sem feriados no banco de dados';
$strUnknown = 'Desconhecido';
$strYear = 'Ano';
$strDeleteHolidaysPrefix = 'Tem a certeza que deseja apagar todos os feriados do ano ';
$strDeleteHolidaysSuffix = '?';
$strReplaceHolidays = 'Tem a certeza que deseja subsituir todos os feriados existentes?';
$sc_holidays_text1 = 'Por favor clique em \''.$cmdGenerate.'\' para processar os feriados para o ano seleccionado.';
$sc_holidays_text2 = 'Por favor clique em \''.$cmdApply.'\' para guardar os feriados para o ano seleccionado.';

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

$sc_show_hours = 'Mostrar horas - das';
$sc_to = 'até às ';

$sc_today = 'Hoje';

$sc_recur_every = 'Repetir a cada';
$sc_months = 'meses';

$sc_private_event = 'Compromisso particular';
$sc_calendar_exists = 'Já existe um calendário com esse nome';
//translate
$sc_month = 'mês';
$sc_view = 'Mostrar';
$sc_calendar = 'calendário';
$sc_default = 'padrão';
$sc_month_view = 'Mostrar por mês';

//text changed, needs new translation
$sc_holidays_no_file_prefix = 'Os feriados não puderam ser gerados porque o ficheiro que contém as informações dos feriados para o ';
$sc_holidays_no_file_suffix = ' idioma está vazio ou não existe.';

$sc_no_events = 'Nenhum compromisso';

//2.03
$cal_export = 'Exportar';
$cal_import = 'Importar';
$cal_please_wait = 'Por favor, aguarde...';
$cal_import_text = 'Seleccione um arquivo iCal (*.ics) e clique em \'Ok\'';
$cal_filename = 'Ficheiro';
$cal_import_success = 'Data importada';

//2.04
$cal_add_todo = 'Nova pendência';
$cal_todos = 'pendências';

$cal_hide_completed = 'Ocultar concluídas';
$cal_show_all = 'Mostrar todas';
$cal_show_own = 'Mostrar as minhas';
$cal_show_responsible = 'Mostrar por responsável';

$cal_priority = 'Prioridade';
$cal_priority_values[0] = 'Baixa';
$cal_priority_values[1] = 'Média';
$cal_priority_values[2] = 'Alta';

$cal_status = 'Situação';
$cal_canceled = 'Cancelada';
$cal_completed = 'Concluída em';
$cal_percent_completed = '% Concluída';

$cal_before_task_start = 'Antes da pendência iniciar';
$cal_before_task_due = 'Antes da pendência ser concluída';
$cal_responsible = 'responsável';

$cal_due_at = 'até';
$cal_no_todos= 'Nenhuma pendência encontrada';
$cal_resend_invitation = 'Reenviar convite';

//2.05
$cal_everybody_write = 'Todos podem alterar este compromisso';
$cal_everybody_read = 'Apenas eu posso alterar este compromisso';

$cal_view = 'Interface | Cores';
$cal_views = 'Interface | Cores';
$sc_view_exists = 'Já existe um interface com este nome';
$cal_new_view = 'Novo interface';
$cal_event_calendars = 'calendário(s)';


//2.06
$cal_view_merged = 'combinado';
$cal_view_emerged = 'imprevisto';

//2.07
$cal_display = 'Vista';
$sc_new_task = "Nova tarefa";
$sc_all_day_events = 'compromissos o dia inteiro';
$sc_all_day_event = 'compromisso o dia inteiro';
$sc_usual_events = 'compromissos';
$sc_usual_event = 'compromisso';

//2.08
$sc_recur_section = 'Periodicidade';
$sc_options_section = 'Opções';
$sc_close = 'Fechar';

$cal_count_single = 'compromisso';
$cal_count_multiple = 'compromissos';

$cal_until = 'até';
$cal_forever = 'para sempre';

//2.09
$cal_merged_overview = 'Vista geral';
$cal_own_availability = 'Disponibilidade pessoal';

//2.11
$cal_no_file = 'Nenhum ficheiro recebido';
$cms_no_calendars_in_view = 'Esta vista não pode ser mostrada porque não há nenhum calendário nesta vista. Clique em \'Vistas\' para gerir as vistas.';
$cal_no_reminder = 'Não avisar';

//2.13
$cal_refresh_rate = 'Taxa de actualização';
$sc_seconds = 'segundos (deixe vazio para não actualizar)';
$cal_default_reminder = 'Aviso padrão';


//2.14
$cal_event = 'Compromisso';
$cal_todo = 'A fazer';
$cal_participants_write = 'Os participantes podem modificar o compromisso';

$cal_everybody_write = 'Todos podem modificar o compromisso'; //modified string from 2.05 this became the
$cal_everybody_read = 'Somente eu posso modificar o compromisso';//modified string from 2.05 

$cal_default_permissions = 'Permissão padrão';
$cal_show_todos = 'Mostrar tarefas no calendário';

$cal_edit_series_or_single = 'Editar somente este caso ou toda a série?';
$cal_single = 'Caso';
$cal_series = 'Série inteira';

$cal_legend = 'Legenda';
$cal_multiple_calendars = 'Calendários múltiplos';

$cal_cumulative = 'Entrada inválida! O compromisso deve ser maior que o intervalo recorrente.';

$cal_weekview = 'A vista semanal mostra';
$cal_mo_to_fr = 'Segunda a Sexta';
$cal_mo_to_su = 'Segunda a Domingo';

$cal_batchcalendars = 'Criar calendários para todos os utilizadores';
$cal_create_all_calendars = 'Criar calendários para todos os utilizadores ou garantir permissões para grupos de utilizadores';

$cal_no_views = 'não foi encontrada nenhuma vista';

$cal_my_cals = 'Os meus Calendários';

$cal_event_colors_override = 'Atribuir cores manualmente para os compromissos';

//2.15
$cal_groups = 'Grupos';
$cal_new_group = 'Novo grupo';
$cal_group_exists = 'Este nome já existe. Por favor, indique outro nome.';
$cal_no_calendars = 'Nenhum calendário neste grupo';
$cal_group = 'Grupo';
$cal_resources = 'Recursos';

$cal_event_mail_subject = 'Compromisso \'%s\' criado no seu calendário';//%s is event name
$cal_event_mail_body = '%s agendou o compromisso \'%s\' no seu calendário \'%s\'.'; //irst %s is the name of the person who created the event. Second is the event name, third is the calendar name
$cal_event_mail_open = 'Compromisso em aberto';
$cal_add_custom_field = 'Adicionar campo personalizado';

$cal_event_modified_mail_subject = 'Compromisso \'%s\' foi modificado no seu calendário';//%s is event name
$cal_event_modified_mail_body = '%s modificou o compromisso \'%s\' no seu calendário \'%s\'.'; //First %s is the name of the person who created the event. Second is the event name, third is the calendar name

$cal_your_event_modified_mail_subject = 'O compromisso \'%s\' que criou foi modificado';//%s is event name
$cal_your_event_modified_mail_body = '%s modificou o compromisso \'%s\' criado por si.'; //First %s is the name of the person who created the event. Second is the event name.
$cal_no_custom_fields = 'Não há campos personalizados definidos';
$cal_add_field = 'Adicionar campo';


$cal_resource_mail_subject = 'Recurso \'%s\' reservado'; //%s is resource name
$cal_resource_mail_body = '%s fez uma reserva para o recurso\'%s\'. Como é o administador deste recurso, por favor abra a reserva para a aceitar ou rejeitar.'; //First %s is the name of the person who created the event. Second is the calendar name
$cal_open_resource = 'Abrir reservas';

$cal_resource_modified_mail_subject = 'Reserva do recurso \'%s\' modificado';//%s is resource name
$cal_resource_modified_mail_body = '%s modificou a reserva para o recurso \'%s\'. Como é o administador deste recurso, por favor abra a reserva para a aceitar ou rejeitar.'; //First %s is the name of the person who created the event. Second is the calendar name

$cal_your_resource_modified_mail_subject = 'A sua reserva para \'%s\' no estado %s foi modificada';//%s is resource name, status
$cal_your_resource_modified_mail_body = '%s modificou a sua reserva para o recurso \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name


$cal_accept_mail_subject = 'Convite para \'%s\' aceite';
$cal_accept_mail_body = '%s aceitou o convite para \'%s\'';

$cal_decline_mail_subject = 'Convite para \'%s\' recusado';
$cal_decline_mail_body = '%s recusou o convite para \'%s\'';

$cal_statuses['NEEDS-ACTION'] = 'Precisa de atenção';
$cal_statuses['ACCEPTED'] = 'Aceite';
$cal_statuses['DECLINED'] = 'Recusado';
$cal_statuses['TENTATIVE'] = 'Tentativa';
$cal_statuses['DELEGATED'] = 'Delegado';
$cal_statuses['COMPLETED'] = 'Concluído';
$cal_statuses['IN-PROCESS'] = 'Em curso';

$cal_booking = 'Registo para \'%s\'';//%s is resource name

$cal_group_admins = 'Administradores';
$cal_no_admins = 'Nenhum adminstrador definido';
$cal_add_admin = 'Adicionar administrador';

$cal_scale='Intervalo';

$cal_show_completed = 'Mostrar tarefas concluídas';

$cal_resource = 'Recurso';
$cal_grid_view = 'Ver em grelha';

$cal_field_types['checkbox'] = 'Caixa de verificação';
$cal_field_types['text'] = 'Texto';
$cal_field_types['textarea'] = 'Area do texto';
$cal_field_types['date'] = 'Campo da data';

$cal_conflict = 'Este compromisso entra em conflito com outro compromisso ou reserva de recurso . Clique de novo em \'Ok\' ou \'Aplicar\' para ignorar ou clique em \'Verificar disponibilidade\' para encontrar uma hora adequada.';
$cal_select_calendar = 'Seleccionar calendário';
$cal_check_conflicts = 'Avise-me sobre conflitos quando eu criar um compromisso';
$cal_email_changes = 'Enviar email quando alguém fizer mudanças no meu calendário';
$cal_shared_calendars = 'Calendários partilhados';

$cal_admin = 'Administração';
$cal_resource_groups = 'Grupos de recursos';
$cal_open_calendar = 'Abrir outro calendário';

$cal_email_reminders='Enviar avisos para o meu email';

$cal_favourites = 'Favoritos';

$cal_your_resource_accepted_mail_subject = 'A reserva para \'%s\' foi aceite';//%s is resource name, status
$cal_your_resource_accepted_mail_body = '%s aceitou a sua reserva para o recurso \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

$cal_your_resource_declined_mail_subject = 'A reserva para \'%s\' foi recusada';//%s is resource name
$cal_your_resource_declined_mail_body = '%s recusou a sua reserva para o recurso \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

$cal_resource_deleted_mail_subject = 'A reserva para \'%s\' foi apagada';//%s is resource name, status

$cal_repeats_at = 'Repetir cada %s na %s';//eg. Repeats every month at the first monday
$cal_repeats_at_not_every = 'Repetir a cada %s %s na %s';//eg. Repeats every 2 weeks at monday 

$cal_repeats = 'Repetir cada %s';

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

$cal_show_busy='Mostrar ocupado';

$cal_subject='Assunto';
$cal_project_notice='Esta reserva também deve ser adicionada ao projecto seleccionado.';
$cal_dismiss_all = 'Remover tudo';
$cal_schedule_group='Agendar compromissos para todos na vista padrão. (Caso contrário será só seleccionado o seu calendário padrão)';

$cal_public_calendar = 'Publicar calendário';
$cal_public_url = 'Endereço público';

$cal_help_url='http://docs.group-office.com/index.php?folder_id=58&file_id=0';
$td_help_url='http://docs.group-office.com/index.php?folder_id=49&file_id=217';

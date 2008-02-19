<?php
require_once($GO_LANGUAGE->get_fallback_base_language_file('common'));

$charset = 'UTF-8';

$lang_modules['scheduler'] = 'Calendário';
$lang_modules['filesystem'] = 'Ficheiros';
$lang_modules['email'] = 'Email';
$lang_modules['projects'] = 'Projectos';
$lang_modules['search'] = 'Pesquisar';
$lang_modules['contacts'] = "Agenda";


//main language menu file register login and other non logged in pages
//common
$cmdLogin = "Entrar";
$cmdSave = "Guardar";
$cmdBack = "Voltar";
$cmdCancel = "Cancelar";
$cmdClose = "Fechar";
$cmdOk = "Ok";
$cmdDelete = "Apagar";
$cmdNext = "Seguinte";
$cmdPrevious = "Anterior";
$cmdContinue = "Continuar";
$cmdUpdate = "Actualizar";
$cmdApply = "Aplicar";
$cmdYes = "Sim";
$cmdNo = "Não";
$cmdAdd = 'Adicionar';
$cmdEdit = 'Editar';
$cmdInvertSelection = 'Inverter a selecção';

$strEdit = "Editar";
$strThis = 'isto';
$strItems = 'itens';
$strDescription = 'Descrição';

$strName = "Nome";
$strUser = "Utilizador";
$strUsername = "Nome do Utilizador";
$strPassword = "Password";
$strAuthcode = "Código de autorização";
$strCountry = "País";
$strState = "Estado";
$strCity = "Localidade";
$strZip = "Código Postal";
$strAddress = "Endereço";
$strEmail = "Email";
$strPhone = "Telefone Casa";
$strWorkphone = "Telefone Trabalho";
$strCellular = "Telemóvel";
$strCompany = "Empresa";
$strDepartment = "Departamento";
$strFunction = "Função";
$strQuestion = "Pergunta Secreta";
$strAnswer = "Resposta";
$strFax = "Fax";
$strWorkFax = "Fax Trabalho";
$strHomepage = "Sítio na Web";
$strWorkAddress = "Endereço Trabalho";
$strWorkZip = "Cód. Postal Trabalho";
$strWorkCountry = "País Trabalho";
$strWorkState = "Estado Trabalho";
$strWorkCity = "Cidade Trabalho";
$strPhoto = "Foto";
$strComments = "Notas";

$strProperties = 'Detalhes';

$strDeleteItem = "Apagar";

$strDate = "Data";

$strSaveSuccess = "Os dados foram guardados com sucesso";
$strSaveError = "Ocorreu um erro ao guardar os dados. Por favor tente outra vez.";
$strDataError = "Ocorreu um erro ao ler os dados. Por favor tente recarregar esta página.";
$strDeleteError = "Não pode apagar estes dados.";
$strAclError = "Erro do controle de acesso";
$strDateError = "A data é inválida";



$strAccessDenied = "Acesso negado";
$AccessDenied_text = "Não tem acesso a esta informação.<br />O acesso foi condicionado por outro utilizador.";
$invalid_chars = "Os seguintes caracteres não são permitidos";

$strGroups = "Grupos";
$strOwner = "Responsável";

$strDeletePrefix = "Confirma apagar ";
$strDeleteSuffix = "?";

//menu
$menu_logout = "Encerrar a sessão";
$menu_applications = "Aplicações";
$menu_help = "Ajuda";
$menu_about = "Sobre";
$menu_documentation = "Documentação";
$menu_configuration = "Configuração";
$menu_groups = "Gerir grupo de utilizadores";
$menu_accounts = "Modificar configurações da conta";
$menu_preferences = "Preferências pessoais";
$menu_bookmarks = "Favoritos";
$menu_bookmarks_manage = "Gerir Favoritos";
$menu_add_bookmark = "Adicionar Favorito";
$menu_add_current = "Adicionar actual";

$menu_invite = "Convidar outros utilizadores";

$menu_search_discussions = "Debates";
$menu_search_files = "Ficheiros e Pastas";
$menu_search_web = "A web";
$menu_scheduler = "Agendar";
$menu_filetypes = "Tipos de Ficheiros";
$menu_discussions = "Zona de debate";

$menu_users = "Utilizadores";
$menu_start = "Página inicial";
$menu_reboot = "reiniciar servidor";
$menu_halt = "parar servidor";
$menu_reboot_text = "AVISO! Todos os serviços de Group-Office ficarão temporariamente indisponíveis. Tem a certeza que deseja continuar?";
$menu_halt_text = "AVISO! Todos os serviços de Group-Office vão ser terminados. Tem a certeza que deseja continuar?";
$menu_new = "Nova";
$menu_message = "Mensagem";
$menu_contact = "Contacto";
$menu_event = "Compromisso";
$menu_bookmark = "Favorito";
$menu_topics = "Tópicos";
$menu_support = "Ajuda";
$menu_projects = "Projectos";
$menu_sysinfo = "Informação sobre o sistema";
$menu_admin = 'Administrador';
$menu_modules = 'Módulos';
$menu_manual = 'Manual';

//class validate
$error_required = "Não indicou o campo obrigatório abaixo.";
$error_match_auth = "Os códigos de autorização não coincidem.";
$error_match_pass = "As passwords não coincidem.";
$error_min_length = "Mínimo de caracteres do campo:";
$error_max_length = "Máximo de caracteres do campo:";
$error_email = "O endereço de email indicado é inválido."; 
$error_email_exists = "O endereço de email indicado já existe.";
$error_username_exists = "O nome do utilizador indicado já existe. Por favor escolha outro.";
$errors_in_form = "Há erros no formulário. Corrija-os e tente outra vez.";
$error_missing_field = "Não preencheu todos os campos obrigatórios.";

//acl control
$acl_control_auth_groups = "Grupos autorizados";
$acl_control_auth_users = "Utilizadores autorizados";
$acl_control_no_contacts = "Não há ninguém em contactos para seleccionar.<br /> Use a Agenda para adicionar membros aos contactos."; 

$strShowProfile = "Ver perfil";
$strShowGroup = "Ver membros do groupo";
$strProcessingTime = "O processamento desta página demorou ";
$strLoggedInAs = "Entrou como ";
$strAt = " em ";
$strEmailTo = "Envie uma mensagem de email para: ";
$strProtectedUser = "Utilizadores Protegidos";

$http_not_found = "Documento não encontrado";
$http_not_found_text = "O documento desejado não foi encontrado. Talvez tenha escrito algo mal.";
$http_error = "Erro Interno do Servidor";
$http_bad_request = "O pedido encontrou um problema";
$strAddContact = "Adicionar aos contactos";

$strReadRights = "Quem pode ver";
$strWriteRights = "Quem pode alterar";
$strPermissions = 'Permissões';

$editor_bold = "Carregado";
$editor_italic = "Itálico";
$editor_underline = "Sublinhado";
$editor_left = "Esquerda";
$editor_right = "Direito";
$editor_center = "Centro";
$editor_ord_list = "Lista Ordenada";
$editor_bul_list = "Lista com Destaque";
$editor_txt_color = "Cor do Texto";
$editor_back_color = "Cor do Fundo";
$editor_hyperlink = "Ligação";
$editor_image = "Imagem";
$editor_rule = "Linha Horizontal";

$editor_very_small = "Muito Pequeno";
$editor_small = "Pequeno";
$editor_medium = "Médio";
$editor_large = "Grande";
$editor_larger = "Maior";
$editor_very_large = "Muito Grande";

$editor_strikethrough = "Rasurado";
$editor_outdent = "Diminuir o avanço";
$editor_indent = "Aumentar o avanço";

$editor_superscript = "Superior à linha";
$editor_subscript = "Inferior à linha";
$editor_undo = "Desfazer";
$editor_redo = "Refazer";
$editor_copy = "Copiar";
$editor_paste = "Colar";
$editor_cut = "Cortar";

$editor_font = "Fonte";
$editor_size = "Tamanho";
$editor_heading = "Cabeçalho";
$editor_select = "Seleccione";

$editor_mode = "Editor";

$days[0]="Dom";
$days[1]="Seg";
$days[2]="Ter";
$days[3]="Qua";
$days[4]="Qui";
$days[5]="Sex";
$days[6]="Sáb";

$months[0]="Janeiro";
$months[1]="Fevereiro";
$months[2]="Março";
$months[3]="Abril";
$months[4]="Maio";
$months[5]="Junho";
$months[6]="Julho";
$months[7]="Agosto";
$months[8]="Setembro";
$months[9]="Outubro";
$months[10]="Novembro";
$months[11]="Dezembro";

$full_days[0] = "Domingo";
$full_days[1] = "Segunda-feira";
$full_days[2] = "Terça-feira";
$full_days[3] = "Quarta-feira";
$full_days[4] = "Quinta-feira";
$full_days[5] = "Sexta-feira";
$full_days[6] = "Sábado";
$strLoadTime = 'Gerado em';
$strDescription = 'Descrição';
$strTitle = 'Título';
$lang_modules['cms'] = "Sites da web";

$lang_modules['addressbook'] = "Agenda";
$cmdShowAll = 'Mostrar tudo';
$cmdSearch = 'Pesquisar';

//translation required
$cmdYesToAll = 'Sim para todos';
$cmdNoToAll = 'Não para todos';

$short_months[0]="Jan";
$short_months[1]="Fev";
$short_months[2]="Mar";
$short_months[3]="Abr";
$short_months[4]="Mai";
$short_months[5]="Jun";
$short_months[6]="Jul";
$short_months[7]="Ago";
$short_months[8]="Set";
$short_months[9]="Out";
$short_months[10]="Nov";
$short_months[11]="Dez";

$strToday = 'Hoje';

$cmdPrint = 'Imprimir';
$strFirstName = 'Nome próprio';
$strLastName = 'Apelidos';
$strInitials = 'Iniciais';
$strSex = 'Sexo';
$strBirthday = 'Aniversário';
$strSexes['M'] = 'Masculino';
$strSexes['F'] = 'Feminino';

$strTitle = 'Título';
$strBeginning = 'Iniciais';
$strMiddleName = 'Meio do nome';
$sir_madam['M'] = 'Sr.';
$sir_madam['F'] = 'Sra.';
$strModifiedAt = 'Modificado em';
$strCreatedAt = 'Criado em';

$htmlarea_language_name = 'pt';
$strLocalization = 'Localização';


//look up the language in controls/jscalendar/lang
$jscalendar_language_name = 'calendar-pt.js';


//2.01 translations

//look up language in 'classes/phpmailer/language'
$php_mailer_lang = 'pt';
$strEmailFail = 'Falhou o envio do email.';

//for people that want to use ktmlarea
$ktml_language = 'english';

$ml_host_unreachable = ' está inacessível. A verificação automática de email está desactivada.';
$strRegion = 'Região';

//2.05
$cmdReset = 'Anular';
$strMyName = 'O meu nome';

//2.06
$strDeleteRow = 'Tem a certeza que deseja apagar esta linha?';
$strAddressNo = 'N.';
$strWorkAddressNo = 'N. (Trabalho)';

$strPostAddress = 'Endereço de correio';
$strPostAddressNo = 'N.';
$strPostCity = 'Localidade';
$strPostState = 'Estado';
$strPostCountry = 'País';
$strPostZip = 'Cód. Postal';
$strVisitAddress = 'Endereço';
$strPostAddress = 'Outro Endereço';



//modules
$lang_modules['scheduler'] = 'Avisos';
$lang_modules['calendar'] = "Calendário";
$lang_modules['filesystem'] = 'Ficheiros';
$lang_modules['email'] = 'Email';
$lang_modules['projects'] = 'Projectos';
$lang_modules['search'] = 'Pesquisar';
$lang_modules['contacts'] = "Livro de Endereços";
$lang_modules['cms'] = "Websites";
$lang_modules['notes'] = "Notas";
$lang_modules['bookmarks'] = "Favoritos";
$lang_modules['summary'] = "Sumário";
$lang_modules['todos'] = "Tarefas";
$lang_modules['opentts'] = "Ajuda";
$lang_modules['products'] = "Loja Software";

$cmdVcard = 'vCard';

//2.07
$strNotEnoughLicenses = 'Não possui licenças suficientes para utilizar esta secção de Group-Office. Contacte sales@intermesh.nl se deseja adquirir licenças adicionais.';

//2.08
$alphabet = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';
									
$strContact = 'Contacto';
$strRelatedTo = 'Referente a';
$strProject = 'Projecto';

$strNewProject = 'Novo Projecto';
$strNewEvent = 'Novo Compromisso';
$strNewTodo = 'Nova Tarefa';
$strNewNote = 'Nova Nota';

$strActivities = 'Actividades';
$strFile = 'Ficheiro';

//2.10
$cmdCopy = 'Copiar';
$strProjects = 'Projectos';
$strFiles = 'Ficheiros';
$cmdSaveNew = 'Guardar & Novo';
$strAvailability = 'Disponibilidade';
$strCheckAvailabilityFor = 'Verificar disponibilidade de';
$cmdPleaseSelect = 'Seleccione, por favor...';

//2.11
$strNewEmail = 'Novo email';
$strConsistencyCheck = "Verificar consistência";
$strSearchAll = 'Todos os campos';
$sync_download_clients = 'Carregar sincronização de clientes';

//2.12
$strNoData = 'Não há dados';
//2.13
$strSynchronization = 'Sincronização';
$strSnooze = 'Snooze';
$strDismiss = 'Cancelar';

//2.14
$strUpload = 'Enviar Ficheiro';
$cmdInsert = 'Inserir';
$strText = 'Texto';
$strType = 'Tipo';

$strNoItemSelected = 'Não seleccionou um item';
$strSelectedItem = 'o item seleccionado';

$strKeyword = 'Palavra chave';

$strNoActivities = 'Não há actividades para este item';

$strCopy = 'Copiar'; //not a verb

//2.15
$strBank = 'Banco';
$strBankNo = 'Banco';
$strSelectedItems = 'itens seleccionados';

$strMonth = 'Mês';
$strYear = 'Ano';
$strDay = 'Dia';
$strHour = 'Hora';
$strMinute = 'Minuto';

$strTwelveHourFormat = 'Formato 12 horas';
$strTwentyfourHourFormat = 'Formato 24 horas';

//2.16
$strCreateLink = 'Criar ligação';
$strCancelLink = 'Cancelar ligação';
$strLinkText = 'A ligação está activa. Veja um item que deseje ligar e clique no botão \'Criar ligação\' para o ligar a \'%s\'. Se criar um item que possa ser ligado, ele será ligado automaticamente.';
$strLinks = 'Ligações';
$strUnlinkPrefix = 'Confirma remover ligação ';
$strUnlinkSuffix = '?';
$cmdUnlink = 'Cancelar ligação';



$strAdministrate = 'Administração';
$strProOnly = 'Esta funcionalidade só está disponível na versão Profissional. <a href="http://www.group-office.com">Clique aqui para saber mais sobre a versão Profissional</a>';

$strMoreColors='Mais cores';

$strNoItems = 'Não há itens';
$cmdExport = 'Exportar';

$error_username = 'Nome inválido. Os caracteres permitidos são: a-z, 0-9,- e _';

$cmdSettings = 'Configurações';


$strTableConfig = 'Configuração da tabela';

$strMonthText = 'Mês seguinte';

$cmdUpload='Enviar';
$cmdAttachFile='Adicionar ficheiro';


$search_text = '<b>Sugestão:</b> Inclua o tipo (contacto, empresa, projecto, etc.) do item que procura se obtiver muitos resultados';
$search_global='Pesquisar';
$search_results='resultados';
$search_result='resultado';
$strTime='Tempo';
$table_config_text='Escreva um número em frente das colunas que deseja ver. As colunas serão ordenadas pelo número que indicado.';
$strMoveUp='Mover uma linha para cima';

$strAddressAndNo='Endereço / N.';
$strWorkAddressAndNo='Endereço (Trabalho) / N.';

$config_help_url='http://docs.group-office.com/index.php?folder_id=49&file_id=202';

$jupload_text='Seleccione os ficheiros do computador e clique em \'Enviar\'.';

$jupload_max_size='Tamanho máximo';
$jupload_width='comprimento';
$jupload_height='altura';

$strReminder = 'Aviso';

$strMin='Minuto';
$strMins='Minutos';
$strHours='Horas';
$strHour='Hora';
$strDay='Dia';
$strDays='Dias';
$strWeek='Semana';
$strWeeks='Semanas';

$strStartTime = 'Início';
$strAddReminder = 'Adicionar Aviso';


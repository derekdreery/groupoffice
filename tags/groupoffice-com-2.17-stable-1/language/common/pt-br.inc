<?php
//Uncomment this line in new translations! 27.09.2006 by rafaeltgross@gmail.com
require_once($GO_LANGUAGE->get_fallback_base_language_file('common'));
$htmldirection='ltr';
$charset = 'UTF-8';
//main language menu file register login and other non logged in pages
//common
$cmdLogin = "Login";
$cmdSave = "Salvar";
$cmdBack = "Voltar";
$cmdCancel = "Cancelar";
$cmdClose = "Fechar";
$cmdOk = "OK";
$cmdDelete = "Excluir";
$cmdNext = "Próximo";
$cmdPrevious = "Anterior";
$cmdContinue = "Continuar";
$cmdUpdate = "Atualizar";
$cmdApply = "Aplicar";
$cmdYes = "Sim";
$cmdNo = "não";
$cmdAdd = 'Adicionar';
$cmdEdit = 'Editar';
$cmdInvertSelection = 'Inverter a seleção';

$strEdit = "Editar";
$strThis = 'Isto';
$strItems = 'Items';
$strDescription = 'Descrição';

$strName = "Nome";
$strUser = "Usuário";
$strUsername = "Nome do Usuário";
$strPassword = "Senha";
$strAuthcode = "Código de autorização";
$strCountry = "País";
$strState = "Estado";
$strCity = "Cidade";
$strZip = "CEP";
$strAddress = "Endereço";
$strEmail = "E-mail";
$strPhone = "Telefone";
$strWorkphone = "Telefone Comercial";
$strCellular = "Telefone Celular";
$strCompany = "Empresa";
$strDepartment = "Departamento";
$strFunction = "Cargo";
$strQuestion = "Pergunta Secreta";
$strAnswer = "Resposta";
$strFax = "Fax";
$strWorkFax = "Fax Comercial";
$strHomepage = "Página de internet";
$strWorkAddress = "Endereço do trabalho";
$strWorkZip = "CEP do trabalho";
$strWorkCountry = "País do trabalho";
$strWorkState = "Estado do trabalho";
$strWorkCity = "Cidade do trabalho";
$strPhoto = "Foto";
$strComments = "Comentários";
$strProperties = 'Propriedades';
$strDeleteItem = "Excluir";
$strDate = "Data";
$strSaveSuccess = "Os dados foram salvos com sucesso";
$strSaveError = "Um erro ocorreu ao tentar salvar os dados. Por favor tente outra vez.";
$strDataError = "Um erro ocorreu ao ler dados. Por favor tente atualizar esta página.";
$strDeleteError = "não foi possível excluir estes dados.";
$strAclError = "Erro de controle de acesso";
$strDateError = "Você inseriu uma data inválida";

$strAccessDenied = "Acesso Negado";
$AccessDenied_text = "Não foi permitido você ver esta sessão ou executar esta ação.<br/>Um outro usuário restringiu o acesso à informação que você pediu.";
$invalid_chars = "Os seguintes caracteres não são permitidos";
$strGroups = "Grupos";
$strOwner = "Propietário";
$strDeletePrefix = "Tem certeza que deseja excluir ";
$strDeleteSuffix = "?";
//menu
$menu_logout = "Sair";
$menu_applications = "Aplicações";
$menu_help = "Ajuda";
$menu_about = "Sobre";
$menu_documentation = "Documentação";
$menu_configuration = "Configuração";
$menu_groups = "Grupos de usuários";
$menu_accounts = "Sua Conta";
$menu_preferences = "Preferências";
$menu_bookmarks = "Favoritos";
$menu_bookmarks_manage = "Organizar meus Favoritos";
$menu_add_bookmark = "Adicionar aos Favoritos";
$menu_add_current = "Adicionar esta página aos Favoritos";
$menu_invite = "Convidar outros usuários";

$menu_search_discussions = "Procurar discussões";
$menu_search_files = "Arquivos e Pastas";
$menu_search_web = "Na Web";
$menu_scheduler = "Calendário";
$menu_filetypes = "Tipos de Arquivos";
$menu_discussions = "Discussão";

$menu_users = "usuários";
$menu_start = "Página Inicial";
$menu_reboot = "Reiniciar o Servidor";
$menu_halt = "Parar o Servidor";
$menu_reboot_text = "AVISO! Todos os serviços do Group-Office estarão temporariamente indisponíveis. Tem certeza que quer continuar?";
$menu_halt_text = "AVISO! Todos os serviços do Phoebus Internet Tools serão terminados. Tem certeza que quer continuar?";

$menu_new = "Nova";
$menu_message = "Mensagem";
$menu_contact = "Contato";
$menu_event = "Evento";
$menu_bookmark = "Favoritos";
$menu_topics = "Tópicos";
$menu_support = "Suporte";
$menu_projects = "Projetos";
$menu_sysinfo = "Informação sobre o sistema";
$menu_admin = 'Administrador';
$menu_modules = 'Módulos';
$menu_manual = 'Manual';

//class validate
$error_required = "você não especificou o campo requerido abaixo.";
$error_match_auth = "Os códigos de autorização não combinaram.";
$error_match_pass = "As senhas não combinaram.";
$error_min_length = "Caracteres minimos do campo abaixo:";
$error_max_length = "Caracteres máximos do campo abaixo:";
$error_email = "O endereço de E-mail que você especificou está inválido."; 
$error_email_exists = "O endereço de E-mail que você especificou já existe.";
$error_username_exists = "Desculpe, mas o nome do usuário que você introduziu já existe. Por favor escolha outro.";
$errors_in_form = "Existem erros no formulário. Corrija-os e tente outra vez.";
$error_missing_field = "você não preencheu todos os campos requeridos.";

//acl control
$acl_control_auth_groups = "Grupos autorizados";
$acl_control_auth_users = "Usuários autorizados";
$acl_control_no_contacts = "Você não tem contatos cadastrados.<br /> Use o Livro de endereços para adicionar membros aos seus contatos."; 

$strShowProfile = "Veja o perfil";
$strShowGroup = "Veja os membros dos grupos";
$strProcessingTime = "Tempo para abrir esta página ";
$strLoggedInAs = "Logado como ";
$strAt = " em ";
$strEmailTo = "Envie uma mensagem para: ";
$strProtectedUser = "usuários Protegidos";

$http_not_found = "O Arquivo não foi encontrado";
$http_not_found_text = "O Arquivo que você esta pesquisando não foi encontrado. Tente talvez corrigir o URL.";
$http_error = "Erro Interno do Servidor";
$http_bad_request = "Erro de requisição";
$strAddContact = "Adicionar aos contatos";

$strReadRights = "Permissão para ler";
$strWriteRights = "Permissão escrever";
$strPermissions = "Permissões";

$editor_bold = "Negrito";
$editor_italic = "Itálico";
$editor_underline = "Sublinhado";
$editor_left = "Esquerda";
$editor_right = "Direita";
$editor_center = "Centro";
$editor_ord_list = "Lista Ordenada";
$editor_bul_list = "Lista Em Balas";
$editor_txt_color = "Cor Do Texto";
$editor_back_color = "Cor Do Fundo";
$editor_hyperlink = "Hyperlink";
$editor_image = "Imagem";
$editor_rule = "Régra";
$editor_very_small = "Muito Pequeno";
$editor_small = "Pequeno";
$editor_medium = "Médio";
$editor_large = "Grande";
$editor_larger = "Maior";
$editor_very_large = "Muito Grande";
$editor_strikethrough = "Tachado";
$editor_outdent = "Outdent";
$editor_indent = "Indent";
$editor_superscript = "Sobreescrever";
$editor_subscript = "Subescrever";
$editor_undo = "Desfazer";
$editor_redo = "Refazer";
$editor_copy = "Copiar";
$editor_paste = "Colar";
$editor_cut = "Cortar";
$editor_font = "Font";
$editor_size = "Tamanho";
$editor_heading = "Cabeçalho";
$editor_select = "Selecione";
$editor_mode = "Modo";

$days[0]="Do";
$days[1]="Sg";
$days[2]="Te";
$days[3]="Qa";
$days[4]="Qi";
$days[5]="Se";
$days[6]="Sa";

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
$strLoadTime = 'Gerado dentro de';
$strDescription = 'Descrição';
$strTitle = 'Título';

$editorCmd['undo'] = "Desfazer";
$editorCmd['redo'] = "Refazer";
$editorCmd['bold'] = "Negrito";
$editorCmd['italic'] = "Itálico";
$editorCmd['underline'] = "Sublinhado";
$editorCmd['left'] = "Alinhar à Esquerda";
$editorCmd['right'] = "Alinhar à Direita";
$editorCmd['full'] = "Justificado";
$editorCmd['center'] = "Centralizado";
$editorCmd['ord_list'] = "Numeração";
$editorCmd['bul_list'] = "Marcadores";
$editorCmd['txt_color'] = "Cor do Texto";
$editorCmd['back_color'] = "Cor do Fundo";
$editorCmd['hyperlink'] = "Hyperlink";
$editorCmd['image'] = "Inserir imagem";
$editorCmd['rule'] = "Restágua Horizontal";
$editorCmd['table'] = "Inserir tabela";
$editorCmd['strikethrough'] = "Tachado";
$editorCmd['outdent'] = "Outdent";
$editorCmd['indent'] = "Indent";
$editorCmd['superscript'] = "Sobrescrever";
$editorCmd['subscript'] = "Subescrever";
$editorCmd['copy'] = "Copiar";
$editorCmd['paste'] = "Colar";
$editorCmd['cut'] = "Recortar";
$editorCmd['font'] = "Fonte";
$editorCmd['size'] = "Tamanho";
$editorCmd['heading'] = "Cabeçalho";
$editorCmd['select'] = "Selecionar";
$editorCmd['mode'] = "Alterar modo";
$editorCmd['textindicator'] = 'Estilo atual';

$cmdShowAll = 'Mostrar todos';
$cmdSearch = 'Procurar';

//translation required
$cmdYesToAll = 'Sim para todos';
$cmdNoToAll = 'não para todos';

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

$strShortWeek = 'sm';

$cal_prev_year = "Ano anterior (pressione para ver o menu)";
$cal_prev_month  = "Mês anterior (pressione para ver o menu)";
$cal_go_today = "Ver Hoje";
$cal_next_month = "Próximo mês (pressione para ver o menu)";
$cal_next_year  = "Próximo ano (pressione para ver o menu)";
$cal_drag_to_move = "Arraste para mover";
$cal_part_today = " (hoje)";
$cal_monday_first = "Exibir Segunda primeiro";
$cal_sunday_first  = "Exibir Domingo primeiro";
$cal_select_date = "Selecionar data";

$cmdPrint = 'Imprimir';
$strFirstName = 'Primeiro nome';
$strLastName = 'Último nome';
$strInitials = 'Iniciais';
$strSex = 'Sexo';
$strBirthday = 'Nascimento';
$strSexes['M'] = 'Masculino';
$strSexes['F'] = 'Feminino';

$strTitle = 'Título';

$strBeginning = 'Início';

$strMiddleName = 'Nome do meio';

$sir_madam['M'] = 'Senhor';
$sir_madam['F'] = 'Senhora';

$strModifiedAt = 'Alterar para';
$strCreatedAt = 'Criado em';

$htmlarea_language_name = 'pt-br';

$strLocalization = 'Localização';

$lang_modules['notes'] = "Notas";
//look up the language in controls/jscalendar/lang
$jscalendar_language_name = 'calendar-br.js';

$lang_modules['bookmarks'] = "Favoritos";

//2.01 translations

//look up language in 'classes/phpmailer/language'
$php_mailer_lang = 'br';
$strEmailFail = 'Falha ao enviar e-mail.';

//for people that want to use ktmlarea
$ktml_language = 'brasileiro';

$ml_host_unreachable = 'Não pôde ser localizado. A verificação automática de e-mails foi desativada.';
$strRegion = 'Região';

//2.05
$cmdReset = 'Limpar';
$strMyName = 'Meu nome';

//2.06
$strDeleteRow = 'Você tem certeza que quer apagar esta linha?';
$strAddressNo = 'numero da casa';
$strWorkAddressNo = 'Numero da casa do trabalho';

$strPostAddress = 'Endereço postal';
$strPostAddressNo = 'Numero postal da casa';
$strPostCity = 'Cidade postal';
$strPostState = 'Estado postal';
$strPostCountry = 'País postal';
$strPostZip = 'Zip postal';
$strVisitAddress = 'Endereço de visita';
$strPostAddress = 'endereço postal';


//modules
$lang_modules['scheduler'] = 'Agenda';
$lang_modules['calendar'] = "Calendário";
$lang_modules['filesystem'] = 'Arquivos';
$lang_modules['email'] = 'E-mail';
$lang_modules['projects'] = 'Projetos';
$lang_modules['search'] = 'Pesquisar';
$lang_modules['contacts'] = "Contatos";
$lang_modules['addressbook'] = "Contatos";
$lang_modules['cms'] = "Websites";
$lang_modules['notes'] = "Anotações";
$lang_modules['bookmarks'] = "Favoritos";
$lang_modules['summary'] = "Sumário";
$lang_modules['todos'] = "Pendências";
$lang_modules['documents'] = "Documentos";

$cmdVcard = 'vCard';

//2.07
$strNotEnoughLicenses = 'Você não tem licenças suficientes para usar esta seção do'.
												' Group-Office. Contatar sales@intermesh.nl se você quiser '.
												'adiquirir licenças adicionais.';
												
//2.08
$alphabet = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';
									
$strContact = 'Contato';
$strRelatedTo = 'Relacionado a';
$strProject = 'Projeto';
$strNewProject = 'Novo Projeto';
$strNewEvent = 'Novo evento';
$strNewTodo = 'Nova Pendência';
$strNewNote = 'Nova Anotação';
$strActivities = 'Atividades';
$strFile = 'Arquivo';
//2.10
$cmdCopy = 'Copiar';
$strProjects = 'Projetos';
$strFiles = 'Arquivos';
$cmdSaveNew = 'Salvar & Novo';
$strAvailability = 'Disponibilidade ';
$strCheckAvailabilityFor = 'Checar disponibilidade para';
$cmdPleaseSelect = 'Favor escolha...';
//2.11
$strNewEmail = 'Novo e-mail';
$strConsistencyCheck = "Checar Consistência";
$strSearchAll = 'Todos campos';
$sync_download_clients = 'Clientes da sincronização do Download';
//2.12
$strNoData = 'Nenhum dado encontrado';
//2.13
$strSynchronization = 'Sincronização';
$strSnooze = 'Relembrar depois';
$strDismiss = 'Descartar';
//2.14
$strUpload = 'Enviar arquivo';
$cmdInsert = 'Inserir';
$strText = 'Texto';
$strType = 'Tipo';
$strNoItemSelected = 'Você não selecionou nenhum item';
$strSelectedItem = 'o item selecionado';
$strKeyword = 'Palavra-Chave';
$strNoActivities = 'Nenhuma atividade para este item';
$strCopy = 'Cópia'; //not a verb
//2.15
$strBank = 'Banco';
$strBankNo = 'Numero do Banco';
$strSelectedItems = 'itens selecionados';
$strMonth = 'Mês';
$strYear = 'Ano';
$strDay = 'Dia';
$strHour = 'Hora';
$strMinute = 'Minuto';

$strTwelveHourFormat = 'formato 12 horas';
$strTwentyfourHourFormat = 'formato 24 horas';
//2.16
$strCreateLink = 'Criar ligação';
$strCancelLink = 'Cancelar ligação';
$strLinkText = 'Ligação está ativa. Navegue ao item ligado e clique o botão \'Criar Link\' para \'%s\'. Se você criar um item novo que seja ligável, então ele estará ligado automaticamente.';
$strLinks = 'Ligações';
$strUnlinkPrefix = 'Você tem certeza que deseja desfazer as ligações';
$strUnlinkSuffix = '?';
$cmdUnlink = 'Desfazer Ligações';
$strAdministrate = 'Administre';
$strProOnly = 'Esta  característica está somente disponível no Group-Office Professional. <a href="http://www.group-office.com">Clique aqui para entender mais sobre o Group-Office Professional</a>';

$strMoreColors='Mais cores';
$strNoItems = 'Nenhum item encontrado';
$cmdExport = 'Exportar';
$error_username = 'Nome de usuário inválido. Somente estes caracteres são permitidos: a-z, 0-9,- pt _';
$cmdSettings = 'Configurações';

<?php
require_once($GO_LANGUAGE->get_fallback_base_language_file('common'));

$charset = 'UTF-8';

$lang_modules['scheduler'] = 'calend&aacute;rio';
$lang_modules['filesystem'] = 'ficheiros';
$lang_modules['email'] = 'e-mail';
$lang_modules['projects'] = 'projetos';
$lang_modules['search'] = 'pesquisar';
$lang_modules['contacts'] = "livro de endere&ccedil;os";


//main language menu file register login and other non logged in pages
//common
$cmdLogin = "login";
$cmdSave = "guardar";
$cmdBack = "voltar para tr&aacute;s";
$cmdCancel = "cancelar";
$cmdClose = "fechar";
$cmdOk = "ok";
$cmdDelete = "suprimir";
$cmdNext = "pr&oacute;ximo";
$cmdPrevious = "anterior";
$cmdContinue = "continuar";
$cmdUpdate = "sincronize";
$cmdApply = "aplique";
$cmdYes = "sim";
$cmdNo = "n&atilde;o";
$cmdAdd = 'adicione';
$cmdEdit = 'edite';
$cmdInvertSelection = 'invertir a sele&ccedil;&atilde;o';

$strEdit = "Editar";
$strThis = 'isto';
$strItems = 'artigos';
$strDescription = 'Descri&ccedil;&atilde;o';

$strName = "Nome";
$strUser = "Utilizador";
$strUsername = "Nome do Utilizador";
$strPassword = "Senha";
$strAuthcode = "C&oacute;digo de autoriza&ccedil;&atilde;o";
$strCountry = "Pa&iacute;s";
$strState = "Estado";
$strCity = "Cidade";
$strZip = "C&oacute;digo Postal";
$strAddress = "Endere&ccedil;o";
$strEmail = "E-mail";
$strPhone = "Telefone Residencial";
$strWorkphone = "Telefone Commercial";
$strCellular = "Telefone Celular";
$strCompany = "Companhia";
$strDepartment = "Departamento";
$strFunction = "Fun&ccedil;&atilde;o";
$strQuestion = "Pergunta Secreta";
$strAnswer = "Resposta";
$strFax = "Fax";
$strWorkFax = "Fax Trabalho";
$strHomepage = "Homepage";
$strWorkAddress = "Endere&ccedil;o Trabalho";
$strWorkZip = "C&oacute;digo Postal Trabalho";
$strWorkCountry = "Pa&iacute;s Trabalho";
$strWorkState = "Estado Trabalho";
$strWorkCity = "Cidade Trabalho";
$strPhoto = "Foto";
$strComments = "Coment&aacute;rios";

$strProperties = 'propriedades';

$strDeleteItem = "Suprimir";

$strDate = "Data";

$strSaveSuccess = "Os dados conservaram com sucesso";
$strSaveError = "Um erro ocorreu ao conservar dados. Por favor tentar outra vez.";
$strDataError = "Um erro ocorreu ao ler dados. Por favor tente recarregar esta p&aacute;gina.";
$strDeleteError = "N&atilde;o podia suprimir estes dados.";
$strAclError = "Erro do controle de acesso";
$strDateError = "Voc&ecirc; incorporou uma data inv&aacute;lida";



$strAccessDenied = "Acesso negado";
$AccessDenied_text = "N&atilde;o &eacute; permitido voc&ecirc; ler esta sec&ccedil;&atilde;o do local ou executar esta action.<br />Um outro utilizador restringiu o acesso &agrave; informa&ccedil;&atilde;o que voc&ecirc; pediu.";
$invalid_chars = "Os seguintes caráteres não são permitidos";

$strGroups = "Grupos";
$strOwner = "Propriet&aacute;rio";

$strDeletePrefix = "Certo que voc&ecirc; quer suprimir ";
$strDeleteSuffix = "?";

//menu
$menu_logout = "encerrar a sess&atilde;o";
$menu_applications = "aplica&ccedil;&otilde;es";
$menu_help = "ajuda";
$menu_about = "sobre";
$menu_documentation = "documenta&ccedil;&atilde;o";
$menu_configuration = "configura&ccedil;&atilde;o";
$menu_groups = "grupos&nbsp;de&nbsp;utilizadores";
$menu_accounts = "a&nbsp;sua&nbsp;conta";
$menu_preferences = "prefer&ecirc;ncias";
$menu_bookmarks = "bookmarks";
$menu_bookmarks_manage = "modifique&nbsp;bookmarks";
$menu_add_bookmark = "addicione&nbsp;como&nbsp;bookmark";
$menu_add_current = "addicione&nbsp;a&nbsp;currente&nbsp;p&aacute;gina";

$menu_invite = "convide&nbsp;outros&nbsp;utilizadores";

$menu_search_discussions = "discuss&otilde;es";
$menu_search_files = "ficheiros&nbsp;e&nbsp;cadernos";
$menu_search_web = "a&nbsp;web";
$menu_scheduler = "calend&aacute;rio";
$menu_filetypes = "tipos&nbsp;de&nbsp;ficheiros";
$menu_discussions = "placa&nbsp;de&nbsp;discuss&atilde;o";

$menu_users = "utilizadores";
$menu_start = "start&nbsp;page";
$menu_reboot = "reinic&iacute;ar&nbsp;o&nbsp;servidor";
$menu_halt = "parar&nbsp;o&nbsp;servidor";
$menu_reboot_text = "AVISO! Todos os servi&ccedil;os do Group-Office ser&atilde;o temporariamente indispon&iacute;veis. Certo que voc&ecirc; quer continuar?";
$menu_halt_text = "AVISO! Todos os servi&ccedil;os do Group-Office ser&atilde;o terminados. Certo que voc&ecirc; quer continuar?";
$menu_new = "nova";
$menu_message = "mensagem";
$menu_contact = "contato";
$menu_event = "evento";
$menu_bookmark = "bookmark";
$menu_topics = "t&oacute;picos";
$menu_support = "suporte";
$menu_projects = "projetos";
$menu_sysinfo = "informa&ccedil;&atilde;o sobre o sistema";
$menu_admin = 'administrador';
$menu_modules = 'm&oacute;dulos';
$menu_manual = 'manual';

//class validate
$error_required = "Voc&ecirc; n&atilde;o especificou o campo requerido abaixo.";
$error_match_auth = "Os c&oacute;digos de autoriza&ccedil;&atilde;o n&atilde;o combinaram.";
$error_match_pass = "As senhas n&atilde;o combinaram.";
$error_min_length = "Car&aacute;teres m&iacute;nimos do campo abaixo:";
$error_max_length = "Car&aacute;teres m&aacute;ximos do campo abaixo:";
$error_email = "O endere&ccedil;o de E-mail que voc&ecirc; especificou &eacute; innv&aacute;lido."; 
$error_email_exists = "O endere&ccedil;o de E-mail que voc&ecirc; especificou j&aacute; existe.";
$error_username_exists = "Desculpe, mas o nome do utilizador que voc&ecirc; introduziu j&aacute; existe. Por favor escolha outro.";
$errors_in_form = "Havia uns erros no formul&aacute;rio. Corrija-os e tente-os outra vez.";
$error_missing_field = "Voc&ecirc; n&atilde;o preencheu todos os campos requeridos.";

//acl control
$acl_control_auth_groups = "Grupos autorizados";
$acl_control_auth_users = "Utilizadores autorizados";
$acl_control_no_contacts = "Voc&ecirc; n&atilde;o tem nenhuns contatos do membro a selecionar.<br /> Use o Livro de Endere&ccedil;os para adicionar membros aos seus contatos."; 

$strShowProfile = "Veja o perfil";
$strShowGroup = "Veja membros de groupos";
$strProcessingTime = "Processar desta p&aacute;gina levou ";
$strLoggedInAs = "Entrado como ";
$strAt = " em ";
$strEmailTo = "Envie uma mensagem E-mail para: ";
$strProtectedUser = "Utilizadores Protegidos";

$http_not_found = "O Ficheiro n&atilde;o foi encontrado";
$http_not_found_text = "O Ficheiro que pesquisa n&atilde;o foi encontrado. Tente talvez corrigir o URL.";
$http_error = "Erro Interno Do Servidor";
$http_bad_request = "Pedido mau";
$strAddContact = "Adicione aos contatos";

$strReadRights = "Permiss&atilde;o para ler";
$strWriteRights = "Permiss&atilde;o para escrever";
$strPermissions = 'Permiss&otilde;es';

$editor_bold = "Bold";
$editor_italic = "It&aacute;lico";
$editor_underline = "Underline";
$editor_left = "Esquerda";
$editor_right = "Direito";
$editor_center = "Centro";
$editor_ord_list = "Lista Ordenada";
$editor_bul_list = "Lista Em Balas";
$editor_txt_color = "Cor Do Texto";
$editor_back_color = "Cor Do Fundo";
$editor_hyperlink = "Hyperlink";
$editor_image = "Imagem";
$editor_rule = "Regra Horizontal";

$editor_very_small = "Muito Pequeno";
$editor_small = "Pequeno";
$editor_medium = "Medio";
$editor_large = "Grande";
$editor_larger = "Maior";
$editor_very_large = "Muito Grande";

$editor_strikethrough = "Strike Through";
$editor_outdent = "Outdent";
$editor_indent = "Indent";

$editor_superscript = "Superscript";
$editor_subscript = "Subscript";
$editor_undo = "Undo";
$editor_redo = "Redo";
$editor_copy = "Copiar";
$editor_paste = "Colar";
$editor_cut = "Cortar";

$editor_font = "Font";
$editor_size = "Tamanho";
$editor_heading = "Cabe&ccedil;alho";
$editor_select = "Seleccione";

$editor_mode = "&quot;Lan&ccedil;amento do editor";

$days[0]="Do";
$days[1]="Sg";
$days[2]="Te";
$days[3]="Qa";
$days[4]="Qi";
$days[5]="Se";
$days[6]="Sa";

$months[0]="Janeiro";
$months[1]="Fevereiro";
$months[2]="Mar&ccedil;o";
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
$full_days[2] = "Ter&ccedil;a-feira";
$full_days[3] = "Quarta-feira";
$full_days[4] = "Quinta-feira";
$full_days[5] = "Sexta-feira";
$full_days[6] = "Sabado";
$strLoadTime = 'Gerado dentro de';
$strDescription = 'Descri&ccedil;&atilde;o';
$strTitle = 'T&iacute;tulo';
$lang_modules['cms'] = "web sites";
?>

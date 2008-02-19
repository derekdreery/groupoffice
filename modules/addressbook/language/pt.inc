<?php
require_once($GO_LANGUAGE->get_fallback_language_file('addressbook'));
//$charset = 'ISO-8859-1';

//contacts
$contacts_title = "Agenda";
$contacts_no_contacts = "Nenhuns contactos encontrados.";
$contacts_members = "Membros";
$contacts_contacts = "Contactos";
$contacts_add = "Novo Contacto";
$contacts_groups = "grupos";
$contacts_other = "Outros Contactos";
$contacts_move_to_group = "Mude os contactos seleccionados para o grupo...";
$groups_no_groups = "Não tem grupos";
$contacts_group = "grupo";
$contacts_empty_group = "Use o menu da lista para mudar contactos para este grupo.";
$contacts_this_group = 'este grupo';

$add_contacts_text = "Indique o endereço de email e o código de autorização e clique 'Ok'";
$add_contacts_success = "Utilizador adicionado com sucesso";
$add_contacts_wrong = "Email errado ou código de autorização";
$add_personal_text = "Clique sobre 'Guardar' para adicionar o contacto. Os campos marcados com * são obrigatórios.";
$contacts_select = "Seleccione os contactos a adicionar e clique em 'Ok'.";
$contacts_select_title = "Contactos";
$contact_profile = "Perfil do contacto";
$user_profile = "Perfil do utilizador";
$contacts_authorise = "autorização";
$contacts_add_member = "Adicione este membro aos seus contactos";
$strConfirmSynchronise = "Este contacto será actualizado com os dados que o utilizador indicou. As alterações manuais serão perdidas. Confirma que deseja continuar?";
$contacts_import = "Importar";
$contacts_export = "Exportar";
$contacts_export_text = "Seleccione o tipo de CSV que deseja criar.";
$contacts_delete = "Apagar";

$contacts_import_text = "Seleccione um ficheiro CSV e clique sobre 'Ok'";
$contacts_import_text_group = "Seleccione um grupo e o ficheiro CSV. Clique sobre 'Ok'.";
$fbNoFile = "Nenhum ficheiro foi recebido";
$fbPleaseWait = "Por favor aguarde enquanto o ficheiro é enviado...";

$strNotIncluded = "Não importar";
$contacts_import_feedback = "As seguintes colunas foram encontradas no ficheiro CSV.<br />";
$contacts_import_feedback .= "Seleccione o grupo que deseja importar ou crie automaticamente grupos baseados numa coluna do ficheiro.<br />";
$contacts_import_feedback .= "Combine colunas correspondentes com as colunas do ficheiro e verifique se não seleccionou a mesma coluna duas vezes.";
$contacts_separator = "Os valores são separados por";
$contacts_no_separator = "Não indicou um caracter de separação.";
$fbFilename = "Nome do ficheiro";
$contacts_import_title = "Importar Livro de Endereços";
$contacts_import_success = "Ficheiro importado com sucesso";
$contacts_import_double = "Seleccionou uma ou mais colunas duas vezes. Corrija e tente de novo.";
$contacts_import_noname = 'Deve seleccionar os campos do nome';
$contacts_import_incompatible = "Formato incompatível de ficheiro.<br /><br />Um ficheiro CSV é um ficheiro com valores separados por vírgulas.";
$strMember = "Membro";
$contact_exist_warning = "<b>Nota:</b> Este utilizador já existe na lista de contactos.";
$contacts_add_to_group = "Adicionar utilizadors seleccionados aos contactos do grupo...";
$contacts_add_to = "Adicionar os utilizadors seleccionados aos contactos";
$contacts_add_user = "adicionar";
$contacts_edit = "Editar";
$contacts_no_select = "Nada foi seleccionado";
$contacts_this_contact = "este contacto";

$contacts_seperator_text = 'Valores separados por';
$contacts_quote = 'Valores incluídos por';
$contacts_clrf = 'Linhas terminadas por';
$contacts_auto_group = 'Criar grupos automaticamente a partir do ficheiro';
$contacts_import_to_group = 'Importar para o grupo';

$contacts_search = 'Pesquisar';
$cmdSearch = 'Pesquisar';
$contacts_search_text = "Clique numa letra ou indique uma frase a procurar e clique em 'Pesquisar'. Pode usar * ";
$contacts_results = 'resultados';
$contacts_result = 'resultado';


//translation required
$contacts_color = 'Cor';

$contacts_colors['black'] = 'Preto';
$contacts_colors['red'] = 'Vermelho';
$contacts_colors['green'] = 'Verde';
$contacts_colors['blue'] = 'Azul';
$contacts_colors['pink'] = 'Rosa';
$contacts_colors['orange'] = 'Laranga';
$contacts_colors['brown'] = 'Castanho';

$ab_addressbooks = 'Agendas';
$ab_owner = 'Pessoal';
$ab_subscribed = 'Subscrito';
$ab_new_ab = 'Nova Agenda';
$ab_addressbook_exists = 'Já existe uma agenda com este nome';
$ab_addressbook = 'Agenda';
$ab_no_addressbook = 'Não há Agendas';
$contacts_select_single = 'Clique num contacto para seleccionar essa pessoa';

$ab_group_exists = 'Já existe uma agenda com esse nome';
$ab_group_on_file = 'Grupo de contactos baseado em ficheiro';
$ab_default = 'Padrão';

//translation required
$ab_edit_contact = 'Clique em \'Ok\' para guardar as alterações. Os campos marcados com * são obrigatórios';
$ab_group_on_file = 'Grupos de contactos baseados no ficheiro';
$ab_default = 'Padrão';
$ab_browse = 'Listar';
$ab_comment = 'Obs.';

$ab_contact = 'Contacto';
$ab_send_message = 'Novo email';
$ab_new_event = 'Novo compromisso';

$ab_templates = 'Modelos';
$ab_your_templates = 'O seu';
$ab_all_templates = 'Todos';

$template_type[0] = 'Email';
$template_type[1] = 'OpenOffice.Org';
$ab_type = 'Type';
$ab_template_exists = 'Já existe um modelo com este nome.';

$sir_madam['M'] = 'Sr.';
$sir_madam['F'] = 'Sra.';

$ab_upload_oo = 'Enviar ficheiro';
$ab_upload_nofile = 'Erro ao enviar ficheiro.';

$ab_oo_doc = 'Documento de texto';
$ab_select_template = 'Seleccionar modelo para este documento:';
$ab_add_field_data = 'Pode usar os seguintes valores no documento:';
$ab_datafield = 'Campo';

$ab_no_template = 'Sem modelo';
$ab_new_note = 'Nota';

$ab_events = 'Compromissos';

$ab_new_contact = 'Novo Contacto';
$ab_new_company = 'Nova Empresa';
$ab_companies = 'Empresas';
$ab_company_properties = 'Perfil da empresa';
$ab_bank_no = 'Banco';
$ab_companies_count_single = 'empresa';
$ab_companies_count = 'empresas';
$ab_no_companies = 'Não foram encontradas empresas';
$ab_no_company = 'Nenhuma empresa encontrada';

$ab_import_type = 'Importar';
$ab_no_company_found = 'Não foram encontradas empresas';
$ab_search_company = 'Procurar empresa';
$ab_edit_company = 'Modificar perfil da empresa';
$ab_new_group = 'Novo grupo';
$ab_export_type = 'Exportar';

$ab_custom_fields = 'Campos Extra';
$ab_field_size = 'Tamanho';

$ab_new_field = 'Novo campo';
$ab_invalid_field_size = 'Tamanho inválido. Escolha um valor entre 10 e 255.';
$ab_field_exists = 'Já existe um campo com esse nome.';

$ab_employees = 'Colaboradores';
$ab_no_employees = 'Nenhum colaborador';

$ab_employee_count_single = 'colaborador';
$ab_employee_count_multiple = 'colaboradores';
$ab_vat_no = 'Taxa do IVA';

$ab_add_new = 'Adicionar Novo';
$ab_add_existing = 'Adicionar existente';

$ab_access = 'Acesso';
//1.98
$ab_mailings = 'Listas de correio';
$ab_mailing_groups = 'Grupos de listas de correio';
$ab_no_mailing_groups = 'Não há grupos de listas de correio';
$ab_no_templates = 'Não há modelos';
$ab_mailing_group_exists = 'Já existe um grupo de listas de correio com este nome.';
$ab_contacts_count_single = 'contacto';
$ab_contacts_count_multiple = 'contactos';
$ab_new_mailing = 'Nova lista de correio';
$ab_all_your_addressbooks = 'todas as suas agendas';

$ab_search_for = 'Procurar por';
$ab_search_on = 'dentro de';
$ab_search_keyword = 'Procurar frase';
$ab_wildcard = '(Use \'%\' para substituir v&aacute;rios caracteres)';

//2.03
$ab_copy_read_acl = 'Copiar as permissões de ver para contacto e empresas';
$ab_copy_write_acl = 'Copiar as permissões de alterar para contacto e empresas';

$ab_catagory_exists = 'Já existe uma categoria com este nome.';
$ab_add_catagory = 'Adicionar categoria';
$ab_add_field = 'Adicionar campo';
$ab_catagory = 'Categoria';

//2.04
$ab_todos = 'Compromissos';


//2.05
$ab_download = 'Descarregar';
$ab_download_template = 'Clique no botão \'Descarregar\' para transferir o modelo para o PC e depois o editar.';
$ab_upload = 'Enviar Ficheiro';
//modified string:
$ab_upload_oo_text = 'Quando concluir a edição do modelo do OpenOffice.org deve enviá-lo para Group-Office. Seleccione o ficheiro e clique em \'Enviar Ficheiro\' para transferir o modelo.';
$ab_file_type = 'Tipo de Ficheiro';
$ab_import_vcf_file_ok = 'O envio do ficheiro parece ter sido bem sucedido. Clique em \'Ok\' para o adoptar na Agenda.';
$ab_import_failed = 'Falhou o envio.';
//modified string:
$contacts_import_text = "Seleccione um ficheiro com dados a importar e clique em 'Ok'";

//2.06
$ab_select_mailing_groups = 'Seleccione um grupo da lista de correio a que este contacto pertence';

//2.11
$ab_move_to_ab = 'Mover contactos seleccionados para a agenda...';
$ab_move_com_to_ab = 'Mover empresas seleccionadas para a agenda...';

//2.12
$ab_search_sender = 'Procurar email';

$ab_contact_exists = 'Este contacto parece já existir. Clique %s para ir para o contacto existente ou clique em \'Ok\' ou \'Aplicar\' para guardar este contacto de qualquer forma.';
$ab_company_exists = 'Esta empresa parece já existir. Clique %s para ir para a empresa existente ou clique em \'Ok\' ou \'Aplicar\' para guadar esta empresa de qualquer forma.';
$ab_here = 'aqui';

//2.13
$ab_search_for_keyword = 'por';

$ab_search_in = 'Pesquisar em'; //changed
$ab_search_on_users = 'Pesquisar dentro de';

$ab_users_count_single='utilizador';
$ab_users_count_multiple='utilizadores';
$ab_no_users='Não há utilizadores';

//2.15
$ab_select_addressbook = 'Seleccione Agenda';
$ab_move = 'Mover';
$ab_file='Ficheiro';

$ab_template_uploaded = 'Documento guardado';
$ab_select_mailing_group = 'Seleccione o grupo para que pretende enviar o correio';

//2.16
$ab_id = 'n.';

$ab_contact ='Contacto';
$ab_company ='Empresa';

$ab_edit_selected = 'Editar seleccionado';
$ab_edit_selected_warning = 'Aviso! Est&aacute; a editar %s %s. Todos serão actualizados com estas alterações. Os campos em branco serão ignorados.';
$ab_contacts='Contactos';
$ab_companies='Empresas';
$ab_users='utilizadores';
$ab_dontchange='Não alterar';

$ab_select_unknown_recipient='O destinatário %s é desconhecido. O endereço de email pode pertencer a uma lista de contactos abaixo. Clique no nome de um contacto para o adicionar à lista, adicionar um novo contacto ou ignorar o endereço de email.';
$ab_ignore='Ignore';

$ab_check_info='O endereço de email foi adicionado ao perfil do contacto. Clique em \'Ok\' para o guardar.';
$ab_reset='Cancelar';
$ab_help_url='http://docs.group-office.com/index.php?folder_id=56&file_id=0';
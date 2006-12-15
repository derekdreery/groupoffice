<?php
require_once($GO_LANGUAGE->get_fallback_language_file('addressbook'));

//contacts
$contacts_title = "Libreta de direcciones";
$contacts_no_contacts = "No se han encontrado contactos.";
$contacts_members = "Miembros";
$contacts_contacts = "Contactos";
$contacts_add = "Nuevo contacto";
$contacts_groups = "Grupos";
$contacts_other = "Otros contactos";
$contacts_move_to_group = "Mover los contactos seleccionados al grupo...";
$groups_no_groups = "No tiene ningún grupo";
$contacts_group = "Grupo";
$contacts_empty_group = "Use la lista combo para mover contactos a este grupo.";
$contacts_this_group = 'este grupo';

$add_contacts_text = "Introduzca la dirección de correo electrónico y el código de autorización en los miembros y haga click en 'Aceptar'";
$add_contacts_success = "Usuario añadido correctamente";
$add_contacts_wrong = "Correo electrónico o código de autorización incorrectos.";
$add_personal_text = "Haga click en 'Guardar' para añadir el contacto. Los campos marcados con un '*' son obligatorios.";
$contacts_select = "Seleccione los contactos a añadir y haga click en 'Aceptar'.";
$contacts_select_title = "Contactos";
$contact_profile = "Perfil";
$user_profile = "Perfil del usuario";
$contacts_authorise = "Autorización";
$contacts_add_member = "Añadir este miembro a sus contactos";
$strConfirmSynchronise = "Esto actualizará este contacto con los datos que el usuario ha entrado el mismo. Los cambios manuales hechos por usted se perderán. ¿Está seguro de querer continuar?";
$contacts_import = "Importar";
$contacts_export = "Exportar";
$contacts_export_text = "Seleccionar el tipo de CSV que desea crear.";
$contacts_delete = "Borrar";

$contacts_import_text_group = "Seleccionar un grupo y el fichero CSV. Haga click en 'Aceptar'.";
$fbNoFile = "No se ha recibido ningún fichero";
$fbPleaseWait = "Por favor espere mientras se carga el fichero...";

$strNotIncluded = "No importar";
$contacts_import_feedback = "Las siguientes columnas han sido encontradas en su fichero CSV.<br />";
$contacts_import_feedback .= "Seleccionar el grupo que desea importar o crear automáticamente los grupos basados en el fichero.<br />";
$contacts_import_feedback .= " Haga coincidir las columnas de Group-Office con las columnas del fichero y asegúrese que no ha seleccionado dos veces la misma columna.";
$contacts_separator = "Los valores están separados por";
$contacts_no_separator = "No ha introducido ningún carácter separador.";
$fbFilename = "Nombre de fichero";
$contacts_import_title = "Importar Libreta de direcciones";
$contacts_import_success = "Fichero importado correctamente";
$contacts_import_double = "Ha seleccionado una o más columnas dos veces. Corríjalo y vuelva a probar.";
//changed:
$contacts_import_noname = 'Debe seleccionar los campos \'Nombre\' y \'Apellidos\' ';
$contacts_import_incompatible = "Formato de fichero incompatible.<br /><br />Un fichero CSV es un fichero con valores separados por puntos o comas.";
$strMember = "Miembro";
$contact_exist_warning = "<b>Nota:</b> Este usuario ya existe en su lista de contactos.";
$contacts_add_to_group = "Añadir los usuarios seleccionados a contactos en el grupo...";
$contacts_add_to = "Añadir los usuarios seleccionados a contactos";
$contacts_add_user = "añadir";
$contacts_edit = "Editar";
$contacts_no_select = "No ha seleccionado nada";
$contacts_this_contact = "este contacto";

$contacts_seperator_text = 'Valores separados por';
$contacts_quote = 'Valores encerrados por';
$contacts_clrf = 'Líneas terminadas por';
$contacts_auto_group = 'Crear grupos automáticamente desde el fichero';
$contacts_import_to_group = 'Importar al grupo';

$contacts_search = 'Buscar';
$cmdSearch = 'Buscar';
$contacts_search_text = "Haga click en una letra o introduzca un frase de búsqueda y haga click en 'buscar'. Puede usar '*' como carácter de comodín.";
$contacts_results = 'resultados';
$contacts_result = 'resultado';


$contacts_color = 'Color';

$contacts_colors['black'] = 'Negro';
$contacts_colors['red'] = 'Rojo';
$contacts_colors['green'] = 'Verde';
$contacts_colors['blue'] = 'Azul';
$contacts_colors['pink'] = 'Rosa';
$contacts_colors['orange'] = 'Naranja';
$contacts_colors['brown'] = 'Marrón';

$ab_addressbooks = 'Libreta de direcciones';
$ab_owner = 'Propietario';
$ab_subscribed = 'Suscrito';
$ab_new_ab = 'Nueva libreta de direcciones';
$ab_addressbook_exists = 'Ya existe una libreta de direcciones con este nombre';
$ab_addressbook = 'Libreta de direcciones';
$ab_no_addressbook = 'Sin libreta de direcciones';
$contacts_select_single = 'Haga click en un contacto para seleccionar esta persona';

$ab_group_exists = 'Ya existe uno con ese mismo nombre en la libreta de direcciones';
$ab_group_on_file = 'Agrupar los contactos basados en el fichero';
$ab_default = 'Por defecto';

//translation required
$ab_edit_contact = 'Haga click en \'Aceptar\' para guardar los cambios. Los campos marcados con un \'*\' son obligatorios';
$ab_group_on_file = 'Agrupar los contactos basados en el fichero';
$ab_default = 'Por defecto';
$ab_browse = 'Examinar';
$ab_comment = 'Comentar';

$ab_contact = 'Contacto';
$ab_send_message = 'Nuevo correo electrónico';
$ab_new_event = 'Nueva cita';

$ab_templates = 'Plantillas';
$ab_your_templates = 'Tus';
$ab_all_templates = 'Todos/as';

$template_type[0] = 'Correo electrónico';
$template_type[1] = 'OpenOffice.Org';
$ab_type = 'Tipo';
$ab_template_exists = 'Ya existe una plantilla con este nombre.';

$sir_madam['M'] = 'Señor';
$sir_madam['F'] = 'Señora';

$ab_upload_oo = 'Cargar la plantilla OpenOffice.org';
$ab_upload_nofile = 'Error mientras se enviaba el fichero.';

$ab_oo_doc = 'Documento de texto';
$ab_select_template = 'Seleccionar la plantilla para este documento:';
$ab_add_field_data = 'Puede utilizar los siguientes valores en su documento:';
$ab_datafield = 'Campo de datos';

$ab_no_template = 'Sin plantilla';
$ab_new_note = 'Nota';

$ab_events = 'Citas';

$ab_new_contact = 'Nuevo&nbsp;contacto';
$ab_new_company = 'Nueva&nbsp;empresa';
$ab_companies = 'Empresas';
$ab_company_properties = 'Perfil&nbsp;de&nbsp;la&nbsp;empresa';
$ab_bank_no = 'Número&nbsp;de&nbsp;banco';
$ab_companies_count_single = 'empresa';
$ab_companies_count = 'empresas';
$ab_no_companies = 'No se han encontrado empresas';
$ab_no_company = 'Sin empresas';

$ab_import_type = 'Importar';
$ab_no_company_found = 'No se han encontrado empresas';
$ab_search_company = 'Buscar empresa';
$ab_edit_company = 'Cambiar el perfil de la empresa';
$ab_new_group = 'Nuevo grupo';
$ab_export_type = 'Exportar';

$ab_custom_fields = 'Campo&nbsp;de&nbsp;datos&nbsp;extra';
$ab_field_size = 'Tamaño';

$ab_new_field = 'Nuevo campo de datos';
$ab_invalid_field_size = 'Tamaño incorrecto. Debe escoger un valor entre 10 y 255.';
$ab_field_exists = 'Ya existe un campo con este nombre.';

$ab_employees = 'Empleados';
$ab_no_employees = 'No se han encontrado empleados';

$ab_employee_count_single = 'empleado';
$ab_employee_count_multiple = 'empleados';
$ab_vat_no = 'Número de VAT';

$ab_add_new = 'Añadir nuevo';
$ab_add_existing = 'Añadir existente';

$ab_access = 'Acceso';
//1.98
$ab_mailings = 'Lista de correos';
$ab_mailing_groups = 'Grupo de listas de correos';
$ab_no_mailing_groups = 'No se ha encontrado ningún grupo de listas de correos';
$ab_no_templates = 'No se han encontrado plantillas';
$ab_mailing_group_exists = 'Ya existe un grupo de listas de correos con este nombre.';
$ab_contacts_count_single = 'contacto';
$ab_contacts_count_multiple = 'contactos';
$ab_new_mailing = 'Nueva lista de correos';
$ab_all_your_addressbooks = 'todas sus libretas de direcciones';

$ab_search_for = 'Buscar';
$ab_search_on = 'en';
$ab_search_keyword = 'Buscar frase';
$ab_wildcard = '(Use \'%\' como un comodín)';

//2.03
$ab_copy_read_acl = 'Copiar los permisos de lectura a contactos y empresas';
$ab_copy_write_acl = 'Copiar los permisos de escritura a contactos y empresas';

$ab_catagory_exists = 'ya existe una categoría con este nombre.';
$ab_add_catagory = 'Añadir una categoría';
$ab_add_field = 'Añadir un campo';
$ab_catagory = 'Categoría';

//2.04
$ab_todos = 'Por-hacer';


//2.05
$ab_download = 'Descargar';
$ab_download_template = 'Haga click en el botón \'Descargar\' para descargar y editar la plantilla en su ordenador.';
$ab_upload = 'Cargar';
//modified string:
$ab_upload_oo_text = 'Cuando haya terminado de editar su plantilla con OpenOffice.org, deberá transferirla a Group-Office. Seleccione el fichero y haga click en \'Cargar\' para transferir su plantilla.';
$ab_file_type = 'Tipo de fichero';
$ab_import_vcf_file_ok = 'El fichero cargado parecer ser correcto. Por favor, haga click en \'Aceptar\' para asociarlo a la libreta de direcciones.';
$ab_import_failed = 'Ha fallado la importación.';
//modified string:
$contacts_import_text = "Seleccione el fichero que contiene los datos para importar y haga click en 'Aceptar'";

//2.06
$ab_select_mailing_groups = 'Seleccione el grupo de listas de correos de donde pertenece este contacto';

//2.11
$ab_move_to_ab = 'Mover los contactos seleccionados a la libreta de direcciones...';
$ab_move_com_to_ab = 'Mover las empresas seleccionadas a la libreta de direcciones...';

//2.12
$ab_search_sender = 'Buscar correo electrónico';

$ab_contact_exists = 'Este contacto ya parece que exista. Haga click %s para ir al contacto existente o haga click en \'Aceptar\' o \'Aplicar\' otra vez para guardar igualmente este contacto.';
$ab_company_exists = 'Esta empresa ya parece que exista. Haga click %s para ir a la empresa existente o haga click en \'Aceptar\' o \'Aplicar\' otra vez para guardar igualmente esta empresa.';
$ab_here = 'aquí';

//2.13
$ab_search_for_keyword = 'por';

$ab_search_in = 'Buscar dentro'; //changed
$ab_search_on_users = 'Buscar en';

$ab_users_count_single='usuario';
$ab_users_count_multiple='usuarios';
$ab_no_users='No se han encontrado usuarios';
?>

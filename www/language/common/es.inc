<?php
require_once($GO_LANGUAGE->get_fallback_base_language_file('common'));
$htmldirection='ltr';
$charset = 'UTF-8';
//main language menu file register login and other non logged in pages
//common
$cmdLogin = "Entrada";
$cmdSave = "Guardar";
$cmdBack = "Atrás";
$cmdCancel = "Cancelar";
$cmdClose = "Cerrar";
$cmdOk = "Aceptar";
$cmdDelete = "Borrar";
$cmdNext = "Siguiente";
$cmdPrevious = "Anterior";
$cmdContinue = "Continuar";
$cmdUpdate = "Sincronizar";
$cmdApply = "Aplicar";
$cmdYes = "Si";
$cmdNo = "No";
$cmdAdd = 'Añadir';
$cmdEdit = 'Editar';
$cmdInvertSelection = 'Invertir selección';

$strEdit = "Editar";
$strThis = 'esto';
$strItems = 'elementos';
$strDescription = 'Descripción';

$strName = "Nombre";
$strUser = "Usuario";
$strUsername = "Usuario";
$strPassword = "Contraseña";
$strAuthcode = "Código de autorización";
$strCountry = "País";
$strState = "Estado";
$strCity = "Ciudad";
$strZip = "ZIP/Código postal";
$strAddress = "Dirección";
$strEmail = "E-mail";
$strPhone = "Teléfono";
$strWorkphone = "Teléfono del trabajo";
$strCellular = "Móvil";
$strCompany = "Empresa";
$strDepartment = "Departamento";
$strFunction = "Cargo";
$strQuestion = "Pregunta secreta";
$strAnswer = "Respuesta";
$strFax = "Fax";
$strWorkFax = "Fax del trabajo";
$strHomepage = "Página web";
$strWorkAddress = "Dirección del trabajo";
$strWorkZip = "Código postal del trabajo";
$strWorkCountry = "País del trabajo";
$strWorkState = "Estado del trabajo";
$strWorkCity = "Ciudad del trabajo";
$strPhoto = "Fotografía";
$strComments = "Comentarios";
$strProperties = 'Propiedades';
$strDeleteItem = "Borrar";
$strDate = "Fecha";
$strSaveSuccess = "Datos guardados correctamente";
$strSaveError = "Ha ocurrido un error mientras se guardaban los datos.";
$strDataError = "Ha ocurrido un error mientras se leían los datos.";
$strDeleteError = "No se han podido borrar los datos.";
$strAclError = "Error en el control de acceso";
$strDateError = "Ha introducido una fecha incorrecta";
$strAccessDenied = "Acceso denegado";
$AccessDenied_text = "No tiene permiso para ver esta sección o realizar esta acción<br />Otro usuario ha restringido el acceso a la información que ha pedido.";
$invalid_chars = "Los siguientes caracters no estan permitidos";
$strGroups = "Grupos";
$strOwner = "Propietario";
$strDeletePrefix = "Está usted seguro de querer borrar ";
$strDeleteSuffix = "?";
//menu
$menu_logout = "Desconectar";
$menu_applications = "Aplicaciones";
$menu_help = "Ayuda";
$menu_about = "Acerca de";
$menu_documentation = "Documentación";
$menu_configuration = "Configuración";
$menu_groups = "Gestionar grupos de usuarios";
$menu_accounts = "Modificar las propiedades de su cuenta";
$menu_preferences = "Ajustar las preferencias personales";
$menu_search_discussions = "Discusiones";
$menu_search_files = "Ficheros&nbsp;y&nbsp;carpetas";
$menu_search_web = "La&nbsp;web";
$menu_filetypes = "Tipos de ficheros";
$menu_discussions = "Panel de discusiones";

$menu_users = "Gestiona usuarios";
$menu_new = "Nuevo";
$menu_message = "Mensaje";
$menu_contact = "Contacto";
$menu_event = "Cita";
$menu_bookmark = "Favoritos";
$menu_topics = "Tema";
$menu_support = "Soporte";
$menu_projects = "Proyectos";
$menu_sysinfo = "Ver información del sistema";
$menu_admin = 'Menu del Administrador';
$menu_modules = 'Añadir/Quitar módulos';
$menu_manual = 'Manual';

//class validate
$error_required = "No ha especificado el campo obligatorio de abajo.";
$error_match_auth = "Los códigos de autorización no coinciden.";
$error_match_pass = "Las contraseñas no coinciden.";
$error_min_length = "Caracteres mínimos del campo de abajo:";
$error_max_length = "Caracteres máximos del campo de abajo:";
$error_email = "La dirección de correo electrónico especificada es incorrecta.";
$error_email_exists = "La dirección de correo electrónico que ha introducido ya existe.";
$error_username_exists = "Lo siento, el nombre de usuario introducido ya existe. Por favor, escoja otro.";
$errors_in_form = "Se han detectado errores en el formulario. Por favor, corríjalos y vuelva a probar.";
$error_missing_field = "No ha rellenado todos los campos obligatorios.";

//acl control
$acl_control_auth_groups = "Grupos autorizados";
$acl_control_auth_users = "Usuarios autorizados";
$acl_control_no_contacts = "No tiene ningún contacto por seleccionar.<br /> Use la Libreta de Direcciones para añadir nuevos contactos.";

$strShowProfile = "Ver el perfil";
$strShowGroup = "Ver los miembros del grupo";
$strProcessingTime = "El procesamiento de esta página a tardado ";
$strLoggedInAs = "Entrado como ";
$strAt = " a ";
$strEmailTo = "Enviar un correo electrónico a: ";
$strProtectedUser = "Usuario protegido";

$http_not_found = "Documento no encontrado";
$http_not_found_text = "El documento no ha sido encontrado. Compruebe la dirección URL.";
$http_error = "Error interno del servidor";
$http_bad_request = "Petición inválida";
$strAddContact = "Añadir a contactos";

$strReadRights = "Leer permisos";
$strWriteRights = "Escribir permisos";
$strPermissions = 'permisos';

$days[0]="Do";
$days[1]="Lu";
$days[2]="Ma";
$days[3]="Mi";
$days[4]="Ju";
$days[5]="Vi";
$days[6]="Sa";

$months[0]="Enero";
$months[1]="Febrero";
$months[2]="Marzo";
$months[3]="Abril";
$months[4]="Mayo";
$months[5]="Junio";
$months[6]="Julio";
$months[7]="Agosto";
$months[8]="Septiembre";
$months[9]="Octubre";
$months[10]="Noviembre";
$months[11]="Diciembre";

$full_days[0] = "Domingo";
$full_days[1] = "Lunes";
$full_days[2] = "Martes";
$full_days[3] = "Miércoles";
$full_days[4] = "Jueves";
$full_days[5]= "Viernes";
$full_days[6] = "Sábado";
$strLoadTime = 'Generado en';
$strDescription = 'Descripción';



$lang_modules['addressbook'] = "Libreta de direcciones";
$cmdShowAll = 'Ver todo';
$cmdSearch = 'Buscar';

//translation required
$cmdYesToAll = 'Si a todo';
$cmdNoToAll = 'No a todo';

$short_months[0]="Ene";
$short_months[1]="Feb";
$short_months[2]="Mar";
$short_months[3]="Abr";
$short_months[4]="May";
$short_months[5]="Jun";
$short_months[6]="Jul";
$short_months[7]="Ago";
$short_months[8]="Sep";
$short_months[9]="Oct";
$short_months[10]="Nov";
$short_months[11]="Dic";

$strToday = 'Hoy';

$cmdPrint = 'Imprimir';
$strFirstName = 'Nombre';
$strLastName = 'Apellidos';
$strInitials = 'Iniciales';
$strSex = 'Sexo';
$strBirthday = 'Fecha Nacimiento';
$strSexes['M'] = 'Hombre';
$strSexes['F'] = 'Mujer';

$strTitle = 'Título';
$strBeginning = 'Inicio';
$strMiddleName = 'Segundo&nbsp;nombre';
$sir_madam['M'] = 'Sr.';
$sir_madam['F'] = 'Sra.';
$strModifiedAt = 'Modificado el';
$strCreatedAt = 'Creado el';

$htmlarea_language_name = 'es';
$strLocalization = 'Localización';


//look up the language in controls/jscalendar/lang
$jscalendar_language_name = 'calendar-es.js';


//2.01 translations

//look up language in 'classes/phpmailer/language'
$php_mailer_lang = 'es';
$strEmailFail = 'No se ha podido enviar el correo electrónico.';

//for people that want to use ktmlarea
$ktml_language = 'spanish';

$ml_host_unreachable = ' Inalcanzable. La comprobación automática de correo electrónico está desactivada.';
$strRegion = 'Región';

//2.05
$cmdReset = 'Reinicia';
$strMyName = 'Mi nombre';

//2.06
$strDeleteRow = '¿Está seguro de querer eliminar esta fila?';
$strAddressNo = 'Número';
$strWorkAddressNo = 'Número del trabajo';

$strPostAddress = 'Dirección postal';
$strPostAddressNo = 'Número postal';
$strPostCity = 'Ciudad postal';
$strPostState = 'Estado postal';
$strPostCountry = 'País postal';
$strPostZip = 'Código postal';
$strVisitAddress = 'Dirección para visitas';
$strPostAddress = 'Dirección postal';



//modules
$lang_modules['scheduler'] = 'Planificador';
$lang_modules['calendar'] = "Calendario";
$lang_modules['filesystem'] = 'Ficheros';
$lang_modules['email'] = 'Correo electrónico';
$lang_modules['projects'] = 'Proyectos';
$lang_modules['search'] = 'Buscar';
$lang_modules['contacts'] = "Libreta de direcciones";
$lang_modules['cms'] = "Sitios Web";
$lang_modules['notes'] = "Notas";
$lang_modules['bookmarks'] = "Favoritos";
$lang_modules['summary'] = "Sumario";
$lang_modules['todos'] = "Tareas";
$lang_modules['opentts'] = "Helpdesk";
$lang_modules['products'] = "Software shop";

$cmdVcard = 'vCard';

//2.07
$strNotEnoughLicenses = 'No dispone de más licencias para utilizar esta sección de'.
												' Group-Office. Contacte sales@intermesh.nl si quiere '.
												'comprar más licencias.';
												
//2.08
$alphabet = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';
									
$strContact = 'Contacto';
$strRelatedTo = 'Relativo a';
$strProject = 'Proyecto';

$strNewProject = 'Nuevo proyecto';
$strNewEvent = 'Nueva cita';
$strNewTodo = 'Nueva tarea';
$strNewNote = 'Nueva nota';

$strActivities = 'Actividades';
$strFile = 'Fichero';

//2.10
$cmdCopy = 'Copiar';
$strProjects = 'Proyectos';
$strFiles = 'Ficheros';
$cmdSaveNew = 'Guardar y Hacerlo nuevo';
$strAvailability = 'Disponibilidad';
$strCheckAvailabilityFor = 'Comprobar disponibilidad para';
$cmdPleaseSelect = 'Por favor seleccione...';

//2.11
$strNewEmail = 'Nuevo correo electrónico';
$strConsistencyCheck = "Comprobar consistencia";
$strSearchAll = 'Todos los campos';
$sync_download_clients = 'Descargar clientes de sincronización';

//2.12
$strNoData = 'No se han encontrado datos';
//2.13
$strSynchronization = 'Sincronización';
$strSnooze = 'Recordar más tarde';
$strDismiss = 'Descartar';

//2.14
$strUpload = 'Enviar fichero';
$cmdInsert = 'Insertar';
$strText = 'Texto';
$strType = 'Tipo';

$strNoItemSelected = 'No ha seleccionado ningún elemento';
$strSelectedItem = 'el elemento seleccionado';

$strKeyword = 'Palabra clave';

$strNoActivities = 'No hay actividades para este elemento';

$strCopy = 'Copia'; //not a verb

//2.15
$strBank = 'Banco';
$strBankNo = 'Número de banco';
?>

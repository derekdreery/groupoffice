<?php

// LANG: "fr", ENCODING: UTF-8
// Author: A. Kermorvant : armek@akserver.dyndns.org
// 24 Oct 2004

// FOR TRANSLATORS:
//
//   1. PLEASE PUT YOUR CONTACT INFO IN THE ABOVE LINE
//      (at least a valid email address)
//
//   2. PLEASE TRY TO USE UTF-8 FOR ENCODING;
//      (if this is not possible, please include a comment
//       that states what encoding is necessary.)

require_once($GO_LANGUAGE->get_fallback_base_language_file('common'));

$charset = 'UTF-8';


//main language menu file register login and other non logged in pages
//common
$cmdLogin = "Connexion";
$cmdSave = "Enregistrer";
$cmdBack = "Retour";
$cmdCancel = "Annuler";
$cmdClose = "Fermer";
$cmdOk = "Ok";
$cmdDelete = "Supprimer";
$cmdNext = "Suivant";
$cmdPrevious = "Précédent";
$cmdContinue = "Continuer";
$cmdUpdate = "Synchroniser";
$cmdApply = "Appliquer";
$cmdYes = "Oui";
$cmdNo = "Non";
$cmdAdd = 'Ajouter';
$cmdEdit = 'Editer';
$cmdInvertSelection = 'Inverser la sélection';

$strEdit = "Editer";
$strThis = 'cet';
$strItems = 'éléments';
$strDescription = 'Description';

$strName = "Nom";
$strUser = "Utilisateur";
$strUsername = "Nom d'utilisateur";
$strPassword = "Mot de passe";
$strAuthcode = "Code d'autorisation";
$strCountry = "Pays";
$strState = "Etat";
$strCity = "Ville";
$strZip = "Code Postal";
$strAddress = "Rue";
$strEmail = "E-mail";
$strPhone = "Téléphone";
$strWorkphone = "Téléphone profesionnel";
$strCellular = "Cellulaire";
$strCompany = "Société";
$strDepartment = "Département";
$strFunction = "Fonction";
$strQuestion = "Question secrète";
$strAnswer = "Réponse";
$strFax = "Fax";
$strWorkFax = "Fax profesionnel";
$strHomepage = "Site web";
$strWorkAddress = "Adresse profesionnelle";
$strWorkZip = "Code postal professionnel";
$strWorkCountry = "Pays de travail";
$strWorkState = "Etat de travail";
$strWorkCity = "Ville de travail";
$strPhoto = "Photo";
$strComments = "Commentaires";
$strProperties = 'propriété';
$strDeleteItem = "Supprimer";
$strDate = "Date";
$strSaveSuccess = "Les données ont été enregistrées avec succès";
$strSaveError = "Une erreur est apparue pendant la sauvegarde des données. Réessayez.";
$strDataError = "Une erreur est apparue pendant la lecture des données. Rechargez cette page.";
$strDeleteError = "Impossible d'effacer les données.";
$strAclError = "Erreur de contrôle d'accès";
$strDateError = "Vous avez entrez une date invalide";
$strAccessDenied = "Accès refusé";
$AccessDenied_text = "Vous n'êtes pas autorisé à voir cette section du site ni à effectuer cette action. Un autre utlisateur a restreint l'accès à l'information que vous avez demandé.";
$invalid_chars = "Les caractères suivants ne sont pas autorisés.";
$strGroups = "Groupes";
$strOwner = "Propriétaire";
$strDeletePrefix = "Etes vous certain(e) de vouloir supprimer";
$strDeleteSuffix = "?";

$strRelatedTo = 'En rapport à';

//menu
$menu_logout = "Déconnecter";
$menu_applications = "Applications";
$menu_help = "Aide";
$menu_about = "A propos de ";
$menu_documentation = "Documentation";
$menu_configuration = "Configuration";
$menu_groups = "Gestion Utilisateurs Groupes";
$menu_accounts = "Modifier les paramètres de votre compte";
$menu_preferences = "Modifier vos préférences Personnel";
$menu_search_discussions = "Discussions";
$menu_search_files = "Fichiers et Dossiers";
$menu_search_web = "Le web";
$menu_filetypes = "Types de fichiers";
$menu_discussions = "Forum de discussion";
$menu_bookmarks = "Favoris";
$menu_bookmarks_manage = "Modifier  les favoris";
$menu_add_bookmark = "Ajouter un favoris";
$menu_add_current = "Ajouter la page courante";
$menu_invite = "Inviter des utilisateurs";
$menu_scheduler = "Agenda";
$menu_users = "Utilisateurs";
$menu_start = "Page de démarrage";
$menu_reboot = "Redémarrer le serveur";
$menu_halt = "Arrêter le serveur";
$menu_reboot_text = "ATTENTION! Tous les services de Group-Office seront temporairement indisponibles. Etes vous certain(e) de vouloir continuer ?";
$menu_halt_text = "ATTENTION ! Tous les services de Group-Office seront arrétés. Etes vous certain(e) de vouloir continuer ?";
$menu_new = "Nouveau";
$menu_message = "Message";
$menu_contact = "Contact";
$menu_event = "Evènement";
$menu_bookmark = "Favoris";
$menu_topics = "Matières";
$menu_support = "Support";
$menu_projects = "Projets";
$menu_sysinfo = "Informations système";
$menu_admin = 'Administrateur';
$menu_modules = 'Modules';
$menu_manual = 'Manuel';

//class validate
$error_required = "Vous n'avez pas spécifié le champs recquis ci-dessous.";
$error_match_auth = "Le code d'autorisation ne correspond pas.";
$error_match_pass = "Le mot de passe de correspond pas.";
$error_min_length = "Nombre minimun de caractères du champs ci-dessous :";
$error_max_length = "Nombre maximum de caractères du champs ci-dessous :";
$error_email = "L'adresse mail que vous avez spécifié n'est pas valide.";
$error_email_exists = "L'adresse email que vous avez entré existe déjà.";
$error_username_exists = "Désolé le nom d'utilisateur que vous avez entré existe déjà. Merci d'en choisir un autre.";
$errors_in_form = "Il y a des erreurs dans le formulaire. Corrigez les et réessayez.";
$error_missing_field = "Vous n'avez pas rempli tous les champs obligatoires.";

//acl control
$acl_control_auth_groups = "Groupes autorisés";
$acl_control_auth_users = "Utilisateurs autorisés";
$acl_control_no_contacts = "Vous n'avez aucun contact à sélectionner. Utilisez le carnet d'adresses pour ajouter des membres à vos contacts.";

$strShowProfile = "Voir le profil";
$strShowGroup = "Voir les membres du groupe";
$strProcessingTime = "Le traitement de cette page à pris ";
$strLoggedInAs = "Connecté en tant que ";
$strAt = " à";
$strEmailTo = "Envoyer un E-mail à: ";
$strProtectedUser = "Utilisateur protégé";

$http_not_found = "Document introuvable";
$http_not_found_text = "Le document demandé est introuvable. Vous avez peut être mal tapé l'adresse.";
$http_error = "Erreur interne au serveur";
$http_bad_request = "Mauvaise requête";
$strAddContact = "Ajouter aux contacts";

$strReadRights = "Permissions de lecture";
$strWriteRights = "Permissions d'écriture";
$strPermissions = 'permissions';

$editor_bold = "Gras";
$editor_italic = "Italique";
$editor_underline = "Souligné";
$editor_left = "Gauche";
$editor_right = "Droite";
$editor_center = "Centrer";
$editor_ord_list = "Liste trié";
$editor_bul_list = "Liste à puces";
$editor_txt_color = "Couleur du texte";
$editor_back_color = "Couleur de fond";
$editor_hyperlink = "Lien hypertexte";
$editor_image = "Image";
$editor_rule = "Règle horizontale";

$editor_very_small = "Très petit";
$editor_small = "Petit";
$editor_medium = "Moyen";
$editor_large = "Large";
$editor_larger = "Plus large";
$editor_very_large = "Très large";

$editor_strikethrough = "Barré";
$editor_outdent = "Retrait à gauche";
$editor_indent = "Retrait à droite";

$editor_superscript = "Exposant";
$editor_subscript = "Indice";
$editor_undo = "Annuler";
$editor_redo = "Refaire";
$editor_copy = "Copier";
$editor_paste = "Coller";
$editor_cut = "Couper";

$editor_font = "Police";
$editor_size = "Taille";
$editor_heading = "Entrée";
$editor_select = "Choix";

$editor_mode = "Changer le mode éditeur";

$days[0]="Dim";
$days[1]="Lun";
$days[2]="Mar";
$days[3]="Mer";
$days[4]="Jeu";
$days[5]="Ven";
$days[6]="Sam";

$months[0]="Janvier";
$months[1]="Février";
$months[2]="Mars";
$months[3]="Avril";
$months[4]="Mai";
$months[5]="Juin";
$months[6]="Juillet";
$months[7]="Aout";
$months[8]="Septembre";
$months[9]="Octobre";
$months[10]="Novembre";
$months[11]="Décembre";

$full_days[0] = "Dimanche";
$full_days[1] = "Lundi";
$full_days[2] = "Mardi";
$full_days[3] = "Mercredi";
$full_days[4] = "Jeudi";
$full_days[5]= "Vendredi";
$full_days[6] = "Samedi";
$strLoadTime = 'généré en ';
$strDescription = 'Description';
$strTitle = 'Titre';


//translation required
$editorCmd['undo'] = "Annuler";
$editorCmd['redo'] = "Refaire";
$editorCmd['bold'] = "Gras";
$editorCmd['italic'] = "Italique";
$editorCmd['underline'] = "Souligné";
$editorCmd['left'] = "Justifié à gauche";
$editorCmd['right'] = "Justifié à droite";
$editorCmd['full'] = "Remplir";
$editorCmd['center'] = "Centrer";
$editorCmd['ord_list'] = "Liste numérotée";
$editorCmd['bul_list'] = "Liste à puces";
$editorCmd['txt_color'] = "Couleur du texte";
$editorCmd['back_color'] = "Couleur de fond";
$editorCmd['hyperlink'] = "Hyper lien";
$editorCmd['image'] = "Insérer une image";
$editorCmd['rule'] = "Règle horizontale";
$editorCmd['table'] = "Insérer une table";

$editorCmd['strikethrough'] = "Barré";
$editorCmd['outdent'] = "Augmenter le retrait";
$editorCmd['indent'] = "Diminuer le retrait";

$editorCmd['superscript'] = "Exposant";
$editorCmd['subscript'] = "Indice";
$editorCmd['undo'] = "Annuler";
$editorCmd['redo'] = "Refaire";
$editorCmd['copy'] = "Copier";
$editorCmd['paste'] = "Coller";
$editorCmd['cut'] = "Couper";

$editorCmd['font'] = "Police";
$editorCmd['size'] = "Taille";
$editorCmd['heading'] = "Entête";
$editorCmd['select'] = "Sélectionner";
$editorCmd['mode'] = "Changer de mode";
$editorCmd['textindicator'] = 'Style courant';


$lang_modules['addressbook'] = "Carnet d'adresses";
$cmdShowAll = 'Tout voir';
$cmdSearch = 'Chercher';

//translation required
$cmdYesToAll = 'Oui pour tout';
$cmdNoToAll = 'non à tout';

$short_months[0]="Jan";
$short_months[1]="Fev";
$short_months[2]="Mar";
$short_months[3]="Avr";
$short_months[4]="Mai";
$short_months[5]="Juin";
$short_months[6]="Juil";
$short_months[7]="Aout";
$short_months[8]="Sept";
$short_months[9]="Oct";
$short_months[10]="Nov";
$short_months[11]="Déc";

$strToday = 'Aujourd\'hui';

$strShortWeek = 'semaine';

$cal_prev_year = "Année précédente (maintenez pour le menu)";
$cal_prev_month  = "Mois précédent (maintenez pour le menu)";
$cal_go_today = "Aller à aujourd'hui";
$cal_next_month = "Mois suivant (maintenez pour le menu)";
$cal_next_year  = "Année suivante (maintenez pour le menu)";
$cal_drag_to_move = "Glissez pour déplacer";
$cal_part_today = " (aujourd'hui)";
$cal_monday_first = "Voir d'abord les lundi";
$cal_sunday_first  = "Voir d'abord les dimanche";
$cal_select_date = "Choisir une date";

$cmdPrint = 'Imprimer';
$strFirstName = 'Prénom';
$strLastName = 'Nom';
$strInitials = 'Initiales';
$strSex = 'Sexe';
$strBirthday = 'Anniversaire';
$strSexes['M'] = 'Homme';
$strSexes['F'] = 'Femme';

$strTitle = 'Titre';

$strBeginning = 'Début';

$strMiddleName = 'Complément du nom';

$sir_madam['M'] = 'Monsieur';
$sir_madam['F'] = 'Madame';

$strModifiedAt = 'Modifié le';
$strCreatedAt = 'Créé le';

$htmlarea_language_name = 'fr';

$strLocalization = 'Localisation';

$lang_modules['notes'] = "Notes";
//look up the language in controls/jscalendar/lang
$jscalendar_language_name = 'calendar-fr.js';


//2.01 translations

//look up language in 'classes/phpmailer/language'
$php_mailer_lang = 'fr';
$strEmailFail = 'L\'envois de l\'email a échoué';

//for people that want to use ktmlarea
$ktml_language = 'fr';

$ml_host_unreachable = ' n\'est pas acessible. La vérification automatique de d\'Email est désactivé.';
$strRegion = 'Région';

//2.05
$cmdReset = 'Remise à zéro';
$strMyName = 'Mon Nom';

//2.06
$strDeleteRow = 'Êtes-vous sûr vous voulez-vous supprimer cette rangée ? ';
$strAddressNo = 'Numéro de la rue (Privé)';
$strWorkAddressNo = 'Numéro de la rue (Professionel)';

$strPostAddress = 'Addresse';
$strPostAddressNo = 'Adresse personnel';
$strPostCity = 'Ville';
$strPostState = 'Etat';
$strPostCountry = 'Pays';
$strPostZip = 'Code postal';
$strVisitAddress = 'adresse de visite';
$strPostAddress = 'Adresse';


$login_register = 'Cliquer ici pour vous enregistrer.';

//modules
$lang_modules['scheduler'] = 'Agenda';
$lang_modules['calendar'] = "Calendrier";
$lang_modules['filesystem'] = 'Fichiers';
$lang_modules['email'] = 'E-mail';
$lang_modules['projects'] = 'Projets';
$lang_modules['search'] = 'Chercher';
$lang_modules['contacts'] = "Carnet d'adresse";
$lang_modules['notes'] = "Notes";
$lang_modules['bookmarks'] = "Favoris";
$lang_modules['summary'] = "Sommaire";
$lang_modules['todos'] = "Tâches";
$lang_modules['cms'] = "Site Web";
$lang_modules['opentts'] = "Support Technique";

$cmdVcard = 'vCard';

//2.07
$strNotEnoughLicenses = 'Vous n\'avez pas assez de license pour utiliser'.
												' Group-Office. Merci de contacter sales@intermesh.nl si vous souhaitez '.
												'acheter des licenses additionels.';
$strActivities = 'Activités';
$strFile = 'Fichier';

$strNewProject = 'Nouveau projet';
$strNewEvent = 'Nouvel évènement';
$strNewTodo = 'Nouvelle tâche';
$strNewNote = 'Nouvelle note';


//2.10
$cmdCopy = 'Copier';
$strProjects = 'Projets';
$strFiles = 'Fichiers';
$cmdSaveNew = 'Sauver & Nouveau';
$strAvailability = 'Disponibilité';
$strCheckAvailabilityFor = 'Vérifier les disponibilités pour';

//2.15
$strBank = 'Banque';
$strBankNo = 'Numéro bancaire';
$strSelectedItems = 'éléments sélectioné';

$strMonth = 'Mois';
$strYear = 'Année';
$strDay = 'Jour';
$strHour = 'Heure';
$strMinute = 'Minute';

$strTwelveHourFormat = 'format 12 heures';
$strTwentyfourHourFormat = 'format 24 heures';

//2.16
$strCreateLink = 'Créé le lien';
$strCancelLink = 'Supprime le lien';
$strLinkText = 'Lien actif. Parcourer un élement liable et cliquer sur le bouton \'Créé un lien\' pour le lier à \'%s\'. Si vous créez un nouvel élément liable alors il sera lié automatiquement.';
$strLinks = 'Liens';
$strUnlinkPrefix = 'Etes vous certain de vouloir le délier';
$strUnlinkSuffix = '?';
$cmdUnlink = 'Délier';



$strAdministrate = 'Administrer';
$strProOnly = 'Cette fonctionnalité est seulement disponible dans Group-Office Professionel. <a href="http://www.group-office.com">Cliquer ici pour en apprendre plus à propos de Group-Office Professional</a>';

$strMoreColors='Plus de couleurs';

$strNoItems = 'Aucun élément trouvé';
$cmdExport = 'Exporter';



?>

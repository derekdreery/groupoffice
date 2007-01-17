<?php

// LANG: "fr", ENCODING: UTF-8
// Author: L. Jullien
// 15 Jan 2007

require_once($GO_LANGUAGE->get_fallback_language_file('cms'));

$strType = "Type";
$strModified = "Modifié";
$strSize = "Taille";
$fbRefresh = "Rafraîchir";
$fbProperties = "Propriétés";
$fbNewFolder = "Nouveau dossier";
$fbDelete = "Supprimer";
$fbCut = "Couper";
$fbPaste = "Coller";
$fbCopy = "Copier";
$fbFiletypes = "Type de fichiers";
$fbPath = "Chemin";
$fbFolders = "Dossiers";
$fbNoSelect = "Vous n'avez pas choisi d'élément. Cliquez sur un icône près du nom pour en choisir un.";
$fbConfirmDelete = "Confirmer la suppression";
$fbGeneral = "Général";
$fbConfirmOverwrite = "Confirmer l'écrasement";
$strOverwritePrefix = "Etes vous certain(e) de vouloir écraser ";
$strOverwriteSuffix = "?";
$fbDescription = "Description";
$fbLocation = "Emplacement";
$fbUpload = "Télécharger";
$fbUpload_text = "Choisissez le fichier à envoyer sur le serveur";
$fbUpload_nofile = "aucun fichier reçu.";
$fbFolderExists = "Le dossier existe déjà.";
$fbFolder = "Dossier";
$fbSelect = "Choisissez le fichier...";
$fbNoFile = "Le fichier que vous avez essayé de charger est trop gros. La taille maximale transferable est fixée à : ";
$fbPleaseWait = "Merci de patienter. Envoi des données...";
$fbFilename = "Nom de fichier";
$fbUpLevel = "Niveau supérieur";
$fbItems = "élément(s)";
$fbFolderSize = "Taille du dossier";
$fbMaxUploadSize = "Taille maximale";
$ml_file_too_big = "Le fichier que vous avez essayé d'attacher est trop gros, la taille maximale pour les attachements est fixée à : ";
$cmdSearch = "Recherche";
$fbNameExists = "Le nom existe déjà.";
$fbThis = 'ce fichier';
$fbFiles = 'Fichiers';
$fs_no_multi_select = 'Vous ne pouvez pas sélectionner plus d\'un élément avec cette option';
$cms_untitled = 'Sans titre';
$cms_insert_file = 'Insérer un fichier';
$cms_preview = 'Prévisualiser';
$cms_keywords = 'mots clés';
$cms_priority = 'Priorité';
$cms_hide_folder = 'Cacher ce dossier sur le site';
$cms_theme = 'Thème';
$cms_use_go_auth = 'Utiliser l\'autentification de Group-Office sur ce site';
$cms_search_files_prefix = 'Les fichiers HTML qui contiennent ce mot';
$cms_search_files_suffix = 'sont automatiquement sélectionnés. Cliquez sur \'Ok\' pour enregistrer cette sélection.';
$cms_new_site = 'Nouveau site';
$cms_search_files = 'rechercher des fichiers';
$cms_subscribe = 's\'enregister';
$cms_sites = 'site(s)';
$cms_style = 'Style';
$cms_insert_file_url = 'Insérer une URL';
$cms_templates = 'modèles';
$cms_files = 'fichiers';
$cms_items = 'élément(s)';
$cms_header = 'Entête';
$cms_footer = 'Pied';
$cms_new_theme = 'nouveau thème';
$cms_themes = 'thème(s)';
$cms_themes_menu = 'thèmes';
$cms_all_sites = 'tous les sites';
$cms_your_sites = 'vos sites';
$cms_cmd_new_site = 'nouveau site';
$cms_template_items = 'modèle(s)';
$cms_new_template_item = 'nouveau modèle';
$cms_found_one = '1 fichier trouvé';
$cms_found = 'fichiers trouvés';
$cms_empty = 'Cette section est vide et probablement en cours de maintenance.';
$cms_new_page = 'nouvelle page';
$cms_search_words = 'mots recherchés';
$cms_no_themes = 'Vous n\'avez pas encore de thème. Commencez par en créer un.';
$cms_edit_search_words = 'éditer les mots recherchés';
$cms_id = 'Identifiant du site';
$cms_cleanup = 'Nettoyage';
$cms_settings = 'Réglages';
$cms_site_exists = 'Un site avec ce nom existe déjà';
$cms_main_template = 'Principal';
$cms_confirm_close = 'Les changements ne seront pas enregistrés, êtes vous certain(e) ?';
$cms_additional_style = 'Style qui est caché de cet éditeur';
$cms_restrict_editor = 'Restreindre l\'éditeur aux styles prédéfinis';
$cms_enable_publish = 'Permettre la publication du site en local';
$cms_path_not_writable = 'Le chemin que vous avez entré est invalide ou n\'est pas accessible en écriture pour Group-Office.';
$cms_publish = 'Publication';
$cms_path_already_used = 'Le chemin entré est déjà utilisé par un autre site.';
$cms_site_offline = 'Ce site est actuellement déconnecté';
$cms_disable_publish = 'Déconnecter le site';
$cms_publish_dynamic = 'Publication dynamique';
$cms_publish_static = 'Publication statique';

//rajouté
$fbEmailFile = "envoyer le fichier par email";

//2.05
$cms_sendmail_error = 'Votre message n\'a pu être envoyé. Veuillez réessayer.';
$cms_sendmail_success = 'Merci. Votre message a été envoyé.';
$cms_message = 'Message';
$cms_subject = 'Sujet';
$cms_domain = 'Domaine';
$cms_replace_filenames = 'Remplacer le nom de fichier';

//2.06
$cms_multipage_display = 'Indexer toutes les pages du dossier et la première page de chaque sous-dossier';
$cms_hot_item = 'Afficher cette page dans les éléments importants';
$cms_normal_display = 'Afficher la première page de ce dossier';
$cms_display_type = 'Lorsqu\'un visiteur ouvre un dossier sur cette page:';
$cms_read_more = 'Lire plus';
$cms_additional_style = 'Style de l\'éditeur';

//2.11
$cms_activate_frontpage = 'Activer la page de garde pour ce thème';
$cms_main_template = 'Modèle principale'; //Changed string
$cms_frontpage_template = 'Modèle de la page de garde';
$cms_websites = 'Sites Web'; 
$cms_insert_component = 'Insérer un composant';
$cms_languages = 'Langages';
$cms_new_language = 'Nouveau langage';
$cms_default = 'Défaut';
$cms_title = 'Titre';
$cms_language = 'Langage';
$cms_image = 'Image';
$cms_no_image_selected = 'Aucune image sélectonnée';
$cms_selected_image = 'Image sélectionnée';
$cms_no_image = 'Pas d\'image';
$cms_multipage = 'Résumer les éléments de ce dossier';
$cms_no_themes = 'Vous n\'avez pas encore de thème. Créez en un d\'abord. Vous devez avoir des droits d\'administration pour créer un thème.'; //changed string
$cms_select_local = 'Sélectionner un fichier local';
$cms_webmaster = 'Email&nbsp;du&nbsp;Webmaster';
$cms_allow_properties = 'Autoriser les autres à modifier les propriétés du site.';
$cms_multilingual = 'Utiliser plusieurs langages avec ce site';

//2.12
$cms_search_words_overview = 'Apperçu de recherche';
$cms_no_occurences = 'Le mot cherché n\'a pas été trouvé dans cette page.';
$cms_there_are_results_for_multiple = 'Il y a %s résultats pour \'%s\'';
$cms_there_are_results_for_single = 'Il y a 1 résultat pour \'%s\'';
$cms_print_style = 'Style d\'impression';
$cms_search = 'Recherche';
$cms_login = 'Login';

//2.14
$cms_searchengine_info = 'Information du moteur de recherche';
$cms_autogenerate_searchengine_info = 'Générer automatiquement les informations du moteur de recherche (Recommandé)';

//2.15 
$cms_template_item = 'Modèle';
$cms_page_template = 'Ce modèle peut être utilisé pour les pages';
$cms_go_auth = 'Utiliser l\'authentification Group-Office';
$cms_start_page = 'Page de démarrage';
$cms_hot_item_text = 'Texte des éléments importants';

//2.16
$cms_unregistered_comments = 'Autoriser les commentaire pour tous les visiteurs';
$cms_comments = 'Commentaires';
$cms_comments_text = 'Vous pouvez placer vos commentaires en bas de page.';
$cms_comments_login = 'Vous devez être connecté pour poster un commentaire.';
$cms_add_comment = 'Ajouter un commentaire';
$cms_comments_missing_field = 'Vous devez entrer un nom et un commentaire.';
$cms_logout = 'Se déconnecter';
$cms_subject = 'Sujet';
$cms_message = 'Message';
$cms_send_message = 'Envoyer';
$cms_contact_form = 'Formulaire de contact';
$cms_user_search = 'Rechercher un utilisateur';
$cms_insert_plugin = 'Insérer un plugin';
$cms_logged_out =  'Vous êtes deconnecté';
$cms_insert_contactform = 'Insérer un formulaire de contact';
$cms_message_height = 'Hauteur de la zone de message';
$cms_insert_comments = 'Insérer un formulaire de commentaire';
$cms_commentsform = 'Formulaire de commentaires';
$cms_login_template_item = 'Template de connexion';
$cms_default_go_login = 'Boite de connexion Group-Office par défaut';
$cms_userfield = 'Champs data utilisateur';
?>
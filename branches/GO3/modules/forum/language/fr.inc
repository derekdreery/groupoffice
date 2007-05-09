<?php
require($GO_LANGUAGE->get_fallback_language_file('forum'));

$frm_new_forum = 'Nouveau forum';
$frm_properties = 'Propriétés';

$frm_read_perm = 'Permissions en lecture';
$frm_write_perm = 'Permissions en écriture';

$frm_name = 'Nom';
$frm_desc = 'Description';

$frm_already_exists = 'Un forum portant ce nom existe déjà';

$frm_added = 'Le forum a bien été enregistré';
$frm_updated = 'Le forum a été correctement mis à jour';
$frm_not_saved = 'Le forum n\'a PAS pu être enregistré';
$frm_not_updated = 'Le forum n\'a PAS été mis à jour';

$frm_acl_read = 'Utilisateurs autorisés à parcourir les forums';
$frm_acl_write = 'Utilisateurs autorisés à poster des messages';

$frm_no_forum = 'Aucun forum';
$frm_confirm_delete = 'Êtes vous sûr de vouloir supprimer le forum';

$frm_title = 'Titre du message';
$frm_body = 'Contenu';

$frm_post_answer = 'Poster une réponse à ce message';
$frm_post_new_message = 'Nouveau sujet';
$frm_responses = 'Autres réponses à ce message';
$frm_reply = 'Répondre';

$frm_fields_missing = 'Veuillez saisir tous les champs';
$frm_msg_no = 'Message N°';

$frm_msg_posted = 'Message posté';
$frm_no_message = 'Aucun message';
$frm_attached_files = 'Fichiers joints';

$frm_posted_by = 'posté par';
$frm_in_answer_to = 'en réponse à';
$frm_no_response = 'Pas de réponse';
$frm_forum = 'Forum';
$frm_created_by = 'Créé par';
$frm_ctime = 'Créé le';
$frm_mtime = 'Dernier message';
$frm_reply_to = 'Répondre à';

$frm_messages_total = 'Messages';
$frm_subjects_total = 'Sujets';
$frm_unread_messages_total = 'Messages non lus';
$frm_unread_subjects_total = 'Nouveaux sujets';

$frm_mailing_list = 'Liste de diffusion';

$frm_mail_subject='Nouveau sujet dans le forum "%forum_name%"';
$frm_mail_subject2='Nouvelle réponse dans le forum "%forum_name%"';
$frm_mail_subject3='%count% %new_s% %reply_s% dans le forum "%forum_name%"';

$frm_new_s[0] = 'nouvelle';
$frm_new_s[1] = 'nouvelles';

$frm_reply_s[0] = 'réponse';
$frm_reply_s[1] = 'réponses';

$frm_mail_body=<<<frm_mail_body
%style%
<div class="automail">
<p class="date">%user% a enregistré un nouveau sujet le %date% à %time%</p>
<div class="box"><p title="%msg_title%" class="title">%msg_title%</p>
<p class="body">
%msg_body%
</p></div>
<a href="%url%">Rejoindre le forum</a>
</div>
frm_mail_body;

$frm_mail_body2=<<<frm_mail_body2
%style%
<div class="automail">
<p class="date">%user% a ajouté une réponse le %date% à %time%</p>
<div class="box"><p title="%msg_title%" class="title">%msg_title%</p>
<p class="body">
%msg_body%
</p></div>
<a href="%url%">Rejoindre le forum</a>
</div>
frm_mail_body2;


$frm_mail_body3=<<<frm_mail_body3
%style%
<div class="automail">
<p class="date">%user% a importé %count% %reply_s% le %date% à %time%</p>
<a href="%url%">Rejoindre le forum</a>
</div>
frm_mail_body3;

$frm_ad_type = 'Avertissement automatique par mail';
$frm_ad_1 = 'A chaque nouveau sujet';
$frm_ad_2 = 'A chaque nouveau message';
$frm_ad_3 = 'Aucun';

$frm_display_type = 'Affichage par';
$frm_display_1 = 'Arborescence';
$frm_display_2 = 'Liste';

$frm_display_sort = 'Trier par';
$frm_sort_1 = 'Titre du message';
$frm_sort_2 = 'Date de parution';
$frm_sort_3 = 'Prénom de l\'auteur';
$frm_sort_4 = 'Nom de l\'auteur';
$frm_ASC = 'Croissant';
$frm_DESC = 'Décroissant';
$frm_order ='Ordre';

$frm_expand_all = 'Tout déplier';
$frm_collapse_all = 'Tout replier';

$mail_style=<<<mail_style
<style>
	.automail{ font-family:Verdana;font-weight:bolder;font-size:12px; }
	.box { border: 1px solid #ddd;background:#f8f8f8;width:400px;padding:5px; }
	.title { font-size:14px;text-align:center;text-decoration:underline; }
	.body { text-align:justify; font-size:10px; }
	
</style>
mail_style;

$frm_edit = 'Editer';
$frm_delete = 'Supprimer';
$frm_confirm_msg_delete = 'Etes vous sur de vouloir supprimer ce message ?';


$frm_close_topic = 'Clôturer le sujet';
$frm_confirm_close_topic = 'Etes vous sur de vouloir clôturer le sujet ?';
$frm_closed_topic = 'Clôturé';
$frm_reopen_topic = 'Réouvrir le sujet';
$frm_confirm_reopen_topic = 'Etes vous sur de vouloir réouvrir le sujet ?';
$frm_custom = 'Champ personnalisé';
$frm_import = 'Importer';
$frm_import_text = 'Veuillez choisir le fichier CSV à importer';
$frm_import_file = 'Fichier CSV';
$frm_choose_sep = 'Choisissez le séparateur du fichier CSV';

$frm_confirm_empty_forum = 'Etes vous sur de vouloir vider ce forum ?';
$frm_empty_forum = 'Tout effacer';

$frm_import_string = 'Veuillez organiser les colonnes de votre fichier CSV comme ceci :<br /> Date, Heure, Sujet, Contenu, Champ1, Champ2, Champ3';
$frm_entries_added = 'entrées ont été ajoutées';
$frm_entry_added = 'entrée a été ajoutée';

$frm_erase_before_import = 'Effacer tous les messages existant avant d\'importer';
$frm_import_all = 'Tout importer';
$frm_import_selected = 'Importer tous les messages de cette période';

$frm_from = 'Du'; $frm_to = 'au';

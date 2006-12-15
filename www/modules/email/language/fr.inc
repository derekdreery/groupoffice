<?php
require_once($GO_LANGUAGE->get_fallback_language_file('email'));

$ml_from = "De";
$ml_to = "A";
$ml_send = "Envoyer";
$ml_attach = "Joindre";
$ml_attachments = "Fichiers joints";
$ml_attachment = "Fichier joint";
$ml_high = "Haute";
$ml_normal = "Normale";
$ml_low = "Basse";
$ml_priority = "Priorité";
$ml_subject = "Sujet";
$ml_filename = "Nom de fichier";
$ml_total_size = "Taille totale";
$ml_size = "Taille";
$ml_to_empty = "Vous n'avez pas spécifié de destinataire.";
$ml_print = "Imprimer";
$fbNoSelect = "Vous n'avez pas sélectionné de message";
$ml_inbox = 'Boîte de réception';
$ml_properties = 'propriétés';
$ml_add_account = "Ajouter un compte";
$em_new_folder = "Nouveau dossier";
$ml_your_accounts = "Vos comptes de courrier";
$ml_no_accounts = "Vous n'avez aucun compte de courrier de configuré.";
$ml_original_follows = "----- Message Original -----";
$ml_welcome = "Bienvenue dans le courrier de Group-Office";
$ml_text = "Cliquez sur les comptes pour ajouter un compte de courrier.";
$ml_accounts = "comptes";
$ml_compose = "composer";
$ml_delete = "supprimer";
$ml_refresh = "rafraîchir";
$ml_reply = "répondre";
$ml_forward = "faire suivre";
$ml_reply_all = "répondre à tous";
$ml_delete_message = "Etes vous certain(e) de vouloir supprimer ce message de manière définitive ?";
$ml_no_subject = "Pas de sujet";
$ml_no_reciepent = "Pas de destinataires";
$ml_online_files = "Fichiers en ligne";
$ml_send_error = "Erreur pendant l'envoi du courrier.";
$ml_attach_forget = "Vous avez sélectionné un fichier mais vous ne l'avez pas attaché.";
$ml_messages = "message(s)";
$ml_message = 'ce message';
$ml_messages2 = 'messages';
$ml_new = "nouveau";
$ml_delete_mailbox = "Etes vous certain(e) de vouloir supprimer ces message de manière définitive ?";
$contact_profile = "Profil de contact";
$ml_add_contact = "Ajouter des destinataires aux contacts";
$strHost = "Hôte";
$strDefault = "Défaut";
$ml_edit_account = "Editer le compte";
$ml_new_account = "Nouveau compte";
$ml_no_messages = "Il n'y a pas de messages dans ce dossier.";
$ml_file_too_big = "Le fichier que vous avez essayé de jondre est trop gros, la taille maximale pour les fichier joints est de : ";
$fbNoRightToOverwrite = "Le fichier existe déjà et vous n'avez pas les permissions pour l'écraser.";
$ml_save_attachment = "Enregistrer les pièces jointes dans Group-Office.";
$ml_folders = "dossiers";
$ml_select_folder = "Choisissez le dossier où Group-Office peut enregistrer les courriers envoyés ou entrez un nom ci-dessous pour créer un dossier";
$ml_connect_failed = "Impossible de se connecter au serveur de Mail :";
$ml_at_port = "sur le port";
$ml_solve_error = 'Cliquez sur \'Comptes\' pour corriger les réglages de votre compte ou cliquez sur \'Rafraîchir\' pour ouvrir votre compte par défaut.';
$ml_sent_items = "Eléments envoyés";
$ml_spam = "Spam";
$ml_use = "Utilisation";
$ml_synchronise = "synchroniser";
$ml_move_mail = "Déplacer les messages sélectionnés vers le dossier...";
$ml_filters = "filtres";
$ml_move_to = "ensuite déplacer le message vers le dossier :";
$ml_email_is = "Si l'adresse mail de la personne qui a envoyé le courrier contient :";
$ml_to_is = "Si le champs destinataire contient :";
$ml_cc_is = "Si le champs Cc contient :";
$ml_subject_is = "Si le champs Sujet contient :";
$ml_new_filter = "Nouveau filtre :";
$ml_field = "Champs :";
$ml_contains = "Contient :";
$ml_folder = "Dossier";
$ml_no_folders = "Vous n'avez pas de dossier, vous ne pouvez donc pas créer de filtres.";
$ml_unwanted = "Cliquez içi si vous ne souhaitez plus recevoir de courriers de cette personne";
$fbFolderSize = "Taille du dossier";
$ml_move_up = "Augmenter la priorité";
$ml_delete_filter = "Etes vous certain(e) de vouloir supprimer ce filtre ?";
$ml_high_priority = "Ce message a une priorité haute !";
$ml_low_priority = "Ce message a une priorité basse.";
$ml_block = "Bloquer l'émetteur";
$ml_sent_items_fail = "Ce message a été envoyé mais il y a eu une erreur pendant la création du message dans votre dossier de messages envoyés. Vérifiez votre compte IMAP.";
$fbProperties = "propriétés";
$ml_notification = "Demander une confirmation de lecture";
$ml_ask_notify = "L'émetteur de ce message a demandé une confirmation de lecture. Souhaitez vous envoyer une confirmation de lecture ?";
$ml_displayed = "Votre message a été lu par ";
$ml_notify = "Confirmation de lecture";
$ml_delete_filter1 = "Supprimer le filtre";
$ml_html_message_attached = "Les messages HTML originaux sont attachés";
$ml_add_recievers = "Ajouter des destinataires inconnus à votre carnet d'adresse";
$ml_no_folders = 'Vous n\'avez pas de dossier à filtrer';
$ml_no_filters = 'Vous n\'avez aucun filtre.';
$ml_host = 'Hôte';
$ml_port = 'Port';
$ml_type = 'Type';
$ml_servertype = 'Type de serveur';
$ml_format = 'Format';
$ml_loose_changes = 'Les modifications seront perdues, êtes vous sur ?';
$ml_markread = "marqué comme lu";
$ml_markunread = "marqué comme non lu";
$ml_root_mailbox = "la racine de la boîte aux lettres";
$ml_inside = "dans";
$ml_no_mailbox = "Ce n'est pas une boîte aux lettres.  Ce dossier ne peut pas contenir de message.";
$ml_signature = 'Signature';
$ml_root = 'Boîte aux lettres principale';
$ml_disable_accounts = 'Désactiver la gestion des comptes';
$ml_trash = 'Poubelle';
$ml_disable = 'Désactiver';
$ml_confirm_empty_mailbox = 'Etes vous sûre de vouloir vider ce dossier ?';
$ml_empty_mailbox = 'Dossier Vide';
$ml_you_have = 'Vous avez';
$ml_new_mail_single = 'un nouveau e-mail .';
$ml_new_mail_multiple = 'nouveau e-mail .';
$ml_auto_check = 'Vérifiez automatiquement ce compte .';

//1.98
$ml_no_contacts_in_mailing_group = 'Il n\'y a aucuns contacts dans le mailing groupe sélectionné. Ajouter des contacts au groupe.';
$ml_attention= 'Attention';

//2.02
$ml_autocheck_disabled = ' n\'a pas pu être atteint. Le contrôle automatique des e-mail pour ce compte est désactivé.';
$ml_mark = 'Messages marqué...';
$ml_flag = 'diminuer';
$ml_clearflag = 'augmenter';
$ml_expand_all = 'développer les dossiers';
$ml_collapse_all = 'fermer tous les dossiers';

//2.05
$ml_search = 'Chercher';
$ml_answered = 'Répondu';
$ml_doesnt_matter = 'Ne vous inquiétez pas';
$ml_body = 'Corps';
$ml_before = 'Plus ancien';
$ml_since = 'Plus récent';
$ml_select_one_criteria = 'SVP, sélectionnez ou entrez au moin un critère de recherche';

//2.06
$ml_mails_were_sent = 'messages envoyés';
$ml_novalidate_cert = 'Ne pas valider le certificat';
$ml_appointment_vcf = 'Ce message contient un contact. Voulez-vous l\'ajouter au carnet d\'adresses?';
$ml_appointment = 'Ce message contient un évènement. Voulez vous l\'ajouter au calendrier?';

//2.08 
$ml_add_senders = 'Demander confirmation avant d\'ajouter des émetteurs inconnus dans le carnet d\'adresses';
$ml_text_format = 'Texte plat';
$ml_html_format_is = 'HTML (Si le navigateur supporte l\'édition HTML)';
$ml_html_format = 'HTML formaté';
$ml_always_ask_notification = 'Toujours demander au destinataire une notification de lecture par défaut';
$ml_ask_add_sender = 'L\'adresse de l\'émetteur %s appartenant à %s n\'est pas dans votre carnet d\'adresses. Voulez-vous l\'y ajouter maintenant?';

$ml_edit_filter = 'Editer filtre';
$ml_choose_action = 'Veuillez sélectionner une action:';
$ml_search_criteria = 'Critère de recherche';
$ml_sender = 'Emetteur';
$ml_reciever = 'Récepteur';
$ml_cc_reciever = 'Récepteur-CC';

//2.10
$ml_empty_trash = 'Vider la poubelle';

// LCCY
$ml_raw = 'Affichage format brut';

?>
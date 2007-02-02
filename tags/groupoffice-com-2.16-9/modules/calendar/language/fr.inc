<?php

// LANG: "fr", ENCODING: UTF-8
// Author: L. Jullien
// 15 Jan 2007

require_once($GO_LANGUAGE->get_fallback_language_file('calendar'));

$sc_title = "Titre";
$sc_description = "Description";
$sc_participants = "Invités";
$sc_location = "Emplacement";
$month_times[0] = "Premier";
$month_times[1] = "Deuxième";
$month_times[2] = "Troisième";
$month_times[3] = "Quatrième";
$sc_new_app = "Nouvel évènement";
$sc_calendars = "Calendriers";
$sc_invited = 'Vous êtes invité à joindre l\'évènement cité ci-dessous.';
$sc_types1[0] = 'Ne répeter qu\'une fois';
$sc_types1[1] = 'jours';
$sc_types1[2] = 'semaines';
$sc_types1[3] = 'mois par date';
$sc_types1[4] = 'mois par jour';
$sc_types1[5] = 'années';
$sc_types[0] = 'Une fois';
$sc_types[1] = 'Tous les jours';
$sc_types[2] = 'Toutes les semaines';
$sc_types[3] = 'Mensuellement par date';
$sc_types[4] = 'Mensuellement par jour';
$sc_types[5] = 'Annuellement';
$sc_start_at = 'Débute à';
$sc_end_at = 'Se termine à';
$sc_start_time = 'Heure de début';
$sc_end_time = 'Heure de fin';
$sc_at_days = 'Les jours';
$sc_of_month = 'du mois.';
$sc_cycle_start = 'Le cycle démarre à';
$sc_cycle_end = 'Récurrent jusqu\'à';
$sc_accept_question = 'Souhaitez vous valider et planifier cet évènement ?';
$sc_accept = 'accepter';
$sc_decline = 'décliner';
$sc_notime = 'L\'heure n\'est pas applicable';
$sc_noend = 'Pas de date de fin.';
$sc_never_happens = 'Mauvaise entrée ! Cet évènement ne se produit jamais.';
$sc_start_later = 'Mauvais entrée !! L\'heure de début est après l\'heure de fin.';
$sc_add_self = 'Entrée manuelle';
$sc_select_calendar = 'Choisissez le calendrier dans lequel vous souhaitez programmer cet évènement';
$sc_accept_confirm = 'L\'évènement est accepté et programmé.';
$sc_no_calendars = 'Pas de calendrier valide trouvé pour programmer cet évènement.';
$sc_bad_event = 'Aucun évènement valide trouvé. L\'évènement est probablement supprimé.';
$sc_accept_title = 'Accepter l\'évènement';
$sc_decline_confirm = 'L\'évènement a été décliné.';
$sc_decline_title = 'Décliner l\'évènement';
$sc_status = 'Statut';
$sc_not_responded = 'Sans réponse';
$sc_accepted = 'Accepté';
$sc_declined = 'Refusé';
$sc_no_participants = 'Pas de participant';
$sc_person = 'Personne';
$sc_owner = 'Propriétaire';
$sc_type = 'Type';
$sc_subscribed = 'Souscrit';
$sc_subscribe = 'souscrire';
$sc_new_calendar = 'Nouveau calendrier';
$sc_no_calendar = 'Aucun calendrier trouvé. Veuillez en créer un maintenant ou laissez Group-Office en créer un automatiquement pour vous.';
$sc_day_view = 'Vue par jours';
$sc_week_view = 'Vue par semaine';
$sc_list_view = 'Liste';
$sc_put_in = 'Placer l\'évènement dans';
$sc_inherit_permissions ='Hériter des permissions de';
$sc_no_inherit = 'Ne pas hériter';
$sc_select_calendar_please = 'Veuillez au moins choisir un calendrier.';
$sc_events = 'Evènements';
$sc_from_calendar = 'depuis le calendrier';
$sc_enitrely = 'Entièrement';
$sc_delete_event = 'Supprimer l\'évènement';
$sc_delete_pre = 'Souhaitez vous supprimer l\'évènement';
$sc_delete_suf = 'entièrement ou juste depuis ce calendrier ?';
$sc_refresh = 'Rafraîchir';
$sc_reminder = 'Rappel';
$sc_mins = 'minutes';
$sc_hour = 'heure';
$sc_hours = 'heures';
$sc_day = 'jour';
$sc_days = 'jours';
$sc_week = 'semaine';
$sc_weeks = 'semaines';
$sc_reminder_set_to_early = "Le rappel ne peut pas se déclencher avant la précédente occurence. Veuillez raccourcir l\'heure de rappel ou la période.";
$sc_cycle_start_later = 'La date de début du cyle est supérieure ou égale à la date de fin.';
$cmdGenerate = 'Générer';
$sc_preferences = 'Préférences';
$sc_holidays = 'Vacances';
$strNoHolidays = 'Pas de vacances dans la base';
$strUnknown = 'Inconnu';
$strYear = 'Année';
$strDeleteHolidaysPrefix = 'Etes vous certain(e) de vouloir supprimer toutes les vacances de l\'année ';
$strDeleteHolidaysSuffix = '?';
$strReplaceHolidays = 'Etes vous certain(e) de vouloir écraser les enregistrements concernants les vancances ?';
$sc_holidays_text1 = 'Veuillez cliquer sur \''.$cmdGenerate.'\' pour générer les vacances pour l\'année choisie.';
$sc_holidays_text2 = 'Veuillez cliquer sur \''.$cmdApply.'\' pour enregistrer toutes les dates de vacances calculées dans la base de données.';

//translation required below this
$sc_client = 'Client';
$sc_no_contact = 'Aucun';
$sc_background = 'Fond';
$sc_colors['red'] = 'Rouge';
$sc_colors['green'] = 'Vert';
$sc_colors['blue'] = 'Bleu';
$sc_colors['pink'] = 'Rose';
$sc_colors['orange'] = 'Orange';
$sc_colors['brown'] = 'Brun';
$sc_colors['yellow'] = 'Jaune';
$sc_colors['grey'] = 'Gris';
$sc_colors['purple'] = 'Pourpre';
$sc_colors['white']= 'Blanc';
$sc_status = 'statut';
$sc_show_hours = 'Voir les heures de';
$sc_to = 'jusqu\'à ';
$sc_today = 'Aujourd\'hui';
$sc_recur_every = 'Répéter tous les';
$sc_months = 'mois';
$sc_private_event = 'Evènement privé';
$sc_calendar_exists = 'Un calendrier avec ce nom existe déjà';
$sc_month = 'Mois';
$sc_view = 'Vue';
$sc_calendar = 'Calendrier';
$sc_default = 'Défaut';
$sc_month_view = 'Vue par mois';

//text changed, needs new translation
$sc_holidays_no_file_prefix = 'Les vacances ne peuvent pas être générées car le fichier qui contient les informations de vacance pour votre région [\'';
$sc_holidays_no_file_suffix = '\'] est vide ou n\'existe pas.';
$sc_no_events = 'Aucun évènement trouvé';

//2.03
$cal_export = 'Exporter';
$cal_import = 'Importer';
$cal_please_wait = 'Veuillez patientez...';
$cal_import_text = 'Sélectionner un fichier iCal (*.ics) et appuyer sur \'Ok\'';
$cal_filename = 'Fichier';
$cal_import_success = 'Données importées';

//2.04
$cal_add_todo = 'Nouvelle tâche';
$cal_todos = 'Tâches';
$cal_hide_completed = 'Cacher les tâches accomplies';
$cal_show_all = 'Voir toutes les tâches';
$cal_show_own = 'Voir vos tâches';
$cal_show_responsible = 'Voir le responsable de la tâche';
$cal_priority = 'Priorité';
$cal_priority_values[0] = 'Basse';
$cal_priority_values[1] = 'Normale';
$cal_priority_values[2] = 'Importante';
$cal_status = 'Status';
$cal_canceled = 'Annuler';
$cal_completed = 'Accomplie à';
$cal_percent_completed = '% accomplie';
$cal_before_task_start = 'Avant que la tâche debute.';
$cal_before_task_due = 'Avant que la tâche doit être finie';
$cal_responsible = 'Responsable';
$cal_due_at = 'Dû à';
$cal_no_todos= 'Aucune tâche trouvée';
$cal_resend_invitation = 'Renvoyer l\'invitation';

//2.05
$cal_everybody_write = 'Tous le monde peut modifier cet évènement';
$cal_everybody_read = 'Je peux uniquement modifier cet evènement';
$cal_view = 'vue';
$cal_views = 'Vues';
$sc_view_exists = 'Une vue avec le même nom existe déja';
$cal_new_view = 'Nouvelle vue';
$cal_event_calendars = 'Calendrier(s)';

//2.06
$cal_view_merged = 'Superposée';
$cal_view_emerged = 'Séparée';

//2.07
$cal_display = 'Affichage';
$sc_new_task = "Nouvelle tâche";
$sc_all_day_events = 'évènements quotidien';
$sc_all_day_event = 'évènement quotidien';
$sc_usual_events = 'évènements';
$sc_usual_event = 'évènement';

//2.08
$sc_recur_section = 'Récurence';
$sc_options_section = 'Options';
$sc_close = 'Fermer';
$cal_count_single = 'évènement';
$cal_count_multiple = 'évènements';
$cal_until = 'jusque';
$cal_forever = 'toujours';

//2.09
$cal_merged_overview = 'Vue superposée';
$cal_own_availability = 'Ma disponibilité';

//2.11
$cal_no_file = 'Aucun fichiers n\'a été reçu';
$cms_no_calendars_in_view = 'Cette vue ne peut être affichée parce qu\'il n\'y a aucun calendrier dans cette vue. Cliquer sur \'Vues\' pour gérer les vues.';
$cal_no_reminder = 'Pas de rappel';

//2.13
$cal_refresh_rate = 'Taux de Rafraichissement';
$sc_seconds = 'secondes (Laisser vide pour aucun rafraichissement)';
$cal_default_reminder = 'Rappel par défaut';

//2.14
$cal_event = 'Evènement';
$cal_todo = 'A faire';
$cal_participants_write = 'Les participants peuvent modifier les évènements';
$cal_everybody_write = 'Tout le monde peut modifier les évènements'; //modified string from 2.05 this became the
$cal_everybody_read = 'Uniquement moi peut modifier les évènements'; //modified string from 2.05
$cal_default_permissions = 'Permissions par défaut';
$cal_show_todos = 'Montrer les tâches dans le calendrier';
$cal_edit_series_or_single = 'Editer seulement cette instance ou la série entière ?';
$cal_single = 'Instance';
$cal_series = 'Série Entière';
$cal_legend = 'Légende';
$cal_multiple_calendars = 'Plusieurs calendriers';
$cal_cumulative = 'Entrée invalide ! L\'évenement durerait plus longtemps que les intervalles cumulés.';
$cal_weekview = 'La semaine est vue du';
$cal_mo_to_fr = 'Lundi à vendredi';
$cal_mo_to_su = 'Lundi à samedi';
$cal_batchcalendars = 'Créé un calendrier pour tous les utilisateurs';
$cal_create_all_calendars = 'Créer un calendrier pour tous les utilisateurs ou ajouter les permissions pour le groupe';
$cal_no_views = 'Aucune vue trouvée';
$cal_my_cals = 'Mon calendrier';
$cal_event_colors_override = 'Utiliser l\'ensemble des couleurs définies manuellement pour les évènements';

//2.15
$cal_groups = 'Groupes';
$cal_new_group = 'Nouveau groupe';
$cal_group_exists = 'Ce groupe existe déjà. Choisissez un autre nom.';
$cal_no_calendars = 'Aucun calendrier n\'a été trouvé dans ce groupe';
$cal_group = 'Groupe';
$cal_resources = 'Ressources';
$cal_event_mail_subject = 'Evènement \'%s\' créé dans votre calendrier'; //%s is event name
$cal_event_mail_body = '%s a créé l\'évènement \'%s\' dans votre calendrier nommé \'%s\'.'; //irst %s is the name of the person who created the event. Second is the event name, third is the calendar name
$cal_event_mail_open = 'Rendez-vous ouvert';
$cal_add_custom_field = 'Ajouter un champ personnalisé';
$cal_event_modified_mail_subject = 'L\'évènement \'%s\' a été modifié dans votre calendrier'; //%s is event name
$cal_event_modified_mail_body = '%s a modifié l\'évènement \'%s\' dans votre calendrier nomé \'%s\'.'; //First %s is the name of the person who created the event. Second is the event name, third is the calendar name
$cal_your_event_modified_mail_subject = 'L\'évènement \'%s\' créé par vous a été modifié'; //%s is event name
$cal_your_event_modified_mail_body = '%s a modifié l\'évènement \'%s\' qui avait été créé par vous.'; //First %s is the name of the person who created the event. Second is the event name.
$cal_no_custom_fields = 'Aucun champ personnalisé n\'est défini';
$cal_add_field = 'Ajouter un champ';
$cal_resource_mail_subject = 'Ressource \'%s\' réservé'; //%s is resource name
$cal_resource_mail_body = '%s a fait une réservation pour cette ressource \'%s\'. Vous êtes le responsable de cette ressource. Veuillez approuver ou refuser la réservation.'; //First %s is the name of the person who created the event. Second is the calendar name
$cal_open_resource = 'Réservation ouverte';
$cal_resource_modified_mail_subject = 'Réservation de la ressource \'%s\' modifiée'; //%s is resource name
$cal_resource_modified_mail_body = '%s a modifié une réservation pour cette ressource \'%s\'. Vous êtes le responsable de cette ressource. Veuillez approuver ou refuser la réservation.'; //First %s is the name of the person who created the event. Second is the calendar name
$cal_your_resource_modified_mail_subject = 'Votre réservation pour \'%s\' de statut %s est modifiée'; //%s is resource name, status
$cal_your_resource_modified_mail_body = '%s a modifié votre réservation pour la ressource \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name
$cal_accept_mail_subject = 'Invitation pour \'%s\' acceptée';
$cal_accept_mail_body = '%s a accepté  votre invitation pour \'%s\'';
$cal_decline_mail_subject = 'Invitation pour \'%s\' refusée';
$cal_decline_mail_body = '%s a refusé votre invitation pour \'%s\'';
$cal_statuses['NEEDS-ACTION'] = 'Besoin d\'une action';
$cal_statuses['ACCEPTED'] = 'Accepté';
$cal_statuses['DECLINED'] = 'Refusé';
$cal_statuses['TENTATIVE'] = 'Tentative';
$cal_statuses['DELEGATED'] = 'Délégué';
$cal_statuses['COMPLETED'] = 'Terminé';
$cal_statuses['IN-PROCESS'] = 'En cours';
$cal_booking = 'Réservé pour \'%s\''; //%s is resource name
$cal_group_admins = 'Admins';
$cal_no_admins = 'Aucun admin défini';
$cal_add_admin = 'Ajouter un admin';
$cal_scale='Echelle';
$cal_show_completed = 'Montrer les tâches terminées';
$cal_resource = 'Ressource';
$cal_grid_view = 'Voir les grilles';
$cal_field_types['checkbox'] = 'Case à cocher';
$cal_field_types['text'] = 'Texte';
$cal_field_types['textarea'] = 'zone de texte';
$cal_field_types['date'] = 'Champs date';
$cal_conflict = 'Cette évènement est en conflit avec un autre évènement ou réservation de ressource. Cliquez sur \'Ok\' ou \'Appliquer\' encore une fois pour ignorer ou cliquez sur \'Vérifier les disponibilités\' pour trouver une période adéquate.';
$cal_select_calendar = 'Sélection de calendrier';
$cal_check_conflicts = 'Etre averti d\'un conflit quand je créé un évènement';
$cal_email_changes = 'Envoie d\'un  e-mail quand quelqu\'un fait des changements dans mon calendrier';
$cal_shared_calendars = 'Calendrier partagé';
$cal_admin = 'Administration';
$cal_resource_groups = 'Groupe de ressources';
$cal_open_calendar = 'Ouvrir un nouveau calendrier';
$cal_email_reminders='Envoyer un rappel d\'évènement par e-mail';
$cal_favourites = 'Préférences';
$cal_your_resource_accepted_mail_subject = 'La réservation pour \'%s\' est acceptée'; //%s is resource name, status
$cal_your_resource_accepted_mail_body = '%s a accepté votre réservation pour la ressource \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name
$cal_your_resource_declined_mail_subject = 'La réservation pour \'%s\' est refusée'; //%s is resource name
$cal_your_resource_declined_mail_body = '%s a refusé votre réservation de ressource \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name
$cal_resource_deleted_mail_subject = 'La réservation pour \'%s\' est supprimée'; //%s is resource name, status
$cal_repeats_at = 'Répéter chaque  %s à %s'; //eg. Repeats every month at the first monday
$cal_repeats_at_not_every = 'Répété chaque %s %s à %s'; //eg. Repeats every 2 weeks at monday
$cal_repeats = 'Répété chaque %s';
$cal_and = 'et';
$sc_months = 'mois';
$sc_month = 'mois';
$sc_year = 'année';
$sc_years = 'années';
$cal_another_resource = 'La ressource a été changée à %s';
$cal_background_colors = 'Couleurs de fond';
$cal_weekday = 'Jour de semaine';
$cal_background_color = 'Couleur de fond';
$cal_background = 'Fond';
?>

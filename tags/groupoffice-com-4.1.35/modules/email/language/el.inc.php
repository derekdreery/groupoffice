<?php
/* Translator for the Greek Language: Konstantinos Georgakopoulos (kgeorga@uom.gr)*/
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));
$lang['email']['name'] = 'Ηλεκτρονικό Ταχυδρομείο';
$lang['email']['description'] = 'Πλήρης πελάτης ηλεκτρονικού ταχυδρομείου. Κάθε χρήστης μπορεί να στέλνει και να λαμβάνει μηνύματα ηλεκτρονικού ταχυδρομείου';

$lang['link_type'][9]='Ηλεκτρονικό Ταχυδρομείο';

$lang['email']['feedbackNoReciepent'] = 'Δεν εισάγατε παραλήπτη';
$lang['email']['feedbackSMTPProblem'] = 'Υπήρξε ένα πρόβλημα με την επικοινωνία με τον εξυπηρετητή SMTP: ';
$lang['email']['feedbackUnexpectedError'] = 'Υπήρξε ένα αναπάντεχο πρόβλημα κατά την δημιουργία του μηνύματος ηλεκτρονικού ταχυδρομείου: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Αποτυχία δημιουργίας φακέλου';
$lang['email']['feedbackDeleteFolderFailed'] = 'Αποτυχία διαγραφής φακέλου';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Αποτυχία εγγραφής φακέλου';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Αποτυχία απεγγραφής φακέλου';
$lang['email']['feedbackCannotConnect'] = 'Αποτυχία σύνδεσης στον %1$s στη θύρα %3$s<br /><br />Ο εξυπηρετητής ηλεκτρονικού ταχυδρομείου επέστρεψε: %2$s';
$lang['email']['inbox'] = 'Εισερχόμενα';

$lang['email']['spam']='Ενοχλητικά μηνύματα';
$lang['email']['trash']='Απορίματα';
$lang['email']['sent']='Απεσταλμένα';
$lang['email']['drafts']='Προσχέδια';

$lang['email']['no_subject']='Χωρίς θέμα';
$lang['email']['to']='Προς';
$lang['email']['from']='Από';
$lang['email']['subject']='Θέμα';
$lang['email']['no_recipients']='Μυστικοί παραλήπτες';
$lang['email']['original_message']='--- Ακολουθεί το πρωτότυπο μήνυμα ---';
$lang['email']['attachments']='Επισυναπτόμενα';

$lang['email']['notification_subject']='Ανάγνωση: %s';
$lang['email']['notification_body']='Το μήνυμα σας με θέμα "%s" προβλήθηκε σαν %s';

$lang['email']['errorGettingMessage']='Αποτυχία λήψης μηνήματος από τον εξυπηρετητή';
$lang['email']['no_recipients_drafts']='Χωρίς παραλήπτες';
$lang['email']['usage_limit'] = 'Χρησιμοποιούνται %s από %s';
$lang['email']['usage'] = 'Χρησιμοποιούνται %s';

$lang['email']['event']='Συνάντηση';
$lang['email']['calendar']='Ημερολόγιο';

$lang['email']['quotaError']="Το γραμματοκιβώτιο σας έχει γεμίσει. Αδείάστε τον φάκελο απορίματα πρώτα. Εάν είναι ήδη άδειος και το γραμματοκιβώτιο σας είναι ακόμα γεμάτο, πρέπει να απενεργοποιήσετε τον φάκελο Απορίματα για να διαγράψτε μηνύματα από άλλους φακέλους. Μπορείτε να τον απενεργοποιήσετε στο:\n\nΡυθμίσεις -> Λογαριασμοί -> Διπλό κλίκ στο λογαριασμό -> Φάκελοι.";

$lang['email']['draftsDisabled']="Το μήνυμα δεν αποθηκεύτηκε γιατί ο φάκελος 'Προσχέδια' είναι απενεργοποιημένος.<br /><br />Πηγαίνετε στο Ρυθμίσεις -> Λογαριασμοί -> Διπλό κλίκ στο λογαριασμό -> Φάκελοι για να τον ρυθμίσετε.";
$lang['email']['noSaveWithPop3']='Το μήνυμα δεν αποθηκεύτηκε γιατί ένας λογαριασμός τύπου POP3 δεν υποστηρίζει αυτή τη λειτουργία.';

$lang['email']['goAlreadyStarted']='Group-Office was al gestart. Het e-mailscherm wordt nu geladen in Group-Office. Sluit dit venster en stel uw bericht op in Group-Office.';

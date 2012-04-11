<?php
/* Translator for the Greek Language: Konstantinos Georgakopoulos (kgeorga@uom.gr)*/
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Ημερολόγιο';
$lang['calendar']['description'] = 'Άρθρωμα Ημερολογίου. Κάθε χρήστης μπορεί να προσθέσει, να τροποποιήσει ή να διαγράψει συναντήσεις.Επιπλέον, υπάρχουν διαθέσιμες οι συναντήσεις από άλλους χρήστες και μπορούν να τροποποιηθούν εάν υπάρξει ανάγκη.';

$lang['link_type'][1]='Συνάντηση';

$lang['calendar']['groupView'] = 'Όψη ομάδας';
$lang['calendar']['event']='Γεγονός';
$lang['calendar']['startsAt']='Ξεκινάει στις';
$lang['calendar']['endsAt']='Τελειώνει στις';

$lang['calendar']['exceptionNoCalendarID'] = 'Τερματικό σφάλμα: Δεν υπάρχει αριθμός ταυτότητας (ID) του ημερολογίου!';
$lang['calendar']['appointment'] = 'Συνάντηση: ';
$lang['calendar']['allTogether'] = 'Όλα μαζί';

$lang['calendar']['location']='Τοποθεσία';

$lang['calendar']['invited']='Έχετε προσκληθεί στο παρακάτω γεγονός';
$lang['calendar']['acccept_question']='Αποδέχεστε αυτό το γεγονός;';

$lang['calendar']['accept']='Αποδοχή';
$lang['calendar']['decline']='Απόρριψη';

$lang['calendar']['bad_event']='Αυτό το γεγονός δεν υφίσταται πλεόν';

$lang['calendar']['subject']='Θέμα';
$lang['calendar']['status']='Κατάσταση';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Χρήζει ενέργειας';
$lang['calendar']['statuses']['ACCEPTED'] = 'Έχει γίνει αποδοχή';
$lang['calendar']['statuses']['DECLINED'] = 'Έχει απορριφθεί';
$lang['calendar']['statuses']['TENTATIVE'] = 'Υπό δοκιμή';
$lang['calendar']['statuses']['DELEGATED'] = 'Δια εκπροσώπου';
$lang['calendar']['statuses']['COMPLETED'] = 'Ολοκληρωμένο';
$lang['calendar']['statuses']['IN-PROCESS'] = 'Σε εξέλιξη';


$lang['calendar']['accept_mail_subject'] = 'Η πρόσκληση για το \'%s\' έχει γίνει αποδεκτή';
$lang['calendar']['accept_mail_body'] = 'Ο/Η %s δέχτηκε την πρόσκληση σας για:';

$lang['calendar']['decline_mail_subject'] = 'Η πρόσκληση για το \'%s\' απορρίφθηκε';
$lang['calendar']['decline_mail_body'] = 'Ο/Η %s απόρριψε την πρόσκληση σας για:';

$lang['calendar']['location']='Τοποθεσία';
$lang['calendar']['and']='και';

$lang['calendar']['repeats'] = 'Επανάλαμβάνεται κάθε %s';
$lang['calendar']['repeats_at'] = 'Επανάλαμβάνεται κάθε %s στις %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Επανάλαμβάνεται κάθε %s %s στις %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['until']='μέχρι'; 

$lang['calendar']['not_invited']='Δεν προσκληθήκατε σε αυτό το γεγονός. Πιθανώς πρέπει να κάνετε είσοδο σαν διαφορετικός χρήστης.';


$lang['calendar']['accept_title']='Αποδοχή';
$lang['calendar']['accept_confirm']='Ο δημιουργός θα ειδοποιηθεί για την αποδοχή σας σε αυτό το γεγονός';

$lang['calendar']['decline_title']='Απόρριψη';
$lang['calendar']['decline_confirm']='Ο δημιουργός θα ειδοποιηθεί για την απόρριψη σας σε αυτό το γεγονός';

$lang['calendar']['cumulative']='Μη έγκυρος κανόνας επανάληψης. Η επόμενη επανάληψη δεν μπορεί να ξεκινά πριν τελειώση η προηγούμενη.';

$lang['calendar']['already_accepted']='Έχετε ήδη αποδεχτεί αυτό το γεγονός.';

$lang['calendar']['private']='Ιδιωτικό';

$lang['calendar']['import_success']='Εισήχθησαν %s γεγονότα';

$lang['calendar']['printTimeFormat']='Από %s έως %s';
$lang['calendar']['printLocationFormat']=' στην τοποθεσία "%s"';
$lang['calendar']['printPage']='Σελίδα %s από %s';
$lang['calendar']['printList']='Λίστα συναντήσεων';

$lang['calendar']['printAllDaySingle']='Όλη την ημέρα';
$lang['calendar']['printAllDayMultiple']='Όλη την ημέρα από %s έως %s';

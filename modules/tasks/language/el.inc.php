<?php
/* Translator for the Greek Language: Konstantinos Georgakopoulos (kgeorga@uom.gr)*/
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='Εργασίες';
$lang['tasks']['description']='Άρθρωμα για την διαχείριση εργασιών';

$lang['link_type'][12]=$lang['tasks']['task']='Εργασία';
$lang['tasks']['status']='Κατάσταση';


$lang['tasks']['scheduled_call']='Δρομολογημένη κλήση στις %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'Χρήζει ενέργειας';
$lang['tasks']['statuses']['ACCEPTED'] = 'Έχει γίνει αποδοχή';
$lang['tasks']['statuses']['DECLINED'] = 'Έχει απορριφθεί';
$lang['tasks']['statuses']['TENTATIVE'] = 'Υπό δοκιμή';
$lang['tasks']['statuses']['DELEGATED'] = 'Δια εκπροσώπου';
$lang['tasks']['statuses']['COMPLETED'] = 'Ολοκληρωμένο';
$lang['tasks']['statuses']['IN-PROCESS'] = 'Σε εξέλιξη';

$lang['tasks']['import_success']='%s εργασίες εισήχθησαν';

$lang['tasks']['call']='Κλήση';

$lang['tasks']['dueAtdate']='Παράδοση στις %s';
?>
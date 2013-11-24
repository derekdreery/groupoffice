<?php
/* Translator for the Greek Language: Konstantinos Georgakopoulos (kgeorga@uom.gr)*/
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));
$lang['users']['name'] = 'Χρήστες';
$lang['users']['description'] = 'Διαχειριστικό άρθρωμα. Διαχείριση των χρηστών του συστήματος.';

$lang['users']['deletePrimaryAdmin'] = 'Δεν μπορείτε να διαγράψετε τον βασικό διαχειριστή';
$lang['users']['deleteYourself'] = 'Δεν μπορείτε να διαγράψετε τον εαυτό σας';

$lang['link_type'][8]=$us_user = 'Χρήστης';

$lang['users']['error_username']='Υπάρχουν μη έγκυροι χαρακτήρες στο όνομα χρήστη';
$lang['users']['error_username_exists']='Λυπόμαστε αλλά αυτό το όνομα χρήστη υπάρχει ήδη';
$lang['users']['error_email_exists']='Λυπόμαστε αλλά αυτή η διεύθυνση ηλεκτρονικού ταχυδρομείου είναι ήδη εγγεγραμμένη.';
$lang['users']['error_match_pass']='Τα συνθηματικά δεν είναι ίδια';
$lang['users']['error_email']='Εισάγατε μια μη έγκυρη διεύθυνση ηλεκτρονικού ταχυδρομείου';

$lang['users']['imported']='Εισήχθησαν %s χρήστες';
$lang['users']['failed']='Αποτυχία';

$lang['users']['incorrectFormat']='Το αρχείο δεν ήταν στη σωστή μορφοποίηση CSV';
<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Calendario';
$lang['calendar']['description'] = 'Modulo calendario; Ogni utente può aggiungere, modificare o cancellare appuntamenti. Anche gli appuntamenti degli altri utenti possono essere visualizzati e modificati se necessario.';

$lang['calendar']['already_accepted']='Hai già accettato questo evento.';
$lang['calendar']['private']='Privato';

$lang['link_type'][1]='Appuntamento';

$lang['calendar']['groupView'] = 'Visualizza in gruppi';
$lang['calendar']['event']='Evento';
$lang['calendar']['startsAt']='Inizio alle';
$lang['calendar']['endsAt']='fine alle';

$lang['calendar']['exceptionNoCalendarID'] = 'ERRORE: Nessun ID calendario!';
$lang['calendar']['appointment'] = 'Appuntamento: ';
$lang['calendar']['allTogether'] = 'Tutti insieme';

$lang['calendar']['location']='Luogo';

$lang['calendar']['invited']='Sei invitato al seguente evento';
$lang['calendar']['acccept_question']='Accetti questo evento?';

$lang['calendar']['accept']='Accetta';
$lang['calendar']['decline']='Rifiuta';

$lang['calendar']['bad_event']='L\'evento non esiste più';

$lang['calendar']['subject']='Oggetto';
$lang['calendar']['status']='Stato';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Occorre intervento';
$lang['calendar']['statuses']['ACCEPTED'] = 'Accettato';
$lang['calendar']['statuses']['DECLINED'] = 'Rifiutato';
$lang['calendar']['statuses']['TENTATIVE'] = 'Tentative';
$lang['calendar']['statuses']['DELEGATED'] = 'Delegato';
$lang['calendar']['statuses']['COMPLETED'] = 'Completato';
$lang['calendar']['statuses']['IN-PROCESS'] = 'In elaborazione';


$lang['calendar']['accept_mail_subject'] = 'Invito per \'%s\' accettato';
$lang['calendar']['accept_mail_body'] = '%s ha accettato il tuo invito per:';

$lang['calendar']['decline_mail_subject'] = 'Invito per \'%s\' rifiutato';
$lang['calendar']['decline_mail_body'] = '%s ha rifiutato il tuo invito per:';

$lang['calendar']['location']='Luogo';
$lang['calendar']['and']='e';

$lang['calendar']['repeats'] = 'Ripetere ogni %s';
$lang['calendar']['repeats_at'] = 'Ripete ogni %s alle %s';//eg. Ripete ogni primo Lunedì del mese
$lang['calendar']['repeats_at_not_every'] = 'Ripete ogni %s %s alle %s';//eg. Ripete di Lunedì ogni 2 settimane
$lang['calendar']['until']='finchè'; 

$lang['calendar']['not_invited']='Non sei stato invitato a questo evento. Potrebbe essere necessario l\'accesso con un utente diverso.';


$lang['calendar']['accept_title']='Accettato';
$lang['calendar']['accept_confirm']='Al proprietario sarà notificato che hai accettato l\'evento';

$lang['calendar']['decline_title']='Rifiutato';
$lang['calendar']['decline_confirm']='Al proprietario sarà notificato che hai rifiutato l\'evento';

$lang['calendar']['cumulative']='Regola di ricorrenza non valida. Il prossimo evento non può iniziare prima che il precedente sia terminato .';

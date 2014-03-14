<?php
//Polish Translation v1.0
//Author : Robert GOLIAT info@robertgoliat.com  info@it-administrator.org
//Date : January, 20 2009
//Polish Translation v1.1
//Author : Paweł Dmitruk pawel.dmitruk@gmail.com
//Date : September, 04 2010
//Polish Translation v1.2
//Author : rajmund
//Date : January, 26 2011
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));
$lang['email']['name'] = 'Email';
$lang['email']['description'] = 'Moduł E-mail; Prosty klient e-mail przez www. Każdy użytkownik będzie mógł wysyłać, odbierać i przekazywać wiadomości email';
$lang['link_type'][9]='E-mail';
$lang['email']['feedbackNoReciepent'] = 'Nie wprowadzono odbiorcy';
$lang['email']['feedbackSMTPProblem'] = 'Wystąpił problem podczas komunikacji SMTP: ';
$lang['email']['feedbackUnexpectedError'] = 'Wystąpił niespodziewany problem podczas tworzenia e-mail: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Nie można utworzyć folderu';
$lang['email']['feedbackDeleteFolderFailed'] = 'Nie mozna usunąc folderu';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Nie można zasubskrybować folderu';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Nie można wyłączyć subskrypcji folderu';
$lang['email']['feedbackCannotConnect'] = 'Nie mogę połączyć się z %1$s po porcie %3$s<br /><br />Serwer pocztowy zwrócił odpoiwedź: %2$s';
$lang['email']['inbox'] = 'Skrzynka odbiorcza';
$lang['email']['spam']='Spam';
$lang['email']['trash']='Kosz';
$lang['email']['sent']='Wysłane';
$lang['email']['drafts']='Szkice';
$lang['email']['no_subject']='Brak tematu';
$lang['email']['to']='Do';
$lang['email']['from']='Od';
$lang['email']['subject']='Temat';
$lang['email']['no_recipients']='Undisclosed recipients';
$lang['email']['original_message']='--- Wiadomość oryginalna ---';
$lang['email']['attachments']='Załączniki';
$lang['email']['notification_subject']='Przeczytano: %s';
$lang['email']['notification_body']='Twoja wiadomość o temacie "%s" została wyświetlona dnia %s';
$lang['email']['errorGettingMessage']='Nie można pobrać danych z serwera';
$lang['email']['no_recipients_drafts']='Brak odbiorców';
$lang['email']['usage_limit'] = 'Używane %s z %s';
$lang['email']['usage'] = 'Używane %s';
$lang['email']['event']='Termin';
$lang['email']['calendar']='kalendarz';
$lang['email']['quotaError']="Twoja skrzynka jest pełna. Na początek wyczyść folder\'Kosz\'. Jeżeli jest pusty a skrzynka jest nadal pełna, to należy wyłączyć \'Kosz\' aby usunąć wiadomości z innych folderów. Możesz wyłączyć w:\n\nZarządzanie -> Konta -> Podwójne kliknięcie na koncie -> Foldery.";
$lang['email']['draftsDisabled']="Wiadomość nie może zostać zapisana ponieważ folder \'Kopie robocze\' jest wyłączony.<br /><br />Przejdź do E-mail -> Zarządzanie -> konta -> Podwójne kliknięcie na koncie -> Foldery aby skonfigurować.";
$lang['email']['noSaveWithPop3']='Wiadomości nie może zostać zapisana ponieważ konto POP3 tego nie obsługuje.';
$lang['email']['goAlreadyStarted']='Group-Office został już uruchomiony. Edytor wiadomości jest już załadowany w Group-Office. Zamknij to okno i utwórz wiadomość w Group-Office.';
$lang['email']['replyHeader']='Dnia %s, %s o %s %s napisał:';
$lang['email']['alias']='Alias';
$lang['email']['aliases']='Aliasy';
$lang['email']['noUidNext']='Twój serwer email nie wspiera UIDNEXT. Folder \'Kopie robocze\' został wyłączony dla tego konta.';
$lang['email']['disable_trash_folder']='Przeniesienie wiadomości do \'Kosza\' nie powiodło się. Może to być spowodowane brakiem miejsca. Możesz uzyskać wolne miejsce poprzez wyłączenie \'Kosza\' w Zarządzanie -> konta -> Podwójne kliknięcie na koncie -> Foldery';
$lang['email']['error_move_folder']='Nie można przenieść folderu';
$lang['email']['error_getaddrinfo']='Podano nieprawidlowy adres hosta';
$lang['email']['error_authentication']='Nieprawidłowy użytkownik lub hasło';
$lang['email']['error_connection_refused']='Połączenie zostało odrzucone. Sprawdź host i numer portu.';
$lang['email']['iCalendar_event_invitation']='Ta wiadomość zawiera zaproszenie na wydarzenie.';
$lang['email']['iCalendar_event_not_found']='Ta wiadomość zawiera aktualizację wydarzenia, które już nie istnieje.';
$lang['email']['iCalendar_update_available']='Ta wiadomość zawiera aktualizację istniejącego wydarzenia.';
$lang['email']['iCalendar_update_old']='Ta wiadomość zawiera aktualizację wydarzenia, które zostało już przetworzone.';
$lang['email']['iCalendar_event_cancelled']='Ta wiadomość zawiera odwołanie wydarzenia.';
$lang['email']['iCalendar_event_invitation_declined']='Ta wiadomość zawiera zaproszenie na wydarzenie, które już odrzucono.';

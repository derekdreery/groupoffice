<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));
$lang['email']['name'] = '메일';
$lang['email']['description'] = 'Full featured e-mail client. Every user will be able to send and receive emails';

$lang['link_type'][9]='메일';

$lang['email']['feedbackNoReciepent'] = 'You didn\'t enter a reciepent';
$lang['email']['feedbackSMTPProblem'] = 'There was a problem communicating with SMTP: ';
$lang['email']['feedbackUnexpectedError'] = 'There was an unexpected problem building the email: ';
$lang['email']['feedbackCreateFolderFailed'] = 'Failed to create folder';
$lang['email']['feedbackDeleteFolderFailed'] = 'Failed to delete folder';
$lang['email']['feedbackSubscribeFolderFailed'] = 'Failed to subscribe folder';
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'Failed to unsubscribe folder';
$lang['email']['feedbackCannotConnect'] = 'Could not connect to %1$s at port %3$s<br /><br />The mail server returned: %2$s';
$lang['email']['inbox'] = 'Inbox';

$lang['email']['spam']='Spam';
$lang['email']['trash']='Trash';
$lang['email']['sent']='Sent items';
$lang['email']['drafts']='Drafts';

$lang['email']['no_subject']='No subject';
$lang['email']['to']='To';
$lang['email']['from']='From';
$lang['email']['subject']='Subject';
$lang['email']['no_recipients']='Undisclosed recipients';
$lang['email']['original_message']='--- Original message follows ---';
$lang['email']['attachments']='Attachments';

$lang['email']['notification_subject']='Read: %s';
$lang['email']['notification_body']='Your message with subject "%s" was displayed at %s';

$lang['email']['errorGettingMessage']='Could not get message from server';
$lang['email']['no_recipients_drafts']='No recipients';
$lang['email']['usage_limit'] = '%s of %s used';
$lang['email']['usage'] = '%s used';

$lang['email']['event']='Appointment';
$lang['email']['calendar']='calendar';

$lang['email']['quotaError']="Your mailbox is full. Empty your trash folder first. If it is already empty and your mailbox is still full, you must disable the Trash folder to delete messages from other folders. You can disable it at:\n\nSettings -> Accounts -> Double click account -> Folders.";

$lang['email']['draftsDisabled']="Message could not be saved because the 'Drafts' folder is disabled.<br /><br />Go to E-mail -> Administration -> Accounts -> Double click account -> Folders to configure it.";
$lang['email']['noSaveWithPop3']='Message could not be saved because a POP3 account does not support this.';

$lang['email']['goAlreadyStarted']='Group-Office has already been started. The e-mail composer is now loaded in Group-Office. Close this window and compose your message in Group-Office.';

//At Tuesday, 07-04-2009 on 8:58 Group-Office Administrator <test@intermeshdev.nl> wrote:
$lang['email']['replyHeader']='At %s, %s on %s %s wrote:';
$lang['email']['alias']='Alias';
$lang['email']['aliases']='Aliases';
$lang['email']['alias']='Alias';
$lang['email']['aliases']='Aliases';

$lang['email']['noUidNext']='Your mail server does not support UIDNEXT. The \'Drafts\' folder is disabled automatically for this account now.';

$lang['email']['disable_trash_folder']='Moving the e-mail to the trash folder failed. This might be because you are out of disk space. You can only free up space by disabling the trash folder at Administration -> Accounts -> Double click your account -> Folders';

$lang['email']['error_move_folder']='Could not move the folder';

$lang['email']['error_getaddrinfo']='Invalid host address specified';
$lang['email']['error_authentication']='Invalid username or password';
$lang['email']['error_connection_refused']='The connection was refused. Please check the host and port number.';

$lang['email']['iCalendar_event_invitation']='This message contains an invitation to an event.';
$lang['email']['iCalendar_event_not_found']='This message contains an update to an event that doesn\'t exists anymore.';
$lang['email']['iCalendar_update_available']='This message contains an update to an existing event.';
$lang['email']['iCalendar_update_old']='This message containts an event that has already been processed.';
$lang['email']['iCalendar_event_cancelled']='This message contains an event cancellation.';
$lang['email']['iCalendar_event_invitation_declined']='This message contains an invitation to an event you have declined.';
<?php
//Uncomment this line in new translations!
//require($GO_LANGUAGE->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'Calendar';
$lang['calendar']['description'] = 'Calendar module; Every user can add, edit or delete appointments Also appointments from other users can be viewed and if necessary it can be changed.';

$lang['link_type'][1]='Appointment';

$lang['calendar']['groupView'] = 'Group view';
$lang['calendar']['event']='Event';
$lang['calendar']['startsAt']='Starts at';
$lang['calendar']['endsAt']='Ends at';

$lang['calendar']['exceptionNoCalendarID'] = 'FATAL: No calendar ID!';
$lang['calendar']['appointment'] = 'Appointment: ';
$lang['calendar']['allTogether'] = 'All together';

$lang['calendar']['location']='Location';

$lang['calendar']['invited']='You are invited for the following event';
$lang['calendar']['acccept_question']='Do you accept this event?';

$lang['calendar']['accept']='Accept';
$lang['calendar']['decline']='Decline';

$lang['calendar']['bad_event']='The event doesn\'t exist anymore';

$lang['calendar']['subject']='Subject';
$lang['calendar']['status']='Status';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'Needs action';
$lang['calendar']['statuses']['ACCEPTED'] = 'Accepted';
$lang['calendar']['statuses']['DECLINED'] = 'Declined';
$lang['calendar']['statuses']['TENTATIVE'] = 'Tentative';
$lang['calendar']['statuses']['DELEGATED'] = 'Delegated';
$lang['calendar']['statuses']['COMPLETED'] = 'Completed';
$lang['calendar']['statuses']['IN-PROCESS'] = 'In process';


$lang['calendar']['accept_mail_subject'] = 'Invitation for \'%s\' accepted';
$lang['calendar']['accept_mail_body'] = '%s has accepted your invitation for:';

$lang['calendar']['decline_mail_subject'] = 'Invitation for \'%s\' declined';
$lang['calendar']['decline_mail_body'] = '%s has declined your invitation for:';

$lang['calendar']['location']='Location';
$lang['calendar']['and']='and';

$lang['calendar']['repeats'] = 'Repeats every %s';
$lang['calendar']['repeats_at'] = 'Repeats every %s at %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Repeats every %s %s at %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['until']='until'; 

$lang['calendar']['not_invited']='You were not invited to this event. You might need to login as a different user.';


$lang['calendar']['accept_title']='Accepted';
$lang['calendar']['accept_confirm']='The owner will be notified that you accepted the event';

$lang['calendar']['decline_title']='Declined';
$lang['calendar']['decline_confirm']='The owner will be notified that you declined the event';

$lang['calendar']['cumulative']='Invalid recurrence rule. The next occurence may not start before the previous has ended.';

$lang['calendar']['already_accepted']='You already accepted this event.';

$lang['calendar']['private']='Private';

$lang['calendar']['import_success']='%s events were imported';

$lang['calendar']['printTimeFormat']='From %s till %s';
$lang['calendar']['printLocationFormat']=' at location "%s"';
$lang['calendar']['printPage']='Page %s of %s';
$lang['calendar']['printList']='List of appointments';

$lang['calendar']['printAllDaySingle']='All day';
$lang['calendar']['printAllDayMultiple']='All day from %s till %s';

$lang['calendar']['calendars']='Calendars';
$lang['calendar']['resource_mail_subject']='Resource \'%s\' booked'; //%s is resource name
$lang['calendar']['resource_mail_body']='%s has made a booking for the resource \'%s\'. You are the maintainer of this resource. Please open the booking to decline or approve it.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['open_resource']='Open booking';
$lang['calendar']['resource_modified_mail_subject']='Resource \'%s\' booking modified';//%s is resource name
$lang['calendar']['resource_modified_mail_body']='%s has modified a booking for the resource \'%s\'. You are the maintainer of this resource. Please open the booking to decline or approve it.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['your_resource_modified_mail_subject']='Your booking for \'%s\' in status %s is modified';
$lang['calendar']['your_resource_modified_mail_body']='%s has modified your booking for the resource \'%s\'.';
$lang['calendar']['your_resource_accepted_mail_subject'] = 'Booking for \'%s\' is accepted';//%s is resource name, status
$lang['calendar']['your_resource_accepted_mail_body'] = '%s has accepted your booking for the resource \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name
$lang['calendar']['your_resource_declined_mail_subject'] = 'Booking for \'%s\' is declined';//%s is resource name
$lang['calendar']['your_resource_declined_mail_body'] = '%s has declined your booking for the resource \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

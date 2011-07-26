<?php
//Uncomment this line in new translations!
//require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('calendar'));
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
$lang['calendar']['statuses']['CONFIRMED'] = 'Confirmed';


$lang['calendar']['accept_mail_subject'] = 'Invitation for \'%s\' accepted';
$lang['calendar']['accept_mail_body'] = '%s has accepted your invitation for:';

$lang['calendar']['decline_mail_subject'] = 'Invitation for \'%s\' declined';
$lang['calendar']['decline_mail_body'] = '%s has declined your invitation for:';

$lang['calendar']['location']='Location';
$lang['calendar']['and']='and';

$lang['calendar']['repeats'] = 'Repeats every %s';
$lang['calendar']['repeats_at'] = 'Repeats every %s at %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'Repeats every %s %s at %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['repeats_not_every'] = 'Repeats every %s %s';
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

$lang['calendar']['open_resource']='Open booking';

$lang['calendar']['resource_mail_subject']='Resource \'%s\' booked for \'%s\' on \'%s\'';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_mail_body']='%s has made a booking for the resource \'%s\'. You are the maintainer of this resource. Please open the booking to decline or approve it.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['resource_modified_mail_subject']='Resource \'%s\' booking for \'%s\' on \'%s\' modified';//%s is resource name, %s is event name, %s is start date
$lang['calendar']['resource_modified_mail_body']='%s has modified a booking for the resource \'%s\'. You are the maintainer of this resource. Please open the booking to decline or approve it.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['your_resource_modified_mail_subject']='Your booking for \'%s\' on \'%s\' in status \'%s\' is modified';//is resource name, %s is event name, %s is start date
$lang['calendar']['your_resource_modified_mail_body']='%s has modified your booking for the resource \'%s\'.';

$lang['calendar']['your_resource_accepted_mail_subject']='Your booking for \'%s\' on \'%s\' is accepted';//%s is resource name, %s is start date
$lang['calendar']['your_resource_accepted_mail_body']='%s has accepted your booking for the resource \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['your_resource_declined_mail_subject']='Your booking for \'%s\' on \'%s\' is declined';//%s is resource name, %s is start date
$lang['calendar']['your_resource_declined_mail_body']='%s has declined your booking for the resource \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

$lang['calendar']['birthday_name']='Birthday: {NAME}';
$lang['calendar']['birthday_desc']='{NAME} has turned {AGE} today';

$lang['calendar']['unauthorized_participants_write']='You have insufficient permissions to schedule appointments for the following users:<br /><br />{NAMES}<br /><br />You might want to send them an invitation so they can accept and schedule it.';

$lang['calendar']['noCalSelected'] = 'No calendars have been selected for this overview. Select at least one calendar in Administration.';

$lang['calendar']['month_times'][1]='the first';
$lang['calendar']['month_times'][2]='the second';
$lang['calendar']['month_times'][3]='the third';
$lang['calendar']['month_times'][4]='the fourth';
$lang['calendar']['month_times'][5]='the fifth';

$lang['calendar']['rightClickToCopy']='Right click to copy link location';

$lang['calendar']['invitation']='Invitation';
$lang['calendar']['invitation_update']='Updated invitation';
$lang['calendar']['cancellation']='Cancellation';

$lang['calendar']['non_selected'] = 'in non-selected calendar';

$lang['calendar']['linkIfCalendarNotSupported']='Only use the links below if your mail client does not support calendaring functions.';
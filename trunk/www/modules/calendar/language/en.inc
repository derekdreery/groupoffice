<?php
//Uncomment this line in new translations!
//require_once($GO_LANGUAGE->get_fallback_language_file('calendar'));

$sc_title = "Title";
$sc_description = "Description";
$sc_participants = "Invitees";

$sc_location = "Location";

$month_times[0] = "First";
$month_times[1] = "Second";
$month_times[2] = "Third";
$month_times[3] = "Fourth";

$sc_new_app = "New event";
$sc_calendars = "Calendars";
$sc_invited = 'You are invited to join the event described below.';

$sc_types1[0] = 'No recurrence';
$sc_types1[1] = 'days';
$sc_types1[2] = 'weeks';
$sc_types1[3] = 'months by date';
$sc_types1[4] = 'months by day';
$sc_types1[5] = 'years';


$sc_types[0] = 'Once';
$sc_types[1] = 'Daily';
$sc_types[2] = 'Weekly';
$sc_types[3] = 'Monthly by date';
$sc_types[4] = 'Monthly by day';
$sc_types[5] = 'Yearly';


$sc_start_at = 'Starting at';
$sc_end_at = 'Ending at';
$sc_start_time = 'Start time';
$sc_end_time = 'End time';

$sc_at_days = 'At days';
$sc_of_month = 'of the month.';

$sc_cycle_start = 'Cycle starts at';
$sc_cycle_end = 'Recur until';
$sc_accept_question = 'Do you wish to accept and schedule this event?';

$sc_accept = 'accept';
$sc_decline = 'decline';

$sc_notime = 'Time is not applicable';
$sc_noend = 'No ending date.';

$sc_never_happens = 'Invalid input! This event never happens.';
$sc_start_later = 'Invalid input!! The starting time is later then the ending time.';

$sc_add_self = 'Manual input';

$sc_select_calendar = 'Select the calendar to schedule this event in';
$sc_accept_confirm = 'The event is accepted and scheduled.';
$sc_no_calendars = 'No valid calendar found to schedule this event in.';
$sc_bad_event = 'No valid event found. The event is probably deleted.';
$sc_accept_title = 'Accept Event';

$sc_decline_confirm = 'The event is declined.';
$sc_decline_title = 'Decline event';

$sc_status = 'Status';
$sc_not_responded = 'Not responded';
$sc_accepted = 'Accepted';
$sc_declined = 'Declined';
$sc_no_participants = 'No participants';

$sc_person = 'Person';
$sc_owner = 'Owner';
$sc_type = 'Type';
$sc_subscribed = 'Subscribed';

$sc_subscribe = 'Subscribe';
$sc_new_calendar = 'New calendar';
$sc_no_calendar = 'No calendar was found. Please create one now or let Group-Office automatically create one for you.';
$sc_day_view = 'Day&nbsp;view';
$sc_week_view = 'Week&nbsp;view';
$sc_list_view = 'List&nbsp;view';
$sc_put_in = 'Put in';
$sc_inherit_permissions ='Inherit permissions from';
$sc_no_inherit = 'Don\'t inherit';

$sc_select_calendar_please = 'Select at least one calendar please.';
$sc_events = 'Events';

$sc_from_calendar = 'from calendar';
$sc_enitrely = 'Entirely';
$sc_delete_event = 'Delete event';
$sc_delete_pre = 'Do you want to delete the event';
$sc_delete_suf = 'entirely or just from this calendar?';

$sc_refresh = 'Refresh';
$sc_reminder = 'Reminder';
$sc_mins = 'mins';
$sc_hour = 'hour';
$sc_hours = 'hours';
$sc_day = 'day';
$sc_days = 'days';
$sc_week = 'week';
$sc_weeks = 'weeks';
$sc_reminder_set_to_early = "The reminder cannnot be set to appear before the previous occurence. Please shorten the reminder time or lengthen the recurring period.";
$sc_cycle_start_later = 'The cycle starting date is later or equal then the ending date.';


$cmdGenerate = 'Generate';
$sc_preferences = 'Preferences';
$sc_holidays = 'Holidays';

$strNoHolidays = 'No holidays in database';
$strUnknown = 'Unknown';
$strYear = 'Year';
$strDeleteHolidaysPrefix = 'Are you sure you want to delete all holiday records of the year ';
$strDeleteHolidaysSuffix = '?';
$strReplaceHolidays = 'Are you sure you want to overwrite all existent holiday records?';
$sc_holidays_text1 = 'Please click on \''.$cmdGenerate.'\' to compute the holiday records for the selected year.';
$sc_holidays_text2 = 'Please click on \''.$GLOBALS['cmdApply'].'\' to store all computed holiday records in the database.';

//translation required below this
$sc_client = 'Client';
$sc_no_contact = 'None';

$sc_background = 'Background';

$sc_colors['red'] = 'Red';
$sc_colors['green'] = 'Green';
$sc_colors['blue'] = 'Blue';
$sc_colors['pink'] = 'Pink';
$sc_colors['orange'] = 'Orange';
$sc_colors['brown'] = 'Brown';
$sc_colors['yellow'] = 'Yellow';
$sc_colors['grey'] = 'Grey';
$sc_colors['purple'] = 'Purple';
$sc_colors['white']= 'White';

$sc_show_hours = 'Show hours from';
$sc_to = 'to';

$sc_today = 'Today';

$sc_recur_every = 'Recur every';
$sc_months = 'months';

$sc_private_event = 'Private event';
$sc_calendar_exists = 'A calendar with this name already exists';
//translate
$sc_month = 'Month';
$sc_view = 'View';
$sc_calendar = 'Calendar';
$sc_default = 'Default';
$sc_month_view = 'Month&nbsp;view';

//text changed, needs new translation
$sc_holidays_no_file_prefix = 'The holidays couldn\'t be generated, because the file containing holidays information for the ';
$sc_holidays_no_file_suffix = ' language is either empty or doesn\'t exist.';

$sc_no_events = 'No events found';

//2.03
$cal_export = 'Export';
$cal_import = 'Import';
$cal_please_wait = 'Please wait...';
$cal_import_text = 'Select an iCal (*.ics) file and click at \'Ok\'';
$cal_filename = 'File';
$cal_import_success = 'Data imported';

//2.04
$cal_add_todo = 'New task';
$cal_todos = 'Tasks';

$cal_hide_completed = 'Hide completed';
$cal_show_all = 'Show all tasks';
$cal_show_own = 'Show own tasks';
$cal_show_responsible = 'Show reponsible tasks';

$cal_priority = 'Priority';
$cal_priority_values[0] = 'Low';
$cal_priority_values[1] = 'Normal';
$cal_priority_values[2] = 'High';

$cal_status = 'Status';
$cal_canceled = 'Cancelled';
$cal_completed = 'Completed at';
$cal_percent_completed = '% completed';

$cal_before_task_start = 'Before the task starts';
$cal_before_task_due = 'Before the task should be finnished';
$cal_responsible = 'Responsible';
$cal_due_at = 'Due at';
$cal_no_todos= 'No tasks found';
$cal_resend_invitation = 'Resend invitation';

//2.05



$cal_view = 'View';
$cal_views = 'Views';
$sc_view_exists = 'A view with this name already exists';
$cal_new_view = 'New view';
$cal_event_calendars = 'Calendar(s)';

//2.06
$cal_view_merged = 'merged';
$cal_view_emerged = 'demerged';

//2.07
$cal_display = 'Display';
$sc_new_task = "New task";
$sc_all_day_events = 'all day events';
$sc_all_day_event = 'all day event';
$sc_usual_events = 'events';
$sc_usual_event = 'event';

//2.08
$sc_recur_section = 'Recurrence';
$sc_options_section = 'Options';
$sc_close = 'Close';

$cal_count_single = 'event';
$cal_count_multiple = 'events';

$cal_until = 'until';
$cal_forever = 'forever';

//2.09
$cal_merged_overview = 'Merged overview';
$cal_own_availability = 'Own availability';

//2.11
$cal_no_file = 'No file was recieved';
$cms_no_calendars_in_view = 'This view can\'t be displayed because there are no calendars in this view. Click at \'Views\' to manage views.';
$cal_no_reminder = 'No reminder';

//2.13
$cal_refresh_rate = 'Refreshing rate';
$sc_seconds = 'seconds (Leave empty for no refresh)';
$cal_default_reminder = 'Default reminder';


//2.14
$cal_event = 'Event';
$cal_todo = 'Todo';
$cal_participants_write = 'Participants may modify the event';

$cal_everybody_write = 'Everybody can modify the event'; //modified string from 2.05 this became the
$cal_everybody_read = 'Only I can modify the event';//modified string from 2.05 

$cal_default_permissions = 'Default permissions';
$cal_show_todos = 'Show tasks in the calendar';

$cal_edit_series_or_single = 'Edit this instance only or the entire series?';
$cal_single = 'Instance';
$cal_series = 'Entire series';

$cal_legend = 'Legend';
$cal_multiple_calendars = 'Multiple calendars';

$cal_cumulative = 'Invalid input! The event would last longer then the recurrence interval.';

$cal_weekview = 'The week view shows';
$cal_mo_to_fr = 'Monday to Friday';
$cal_mo_to_su = 'Monday to Sunday';

$cal_batchcalendars = 'Create calendars for all users';
$cal_create_all_calendars = 'Create calendars for all users or grant user groups permission';

$cal_no_views = 'No views found';

$cal_my_cals = 'My calendars';

$cal_event_colors_override = 'Use the manually set colors for events';

//2.15
$cal_groups = 'Groups';
$cal_new_group = 'New group';
$cal_group_exists = 'This group name already exists. Please choose another name.';
$cal_no_calendars = 'No calendars were found in this group';
$cal_group = 'Group';
$cal_resources = 'Resources';

$cal_event_mail_subject = 'Event \'%s\' created in your calendar';//%s is event name
$cal_event_mail_body = '%s has made appointment \'%s\' in your calendar named \'%s\'.'; //irst %s is the name of the person who created the event. Second is the event name, third is the calendar name
$cal_event_mail_open = 'Open appointment';
$cal_add_custom_field = 'Add custom field';

$cal_event_modified_mail_subject = 'Event \'%s\' has been modified in your calendar';//%s is event name
$cal_event_modified_mail_body = '%s has modified appointment \'%s\' in your calendar named \'%s\'.'; //First %s is the name of the person who created the event. Second is the event name, third is the calendar name

$cal_your_event_modified_mail_subject = 'The event \'%s\' created by you has been modified';//%s is event name
$cal_your_event_modified_mail_body = '%s has modified appointment \'%s\' that was created by you.'; //First %s is the name of the person who created the event. Second is the event name.
$cal_no_custom_fields = 'No custom fields are defined';
$cal_add_field = 'Add field';


$cal_resource_mail_subject = 'Resource \'%s\' booked'; //%s is resource name
$cal_resource_mail_body = '%s has made a booking for the resource \'%s\'. You are the maintainer of this resource. Please open the booking to decline or approve it.'; //First %s is the name of the person who created the event. Second is the calendar name
$cal_open_resource = 'Open booking';

$cal_resource_modified_mail_subject = 'Resource \'%s\' booking modified';//%s is resource name
$cal_resource_modified_mail_body = '%s has modified a booking for the resource \'%s\'. You are the maintainer of this resource. Please open the booking to decline or approve it.'; //First %s is the name of the person who created the event. Second is the calendar name

$cal_your_resource_modified_mail_subject = 'Your booking for \'%s\' in status %s is modified';//%s is resource name, status
$cal_your_resource_modified_mail_body = '%s has modified your booking for the resource \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name


$cal_accept_mail_subject = 'Invitation for \'%s\' accepted';
$cal_accept_mail_body = '%s has accepted your invitation for \'%s\'';

$cal_decline_mail_subject = 'Invitation for \'%s\' declined';
$cal_decline_mail_body = '%s has declined your invitation for \'%s\'';

$cal_statuses['NEEDS-ACTION'] = 'Needs action';
$cal_statuses['ACCEPTED'] = 'Accepted';
$cal_statuses['DECLINED'] = 'Declined';
$cal_statuses['TENTATIVE'] = 'Tentative';
$cal_statuses['DELEGATED'] = 'Delegated';
$cal_statuses['COMPLETED'] = 'Completed';
$cal_statuses['IN-PROCESS'] = 'In process';

$cal_booking = 'Booking for \'%s\'';//%s is resource name

$cal_group_admins = 'Admins';
$cal_no_admins = 'No admins defined';
$cal_add_admin = 'Add admin';

$cal_scale='Scale';

$cal_show_completed = 'Show completed tasks';

$cal_resource = 'Resource';
$cal_grid_view = 'Grid view';

$cal_field_types['checkbox'] = 'Checkbox';
$cal_field_types['text'] = 'Text';
$cal_field_types['textarea'] = 'Textarea';
$cal_field_types['date'] = 'Date field';

$cal_conflict = 'This event conflicts with another event or resource booking. Click at \'Ok\' or \'Apply\' again to ignore or click at 
\'Check availability\' to find a suitable time.';

$cal_select_calendar = 'Select calendar';
$cal_check_conflicts = 'Warn me about conlicts when I create an event';
$cal_email_changes = 'Send e-mail when somebody makes changes to my calendar';
$cal_shared_calendars = 'Shared calendars';

$cal_admin = 'Administration';
$cal_resource_groups = 'Resource groups';
$cal_open_calendar = 'Open another calendar';

$cal_email_reminders='Send me event reminders by e-mail';

$cal_favourites = 'Favorites';

$cal_your_resource_accepted_mail_subject = 'Booking for \'%s\' is accepted';//%s is resource name, status
$cal_your_resource_accepted_mail_body = '%s has accepted your booking for the resource \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

$cal_your_resource_declined_mail_subject = 'Booking for \'%s\' is declined';//%s is resource name
$cal_your_resource_declined_mail_body = '%s has declined your booking for the resource \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name

$cal_resource_deleted_mail_subject = 'Booking for \'%s\' is deleted';//%s is resource name, status

$cal_repeats_at = 'Repeats every %s at %s';//eg. Repeats every month at the first monday
$cal_repeats_at_not_every = 'Repeats every %s %s at %s';//eg. Repeats every 2 weeks at monday 

$cal_repeats = 'Repeats every %s';

$cal_and='and';
$sc_months='months';
$sc_month='month';
$sc_year='year';
$sc_years='years';

$cal_another_resource = 'The resource has been changed to %s';

$cal_background_colors = 'Background colors';

$cal_weekday = 'Weekday';
$cal_background_color='Background color';
$cal_background = 'Background';

$cal_show_busy='Show as busy';

$cal_subject='Subject';
$cal_dismiss_all = 'Dismiss all';
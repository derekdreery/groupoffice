<?php
//Uncomment this line in new translations!
//Bangla Translation by Shubhra Prakash Paul <shuvro.paul@gmail.com>
require($GO_LANGUAGE->get_fallback_language_file('email'));
$lang['email']['name'] = 'ই-মেইল ';
$lang['email']['description'] = ' সম্পূর্ণ বৈশিষ্ট্যমন্ডিত ই-মেইল ক্লায়েন্ট  । প্রত্যকে ব্যবহারকারি  ই-মেইল পাঠাতে এবং পেতে সমর্থ হবেন ।';

$lang['link_type'][9]='ই- মেইল';

$lang['email']['feedbackNoReciepent'] = ' আপনি কোন প্রাপক নির্ধারণ করেন নি ';
$lang['email']['feedbackSMTPProblem'] = 'SMTP সংযোগ স্থাপনের সময় সমস্যা দেখা দিয়েছেঃ ';
$lang['email']['feedbackUnexpectedError'] = ' ই-মেইল তৈরি করতে অনাকাঙ্খিত একটা সমস্যা দেখা দিয়েছেঃ  ';
$lang['email']['feedbackCreateFolderFailed'] = ' ফোল্ডার তৈরি করতে অসমর্থ ';
$lang['email']['feedbackDeleteFolderFailed'] = ' ফোল্ডার মুছে ফেলতে অসমর্থ ';
$lang['email']['feedbackSubscribeFolderFailed'] = ' ফোল্ডারে গ্রাহক হতে অসমর্থ ';
$lang['email']['feedbackUnsubscribeFolderFailed'] = ' ফোল্ডার থেকে গ্রাহকত্ব অপসারণ করতে অসমর্থ ';
$lang['email']['feedbackCannotConnect'] = '%3$s পোর্টে   %1$s সংযুক্ত হতে অসমর্থ <br /><br /> মেইল সার্ভারের ফিরতি বার্তাঃ  %2$s';
$lang['email']['inbox'] = ' অন্তর্বাক্স ';

$lang['email']['spam']='অযাচিত ';
$lang['email']['trash']=' আবর্জনাস্তুপ';
$lang['email']['sent']='পাঠানো উপকরণসমূহ ';
$lang['email']['drafts']='খসড়া ';

$lang['email']['no_subject']=' কোন বিষয় নেই ';
$lang['email']['to']=' প্রাপক ';
$lang['email']['from']='প্রেরক ';
$lang['email']['subject']='বিষয ';
$lang['email']['no_recipients']=' পরিচিতবিহীন  প্রাপকবৃন্দ ';
$lang['email']['original_message']='--- প্রকৃত বার্তাটি নিম্নরূপ ---';
$lang['email']['attachments']=' সংযুক্তিসমূহ ';

$lang['email']['notification_subject']='পঠিতঃ  %s';
$lang['email']['notification_body']=' "%s" নামের বিষয়সহ বার্তাসমূহ   %s এ প্রদর্শিত হচ্ছে ';

$lang['email']['errorGettingMessage']=' সার্ভার থেকে বার্তা প্রাপ্তিতে সমর্থ  নয় ';
$lang['email']['no_recipients_drafts']=' কোন প্রাপক নেই ';
$lang['email']['usage_limit'] = '%s, %s এর মধ্যে ব্যবহৃত হচ্ছে ';
$lang['email']['usage'] = '%s ব্যবহৃত হচ্ছে ';

$lang['email']['event']=' সাক্ষাৎকার ';
$lang['email']['calendar']=' দিনপঞ্জী ';

$lang['email']['quotaError']=" আপনার বার্তাবাক্সটি পরিপূর্ণ। প্রথমেই আবর্জনাস্তুপ ফোল্ডারটি খালি করুন । যদি এটি খালিই থাকে আর বার্তাবাক্সটি যদি এখনও পূর্ণই থাকে , তবে  বার্তা মুছে ফেলতে আবর্জনাস্তুপ ফোল্ডার নিষ্ক্রিয় করা আবশ্যক । নিষ্ক্রিয় করার উপায়ঃ \n\nসেটিংস -> একাউন্ট -> একাউন্টে ডাবলক্লিক করুন -> ফোল্ডারগুলো ।";

$lang['email']['draftsDisabled']=" 'খসড়া '   ফোল্ডারটি নিষ্ক্রিয় থাকায়  বার্তা সংরক্ষণ করা সম্ভব হলো না । <br /><br/>  ই-মেইল ->  প্রশাসন -> একাউন্ট ->  একাউন্টে ডবলক্লিক করুন -> ফোল্ডারগুলো  তে গিয়ে এটি কনফিগার করতে পারেন ।";
$lang['email']['noSaveWithPop3']='Message could not be saved because a POP3 account does not support this.';

$lang['email']['goAlreadyStarted']='Group-Office has already been started. The e-mail composer is now loaded in Group-Office. Close this window and compose your message in Group-Office.';

//On Tuesday, 07-04-2009 on 8:58 Group-Office Administrator <test@intermeshdev.nl> wrote:
$lang['email']['replyHeader']='নির্ঘন্ট  %s, %s সময়ে  %s %s লিখেছেনঃ ';
$lang['email']['alias']=' ছদ্মনাম ';
$lang['email']['aliases']='ছদ্মনামসমূহ ';
$lang['email']['alias']='ছদ্মনাম ';
$lang['email']['aliases']='ছদ্মনামসমূহ';

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

$lang['email']['untilDateError']='I tried to process the following "Until Date", but the processing stopped because an error occurred';
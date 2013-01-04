<?php
//Uncomment this line in new translations!
//Bangla translation by Shubhra Prakash Paul <shuvro.paul@gmail.com>
require($GO_LANGUAGE->get_fallback_language_file('users'));
$lang['users']['name'] = ' সদস্যবৃন্দ';
$lang['users']['description'] = ' প্রশাসন মডিউল । সিস্টেমের ব্যবহারকারি ব্যবস্থাপনা করে।';

$lang['users']['deletePrimaryAdmin'] = ' আপনি প্রাথমিক প্রশাসককে মুছে ফেলতে পারবেন না।';
$lang['users']['deleteYourself'] = ' আপনি নিজেকে মুছে ফেলতে পারবেন না ';

$lang['link_type'][8]=$us_user = 'ব্যবহারকারি';

$lang['users']['error_username']=' সদস্যনামে অননুমোদিত বর্ণ বা অক্ষর প্রদান করেছেন ';
$lang['users']['error_username_exists']='দুঃখিত, এই নামের আর একজন ব্যবহারকারি বিদ্যমান ';
$lang['users']['error_email_exists']='দুঃখিত, এই ই-মেইল ঠিকানাটি ইতোমধ্যেই এখানে নিবন্ধিত হয়েছে।';
$lang['users']['error_match_pass']=' কূটশব্দদ্বয় পরষ্পরের সাথে মেলে না ';
$lang['users']['error_email']=' আপনার প্রদত্ত ই-মেইল ঠিকানাটি অনুমোদিত নয় ।';
$lang['users']['error_user']=' ব্যবহারকারি তৈরি করা সম্ভব হলো না ।';

$lang['users']['imported']='আমদানি করা  %s জন্য ব্যবহারকারি ';
$lang['users']['failed']='ব্যর্থ ';

$lang['users']['incorrectFormat']=' ফাইলটি সঠিক  CSV ফর্ম্যাটের নয় ';

$lang['users']['register_email_subject']='Group-Office এ আপনার একাউন্টের বিশদ বিবরণ ';
$lang['users']['register_email_body']='আপনার জন্য   {url} এ Group-Office একাউন্ট তৈরি করা হয়েছে 
আপনার প্রবেশের বিভিন্ন তথ্য নিম্নরূপঃ 

ব্যবহারকারি নামঃ  {username}
কূটশব্দঃ  {password}';


$lang['users']['max_users_reached']='The maximum number of users has been reached for this system.';
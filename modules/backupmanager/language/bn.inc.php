<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('backupmanager'));

$lang['backupmanager']['name']=' ব্যাক-আপ ব্যবস্থাপক ';
$lang['backupmanager']['description']=' ব্যাক-আপ ক্রন জব  কনফিগার কর ';
$lang['backupmanager']['save_error']=' সেটিংস সংরক্ষণ করতে সমস্যা দেখা দিয়েছে ';
$lang['backupmanager']['empty_key']='কী টি ফাঁকা ';
$lang['backupmanager']['connection_error']=' সা্র্ভারে  সংযুক্ত হতে অসমর্থ ';
$lang['backupmanager']['no_mysql_config']='Group-Office mysql কনফিগ ফাইল খুঁজে পেতে সমর্থ নয় ।  সম্পূর্ণ ডাটাবেজটির ব্যাক-আপ নিতে ফাইলটি ব্যবহৃত হবে ।  নিম্নলিখিত কন্টেন্টসহ  /etc/groupoffice/ ফোল্ডারে backupmanager.inc.php নামের ফাইল যোগ করেও  আপনি এই কাজ টি করতে পারবেনঃ
    <br /><br />&lt;?php<br />
    $bm_config[\'mysql_user\'] = \'\';<br />
    $bm_config[\'mysql_pass\'] = \'\';<br />
    ?><br /><br />
        এই ফাইলটি ছাড়াও ব্যাক-আপ তৈরি করা যাবে, কিন্তু সেটা ডাটাবেজ থেকে নয় ।';
$lang['backupmanager']['target_does_not_exist']=' উদ্দিষ্ট গন্তব্য ডিরেক্টরিটি অস্তিত্বহীন ! ';
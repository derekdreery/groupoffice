<?php
//Bangla translation by Shubhra Prakash Paul <shuvro.paul@gmail.com>
require($GO_LANGUAGE->get_fallback_language_file('smime'));

$lang['smime']['name']='SMIME সমর্থন ';
$lang['smime']['description']=' ই-মেইল মডিউলের সাথে  SMIME স্বাক্ষর করা এবং  সংকেতায়ন কর । ';

$lang['smime']['noPublicCertForEncrypt']="%s এর জন্য সার্বজনীন  সনদপত্র না থাকায় বার্তা সংকেতায়িত করা সম্ভব হলো না । সার্বজনীন কী টি আমদানি করতে প্রাপকের স্বাক্ষরিত কোন একটি বার্তা খুলে  দেখুন, স্বাক্ষর যাচাই করুন ।";
$lang['smime']['noPrivateKeyForDecrypt']=" এই বার্তাটি সংকেতায়িত এবং আপনার কাছে একান্ত কী টি না থাকায় এই বার্তাটি অসংকেতায়ন করা সম্ভব হলো না ।";

$lang['smime']['badGoLogin']="Group-Office কূটশব্দটি সঠিক নয় ।";
$lang['smime']['smime_pass_matches_go']="আপনার SMIME কী এর কূটশব্দটি Group-Office কূটশব্দের হুবহু অনুরূপ । নিরাপত্তার  খাতিরে এটি নিষিদ্ধ ! ";
$lang['smime']['smime_pass_empty']="আপনার SMIME কী তে কোন কূটশব্দ নেই ।নিরাপত্তার  খাতিরে এটি নিষিদ্ধ ! ";

$lang['smime']['invalidCert']=" সনদপত্রটি অবৈধ ! ";
$lang['smime']['validCert']=" বৈধ সনদপত্র ";
$lang['smime']['certEmailMismatch']=" সনদপত্রটি বৈধ কিন্তু   প্রেরকের ই-মেইলের সাথে সনদপত্রের ই-মেইলটি মেলে না ।";

$lang['smime']['decryptionFailed']='এই বার্তার জন্য অংসকেতায়ন ব্যর্থ ।';

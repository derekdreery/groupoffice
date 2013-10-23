<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));
$lang['email']['name'] = 'Email';
$lang['email']['description'] = '電子郵件模組; 基本的web郵件客戶端。所有人都可以發送、回覆和轉發郵件。';

$lang['link_type'][9]='郵件';

$lang['email']['feedbackNoReciepent'] = '未輸入收件者';
$lang['email']['feedbackSMTPProblem'] = 'SMTP通信出錯: ';
$lang['email']['feedbackUnexpectedError'] = '建立郵件出現一個未知錯誤: ';
$lang['email']['feedbackCreateFolderFailed'] = '建立資料夾失敗';
$lang['email']['feedbackSubscribeFolderFailed'] = '訂閱資料夾失敗Failed to subscribe folder';
$lang['email']['feedbackUnsubscribeFolderFailed'] = '註銷資料夾失敗Failed to unsubscribe folder';
$lang['email']['feedbackCannotConnect'] = '不能連接到 %1$s<br /><br />郵件服務器返回: %2$s';
$lang['email']['inbox'] = '收件匣';

$lang['email']['spam']='垃圾郵件';
$lang['email']['trash']='刪除';
$lang['email']['sent']='發送郵件';
$lang['email']['drafts']='草稿';

$lang['email']['no_subject']='沒有主旨';
$lang['email']['to']='收件者';
$lang['email']['from']='寄件者';
$lang['email']['subject']='主旨';
$lang['email']['no_recipients']='未知的收件人';
$lang['email']['original_message']='--- 原始郵件信息 ---';
$lang['email']['attachments']='附件';

$lang['email']['notification_subject']='讀: %s';
$lang['email']['notification_body']='郵件主旨 "%s" 顯示在 %s';

$lang['email']['errorGettingMessage']='無法從服務器接收郵件';
$lang['email']['no_recipients_drafts']='沒有收件者';

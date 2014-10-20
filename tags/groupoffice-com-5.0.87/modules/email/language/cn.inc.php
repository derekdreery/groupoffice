<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));
$lang['email']['name'] = 'Email';
$lang['email']['description'] = 'Email 模块; 基本的web邮件客户端。所有人都可以发送、回复和转发邮件。';

$lang['link_type'][9]='E-mail';

$lang['email']['feedbackNoReciepent'] = '未输入收件人';
$lang['email']['feedbackSMTPProblem'] = 'SMTP通信出错: ';
$lang['email']['feedbackUnexpectedError'] = '创建邮件出现一个未知错误: ';
$lang['email']['feedbackCreateFolderFailed'] = '新建文件夹失败';
$lang['email']['feedbackSubscribeFolderFailed'] = '订阅文件夹失败Failed to subscribe folder';
$lang['email']['feedbackUnsubscribeFolderFailed'] = '注销文件夹失败Failed to unsubscribe folder';
$lang['email']['feedbackCannotConnect'] = '不能连接到 %1$s<br /><br />邮件服务器返回: %2$s';
$lang['email']['inbox'] = '收件箱';

$lang['email']['spam']='垃圾Spam';
$lang['email']['trash']='垃圾';
$lang['email']['sent']='发送邮件';
$lang['email']['drafts']='草稿';

$lang['email']['no_subject']='没有主题';
$lang['email']['to']='To';
$lang['email']['from']='From';
$lang['email']['subject']='主题';
$lang['email']['no_recipients']='未知的收件人';
$lang['email']['original_message']='--- 原始邮件信息 ---';
$lang['email']['attachments']='附件';

$lang['email']['notification_subject']='读: %s';
$lang['email']['notification_body']='邮件主题 "%s" 显示在 %s';

$lang['email']['errorGettingMessage']='无法从服务器接收邮件';
$lang['email']['no_recipients_drafts']='没有收件人';

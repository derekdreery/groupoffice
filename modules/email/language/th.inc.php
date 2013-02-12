<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('email'));
$lang['email']['name'] = 'อีเมล';
$lang['email']['description'] = 'บริการอีเมล ผู้ใช้งานสามารถส่งอีเมลและรับอีเมล';//Small webbased e-mail client.Every user will be able to sent.receive and forward emais

$lang['link_type'][9]='อีเมล';

$lang['email']['feedbackNoReciepent'] = 'ยังไม่ได้กรอกรายการที่ได้รับ';//You didn\'t enter a reciepent
$lang['email']['feedbackSMTPProblem'] = ' เกิดปัญหาในการเชื่อมโยงกับ SMTP: ';//There was a problem communicating with
$lang['email']['feedbackUnexpectedError'] = 'เกิดข้อผิดพลาดในการสร้างอีเมล: ';//There was an unexpected problem building the email
$lang['email']['feedbackCreateFolderFailed'] = 'เกิดข้อผิดพลาดในการสร้างโฟลเดอร์';//Failed to create folder
$lang['email']['feedbackDeleteFolderFailed'] = 'เกิดข้อผิดพลาดในการลบโฟลเดอร์';//Failed to delete folder
$lang['email']['feedbackSubscribeFolderFailed'] = 'เกิดข้อผิดพลาดในการสร้างโฟลเดอร์';//Failed to subscribe folder
$lang['email']['feedbackUnsubscribeFolderFailed'] = 'เกิดข้อผิดพลาดในการลบโฟลเดอร์';//Failed to unsubscribe folder
$lang['email']['feedbackCannotConnect'] = 'ไม่สามารถเชื่อมโยงกับ %1$s พอร์ต %3$s<br /><br /เมล์เซิร์ฟเวอร์ส่งค่าคืน: %2$s';//Could not connect to %1$s at port %3$s<br /><br />The mail server returned: %2$s
$lang['email']['inbox'] = 'กล่องจดหมาย';

$lang['email']['spam']='สแปม';
$lang['email']['trash']='ถังขยะ';
$lang['email']['sent']='ส่งจดหมาย';
$lang['email']['drafts']='ร่างจดหมาย';

$lang['email']['no_subject']='ไม่มีหัวข้อ';
$lang['email']['to']='ถึง';
$lang['email']['from']='จาก';
$lang['email']['subject']='หัวข้อ';
$lang['email']['no_recipients']='ไม่เปิดเผยผู้รับ';
$lang['email']['original_message']='--- ข้อความต้นฉบับดังนี้ ---';//Original message follows
$lang['email']['attachments']='แนบไฟล์';//Attachments

$lang['email']['notification_subject']='อ่าน: %s';
$lang['email']['notification_body']='ข้อความอีเมลกับหัวข้อ "%s" โดยแสดงแล้ว %s';//Your message with subject "%s" was displayed at %s

$lang['email']['errorGettingMessage']='ไม่สามารถรับข้อมูลจากเซิร์ฟเวอร์ได้';//Could not get message from server
$lang['email']['no_recipients_drafts']='ไม่มีผู้รับ';//No recipients
$lang['email']['usage_limit'] = '%s จาก %s ทั้งหมด';
$lang['email']['usage'] = '%s ทั้งหมด';

$lang['email']['event']='ตารางการนัดหมาย';
$lang['email']['calendar']='ปฏิทิน';

$lang['email']['quotaError']="เมื่อกล่องจดหมายของคุณเต็ม.และได้มีการใช้งานถังขยะ.เมื่อทำการลบข้อมูลในถังขยะแล้วกล่องจดหมายก็ยังไม่มีพิ้นที่เพียงพอ ,ต้องการปิดการใช้งานถังขยะเพื่อให้สามารถลบข้อมูลได้ .สามารถทำได้ดังนี้โดยไปที่ :\n\การตั้งค่า -> บัญชีผู้ใช้งาน  ->ดับเบิ้ล คลิกที่ ชื่อผู้ใช้งาน  -> เลือกโฟลเดอร์ -> รายการถังขยะ เลือกคลิกเครื่องหมาย กากบาท เพื่อทำการปิดการใช้งานถังขยะ.";//Your mailbox is full.If it is already empty and your mailbox is still full. you must disable the Trash folder to delete messages from other folders.You can disable it at

$lang['email']['draftsDisabled']="ไม่สามารถบันทึกข้อความได้เนื่องจากโฟลเดอร์ 'Drafts' ได้ถูกปิดการใช้งาน.<br /><br />สามารถยกเลิกการปิดการใช้งานได้ที่  การตั้งค่า -> บัญชีผู้ใช้งาน -> ดับเบิ้ลคลิกชื่อผู้ใช้งาน  -> โฟลเดอร์ -> รายการร่างจดหมาย  ลิสต์รายการด้านหลังให้เลือก รายการ INBOX:ร่างจดหมาย.";//Message could not be saved because the 'Drafts' folder is disabled
$lang['email']['noSaveWithPop3']='ไม่สามารถทำการบันทึกข้อความนี้ได้. เนื่องจากบัญชีผู้ใช้งานนี้ไม่สนับสนุนการทำงาน POP3 .';//Message could not be saved because a POP3 account does not support this

$lang['email']['goAlreadyStarted']='Group-Office was al gestart. Het e-mailscherm wordt nu geladen in Group-Office. Sluit dit venster en stel uw bericht op in Group-Office.';

//At Tuesday, 07-04-2009 on 8:58 Group-Office Administrator <test@intermeshdev.nl> wrote:
$lang['email']['replyHeader']='At %s, %s on %s %s wrote:';
$lang['email']['alias']='Alias';
$lang['email']['aliases']='Aliases';
$lang['email']['alias']='Alias';
$lang['email']['aliases']='Aliases';

$lang['email']['noUidNext']='เมล์เซิร์ฟเวอร์ของคุณไม่รองรับการ UIDNEXT \'กล่องร่างจดหมาย\' ';

$lang['email']['disable_trash_folder']='เกิดข้อผิดพลาดในการย้ายอีเมล์ไปยังถังขยะ. อาจจะเกิดจากพื้นที่ไม่พอ คุณสามารถติดต่อผู้ดูแลระบบเพื่อแก้ไข.หรือคุณสามารถยกเลิกการใช้งานโฟลเดอร์ถังขยะโดยไปที่  กำหนดค่าระบบ -> บัญชีผู้ใช้งาน -> ดับเบิ้ลคลิ๊กชื่อบัญชีของท่าน -> โฟลเดอร์';
$lang['email']['error_move_folder']='ไม่สามารถย้ายไปยังโฟลเดอร์ได้';
$lang['email']['error_getaddrinfo']='ระบุที่อยู่โฮสต์ไม่ถูกต้อง';
$lang['email']['error_authentication']='ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
$lang['email']['error_connection_refused']='การเชื่อมต่อผิดพลาด กรุณาตรวจสอบโฮสต์และหมายเลขพ็อต';

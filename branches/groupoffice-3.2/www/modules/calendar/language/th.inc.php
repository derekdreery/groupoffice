<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('calendar'));
$lang['calendar']['name'] = 'ปฏิทิน';
$lang['calendar']['description'] = 'ผู้ใช้สามารถเพิ่ม ,แก้ไข และลบตารางการนัดหมาย โดยผู้ใช้ท่านอื่นสามารถทำการ แก้ไข เมื่อมีการกำหนดสิทธิให้สามารถแก้ไขได้.';//Every user can add. edit or delete appointments Also appointment. from other users can be viewed and if necessary it can be changed

$lang['link_type'][1]='ตารางการนัดหมาย';

$lang['calendar']['groupView'] = 'ตรวจสอบกลุ่ม';
$lang['calendar']['event']='เรื่อง';
$lang['calendar']['startsAt']='เริ่มต้น';
$lang['calendar']['endsAt']='สิ้นสุด';

$lang['calendar']['exceptionNoCalendarID'] = 'ผิดพลาด:ไม่มีหมายเลขในปฏิทิน!';
$lang['calendar']['appointment'] = 'ตารางการนัดหมาย: ';
$lang['calendar']['allTogether'] = 'ทั้งหมด';

$lang['calendar']['location']='สถานที่';

$lang['calendar']['invited']='ได้รับการตอบรับจากการทำรายการ';//You are invited for the following event
$lang['calendar']['acccept_question']='ยืนยันการทำรายการข้างต้น?';

$lang['calendar']['accept']='ยอมรับ';
$lang['calendar']['decline']='ปฏิเสธ';

$lang['calendar']['bad_event']='ไม่มีรายการนี้';

$lang['calendar']['subject']='หัวเรื่อง';
$lang['calendar']['status']='สถานะ';



$lang['calendar']['statuses']['NEEDS-ACTION'] = 'เร่งดำเนินการ';//NEEDS-ACTION
$lang['calendar']['statuses']['ACCEPTED'] = 'ตอบรับ';
$lang['calendar']['statuses']['DECLINED'] = 'ไม่ตอบรับ';//DECLINED
$lang['calendar']['statuses']['TENTATIVE'] = 'ทำการทดสอบ';//TENTATIVE
$lang['calendar']['statuses']['DELEGATED'] = 'ลบ';
$lang['calendar']['statuses']['COMPLETED'] = 'เสร็จสิ้น';
$lang['calendar']['statuses']['IN-PROCESS'] = 'กำลังดำเนินการ';


$lang['calendar']['accept_mail_subject'] = 'การเชิญ \'%s\'ได้รับการตอบรับ';
$lang['calendar']['accept_mail_body'] = '%s ได้ตอบรับการตอบรับ:';//%s has accepted your invitation for:

$lang['calendar']['decline_mail_subject'] = 'การตอบรับ \'%s\'ถูกปฏิเสธ';//Invitation for \'%s\' declined
$lang['calendar']['decline_mail_body'] = '%s ได้ปฏิเสธการตอบรับ ';//has declined your invitation for:

$lang['calendar']['location']='สถานที่';//location
$lang['calendar']['and']='และ'; //and

$lang['calendar']['repeats'] = 'แสดงซ้ำ %s';//Repeats every
$lang['calendar']['repeats_at'] = 'แสดงซ้ำ  %s ถึง  %s';//eg. Repeats every month at the first monday
$lang['calendar']['repeats_at_not_every'] = 'แสดงซ้ำ %s %s ถึง %s';//eg. Repeats every 2 weeks at monday
$lang['calendar']['until']='จนถึง'; 

$lang['calendar']['not_invited']='ไม่ได้รับสิทธิ์ในรายการนี้. ต้องการทำรายการนี้กรุณาเข้าใช้งานชื่อผู้ใช้งานอื่น.';//You were not invited to this event. You might need to login as a different user


$lang['calendar']['accept_title']='ตกลง';
$lang['calendar']['accept_confirm']='ผู้ส่งจะได้รับแจ้งการตอบรับจากคุณ';//The owner will be notified that you accepted the event
$lang['calendar']['decline_title']='ยกเลิก';
$lang['calendar']['decline_confirm']='ผู้ส่งจะได้รับแจ้งการปฏิเสธจากคุณ';//The owner will be notified that you declined the event

$lang['calendar']['cumulative']='การแก้ไขปัญหาการตอบกลับที่ไม่ถูกต้อง. อาจยังไม่แสดงผล . ทำการบันทึกและเข้าสู่ระบบใหม่ ';//Invalid recurrence rule. The next occurence may not start before the previous has ended

$lang['calendar']['already_accepted']='เสร็จสิ้นรายการ.';//You already accepted this event

$lang['calendar']['private']='ส่วนบุคคล';

$lang['calendar']['import_success']='%s กำลังทำรายการนำเข้า';

$lang['calendar']['printTimeFormat']='From %s till %s';
$lang['calendar']['printLocationFormat']=' at location "%s"';
$lang['calendar']['printPage']='Page %s of %s';
$lang['calendar']['printList']='List of appointments';

$lang['calendar']['printAllDaySingle']='All day';
$lang['calendar']['printAllDayMultiple']='All day from %s till %s';


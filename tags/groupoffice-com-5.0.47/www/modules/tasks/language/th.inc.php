<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']='งาน';
$lang['tasks']['description']='แสดงรายการงานที่ต้องปฏิบัติ เพื่อความสะดวกในการช่วยเตือนและการจัดลำดับงานที่ต้องปฏิบัติเพื่อให้การทำงานที่ได้ประสิทธิภาพยิ่งขึ้น';

$lang['link_type'][12]=$lang['tasks']['task']='งาน';
$lang['tasks']['status']='สถานนะ';


$lang['tasks']['scheduled_call']='การเรียกใช้ %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'เร่งดำเนินการ';//Needs action
$lang['tasks']['statuses']['ACCEPTED'] = 'ตอบรับ';
$lang['tasks']['statuses']['DECLINED'] = 'ไม่ตอบรับ';
$lang['tasks']['statuses']['TENTATIVE'] = 'ทำการทดสอบ';
$lang['tasks']['statuses']['DELEGATED'] = 'ลบ';
$lang['tasks']['statuses']['COMPLETED'] = 'เสร็จสิ้น';
$lang['tasks']['statuses']['IN-PROCESS'] = 'กำลังดำเนินการ';

$lang['tasks']['import_success']='%s งานที่นำเข้า';

$lang['tasks']['call']='เรียกใช้';

$lang['tasks']['dueAtdate']='Due at %s';

$lang['tasks']['list']='รายการงาน';
$lang['tasks']['tasklistChanged']="* รายการงานเปลี่ยนจาก '%s' ถึง '%s'";
$lang['tasks']['statusChanged']="* สถานะถูกเปลี่ยนจาก '%s' ถึง '%s'";

?>
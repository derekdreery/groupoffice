<?php
//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('users'));
$lang['users']['name'] = 'ผู้ใช้งาน';
$lang['users']['description'] = 'ระบบผู้ใช้งาน: การจัดการระบบผู้ใช้งาน.';

$lang['users']['deletePrimaryAdmin'] = 'ไม่ได้รับสิทธิ์ให้ลบผู้ดูแลระบบหลัก';//You can\t delete the primary administrator
$lang['users']['deleteYourself'] = 'ไม่ได้รับสิทธิ์ให้ลบตัวเอง';//You can\'t delete yourself

$lang['link_type'][8]=$us_user = 'ผู้ใช้งาน';

$lang['users']['error_username']='เกิดข้อผิดพลาด.ตรวจสอบชื่อผู้ใช้งาน';//You have invalid characters in the username
$lang['users']['error_username_exists']='มีชื่อผู้ใช้นี้แล้ว';//Sorry, that username already exists
$lang['users']['error_email_exists']='มีการลงทะเบียนด้วยชื่ออีเมลนี้แล้ว.';//Sorry, that e-mail address is already registered here
$lang['users']['error_match_pass']='เกิดข้อผิดพลาด.ตรวจสอบรหัสผ่าน';//The passwords didn\'t match
$lang['users']['error_email']='เกิดข้อผิดพลาด.ที่อยู่อีเมลไม่ถูกต้อง';//You entered an invalid e-mail address

$lang['users']['imported']='นำเข้าผู้ใช้งาน  %s คน';
$lang['users']['failed']='การดำเนินการล้มเหลว';

$lang['users']['incorrectFormat']='รูปแบบไฟล์ไม่ถูกต้อง.ไฟล์ต้องอยู่ในรูปแบบนามสกุล CSV';//File was not in correct CSV format

$lang['users']['error_user']='ไม่สามารถเพิ่มผู้ใช้นี้ได้';
$lang['users']['register_email_subject']='ข้อมูลบัญชีผู้ใช้ {product_name} ของคุณ';
$lang['users']['register_email_body']='{product_name} บัญชีของคุณถูกสร้างที่ {url}';
$lang['users']['max_users_reached']='จำนวนผู้ใช้สูงสุดที่ระบบนี้รองรับได้';

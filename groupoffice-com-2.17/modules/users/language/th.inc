<?php
//Uncomment this line in new translations!
require_once($GO_LANGUAGE->get_fallback_language_file('users'));

$strLogins = "ล็อกอิน";
$strRegistrationDate = "วันลงทะเบียน";
$strUsers = "ผู้ใช้";
$user_profile = "ข้อมูลผู้ใช้";
$ac_login_info = "ข้อมูลการล็อกอิน";
$ac_registration_time = "เวลาลงทะเบียน";
$ac_lastlogin = "นามสกุล";
$ac_logins = "ล็อกอินทั้งหมด";
$strMaxOf = "การลงทะเบียนสูงสุดได้";
$strUsername = "ยูเซอร์เนม";
$delete_fail = "คุณไม่สามารถลบได้ด้วยตัวคุณเองและซุปเปอร์ยูเซอร์";
$admin_new_user = "สร้างผู้ใช้ใหม่";

$admin_modules = 'โมดูล';
$admin_module_access = 'ผู้ใช้ผู้นี้ได้ทำการติดต่อผ่านโมดูล:';

$admin_password = 'พาสเวิร์ส';
$admin_confirm_password = 'ยืนยันพาสเวิร์ด';
$admin_change_password = 'เปลี่ยนพาสเวิร์ด';
$admin_pass_match = 'พาสเวิร์ดไม่ตรงกัน. โปรดใส่อีกครั้ง';

//registration
$registration_personal_info = "ข้อมูลบุคคล";
$registration_account_info = "ข้อมูลบัญชี";
$registartion_username_text = "ทำาการใส่ชื่อยูเซอร์และพาสเวิรด์เพื่อทำการเข้าไซท์นี้";

$registration_question_text=<<<registration_question_text
ใส่คำถามลับที่รู้คำตอบ เราจะถามเขาเมื่อเขาลืมพาสเวิร์ดนั้น เมื่อใส่คำตอบถูกต้อง จะทำการส่งอีเมลล์ ไปยังอีเมล์ที่ได้สมัครในครั้งแรกโดยมี ยูเซอร์เดิมและพาสเวิร์ดใหม่
registration_question_text;

$registration_text=<<<registration_text
Fill out this form and click on 'Ok' to register. The fields marked with a * are required.<br />
The information you provide is only for use at Group-Office so we will not provide them to third parties.
registration_text;

$registartion_authcode_text = "Enter an authorisation code. The authorisationcode is a code that other users will need to add this user to thier contactlist and view your details.";
$registartion_confirm_authcode = "Confirm authorisation code";
$registration_visibility = "Make this user visible to all users.";
$registration_success = "<h1>Congratulations!</h1>You are successfully registered and a welcome message has been sent to:";
$registration_failure = "An error occured while trying to register your information. Please try again.";
$registration_mail_subject = "ยินดีต้อนรับเข้าสู้กรุ๊ปออฟฟิศ";
$registration_folder_error = "การสร้างโฟลเดอร์ล้มเหลว";
$registration_title = "ลงทะเบียนกรุ๊ปเมเนเจอร์";

$registration_email = "สร้างอีเมล์แอดเดรสสำหรับยูเซอร์ที่กรุ๊ปออฟฟิส.";
$registration_samba = "ไฟลล์/ปรินต์  เซอร์เวอร์";
$registration_email_error = "An error occured while creating your e-mail account. Contact us for support.";
$strPasswordConfirm = "ยืนยันรหัสผ่าน";
$strPassword = "รหัสผ่าน";

$reg_company_info = "ข้อมูลทางบริษัท";

$max_user_limit = "จำนวนยูเซอร์สูงสุดที่จำกัดไว้";
$max_users_text = "ขออภัยคุณไม่สามรถลงทะเบียนยูเซอร์ได้มากเกินไปติดต่อทางเรา หากคุณต้องการเพิ่มหมายเลขยูเซอร์";
$ml_sent_items = "ส่งอุปกรณ์";
$ml_spam = "สแปม";
$ac_samba_user = "ยูเซอร์มีการติดต่อไฟลล์เซอร์เวอร์และปรินเซอร์เวอร์";
$ml_email_only = "มีการเรียกร้องอีเมล์ใหม่";
$register_new_user = 'สร้างซับสคริปใหม่ในกรุ๊ปออฟฟิส';
$register_thanks = '<h2>ขอบคุณ</h2>ซับสคริปจะประมวลผลเป็นไปได้ว่าคุณรับคำเตือน .';
$register_accept = 'เพิ่มผู้ใช้งาน';
$register_addressbook = 'เพิ่มไปยังสมุดที่อยู่';
$register_title = 'ซับสคริป';
$registration_mail_body=<<<registration_mail_body
ถึง %sex% %last_name%,

คุณได้ทำการลงทะเบียนกับ Group-Manager. เรียบร้อยแล้ว
เราหวังว่าคุณจะสนุกกับบริการของเรา

คุณสามารถเข้าสู่ระบบด้วยข้อมูลด้านล่างนี้ :

ผู้ใช้: %username%
รหัสผ่าน: %password%

Click on the link below to login:

registration_mail_body;

//1.98
$reg_language = 'ภาษา';
$reg_theme = 'ทีม';

//2.05
$admin_use = 'ผู้ใช้';
$admin_module = 'โมดูล';
$admin_manage = 'บริหาร';

$admin_groups = 'กลุ่มผู้ใช้';
$admin_groups_user = 'User is member of the following user groups';

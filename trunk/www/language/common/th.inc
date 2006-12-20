<?php
//Uncomment this line in new translations!
require_once($GO_LANGUAGE->get_fallback_base_language_file('common'));

$charset = 'UTF-8';
//main language menu file register login and other non logged in pages
//common
$cmdLogin = "ล็อกอิน";
$cmdSave = "บันทึก";
$cmdBack = "กลับ";
$cmdCancel = "ยกเลิก";
$cmdClose = "ปิด";
$cmdOk = "ตกลง";
$cmdDelete = "ลบ";
$cmdNext = "ถัดไป";
$cmdPrevious = "ก่อนหน้า";
$cmdContinue = "ต่อไป";
$cmdUpdate = "ซิงโครไนซ์";
$cmdApply = "ปรับปรุง";
$cmdYes = "ใช่";
$cmdNo = "ไม่";
$cmdAdd = 'เพิ่ม';
$cmdEdit = 'แก้ไข';
$cmdInvertSelection = 'อินเวิร์สเซกชัน';

$strEdit = "แก้ไข";
$strThis = 'นี้';
$strItems = 'อุปกรณ์';
$strDescription = 'บรรยาย';

$strName = "ชื่อ";
$strUser = "ผู้ใช้งาน";
$strUsername = "ชื่อผู้ใช้งาน";
$strPassword = "รหัสผ่าน";
$strAuthcode = "รหัสผ่าน";
$strCountry = "ประเทศ";
$strState = "จังหวัด";
$strCity = "อำเภอ";
$strZip = "รหัสไปรษณีย์";
$strAddress = "ถนน";
$strEmail = "อีเมล์";
$strPhone = "โทรศัพท์";
$strWorkphone = "โทรศัพท์ที่ทำงาน";
$strCellular = "มือถือ";
$strCompany = "บริษัท";
$strDepartment = "ภาค/คณะ";
$strFunction = "ฟังก์ชัน";
$strQuestion = "คำถามลับ";
$strAnswer = "คำตอบ";
$strFax = "โทรสาร";
$strWorkFax = "โทรสารที่ทำงาน";
$strHomepage = "โฮมเพจ";
$strWorkAddress = "ที่อยู่ที่ทำงาน";
$strWorkZip = "รหัสไปรษณีย์";
$strWorkCountry = "ประเทศ";
$strWorkState = "จังหวัด";
$strWorkCity = "อำเภอ";
$strPhoto = "ภาพ";
$strComments = "แนะนำติชม";
$strProperties = 'คุณสมบัติ';
$strDeleteItem = "ลบ";
$strDate = "วัน";
$strSaveSuccess = "บันทกข้อมูลเรียบร้อย";
$strSaveError = "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
$strDataError = "เกิดข้อผิดพลาดในการอ่านข้อมูล";
$strDeleteError = "ไม่ควรลบข้อมูล";
$strAclError = "การติดต่อเกิดความผิดพลาด";
$strDateError = "คุณใส่วันไม่ถูกต้องตามชนิด";
$strAccessDenied = "การติดต่อถูกปฏิเสธ";
$AccessDenied_text = "คุณไม่มีสิทธิในการเข้าเซคชั่นนี้.";
$invalid_chars = "ตัวอักษรนี้ไม่อนุญาติให้ใช้งานโปรดตรวจสอบอีกคร้ง";
$strGroups = "กลุ่ม";
$strOwner = "กลุ่มเดียวกัน";
$strDeletePrefix = "คุรต้องการที่จะลบใช่หรือไม่";
$strDeleteSuffix = "?";
//menu
$menu_logout = "ออกจากระบบ";
$menu_applications = "แอพลิเคชัน";
$menu_help = "ช่วยเหลือ";
$menu_about = "เกี่ยวข้อง";
$menu_documentation = "การนำเสนอ";
$menu_configuration = "ตั้งค่าระบบ";
$menu_groups = "จัดการกลุ่มของผู้ใช้งาน";
$menu_accounts = "ปรับปรุงข้อมูลส่วนตัว";
$menu_preferences = "ปรับเปลี่ยนส่วนต่างๆ";
$menu_search_discussions = "วิจารณ์";
$menu_search_files = "ไฟลล์&nbsp;and&nbsp;กล่อง";
$menu_search_web = "เว็บ";
$menu_filetypes = "ชนิดของไฟล์";
$menu_discussions = "กระดานวิจารณ์";

$menu_users = "บริหารผู้ใช้";
$menu_new = "สร้าง";
$menu_message = "ข้อความ";
$menu_contact = "ติดต่อ";
$menu_event = "เหตุการณ์";
$menu_bookmark = "ที่คั่น";
$menu_topics = "หัวข้อ";
$menu_support = "บริการ";
$menu_projects = "โปรเจ็ค";
$menu_sysinfo = "ดูข้อมูลของระบบ";
$menu_admin = 'เมนูผู้ดูแลระบบ';
$menu_modules = 'เพิ่ม/ลบ โมดูล';
$menu_manual = 'คู่มือ';

//class validate
$error_required = "คุณไม่ได้กรอกขอ้มูลดังที่กำหนดไว้.";
$error_match_auth = "รหัสไม่ตรงกัน";
$error_match_pass = "รหัสผ่านไม่ตรงกัน";
$error_min_length = "ตัวอักษรต่ำสุดในช่อง:";
$error_max_length = "ตัวอักษรสูงสุดในช่อง:";
$error_email = "อีเมลของคุณใช้ไม่ได้.";
$error_email_exists = "อีเมล์ของคุณได้ใช้ไปแล้ว";
$error_username_exists = "ขออภัยชื่อผู้ใช้งานของคุณมีผู้อื่นเลือกใช้แล้ว";
$errors_in_form = "เกิดข้อผิดพลาดในฟร์อมนี้กรุณาตรวจสอลแล้วลองอีกครั้ง";
$error_missing_field = "คุณไม่ได้กรอกขอ้มูลดังที่กำหนดไว้.";

//acl control
$acl_control_auth_groups = "กำหนดสิทธิสำหรับกลุ่ม";
$acl_control_auth_users = "กำหนดสิทธิสำหรับผู้ใช้งาน";
$acl_control_no_contacts = "คุณไม่มีสมาชิกให้เลือก<br /> ใช้สมุดจดที่อยู่เพื่อเลือกสมาชิกที่คุณต้องการติดต่อ";

$strShowProfile = "ดูข้อมูลทั่วไป";
$strShowGroup = "ดูข้อมูลกลุ่ม";
$strProcessingTime = "ดำเนินการจากหน้านี้";
$strLoggedInAs = "เข้าระบบด้วย : ";
$strAt = " at ";
$strEmailTo = "ส่งอีเมล์ไปยัง : ";
$strProtectedUser = "ป้องกันผู้ใช้งาน";

$http_not_found = "ไม่พบเอกสาร";
$http_not_found_text = "เอกสารที่ต้องการไม่พบ. อาจเป็นไปได้ว่าพิมพ์ URL ผิด.";
$http_error = "เกิดข้อผิดพลาดภายในเซิร์ฟเวอร์";
$http_bad_request = "การเรียกใช้งานล้มเหลว";
$strAddContact = "เพิ่มในผู้ติดต่อ";

$strReadRights = "สิทธิ์ในการอ่าน";
$strWriteRights = "สิทธิ์ในการเขียน";
$strPermissions = 'กำหนดสิทธ์';

$days[0]="อา.";
$days[1]="จ.";
$days[2]="อ.";
$days[3]="พ.";
$days[4]="พฤ.";
$days[5]="ศ";
$days[6]="ส.";

$months[0]="มกราคม";
$months[1]="กุมภาพันธ์";
$months[2]="มีนาคม";
$months[3]="เมษายน";
$months[4]="พฤษภาคม";
$months[5]="มิถุนายน";
$months[6]="กรกฎาคม";
$months[7]="สิหาคม";
$months[8]="กันยายน";
$months[9]="ตุลาคม";
$months[10]="พฤษจิกายน";
$months[11]="ธันวาคม";

$full_days[0] = "อาทิตย์";
$full_days[1] = "จันทร์";
$full_days[2] = "อังคาร";
$full_days[3] = "พุธ";
$full_days[4] = "พฤหัส";
$full_days[5]= "ศุกร์";
$full_days[6] = "เสาร์";
$strLoadTime = 'สร้างใน';
$strDescription = 'คำอธิบาย';



$lang_modules['addressbook'] = "สมุดจดที่อยู่";
$cmdShowAll = 'แสดงทั้งหมด';
$cmdSearch = 'ค้นหา';

//translation required
$cmdYesToAll = 'ใช่ทั้งหมด';
$cmdNoToAll = 'ไม่ทั้งหมด';

$short_months[0]="ม.ค.";
$short_months[1]="ก.พ.";
$short_months[2]="มี.ค.";
$short_months[3]="เม.ย.";
$short_months[4]="พ.ค.";
$short_months[5]="มิ.ย.";
$short_months[6]="ก.ค.";
$short_months[7]="ส.ค.";
$short_months[8]="ก.ย.";
$short_months[9]="ต.ค.";
$short_months[10]="พ.ย.";
$short_months[11]="ธ.ค.";

$strToday = 'วันนี้';

$cmdPrint = 'พิมพ์';
$strFirstName = 'ชื่อ';
$strLastName = 'นามสกุล';
$strInitials = 'เริ่มต้น';
$strSex = 'เพศ';
$strBirthday = 'วันเกิด';
$strSexes['M'] = 'ชาย';
$strSexes['F'] = 'หญิง';

$strTitle = 'ชื่อเรื่อง';
$strBeginning = 'เริ่มต้น';
$strMiddleName = 'ชื่อกลาง';
$sir_madam['M'] = 'ครับ';
$sir_madam['F'] = 'มาดาม';
$strModifiedAt = 'แก้ไขที่';
$strCreatedAt = 'สร้างที่';

$htmlarea_language_name = 'th';
$strLocalization = 'ตำแหน่ง';


//look up the language in controls/jscalendar/lang
$jscalendar_language_name = 'calendar-en.js';


//2.01 translations

//look up language in 'classes/phpmailer/language'
$php_mailer_lang = 'th';
$strEmailFail = 'ส่งอีเมล์ล้มเหลว';

//for people that want to use ktmlarea
$ktml_language = 'thailand';

$ml_host_unreachable = 'การเช็คอีเมล์อัตโนมัติไม่ได้ถูกใช้งาน';
$strRegion = 'ขอบเขต';

//2.05
$cmdReset = 'รีเซท';
$strMyName = 'ชื่อ';

//2.06
$strDeleteRow = 'คุณต้องการลบแถวนี้?';
$strAddressNo = 'บ้านเลขที่';
$strWorkAddressNo = 'บ้านเลขที่(ทำงาน)';

$strPostAddress = 'ที่อยู่';
$strPostAddressNo = 'ที่อยู่';
$strPostCity = 'อำเภอ';
$strPostState = 'จังหวัด';
$strPostCountry = 'ประเทศ';
$strPostZip = 'รหัสไปรษณีย์';
$strVisitAddress = 'เข้าไปยังที่อยู่';
$strPostAddress = 'แจ้งที่อยู่';

$login_register = 'คลิกที่นี่เพื่อสมัครสมาชิก';
//modules
$lang_modules['scheduler'] = 'ตารางเวลา';
$lang_modules['calendar'] = "ปฏิทิน";
$lang_modules['filesystem'] = 'ไฟล์';
$lang_modules['email'] = 'อีเมล์';
$lang_modules['projects'] = 'โปรเจ็ค';
$lang_modules['search'] = 'ค้นหา';
$lang_modules['contacts'] = "สมุดจดที่อยู่";
$lang_modules['cms'] = "เว็บไซท์";
$lang_modules['notes'] = "บันทึก";
$lang_modules['bookmarks'] = "เว็บโปรด";
$lang_modules['summary'] = "ข้อมุลโดยรวม";
$lang_modules['todos'] = "งาน";
$lang_modules['opentts'] = "ช่วยเหลือ";

$cmdVcard = 'วีการ์ด';

//2.08
$alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','ก','ข','ฃ','ค','ฅ','ฆ','ง','จ','ฉ','ช','ซ','ฌ','ญ','ฎ','ฏ','ฐ','ฑ','ฒ','ณ','ด','ต','ถ','ท','ธ','น','บ','ป','ผ','ฝ','พ','ฟ','ภ','ม','ย','ร','ล','ว','ศ','ษ','ส','ห','ฬ','อ','ฮ','โ','เ','ใ','ไ','แ','ฤ');
                                                                        
$strContact = 'ติดต่อ';
$strRelatedTo = 'Related to';
$strProject = 'โปรเจ็ค';

$strNewProject = 'โปรเจ็คใหม่';
$strNewEvent = 'เหตุการณ์ใหม่';
$strNewTodo = 'งานใหม่';
$strNewNote = 'โน๊ตใหม่';

$strActivities = 'กิจกรรม';
$strFile = 'ไฟล์';

//2.10
$cmdCopy = 'คัดลอก';
$strProjects = 'โปรเจ็ค';
$strFiles = 'ไฟล์';
$cmdSaveNew = 'บันทึก & ใหม่';
$strAvailability = 'ให้บริการ';
$strCheckAvailabilityFor = 'เช็คการให้บริการสำหรับ';

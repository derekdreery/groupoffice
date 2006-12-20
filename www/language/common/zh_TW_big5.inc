<?php
require_once($GO_LANGUAGE->get_fallback_base_language_file('common'));

$charset = 'BIG5';

$lang_modules['scheduler'] = "行事曆";
$lang_modules['calendar'] = "行事曆";
$lang_modules['filesystem'] = '檔案';
$lang_modules['email'] = '電子郵件';
$lang_modules['projects'] = '專案';
$lang_modules['search'] = "搜尋引擎";
$lang_modules['contacts'] = "連絡人";
$lang_modules['cms'] = "網站";
$lang_modules['addressbook'] = "通訊錄";

//main language menu file register login and other non logged in pages
//common
$cmdLogin = "登入";
$cmdSave = "儲存";
$cmdBack = "上一步";
$cmdCancel = "取消";
$cmdClose = "關閉";
$cmdOk = "確定";
$cmdDelete = "移除";
$cmdNext = "下一個";
$cmdPrevious = "上一個";
$cmdContinue = "繼續";
$cmdUpdate = "同步";
$cmdApply = "套用";
$cmdYes = "是";
$cmdNo = "否";
$cmdAdd = '新增';
$cmdEdit = '修改';
$cmdInvertSelection = '反向選擇';

$strEdit = "修改";
$strThis = '這';
$strItems = '項目';
$strDescription = "內容";

$strName = "名稱";
$strUser = "使用者";
$strUsername = "使用者名稱";
$strPassword = "密碼";
$strAuthcode = "授權密碼";
$strCountry = "國家";
$strState = "州郡";
$strCity = "城市";
$strZip = "郵遞區號";
$strAddress = "街道地址";
$strEmail = "電子郵件";
$strPhone = "電話";
$strWorkphone = "工作電話";
$strCellular = "流動電話";
$strCompany = "公司";
$strDepartment = "部門";
$strFunction = "職能";
$strQuestion = "秘密問題";
$strAnswer = "答案";
$strFax = "傳真";
$strWorkFax = "工作傳真";
$strHomepage = "網頁";
$strWorkAddress = "工作地址";
$strWorkZip = "工作郵遞區號";
$strWorkCountry = "工作國家";
$strWorkState = "工作州郡";
$strWorkCity = "工作城市";
$strPhoto = "相片";
$strComments = "備註";

$strProperties = '內容';

$strDeleteItem = "移除";

$strDate = "日期";

$strSaveSuccess = "資料已成功儲存";
$strSaveError = "資料儲存錯誤，請重試";
$strDataError = "資料讀取錯誤，請重新載入這頁";
$strDeleteError = "資料不能移除";
$strAclError = "存取控制錯誤";
$strDateError = "你/妳所輸入的日期錯誤";


$strAccessDenied = "拒絕存取";
$AccessDenied_text = "你/妳沒有被授權觀看這部份或執行這個動作<br />另外的使用者已限制你/妳存取這些資料";

$invalid_chars = "不容許以下的字元";

$strGroups = "群組";
$strOwner = "擁有者";

$strDeletePrefix = "你/妳是否確定移除";
$strDeleteSuffix = "?";

//menu
$menu_logout = "登出";
$menu_applications = "程式集";
$menu_help = "幫助";
$menu_about = "關於";
$menu_documentation = "文件";
$menu_configuration = "設定";
$menu_groups = "使用者群組";
$menu_accounts = "你/妳的帳號";
$menu_preferences = "喜好設定";
$menu_bookmarks = "書籤";
$menu_bookmarks_manage = "修改書籤";
$menu_add_bookmark = "新增書籤";
$menu_add_current = "新增&nbsp;當前&nbsp;這頁";

$menu_invite = "邀請&nbsp;使用者";

$menu_search_discussions = "討論";
$menu_search_files = "檔案&nbsp;和&nbsp;資料夾";
$menu_search_web = "這個網站";
$menu_scheduler = "行事曆";
$menu_filetypes = "檔案類型";
$menu_discussions = "討論板";

$menu_users = "使用者";
$menu_start = "首頁";
$menu_reboot = "重新啟動伺服器";
$menu_halt = "關閉伺服器";
$menu_reboot_text = "警告！所有Group-Office服務將會暫時停止。你/妳是否確定要繼續？";
$menu_halt_text = "警告！所有Group-Office服務將會中止。你/妳是否確定要繼續？";
$menu_new = "新增";
$menu_message = "訊息";
$menu_contact = "聯絡人";
$menu_event = "事件";
$menu_bookmark = "書籤";
$menu_topics = "主題";
$menu_support = "支援";
$menu_projects = "專案";
$menu_sysinfo = "系統資訊";
$menu_admin = '系統管理員';
$menu_modules = '模組';
$menu_manual = '使用手冊';

//class validate
$error_required = "你/妳沒有指定以下必須的欄位";
$error_match_auth = "授權密碼不符";
$error_match_pass = "密碼不符";
$error_min_length = "以下欄位的最小字節";
$error_max_length = "以下欄位的最大字節";
$error_email = "你/妳所指定的電郵地址無效";
$error_email_exists = "你/妳所輸入的電郵地址已存在";
$error_username_exists = "對不起，你/妳所輸入的使用者名稱已存在，請使用其他名稱。";
$errors_in_form = "表格有錯誤，請改正後重試。";
$error_missing_field = "你/妳沒有填寫所有必須的欄位";

//acl control
$acl_control_auth_groups = "已授權的群組";
$acl_control_auth_users = "已授權的使用者";
$acl_control_no_contacts = "你/妳沒有聯絡人成員可供選擇<br />使用通訊錄新增成員到你/妳的聯絡人";

$strShowProfile = "檢視設定檔";
$strShowGroup = "檢視群組成員";
$strProcessingTime = "處理這頁所需時間";
$strLoggedInAs = "已登入者: ";
$strAt = " 在 ";
$strEmailTo = "傳送電郵訊息到：";
$strProtectedUser = "已受保護的使用者";

$http_not_found = "找不到文件";
$http_not_found_text = "找不到要求的文件或輸入的網址錯誤";
$http_error = "內部伺服器錯誤";
$http_bad_request = "錯誤的要求";
$strAddContact = "新增到聯絡人";

$strReadRights = "讀取權限";
$strWriteRights = "寫入權限";
$strPermissions = '權限';

$editor_bold = "粗體";
$editor_italic = "斜體";
$editor_underline = "底線";
$editor_left = "靠左";
$editor_right = "靠右";
$editor_center = "置中";
$editor_ord_list = "順序符號";
$editor_bul_list = "項目符號";
$editor_txt_color = "文字色彩";
$editor_back_color = "背景色彩";
$editor_hyperlink = "超連結";
$editor_image = "影像";
$editor_rule = "水平線";

$editor_very_small = "非常小";
$editor_small = "小";
$editor_medium = "中等";
$editor_large = "大";
$editor_larger = "較大";
$editor_very_large = "非常大";

$editor_strikethrough = "貫穿";
$editor_outdent = "對齊";
$editor_indent = "縮排";

$editor_superscript = "超級指令集";
$editor_subscript = "子指令集";
$editor_undo = "復原";
$editor_redo = "重做";
$editor_copy = "複製";
$editor_paste = "貼上";
$editor_cut = "剪下";

$editor_font = "字型";
$editor_size = "大小";
$editor_heading = "標題";
$editor_select = "選擇";

$editor_mode = "轉為編輯器模式";

$days[0]="日";
$days[1]="一";
$days[2]="二";
$days[3]="三";
$days[4]="四";
$days[5]="五";
$days[6]="六";

$months[0]="一月";
$months[1]="二月";
$months[2]="三月";
$months[3]="四月";
$months[4]="五月";
$months[5]="六月";
$months[6]="七月";
$months[7]="八月";
$months[8]="九月";
$months[9]="十月";
$months[10]="十一月";
$months[11]="十二月";

$full_days[0] = "星期日";
$full_days[1] = "星期一";
$full_days[2] = "星期二";
$full_days[3] = "星期三";
$full_days[4] = "星期四";
$full_days[5]= "星期五";
$full_days[6] = "星期六";
$strLoadTime = "產生於";
$strDescription = "內容";
$strTitle = "標題";


//translation required
$editorCmd['undo'] = "復原";
$editorCmd['redo'] = "重試";
$editorCmd['bold'] = "粗體";
$editorCmd['italic'] = "斜體";
$editorCmd['underline'] = "底線";
$editorCmd['left'] = "對齊左";
$editorCmd['right'] = "對齊右";
$editorCmd['full'] = "填滿";
$editorCmd['center'] = "對齊中";
$editorCmd['ord_list'] = "編號符號";
$editorCmd['bul_list'] = "項目符號";
$editorCmd['txt_color'] = "文字顏色";
$editorCmd['back_color'] = "背景顏色";
$editorCmd['hyperlink'] = "超連結";
$editorCmd['image'] = "插入圖像";
$editorCmd['rule'] = "水平線";
$editorCmd['table'] = "插入表格";

$editorCmd['strikethrough'] = "刪除線";
$editorCmd['outdent'] = "減少縮排";
$editorCmd['indent'] = "增加縮排";

$editorCmd['superscript'] = "上標";
$editorCmd['subscript'] = "下標";
$editorCmd['undo'] = "復原";
$editorCmd['redo'] = "重試";
$editorCmd['copy'] = "複製";
$editorCmd['paste'] = "貼上";
$editorCmd['cut'] = "剪下";

$editorCmd['font'] = "字體";
$editorCmd['size'] = "大小";
$editorCmd['heading'] = "標題";
$editorCmd['select'] = "選擇";
$editorCmd['mode'] = "轉換模式";
$editorCmd['textindicator'] = "當前風格";



$cmdShowAll = '顯示所有';
$cmdSearch = '搜尋';

//translation required
$cmdYesToAll = '是所有';

$cmdNoToAll = '不是所有';
$short_months[0]="一月";
$short_months[1]="二月";
$short_months[2]="三月";
$short_months[3]="四月";
$short_months[4]="五月";
$short_months[5]="六月";
$short_months[6]="七月";
$short_months[7]="八月";
$short_months[8]="九月";
$short_months[9]="十月";
$short_months[10]="十一月";
$short_months[11]="十二月";

$strToday = '今天';

$strShortWeek = '週';

$cal_prev_year = "上年 (hold for menu)";
$cal_prev_month  = "上月 (hold for menu)";
$cal_go_today = "到今天";
$cal_next_month = "下月 (hold for menu)";
$cal_next_year  = "下年 (hold for menu)";
$cal_drag_to_move = "拖曳";
$cal_part_today = " (今天)";
$cal_monday_first = "顯示星期一為第一天";
$cal_sunday_first  = "顯示星期日為第一天";
$cal_select_date = "選擇日期";

$cmdPrint = '列印';
$strFirstName = '名字';
$strLastName = '姓氏';
$strInitials = '姓名簡稱';
$strSex = '性別';
$strBirthday = '出生日期';
$strSexes['M'] = '男';
$strSexes['F'] = '女';

$strTitle = '稱謂';

$strBeginning = '開始';

$strMiddleName = "中間&nbsp;名字";

$sir_madam['M'] = '先生';
$sir_madam['F'] = '女士';

$strModifiedAt = '修改於';
$strCreatedTAt = '建立於';

$htmlarea_language_name = "big5";

$strLocalization = "本地化";

$lang_modules['notes'] = "備忘錄";
//look up the language in controls/jscalendar/lang
$jscalendar_language_name = "calendar-zh.js";
$lang_modules['bookmarks'] = "書籤";

//look up language in 'classes/phpmailer/language'
$php_mailer_lang = 'en';
$strEmailFail = 'Failed sending the e-mail.';

?>

<?php
require_once($GO_LANGUAGE->get_fallback_language_file('users'));

$strLogins = "登入";
$strRegistrationDate = "登記日前";
$strUsers = "使用者";
$user_profile = "使用者設定檔";
$ac_login_info = "登入資訊";
$ac_registration_time = "登記時間";
$ac_lastlogin = "最後登入";
$ac_logins = "總共登入";
$strMaxOf = "已登記由最大";
$strUsername = "使用者名稱";
$delete_fail = "你/妳不能夠移除自己及超級使用者";
$admin_new_user = "新增使用者";

$admin_modules = '模組';
$admin_module_access = '這個使用者可存取以下的模組';

$admin_password = '密碼';
$admin_confirm_password = '確定密碼';
$admin_change_password = '變更密碼';
$admin_pass_match = '密碼不吻合，請重試。';

//registration
$registration_personal_info = "私人資訊";
$registration_account_info = "帳號資訊";
$registartion_username_text = "輸入使用者名稱及密碼以便這使用者能登入這網站";

$registration_question_text=<<<registration_question_text
輸入一條秘密問題，而這問題的答案只有這個使用者知道。如果使用者忘記自己的密碼，我們將會提出這條問題，
當他.他/她能夠回答正確的答案，這將會寄出一封包含新密碼的電子郵件到這個使用者在Group-Office所登記的電郵地址。
registration_question_text;

$registration_text=<<<registration_text
請填寫這份表格再按「確定」完成登記。欄位有 * 符號表示必須填寫<br />
你/妳所提供的資料只會用於Group-Office，我們不會將資料提供給第三者。
registration_text;

$registartion_authcode_text = "輸入授權碼，其他使用者如想新增這個使用者到他/她們的聯絡人和觀看資料，他/她們須要要這個授權碼。";
$registartion_confirm_authcode = "確定授權碼";
$registration_visibility = "其他使用者可看見這個使用者";
$registration_success = "<p class=\"head\">恭喜你妳!</p>你/妳已經成功登記了，歡迎訊息將會寄到:";	
$registration_failure = "當嘗試登記你/妳的資料時發生錯誤，請重試。";
$registration_mail_subject = "歡迎來到Group-Office";
$registration_folder_error = "建立私人資料夾失敗，請通知我們回復它!";
$registration_title = "登記Group-Office";

$registration_email = "在Group-Office為這個使用者建立電子郵件地址";
$registration_samba = "檔案及列印伺服器";
$registration_email_error = "當建立你/妳的帳號時發生錯誤，請聯絡我們!";
$strPasswordConfirm = "確定密碼";
$strPassword = "密碼";

$reg_company_info = "公司資訊";

$max_user_limit = "已到達最高使用者數量";
$max_users_text = "對不起，你/妳不可以再登記更多的使用者，如果你/妳想增加使用者數量，請聯絡我們。";
$ml_sent_items = "寄件備份";
$ml_spam = "垃圾郵件";
$ac_samba_user = "使用者可存取檔案及列印伺服器";
$ml_email_only = "當你妳沒有建立電郵地址，這是必需的。";
$register_new_user = "新增Group-Office 訂閱者";
$register_thanks = '<h2>多謝你妳</h2>. 我們將會盡快處理你/妳的訂閱, 稍後你/妳將會收到我們的通知';
$register_accept = "新增到使用者";
$register_addressbook = "新增到通訊錄";
$register_title = "訂閱";
$registration_mail_body=<<<registration_mail_body
親愛的 %sex% %last_name%,

你/妳已登記到Group-Office，
我們希望你/妳享受我們的服務

你/妳可使用以下的登入資料:

使用者名稱: %username%
密碼: %password%

按以下的連結登入到Group-Office:

registration_mail_body;

//1.98
$reg_language = '語系';
$reg_theme = '佈景主題';

//2.05
$admin_use = 'Use';
$admin_module = '模組';
$admin_manage = '管理';

$admin_groups = '使用者群組';
$admin_groups_user = '這個使用者是下列群組的一員';

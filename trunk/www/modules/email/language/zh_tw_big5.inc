<?php
require_once($GO_LANGUAGE->get_fallback_language_file('email'));

//Email
$ml_from = "寄件者";
$ml_to = "收件者";
$ml_send = "寄出";
$ml_attach = "附上";
$ml_attachments = "附件檔案";
$ml_attachment = "附件檔案";
$ml_high = "高";
$ml_normal = "一般";
$ml_low = "低";
$ml_priority = "優先順序";
$ml_subject = "主旨";
$ml_filename = "檔案名稱";
$ml_total_size = "總共大小";
$ml_size = "大小";
$ml_to_empty = "你/妳沒有指定收件者";
$ml_print = "列印";
$fbNoSelect = "你/妳沒有選擇郵件";

$ml_inbox = '收件匣';
$ml_properties = '內容';
$ml_add_account = "新增帳號";
$em_new_folder = "新增資料夾";
$ml_your_accounts = "你/妳的電郵帳號";
$ml_no_accounts = "你/妳沒有任何電郵帳號可供設定";
$ml_original_follows = "----- 原始訊息 -----";
$ml_welcome = "歡迎來到Group-Office電子郵件";
$ml_text = "請先按「帳號」新增電郵帳號!";
$ml_accounts = "帳號";
$ml_compose = "編寫郵件";
$ml_delete = "移除";
$ml_refresh = "重新整理";
$ml_reply = "回覆";
$ml_forward = "轉寄";
$ml_reply_all = "全部回覆";
$ml_delete_message = "你/妳是否確定永遠移除這封郵件？";
$ml_no_subject = "沒有主旨";
$ml_no_reciepent = "沒有收件者";
$ml_online_files = "線上檔案";
$ml_send_error = "電郵傳送失敗";

$ml_attach_forget = "你/妳已選擇了檔案，但沒有附上它。";
$ml_messages = "封郵件";
$ml_message = '這封郵件';
$ml_messages2 = '封郵件';
$ml_new = "封未閱讀";
$ml_delete_mailbox = "你/妳是否確定永遠移除已選擇的所有郵件？";

$contact_profile = "聯絡人設定檔";
$ml_add_contact = "新增寄件者到聯絡人";
$strHost = "主機";
$strDefault = "預設";
$ml_edit_account = "修改帳號";
$ml_new_account = "新增帳號";
$ml_no_messages = "這個資料夾沒有郵件";
$ml_file_too_big = "你/妳所附加的檔案太大，附件檔案大小限制已設定為：";
$fbNoRightToOverwrite = "這個檔案已存在以及你/妳不被容許覆蓋它";
$ml_save_attachment = "儲存附件檔案到Group-Office";
$ml_folders = "資料夾";

$ml_select_folder = "選擇資料夾以便Group-Office能儲存已寄出的郵件或在以下輸入名稱建立資料夾";
$ml_connect_failed = "連接電郵伺服器失敗";
$ml_at_port = "連接埠";
$ml_solve_error = '按「帳號」改正你/妳的帳號設定或按「重新整理」開啟你/妳的預設帳號';
$ml_sent_items = "寄件備份";
$ml_spam = "垃圾郵件";
$ml_use = "使用";
$ml_synchronise = "同步";
$ml_move_mail = "移動已選擇的郵件到資料夾...";
$ml_filters = "過濾";
$ml_move_to = "移動郵件到資料夾";
$ml_email_is = "如果寄件者的電郵地址包含";
$ml_to_is = "如果收件者包含";
$ml_cc_is = "如果副本欄位包含";

$ml_subject_is = "如果主旨包含";
$ml_new_filter = "新增過濾";
$ml_field = "欄位";
$ml_contains = "包含";
$ml_folder = "資料夾";
$ml_no_folders = "你/妳沒有任何資料夾因此你不能夠建立過濾";
$ml_unwanted = "按這裡如果你/妳不想要來自這個寄件者的電郵地址";
$fbFolderSize = "資料夾大小";
$ml_move_up = "增加優先順序";
$ml_delete_filter = "你/妳是否確定移除這個過濾？";
$ml_high_priority = "這封郵件擁有高的優先順序！";
$ml_low_priority = "這封郵件擁有低的優先順序";
$ml_block = "封鎖寄件者";
$ml_sent_items_fail = "這封郵件已經寄出，但當儲存這封郵件到你/妳的寄件備份資料夾時發生錯誤，請檢查你/妳的IMAP帳號。";
$fbProperties = "內容";
$ml_notification = "要求閱讀回條";
$ml_ask_notify = "這封郵件的寄件者要求你/妳寄回閱讀回條，是否要寄出閱讀回條?";
$ml_displayed = "以下的電子郵件已顯示為";
$ml_notify = "閱讀通知";
$ml_delete_filter1 = "移除過濾";
$ml_html_message_attached = "原始HTML郵件已附上";
$ml_add_recievers = "新增不知名收件者到你/妳的通訊錄";
$ml_no_folders = '你/妳沒有任何資料夾以便使用過濾';
$ml_no_filters = '你/妳沒有任何過濾';

$ml_host = '主機';
$ml_port = '通訊埠';
$ml_type = '類型';
$ml_servertype = '伺服器類型';
$ml_format = '格式';
$ml_loose_changes = '所有變更將會遺失，你/妳是否確定?';

$ml_markread = "標示成已閱讀";
$ml_markunread = "標示成未閱讀";
$ml_root_mailbox = "根郵件信箱";
$ml_inside = "在";
$ml_no_mailbox = "這個不是電郵信箱，這個資料夾不能包含信息。";
$ml_signature = "簽名";
$ml_root = "根電郵信箱";
$ml_disable_accounts = '停用帳號管理';
$ml_trash = '垃圾桶';
$ml_disable = '停用';
$ml_confirm_empty_mailbox = "你/妳是否確定要清空這資料夾？";
$ml_empty_mailbox = '清空資料夾';
$ml_you_have = '你/妳有';
$ml_new_mail_single = '封新的郵件';
$ml_new_mail_multiple = '封新的郵件';
$ml_auto_check = '自動檢查這個帳號的新郵件';
//1.98
$ml_no_contacts_in_mailing_group = '你/妳所選擇的郵寄群組內沒有任何聯絡人，請先在郵寄群組內新增聯絡人';
$ml_attention= '注意';

?>

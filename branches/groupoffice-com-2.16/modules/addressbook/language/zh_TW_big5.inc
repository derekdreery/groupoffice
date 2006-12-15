<?php
require_once($GO_LANGUAGE->get_fallback_language_file('addressbook'));

//contacts
$contacts_title = "通訊錄";
$contacts_no_contacts = "找不到聯絡人";
$contacts_members = "成員";
$contacts_contacts = "聯絡人";
$contacts_add = "新增聯絡人";
$contacts_groups = "群組";
$contacts_other = "其他聯絡人";
$contacts_move_to_group = "移動已選擇的聯絡人到群組....";
$groups_no_groups = "你/妳沒有任何群組";
$contacts_group = "群組";
$contacts_empty_group = "使用下拉式選單移動聯絡人群組";
$contacts_this_group = "這個群組";

$add_contacts_text = "填入群組電郵地址及授權密碼再按「確定」";
$add_contacts_success = "使用者新增成功\";
$add_contacts_wrong = "電郵地址或授權碼錯誤";
$add_personal_text = "按「儲存」新增聯絡人，有 * 的欄位必須填寫。";
$contacts_select = "選擇新增的聯絡人再按「確定」";
$contacts_select_title = "聯絡人";
$contact_profile = "聯絡人設定檔";
$user_profile = "使用者設定檔";
$contacts_authorise = "授權";
$contacts_add_member = "新增這個成員到你/妳的聯絡人";
$strConfirmSynchronise = "這樣會更新這個聯絡人他自己所輸入的資料，你/妳手動更改的資料將會遺失，是否確定要繼續？";
$contacts_import = "匯入";
$contacts_export = "匯出";
$contacts_export_text = "選擇你/妳想新增的CVS類型";
$contacts_delete = "移除";

$contacts_import_text = "選擇CVS檔案再按「確定」";
$contacts_import_text_group = "選擇群組和CVS檔案再按「確定」";
$fbNoFile = "沒有已接收的檔案";
$fbPleaseWait = "請等待，檔案正在上載....";

$strNotIncluded = "沒有匯入";
$contacts_import_feedback = "在你/妳的CVS檔案已發現以下的欄<br />";
$contacts_import_feedback .= "選擇你/妳想匯入的群組或以檔案的欄自動新增群組<br />";
$contacts_import_feedback .= "由檔案的欄符合Group-office相對應的欄以及確定你/妳沒有重複選擇欄兩次";
$contacts_separator = "分割數值以";
$contacts_no_separator = "你/妳沒有輸入分割字元";
$fbFilename = "檔案名稱";
$contacts_import_title = "匯入通訊錄";
$contacts_import_success = "檔案已成功匯入";
$contacts_import_double = "你/妳已重複選擇一欄或多欄，請更正後重試。";
//changed:
$contacts_import_noname = "你/妳必須選擇 \'姓氏\' 及 \'名字\'欄位";
$contacts_import_incompatible = "檔案格式不相容<br /><br />CVS檔案是一組由點或逗號分隔數值的一種檔案";
$strMember = "成員";
$contact_exist_warning = "<b>注意</b>你/妳的聯絡人已存在這個使用者";
$contacts_add_to_group = "新增已選擇的使用者到群組聯絡人....";
$contacts_add_to = "新增已選擇的使用者到聯絡人";
$contacts_add_user = "新增";
$contacts_edit = "修改";
$contacts_no_select = "你/妳沒有選擇任何物件";
$contacts_this_contact = "這個聯絡人";

$contacts_seperator_text = "分割數值以";
$contacts_quote = "附上數值以";
$contacts_clrf = "排列結束以";
$contacts_auto_group = "自動由檔案新增群組";
$contacts_import_to_group = "匯入到群組";

$contacts_search = '搜尋';
$cmdSearch = '搜尋';
$contacts_search_text = "輸入搜尋字句再按「搜尋」，你/妳可以使用*當作萬用字符。";
$contacts_results = '結果';
$contacts_result = '結果';

$contacts_color = '顏色';

$contacts_colors['black'] = '黑色';
$contacts_colors['red'] = '紅色';
$contacts_colors['green']= '綠色';
$contacts_colors['blue'] = '藍色';
$contacts_colors['pink'] = '粉紅色';
$contacts_colors['orange'] = '橙色';
$contacts_colors['brown'] = '棕色';

$ab_addressbooks = "通訊錄";
$ab_owner = "擁有者";
$ab_subscribed = "訂閱\";
$ab_new_ab = "新增通訊錄";
$ab_addressbook_exists = "這個通訊錄的名稱已存在";
$ab_addressbook = "通訊錄";
$contacts_select_single = "按下聯絡人來選取這個人";
$ab_group_exists = "這裡已經有這名稱的通訊錄";

//translation required
$ab_edit_contact = '按下 \'確定\' 儲存變更。有*的欄位是必須的';
$ab_group_on_file = '以檔案建立群組連絡人';
$ab_default = '預設';
$ab_browse = '瀏覽';
$ab_comment = '註解';

$ab_contact = '連絡人';
$ab_send_message = '電子郵件';
$ab_new_event = '新增專案';

$ab_templates = '範本';
$ab_your_templates = '你的';
$ab_all_templates = '所有';

$template_type[0] = '電子郵件';
$template_type[1] = 'OpenOffice.org';
$ab_type = '種類';
$ab_template_esists = '這個名稱的範本已存在';


$sir_madam['M'] = '先生';
$sir_madam['F'] = '女士';

$ab_upload_oo = '上傳OpenOffice.org 範本';
$ab_upload_oo_text = '選擇你已經使用OpenOffice.org修改好的範本然後按\'確定\'';

$ab_upload_nofile = '檔案傳送時發生錯誤';


$ab_oo_doc = '文字文件';
$ab_select_template = '選擇這文件所使用的範本:';

$ab_add_field_data = '你的文件可以使用以下的數值:';

$ab_datafield = '資料欄';

$ab_no_template = '沒有範本';
$ab_new_note = '新增備忘';
$ab_events = '約定';

$ab_new_contact = '新增聯絡人';

$ab_new_company = '新增公司';

$ab_companies = '公司';
$ab_company_properties = '公司內容';
$ab_bank_no = '銀行;號碼';
$ab_companies_count_single = '公司';
$ab_companies_count = '公司';
$ab_no_companies = '找不到公司';
$ab_no_company = '找不到公司';

$ab_import_type = '匯入';
$ab_no_company_found = '找不到公司';
$ab_search_company = '尋找公司';
$ab_edit_company = '修改公司內容';
$ab_new_group = '新增群組';
$ab_export_type = '匯出';

$ab_custom_fields = '額外的;資料欄位';
$ab_field_size = '大小';

$ab_new_field = '新增資料欄位';
$ab_invalid_field_size = '欄位大小值無效，大小值應介乎10-255。';
$ab_field_exists = '欄位名稱已經存在';


$ab_employees = '雇員';
$ab_no_employees = '找不到雇員';

$ab_employee_count_single = '雇員';
$ab_employee_count_multiple = '雇員';
$ab_vat_no = 'VAT 號碼';

$ab_add_new = '新增';
$ab_add_existing = '新增現有的';

$ab_access = '存取';
//1.98
$ab_mailings = '郵寄';
$ab_mailing_groups = '郵寄群組';
$ab_no_mailing_groups = '找不到任何郵寄群組';
$ab_no_templates = '找不到任何範本';
$ab_mailing_group_exists = '這個名稱的郵寄群組已存在';
$ab_contacts_count_single = '聯絡人';
$ab_contacts_count_multiple = '聯絡人';
$ab_new_mailing = '新增郵寄';
$ab_all_your_addressbooks = '你的所有通訊錄';

$ab_search_for = '搜尋';
$ab_search_in = '之內';
$ab_search_on = '之外';
$ab_search_keyword = '搜尋關鍵字';
$ab_wildcard = '(使用 \'%\' 當作百搭字符)';


?>

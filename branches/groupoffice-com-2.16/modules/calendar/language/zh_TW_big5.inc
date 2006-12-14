<?php
require_once($GO_LANGUAGE->get_fallback_language_file('calendar'));

$sc_title = "標題";
$sc_description = "內容";
$sc_participants = "被邀請者";

$sc_location = "場所";

$month_times[0] = "第一";
$month_times[1] = "第二";
$month_times[2] = "第三";
$month_times[3] = "第四";

$sc_new_app = "新增事件";
$sc_calendars = "行事曆";
$sc_invited = '你/妳已被邀請參加以下的事件';

$sc_types1[0] = '不重複';
$sc_types1[1] = '日';
$sc_types1[2] = '星期';
$sc_types1[3] = '個月這日';
$sc_types1[4] = '個月指定日';
$sc_types1[5] = '年';

$sc_types[0] = '一次';
$sc_types[1] = '每日';
$sc_types[2] = '每星期';
$sc_types[3] = '每月指定日期';
$sc_types[4] = '每月指定日子';
$sc_types[5] = '每年';

$sc_start_at = '開始在';
$sc_end_at = '結束在';
$sc_start_time = '開始時間';
$sc_end_time = '結束時間';

$sc_at_days = '在這日';
$sc_of_month = '這月份的';

$sc_cycle_start = '週期開始在';
$sc_cycle_end = '週期結束在';
$sc_accept_question = '你/妳是否願意接受這事件?';

$sc_accept = '接受';
$sc_decline = '拒絕';

$sc_notime = '不應用時間';
$sc_noend = '沒有結束日期';

$sc_never_happens = '輸入錯誤! 這事件永不會發生';
$sc_start_later = '輸入錯誤! 開始時間遲於結束時間';


$sc_add_self = '手動輸入';

$sc_select_calendar = '選擇行事曆排程這事件';
$sc_accept_confirm = '事件已被接受及已排程';
$sc_no_calendars = '沒有有效的行事曆可排程這事件';
$sc_bad_event = '找不到有效的事件，這個事件可能已移除。';
$sc_accept_title = '接受事件';

$sc_decline_confirm = '這事件已被拒絕';
$sc_decline_title = '拒絕事件';

$sc_status = '狀態';
$sc_not_responded = '沒有回應';
$sc_accepted = '已接受';
$sc_declined = '已拒絕';
$sc_no_participants = '沒有參與者';

$sc_person = '人員';
$sc_owner = '擁有者';
$sc_type = '類型';
$sc_subscribed = "已訂閱\";

$sc_subscribe = "訂閱\";
$sc_new_calendar = '新增行事曆';
$sc_no_calendarr = '找不到行事曆，請現在建立一個或讓Group-Office自動為你/妳建立。';
$sc_day_view = '檢視日';
$sc_week_view = '檢視週';
$sc_list_view = '列表檢視';
$sc_put_in = '將事件加入到';
$sc_inherit_permissions ='繼承權限由';
$sc_no_inherit = '不繼承';

$sc_select_calendar_please = '請至少選擇一個行事曆';
$sc_events = '事件';

$sc_from_calendar = '由這行事曆';
$sc_enitrely = '所有的';
$sc_delete_event = '移除事件';
$sc_delete_pre = '你/妳是否確定要移除這事件';
$sc_delete_suf = '在所有的行事曆還是只有這個行事曆?';

$sc_refresh = '重新整理';
$sc_reminder = '提醒';
$sc_mins = '分鐘';
$sc_hour = '小時';
$sc_hours = '小時';
$sc_day = '日';
$sc_days = '日';
$sc_week = '週';
$sc_weeks = '週';
$sc_reminder_set_to_early = "提醒功能不能夠設定早於事件發生前，請縮短提醒時間或延長循環週期。";
$sc_cycle_start_later = '週期開始日期遲於或等於結束日期';

$cmdGenerate = '產生';
$sc_preferences = '喜好設定';

$sc_holidays = '假期';

$sc_holidays_text1 = '請按 \''.$cmdGenerate.'\' 計算所選擇年份的假期記錄';

$sc_holidays_text2 = '請按 \''.$cmdApply.'\' 儲存所有已計算的假期記錄到資料庫';

$strNoHolidays = '沒有假期在資料庫';
$strUnknown = '不名的';
$strYear = '年';
$strDeleteHolidaysPrefix = '你/妳是否確定要移除在這年的所有假期記錄';
$strDeleteHolidaysSuffix = '?';
$strReplaceHolidays = '你/妳是否確定要覆蓋所有現存的假期記錄?';

//translation required below this
$sc_client = '客戶';
$sc_no_contact = '沒有';

$sc_background = '背景';

$sc_colors['red'] = '紅色';
$sc_colors['green'] = '綠色';
$sc_colors['blue'] = '藍色';
$sc_colors['pink'] = '粉紅色';
$sc_colors['orange'] = '橙色';
$sc_colors['brown'] = '棕色';
$sc_colors['yellow'] = '黃色';
$sc_colors['grey'] = '灰色';
$sc_colors['purple'] = '紫色';
$sc_colors['white']= '白色';
$sc_status = '狀態';

$sc_show_hours = "顯示小時由";
$sc_to = "到";

$sc_today = "今天";

$sc_recur_every = "重複每";
$sc_months = "月份";

$sc_private_event = "私人事件";
$sc_calendar_exists = '這個行事曆的名稱已存在';
//translate
$sc_month = '月';
$sc_view = '檢視';
$sc_calendar = '行事曆';
$sc_default = '預設';
$sc_month_view = '檢視月';


//text changed, needs new translation

$sc_holidays_no_file_prefix = "這假期不能產生，因為檔案包含之假期資料";
$sc_holidays_no_file_suffix = "的語系不存在或是空的";

$sc_no_events = "找不到時件";

?>

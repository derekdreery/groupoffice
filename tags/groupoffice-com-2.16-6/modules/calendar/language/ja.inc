<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('calendar'));

$sc_title = "タイトル";
$sc_description = "説明";
$sc_participants = "参加者";

$sc_location = "ロケーション";

$month_times[0] = "第１";
$month_times[1] = "第２";
$month_times[2] = "第３";
$month_times[3] = "第４";

$sc_new_app = "新規イベント";
$sc_calendars = "カレンダー";
$sc_invited = '以下の理由によりイベントに招待されました。';

$sc_types1[0] = '繰り返しなし';
$sc_types1[1] = '毎日';
$sc_types1[2] = '毎週';
$sc_types1[3] = '日付によって毎月';
$sc_types1[4] = '毎月';
$sc_types1[5] = '毎年';


$sc_types[0] = '一度だけ';
$sc_types[1] = '毎日';
$sc_types[2] = '毎週';
$sc_types[3] = '日付によって毎月';
$sc_types[4] = '毎月';
$sc_types[5] = '毎年';


$sc_start_at = '開始日時';
$sc_end_at = '終了日時';
$sc_start_time = '開始時間';
$sc_end_time = '終了時間';

$sc_at_days = '曜日';
$sc_of_month = 'of the month.';

$sc_cycle_start = '周期開始';
$sc_cycle_end = '周期終了';
$sc_accept_question = 'このイベントを受け入れてスケジュールしますか？';

$sc_accept = '許諾';
$sc_decline = '辞退';

$sc_notime = '時間指定無し';
$sc_noend = '終了日付がありません。';

$sc_never_happens = '不正な入力です！ このイベントは起こりません。';
$sc_start_later = '不正な入力です！開始時間が終了時間より後です。';

$sc_add_self = 'マニュアル入力';

$sc_select_calendar = 'このイベントをスケジュールするカレンダーを選ぶ';
$sc_accept_confirm = 'このイベントは了承、スケジュールされました。';
$sc_no_calendars = 'イベントをスケジュールするのに無効なカレンダーです。';
$sc_bad_event = '無効なイベントです。 このイベントは削除されているでしょう。';
$sc_accept_title = 'イベントを受け入れる';

$sc_decline_confirm = 'イベントは辞退されました。';
$sc_decline_title = '辞退イベント';

$sc_status = 'ステータス';
$sc_not_responded = '応答なし';
$sc_accepted = '受け入れる';
$sc_declined = '辞退する';
$sc_no_participants = '関係者なし';

$sc_person = '個人';
$sc_owner = '所有者';
$sc_type = '種類';
$sc_subscribed = '署名';

$sc_subscribe = '署名';
$sc_new_calendar = '新規カレンダー';
$sc_no_calendar = 'カレンダーがありません。新規作成するか自動作成してください。';
$sc_day_view = '日表示';
$sc_week_view = '週表示';
$sc_list_view = 'リスト表示';
$sc_put_in = 'イベントを挿入';
$sc_inherit_permissions ='引継ぎ権限';
$sc_no_inherit = '引き継ぎできません。';

$sc_select_calendar_please = '少なくとも１つのカレンダーを選択してください。';
$sc_events = 'イベント';

$sc_from_calendar = 'カレンダーから';
$sc_enitrely = '完全';
$sc_delete_event = 'イベントを削除';
$sc_delete_pre = 'イベントを削除しますか';
$sc_delete_suf = 'このカレンダーから完全に？';

$sc_refresh = '更新';
$sc_reminder = '通知';
$sc_mins = '分';
$sc_hour = '時';
$sc_hours = '時';
$sc_day = '日';
$sc_days = '日';
$sc_week = '週';
$sc_weeks = '週';
$sc_reminder_set_to_early = "通知設定は前回の発生より前には設定できません。繰り返し時間や期間を短く設定してください";
$sc_cycle_start_later = '繰り返し開始日が終了日より後になっています';


$cmdGenerate = '生成';
$sc_preferences = 'プリファレンス';
$sc_holidays = '祝日';

$strNoHolidays = '祝日データベースがありません。';
$strUnknown = '未定義';
$strYear = '年';
$strDeleteHolidaysPrefix = '祝日レコードをすべて消去しますか';
$strDeleteHolidaysSuffix = '？';
$strReplaceHolidays = '祝日レコードをすべて上書きしますか？';
$sc_holidays_text1 = '選択した年の祝日レコードを完了するために \''.$cmdGenerate.'\' をクリックしてください。';
$sc_holidays_text2 = '算出した全ての祝日レコードをデータベースに保存するために \''.$cmdApply.'\' をクリックしてください。';

//translation required below this
$sc_client = 'クライアント';
$sc_no_contact = 'なし';

$sc_background = '背景';

$sc_colors['red'] = '赤';
$sc_colors['green'] = '緑';
$sc_colors['blue'] = '青';
$sc_colors['pink'] = 'ピンク';
$sc_colors['orange'] = '橙';
$sc_colors['brown'] = '茶';
$sc_colors['yellow'] = '黄';
$sc_colors['grey'] = '灰';
$sc_colors['purple'] = '紫';
$sc_colors['white']= '白';

$sc_show_hours = 'ここから';
$sc_to = 'ここまでの時間を表示';

$sc_today = '本日';

$sc_recur_every = '全部繰り返し';
$sc_months = '月';

$sc_private_event = 'プライベートイベント';
$sc_calendar_exists = 'この名前のカレンダーはすでに存在しています。';
//translate
$sc_month = '月';
$sc_view = '表示';
$sc_calendar = 'カレンダー';
$sc_default = 'デフォルト';
$sc_month_view = '月を表示';

//text changed, needs new translation
$sc_holidays_no_file_prefix = '祝日が生成されませんでした。祝日情報を含むファイルが';
$sc_holidays_no_file_suffix = '言語が空か存在しません。';

$sc_no_events = 'イベントはありません。';

//2.03
$cal_export = 'エキスポート';
$cal_import = 'インポート';
$cal_please_wait = 'しばらくお待ちください...';
$cal_import_text = 'iCal (*.ics) ファイルを選択した場合は「OK」をクリックしてください。';
$cal_filename = 'ファイル';
$cal_import_success = 'データをインポートしました。';

//2.04
$cal_add_todo = '新規タスク';
$cal_todos = 'タスク';

$cal_hide_completed = '不可視化完了';
$cal_show_all = '全てのタスクを表示';
$cal_show_own = '自分のタスクを表示';
$cal_show_responsible = '責任者のタスクを表示';

$cal_priority = '優先度';
$cal_priority_values[0] = '低';
$cal_priority_values[1] = '普通';
$cal_priority_values[2] = '高';

$cal_status = 'ステータス';
$cal_canceled = 'キャンセル';
$cal_completed = '完了';
$cal_percent_completed = '% 完了済み';

$cal_before_task_start = 'タスク開始前';
$cal_before_task_due = 'タスク終了前';
$cal_responsible = '責任者';
$cal_due_at = '締め切り';
$cal_no_todos= 'タスクなし';
$cal_resend_invitation = '招待を再送';

//2.05



$cal_view = 'ビュー';
$cal_views = 'ビュー';
$sc_view_exists = 'この名前のビューは既に存在しています。';
$cal_new_view = '新規ビュー';
$cal_event_calendars = 'カレンダー';

//2.06
$cal_view_merged = '結合';
$cal_view_emerged = '分割';

//2.07
$cal_display = '表示';
$sc_new_task = "新規タスク";
$sc_all_day_events = '終日イベント';
$sc_all_day_event = '終日イベント';
$sc_usual_events = 'イベント';
$sc_usual_event = 'イベント';

//2.08
$sc_recur_section = '繰り返し設定';
$sc_options_section = 'オプション';
$sc_close = '閉じる';

$cal_count_single = 'イベント';
$cal_count_multiple = 'イベント';

$cal_until = '期限付き';
$cal_forever = '無期限';

//2.09
$cal_merged_overview = '概要を結合';
$cal_own_availability = '自分の利用権限';

//2.11
$cal_no_file = 'ファイルなし';
$cms_no_calendars_in_view = 'このビューにカレンダーがないため表示できません。 \'表示\' をクリックしてビューを結合してください。';
$cal_no_reminder = '通知なし';

//2.13
$cal_refresh_rate = 'リフレッシュレート';
$sc_seconds = '秒 (リフレッシュしないなら空)';
$cal_default_reminder = '初期通知';


//2.14
$cal_event = 'イベント';
$cal_todo = 'Todo';
$cal_participants_write = '参加者のイベントの編集を許可';

$cal_everybody_write = '全員がイベントを編集'; //modified string from 2.05 this became the
$cal_everybody_read = '自分だけがイベントを編集';//modified string from 2.05 

$cal_default_permissions = '標準権限';
$cal_show_todos = 'カレンダーにタスクを表示';

$cal_edit_series_or_single = 'このインスタンスだけか全体を編集しますか？';
$cal_single = 'インスタンス';
$cal_series = '全体';

$cal_legend = '凡例';
$cal_multiple_calendars = 'マルチカレンダー';

$cal_cumulative = '無効な入力です。イベントは再現期間より長く取ってください。';

$cal_weekview = '週表記';
$cal_mo_to_fr = '月曜日-金曜日';
$cal_mo_to_su = '月曜日-土曜日';

$cal_batchcalendars = '全ユーザ向けにカレンダーを作成';
$cal_create_all_calendars = '全ユーザか許可されたユーザグループ向けにカレンダーを作成';

$cal_no_views = 'ビューがありません';

$cal_my_cals = 'マイカレンダー';

$cal_event_colors_override = 'イベントの色分けを手動で設定したものを使用する';

//2.15
$cal_groups = 'グループ';
$cal_new_group = '新規グループ';
$cal_group_exists = 'グループ名はすでに存在します。違う名前を選択してください。';
$cal_no_calendars = 'グループのカレンダーは見つかりませんでした';
$cal_group = 'グループ';
$cal_resources = 'リソース';

$cal_event_mail_subject = 'イベント名 \'%s\' はカレンダーに作成されました';//%s is event name
$cal_event_mail_body = '%s さんは \'%s\' のイベントをカレンダー名 \'%s\' に作成しました'; //irst %s is the name of the person who created the event. Second is the event name, third is the calendar name
$cal_event_mail_open = 'アポイントメントを開く';
$cal_add_custom_field = 'カスタム項目を追加する';

$cal_event_modified_mail_subject = 'イベント名 \'%s\' はカレンダーで編集されました';//%s is event name
$cal_event_modified_mail_body = '%s さんは \'%s\' のイベントを \'%s\' で編集しました'; //First %s is the name of the person who created the event. Second is the event name, third is the calendar name

$cal_your_event_modified_mail_subject = 'イベント名 \'%s\' を編集し作成しました';//%s is event name
$cal_your_event_modified_mail_body = '%s さんがあなたが作成したイベント \'%s\' を編集しました'; //First %s is the name of the person who created the event. Second is the event name.
$cal_no_custom_fields = 'カスタム項目が定義されていません';
$cal_add_field = '項目を追加';


$cal_resource_mail_subject = 'リソース \'%s\' は予約されました'; //%s is resource name
$cal_resource_mail_body = '%s さんはリソース \'%s\' を予約しました。あなたはこのリソースの維持責任者です。予約を開いてリソースを拒否、または許可をしてください'; //First %s is the name of the person who created the event. Second is the calendar name
$cal_open_resource = '予約を開く';

$cal_resource_modified_mail_subject = 'リソース \'%s\' は編集されました';//%s is resource name
$cal_resource_modified_mail_body = '%s さんはリソース \'%s\' を編集しました。あなたはこのリソースの維持責任者です。予約を開いてリソースを拒否、または許可をしてください'; //First %s is the name of the person who created the event. Second is the calendar name

$cal_your_resource_modified_mail_subject = 'Your booking for resource \'%s\' has been modified';//%s is resource name
$cal_your_resource_modified_mail_body = '%s has modified your booking for the resource \'%s\'.'; //First %s is the name of the person who created the event. Second is the calendar name


$cal_accept_mail_subject = ' \'%s\' からの招待を受け付けました';
$cal_accept_mail_body = '%s は \'%s\' の招待を受け付けました';

$cal_decline_mail_subject = ' \'%s\' からの招待を拒否しました';
$cal_decline_mail_body = '%s は \'%s\' の招待を拒否しました';

$cal_statuses['NEEDS-ACTION'] = 'Needs action';
$cal_statuses['ACCEPTED'] = '許可';
$cal_statuses['DECLINED'] = '拒否';
$cal_statuses['TENTATIVE'] = '一時的';
$cal_statuses['DELEGATED'] = '代表';
$cal_statuses['COMPLETED'] = '完了';
$cal_statuses['IN-PROCESS'] = '進行中';

$cal_booking = ' \'%s\' を予約';//%s is resource name

$cal_group_admins = '管理者';
$cal_no_admins = '管理者が定義されていません';
$cal_add_admin = '管理者を追加';

$cal_scale='スケール';

$cal_show_completed = '完了したタスクを表示する';

$cal_resource = 'リソース';
$cal_grid_view = 'グリッド表示';

$cal_field_types['checkbox'] = 'チェックボックス';
$cal_field_types['text'] = 'テキスト';
$cal_field_types['textarea'] = 'テキスト領域';
$cal_field_types['date'] = '日付領域';
?>

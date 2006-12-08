<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('forum'));

$frm_new_forum = '新規フォーラム';
$frm_properties = 'プロパティ';

$frm_read_perm = '閲覧許可';
$frm_write_perm = '書き込み許可';

$frm_name = 'フォーラム名';
$frm_desc = '説明';

$frm_already_exists = 'このフォーラムは既に存在しています。';

$frm_added = 'フォーラムが追加されました。';
$frm_updated = 'フォーラムが更新されました。';
$frm_not_saved = 'フォーラムを保存できませんでした。';
$frm_not_updated = 'フォーラムを更新できませんでした。';

$frm_acl_read = 'ユーザがフォーラムを閲覧可能';
$frm_acl_write = 'ユーザがメッセージを投稿可能';

$frm_no_forum = 'フォーラムがありません。';
$frm_confirm_delete = '本当にフォーラムを削除してよろしいですか';

$frm_post_answer = 'メッセージに返信';
$frm_post_new_message = '新規トピック';
$frm_responses = '他の人のレス';

$frm_fields_missing = '全ての項目を入力してください。';
$frm_msg_no = 'メッセージNo.';

$frm_msg_posted = 'Message posted';
$frm_no_message = 'メッセージ無し';
$frm_no_response = 'レス無し';
$frm_attached_files = '添付ファイル';

$frm_title = 'タイトル';
$frm_body = '本文';
$frm_posted_by = '投稿者';

$frm_in_answer_to = 'in answer to';
$frm_forum = 'フォーラム';
$frm_created_by = '作成者';
$frm_ctime = 'Creation on';
$frm_mtime = '最終投稿';
$frm_reply = '返信';
$frm_reply_to = '返信';

$frm_messages_total = 'メッセージ';
$frm_subjects_total = 'トピックス';
$frm_unread_messages_total = '未読メッセージ';
$frm_unread_subjects_total = '新規トピック';

$frm_mailing_list = 'メーリングリスト';

$frm_mail_subject=' "%forum_name%" フォーラムの新規トピック';
$frm_mail_subject2=' "%forum_name%" フォーラムの新規メッセージ';



$frm_mail_body=<<<frm_mail_body
%style%
<div class="automail">
<p class="date">%user% は %date% %time% に新規トピックを投稿しました</p>
<div class="box"><p class="title">%msg_title%</p>
<p class="body">
%msg_body%
</p></div>
<a href="%url%">フォーラムへ</a>
</div>
frm_mail_body;




$frm_mail_body2=<<<frm_mail_body2
%style%
<div class="automail">
<p class="date">%user% は %date% %time% に新規メッセージを投稿しました</p>
<div class="box"><p class="title">%msg_title%</p>
<p class="body">
%msg_body%
</p></div>
<a href="%url%">フォーラムへ</a>
</div>
frm_mail_body2;


$frm_ad_type = 'E-Mail通知';
$frm_ad_1 = '新規トピックのみ';
$frm_ad_2 = '新規メッセージごと';
$frm_ad_3 = 'しない';

$frm_display_type = '表示';
$frm_display_1 = 'ツリー表示';
$frm_display_2 = 'リスト表示';

$frm_display_sort = '並び替え';
$frm_sort_1 = 'タイトル';
$frm_sort_2 = '日付';
$frm_sort_3 = '作成者の名前';
$frm_sort_4 = '作成者の名字';
$frm_ASC = '昇順';
$frm_DESC = '降順';
$frm_order ='並び替え';

$frm_expand_all = '全てを展開';
$frm_collapse_all = '全てを閉じる';

$mail_style=<<<mail_style
<style>
	.automail{ font-family:Verdana;font-weight:bolder;font-size:12px; }
	.box { border: 1px solid #ddd;background:#f8f8f8;width:400px;padding:5px; }
	.title { font-size:14px;text-align:center;text-decoration:underline; }
	.body { text-align:justify; font-size:10px; }
	
</style>
mail_style;

$frm_edit = '編集';
$frm_delete = '削除';
$frm_confirm_msg_delete = '本当にこのメッセージを削除してよろしいですか ?';

$frm_close_topic = 'トピックを閉鎖';
$frm_confirm_close_topic = '本当にこのトピックを閉鎖してよろしいですか ?';
$frm_closed_topic = '閉鎖';
$frm_reopen_topic = 'トピックを再開';
$frm_confirm_reopen_topic = '本当にこのトピックを再開してよろしいですか ?';
$frm_custom = 'カスタムフィールド';

$frm_import = 'インポート';
$frm_import_text = 'インポートしたいCSVファイルを選択してください。';
$frm_import_file = 'CSV ファイル';
$frm_confirm_empty_forum = '本当にこのフォーラムを空にしてよろしいですか ?';
$frm_empty_forum = 'フォーラムを空に';
$frm_choose_sep = 'CSVファイル区切りを入力';
$frm_import_string = 'Please organize your CSV fields like this :<br /> Date, Time, Topic, Body, Field1, Field2, Field3';
$frm_entries_added = 'エントリーは追加されました';
$frm_entry_added = 'エントリーは追加されました';

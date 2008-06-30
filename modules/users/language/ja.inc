<?php
//Uncomment this line in new translations!
//require_once($GO_LANGUAGE->get_fallback_base_language_file('users'));

$strLogins = "ログイン";
$strRegistrationDate = "登録日";
$strUsers = "ユーザ";
$user_profile = "ユーザプロフィール";
$ac_login_info = "ログイン情報";
$ac_registration_time = "登録時間";
$ac_lastlogin = "最終ログイン";
$ac_logins = "ログイン合計";
$strMaxOf = "登録最大数";
$strUsername = "ユーザ名";
$delete_fail = "自分自身とスーパユーザは削除できません。";
$admin_new_user = "新規ユーザ";

$admin_modules = 'モジュール';
$admin_module_access = 'このユーザでアクセスを許可するモジュール:';

$admin_password = 'パスワード';
$admin_confirm_password = 'パスワードを確認';
$admin_change_password = 'パスワードを変更';
$admin_pass_match = 'パスワードが合致しません。入力し直してください。';

//registration
$registration_personal_info = "個人情報";
$registration_account_info = "アカウント情報";
$registartion_username_text = "サイトにログインするため、ユーザ名とパスワードを入力してください。";

$registration_question_text=<<<registration_question_text
このユーザしかわからない秘密の質問を入力してください。 ユーザがパスワードを忘れたときのための質問です。
解答が正しかった場合、登録されたE-mailアドレス宛てに、ログインするためのユーザ名と新しいパスワードが送られてきます。
registration_question_text;

$registration_text=<<<registration_text
登録するためには、フォームに入力して「OK」をクリックしてください。* は必須項目です。<br />
あなたの情報はGroup-Officeでしか使用せず、第三者には提供しません。
registration_text;

$registartion_authcode_text = "認証コードを入力してください。認証コードは他ののユーザが連絡先リストを追加、または詳細を閲覧するために必要なものです。";
$registartion_confirm_authcode = "認証コードを確認";
$registration_visibility = "このユーザを全てのユーザに閲覧可能にする";
$registration_success = "<h1>おめでとうございます</h1>登録が完了しました。ウェルカムメッセージを以下のアドレスに送信しました:";
$registration_failure = "あなたの登録情報を作成中にエラーが発生しました。やり直してください。";

$registration_folder_error = "個人フォルダの作成に失敗しました。修復するために連絡してください。";


$registration_email = "Group-OfficeでこのユーザのE-mailアドレスを作成しました。";
$registration_samba = "ファイルとプリントサーバ";
$registration_email_error = "E-mailアカウント作成でエラーが発生しました。サポートに連絡を取ってください。";
$strPasswordConfirm = "確認パスワード";
$strPassword = "パスワード";

$reg_company_info = "会社情報";

$max_user_limit = "ユーザの最大人数です。";
$max_users_text = "これ以上ユーザを登録できません。ユーザを増やす場合には連絡をしてください。";
$ml_sent_items = "送信済みアイテム";
$ml_spam = "スパム";
$ac_samba_user = "プリントサーバとファイルサーバにユーザがアクセスしました";
$ml_email_only = "アドレスを作成しない場合のみ要求する";
$register_new_user = '新規 Group-Office 契約者';
$register_thanks = '<h2>ありがとうございます</h2>サブスクリプション契約はすぐに完了します。 また、追って通知が送られてきます。';
$register_accept = 'ユーザ追加';
$register_addressbook = 'アドレス帳追加';
$register_title = '寄付';


//1.98
$reg_language = '言語';
$reg_theme = 'テーマ';

//2.05
$admin_use = '使用';
$admin_module = 'モジュール';
$admin_manage = '管理';

$admin_groups = 'ユーザグループ';
$admin_groups_user = '以下のユーザグループのメンバーです';

//2.11

//Modified string (Added %groupoffice_title%)
$registration_mail_body=<<<registration_mail_body
%title% %last_name%, 様

%groupoffice_title%. として登録されました。
このサービスをお楽しみください。

以下のアカウントでログイン可能です:

ユーザ名: %username%
パスワード: %password%

以下のリンクをクリックしてログイン:

registration_mail_body;

//2.13
$registration_mail_subject = " %groupoffice_title% へようこそ"; //changed string
$registration_title = " %groupoffice_title% へ登録"; //changed string

$lang_modules['users'] = 'ユーザ';

//2.14
$users_enabled = 'アカウント有効';

$admin_groups_visible = 'このユーザは以下のユーザグループに閲覧可能';
$admin_look_feel = 'ルックアンドフィール';

<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('addressbook'));

//contacts
$contacts_title = "アドレス帳";
$contacts_no_contacts = "連絡先はありません";
$contacts_members = "メンバー";
$contacts_contacts = "連絡先";
$contacts_add = "新規連絡先";
$contacts_groups = "グループ";
$contacts_other = "そのほかの連絡先";
$contacts_move_to_group = "選択した連絡先をグループに移動...";
$groups_no_groups = "グループはありません。";
$contacts_group = "グループ";
$contacts_empty_group = "このグループに連絡先を移動するため、ドロップダウンボックスを使います";
$contacts_this_group = 'このグループ';

$add_contacts_text = "メールアドレスと認証コードを入力して、「OK」をクリックしてください。";
$add_contacts_success = "ユーザを追加しました。";
$add_contacts_wrong = "E-mailか認証コードが誤っています。";
$add_personal_text = "「保存」をクリックして連絡先に追加してください。*のついた項目は必須です。";
$contacts_select = "追加する連絡先を選択して「OK」をクリックしてください。";
$contacts_select_title = "連絡先";
$contact_profile = "プロフィール";
$user_profile = "ユーザプロフィール";
$contacts_authorise = "認証";
$contacts_add_member = "連絡先にメンバーを追加";
$strConfirmSynchronise = "ユーザが入力を済ませたデータを元に連絡先を更新します。 マニュアルは変更されます。 続行してよろしいですか？";
$contacts_import = "インポート";
$contacts_export = "エキスポート";
$contacts_export_text = "生成したいCSVファイルを選択してください。";
$contacts_delete = "削除";

$contacts_import_text_group = "グループとCSVファイルを選択して「OK」をクリックしてください。";
$fbNoFile = "ファイルを送信できませんでした。";
$fbPleaseWait = "ファイルをアップロードしています。しばらくお待ちください...";

$strNotIncluded = "インポートできません。";
$contacts_import_feedback = "以下の項目はCSVファイルから見つかったものです。<br />";
$contacts_import_feedback .= "ファイルの項目を元に自動的に作成するグループか、インポートしたいグループを選択してください。<br />";
$contacts_import_feedback .= " Match corrosponding Group-Office columns with the columns from the file and make sure that you don't select columns twice.";
$contacts_separator = "区切り文字の値";
$contacts_no_separator = "区切り文字を入力していません。";
$fbFilename = "ファイル名";
$contacts_import_title = "アドレス帳をインポート";
$contacts_import_success = "インポートが成功しました。";
$contacts_import_double = "項目が二度選択されました。再度確認してください。";
//changed:
$contacts_import_noname = ' \'名前\' と \'苗字\' は必ず選択してください。';
$contacts_import_incompatible = "不適切なファイルフォーマットです<br /><br />CSVファイルはカンマかドットで区切られています。";
$strMember = "メンバー";
$contact_exist_warning = "<b>注意:</b> このユーザは連絡先リストに既に存在します。";
$contacts_add_to_group = "選択したユーザをグループの連絡先に追加します。...";
$contacts_add_to = "選択したユーザを連絡先に追加";
$contacts_add_user = "追加";
$contacts_edit = "編集";
$contacts_no_select = "何も選択されていません。";
$contacts_this_contact = "この連絡先";

$contacts_seperator_text = '区切り文字の値';
$contacts_quote = '囲む値';
$contacts_clrf = '終端ライン';
$contacts_auto_group = 'ファイルから自動的にグループを作成する';
$contacts_import_to_group = 'インポート先グループ';

$contacts_search = '検索';
$cmdSearch = '検索';
$contacts_search_text = "語句を選ぶか検索語句を入力して「検索」をクリックしてください。*でワイルドカードを指定できます。";
$contacts_results = '結果';
$contacts_result = '結果';


$contacts_color = '色';

$contacts_colors['black'] = '黒';
$contacts_colors['red'] = '赤';
$contacts_colors['green'] = '緑';
$contacts_colors['blue'] = '青';
$contacts_colors['pink'] = 'ピンク';
$contacts_colors['orange'] = '橙';
$contacts_colors['brown'] = '茶';

$ab_addressbooks = 'アドレス帳';
$ab_owner = '所有者';
$ab_subscribed = '署名';
$ab_new_ab = '新規アドレス帳';
$ab_addressbook_exists = '同じ名前のアドレス帳が存在します。';
$ab_addressbook = 'アドレス帳';
$ab_no_addressbook = 'アドレス帳がありません';
$contacts_select_single = '個人を選択するために連絡先をクリックしてください。';

$ab_group_exists = '同じ名前のアドレス帳が既に存在します。';
$ab_group_on_file = 'グループ連絡先の元のファイル';
$ab_default = 'デフォルト';

//translation required
$ab_edit_contact = '「OK」をクリックして変更を保存してください。*は必須項目です。';
$ab_group_on_file = 'グループ連絡先の元のファイル';
$ab_default = 'デフォルト';
$ab_browse = 'ブラウズ';
$ab_comment = '注釈';

$ab_contact = '連絡先';
$ab_send_message = 'E-mail';
$ab_new_event = 'イベント';

$ab_templates = 'テンプレート';
$ab_your_templates = 'あなたの';
$ab_all_templates = 'すべて';

$template_type[0] = 'E-mail';
$template_type[1] = 'OpenOffice.Org';
$ab_type = '種類';
$ab_template_exists = '同じ名前のテンプレートが存在します。';

$sir_madam['M'] = '';
$sir_madam['F'] = '';

$ab_upload_oo = 'OpenOffice.orgテンプレートをアップロードします。';
$ab_upload_nofile = 'ファイル送信エラー';

$ab_oo_doc = 'テキストドキュメント';
$ab_select_template = 'ドキュメントのテンプレートを選択:';
$ab_add_field_data = 'ドキュメントに以下の値を使用可能:';
$ab_datafield = 'データ項目';

$ab_no_template = 'テンプレートなし';
$ab_new_note = 'メモ';

$ab_events = 'アポイント';

$ab_new_contact = '新規連絡先';
$ab_new_company = '新規会社';
$ab_companies = '会社';
$ab_company_properties = '会社プロフィール';
$ab_bank_no = 'バックナンバー';
$ab_companies_count_single = '会社';
$ab_companies_count = '会社';
$ab_no_companies = '会社がありません。';
$ab_no_company = '会社がありません。';

$ab_import_type = 'インポート';
$ab_no_company_found = '会社がありません。';
$ab_search_company = '会社を参照';
$ab_edit_company = '会社プロフィールを編集';
$ab_new_group = '新規グループ';
$ab_export_type = 'エキスポート';

$ab_custom_fields = 'エクストラデータ項目';
$ab_field_size = 'サイズ';

$ab_new_field = '新規データ項目';
$ab_invalid_field_size = '無効なサイズです。10～255の間を選択してください。';
$ab_field_exists = '同じ名前の項目が存在します。';

$ab_employees = '従業員';
$ab_no_employees = '従業員がありません。';

$ab_employee_count_single = '従業員';
$ab_employee_count_multiple = '従業員';
$ab_vat_no = 'VAT番号';

$ab_add_new = '新規追加';
$ab_add_existing = '既存のものを追加';

$ab_access = 'アクセス';
//1.98
$ab_mailings = 'メーリング';
$ab_mailing_groups = 'メーリンググループ';
$ab_no_mailing_groups = 'メーリンググループがありません。';
$ab_no_templates = 'テンプレートがありません。';
$ab_mailing_group_exists = '同じ名前のメーリンググループが存在します。';
$ab_contacts_count_single = '連絡先';
$ab_contacts_count_multiple = '連絡先';
$ab_new_mailing = '新規メーリング';
$ab_all_your_addressbooks = 'すべてのアドレス帳';

$ab_search_for = '検索';
$ab_search_on = 'on';
$ab_search_keyword = '検索語句';
$ab_wildcard = '(ワイルドカード\'%\'を使用する)';

//2.03
$ab_copy_read_acl = '連絡先と会社に読み込み権限をコピー';
$ab_copy_write_acl = '連絡先と会社に書き込み権限をコピー';

$ab_catagory_exists = '同じ名前のカテゴリが存在します。';
$ab_add_catagory = 'カテゴリを追加';
$ab_add_field = '項目を追加';
$ab_catagory = 'カテゴリ';

//2.04
$ab_todos = 'Todo\'s';


//2.05
$ab_download = 'ダウンロード';
$ab_download_template = 'ダウンロードボタンをクリックして、テンプレートをPCにダウンロードして編集してください';
$ab_upload = 'アップロード';
//modified string:
$ab_upload_oo_text = 'OpenOffice.orgテンプレートを編集したら、Group-Officeにフィードバックのために送信してください。作成したテンプレートを転送するので、ファイルを選択してアップロードをクリックしてください';
$ab_file_type = 'ファイルタイプ';
$ab_import_vcf_file_ok = 'アップロードに成功しました。アドレス帳に追加するためにOKをクリックしてください';
$ab_import_failed = 'インポート失敗';
//modified string:
$contacts_import_text = "インポートするためのファイルデータを選択し、OKをクリックしてください";

//2.06
$ab_select_mailing_groups = '連絡先が所属するメーリンググループを選択してください';

//2.11
$ab_move_to_ab = '選択した連絡先をアドレス帳に移動...';
$ab_move_com_to_ab = '選択した会社をアドレス帳に移動...';

//2.12
$ab_search_sender = 'e-mail検索';

$ab_contact_exists = '連絡先は既に存在しているようです。 %s をクリックして既存の連絡先を参照するか \'OK\' か \'適用\' をクリックして再度連絡先を保存してください。';
$ab_company_exists = '会社は既に存在しているようです。 %s をクリックして既存の会社を参照するか \'OK\' か \'適用\'をクリックして再度会社を保存してください。';
$ab_here = 'ここ';

//2.13
$ab_search_for_keyword = 'キーワード';

$ab_search_in = '検索対象'; //changed
$ab_search_on_users = '検索ユーザ';

$ab_users_count_single='ユーザ';
$ab_users_count_multiple='ユーザ';
$ab_no_users='ユーザは見つかりませんでした';

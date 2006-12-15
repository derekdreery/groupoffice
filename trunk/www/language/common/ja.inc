<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_base_language_file('common'));
$htmldirection='ltr';
$charset = 'UTF-8';
//main language menu file register login and other non logged in pages
//common
$cmdLogin = "ログイン";
$cmdSave = "保存";
$cmdBack = "戻る";
$cmdCancel = "取消";
$cmdClose = "閉じる";
$cmdOk = "Ok";
$cmdDelete = "削除";
$cmdNext = "次へ";
$cmdPrevious = "前へ";
$cmdContinue = "続ける";
$cmdUpdate = "同期";
$cmdApply = "適用";
$cmdYes = "はい";
$cmdNo = "いいえ";
$cmdAdd = '追加';
$cmdEdit = '編集';
$cmdInvertSelection = '反転選択';

$strEdit = "編集";
$strThis = 'この';
$strItems = 'アイテム';
$strDescription = '説明';

$strName = "名前";
$strUser = "ユーザ";
$strUsername = "ユーザ名";
$strPassword = "パスワード";
$strAuthcode = "認証コード";
$strCountry = "国";
$strState = "県";
$strCity = "市区町村";
$strZip = "郵便番号";
$strAddress = "番地";
$strEmail = "E-mail";
$strPhone = "電話番号";
$strWorkphone = "仕事先電話番号";
$strCellular = "携帯電話";
$strCompany = "会社";
$strDepartment = "部署";
$strFunction = "課";
$strQuestion = "質問を選ぶ";
$strAnswer = "回答";
$strFax = "Fax";
$strWorkFax = "Fax（会社）";
$strHomepage = "ホームページ";
$strWorkAddress = "住所（会社）";
$strWorkZip = "郵便番号（会社）";
$strWorkCountry = "国（会社）";
$strWorkState = "県（会社）";
$strWorkCity = "市区町村（会社）";
$strPhoto = "写真";
$strComments = "コメント";
$strProperties = 'プロパティ';
$strDeleteItem = "削除";
$strDate = "日付";
$strSaveSuccess = "データを保存しました。";
$strSaveError = "データの保存に失敗しました。";
$strDataError = "データの読み込みにに失敗しました。";
$strDeleteError = "データを削除できません。";
$strAclError = "アクセスコントロールエラーです。";
$strDateError = "不正な日付です。";
$strAccessDenied = "アクセスが拒否されました";
$AccessDenied_text = "サイトのこのセクションを閲覧、またはこのアクションを実行する権限がありません or to perform this action.<br />ほかのユーザがこの情報にアクセスすることは制限されています。";
$invalid_chars = "不正な文字です。";
$strGroups = "グループ";
$strOwner = "オーナー";
$strDeletePrefix = "本当に削除してよろしいですか";
$strDeleteSuffix = "?";
//menu
$menu_logout = "ログアウト";
$menu_applications = "アプリケーション";
$menu_help = "ヘルプ";
$menu_about = "詳細";
$menu_documentation = "書類";
$menu_configuration = "設定";
$menu_groups = "ユーザグループ管理";
$menu_accounts = "あなたのアカウント設定";
$menu_preferences = "個人プリファレンス";
$menu_search_discussions = "ディスカッション";
$menu_search_files = "ファイルとフォルダ";
$menu_search_web = "ウェブ";
$menu_filetypes = "ファイルタイプ";
$menu_discussions = "ディスカッションボード";

$menu_users = "ユーザ管理";
$menu_new = "新規メニュー";
$menu_message = "メッセージ";
$menu_contact = "コンタクト";
$menu_event = "イベント";
$menu_bookmark = "ブックマーク";
$menu_topics = "トピックス";
$menu_support = "サポート";
$menu_projects = "プロジェクト";
$menu_sysinfo = "システム情報を表示";
$menu_admin = '管理者メニュー';
$menu_modules = 'モジュールの追加/削除';
$menu_manual = 'マニュアル';

//class validate
$error_required = "必須項目が指定されていません。";
$error_match_auth = "認証コードが間違っています。";
$error_match_pass = "パスワードが間違っています。";
$error_min_length = "フィールドの最小文字数:";
$error_max_length = "フィールドの最大文字数:";
$error_email = "指定したE-mailアドレスは無効です。";
$error_email_exists = "入力したE-mailアドレスは存在しています。";
$error_username_exists = "入力したユーザ名は存在しています。違うものを選択してください。";
$errors_in_form = "フォームエラーです。正確に入力しなおしてください。";
$error_missing_field = "すべての必須項目が入力されていません。";

//acl control
$acl_control_auth_groups = "認証グループ";
$acl_control_auth_users = "認証ユーザ";
$acl_control_no_contacts = "選択した連絡帳にメンバーはいません。<br /> アドレス帳を使って連絡帳にメンバーを追加してください。";

$strShowProfile = "プロパティを表示";
$strShowGroup = "グループメンバーを表示";
$strProcessingTime = "ページ作成時間 ";
$strLoggedInAs = "ログイン：";
$strAt = " at ";
$strEmailTo = "メッセージの送信先: ";
$strProtectedUser = "保護されたユーザ";

$http_not_found = "書類が見つかりません。";
$http_not_found_text = "書類が見つかりませんでした。URLを正しく入力してください。";
$http_error = "Internal Server Error";
$http_bad_request = "不正なリクエストです";
$strAddContact = "連絡先に追加";

$strReadRights = "読込権限";
$strWriteRights = "書込権限";
$strPermissions = '権限';

$days[0]="日";
$days[1]="月";
$days[2]="火";
$days[3]="水";
$days[4]="木";
$days[5]="金";
$days[6]="土";

$months[0]="1月";
$months[1]="2月";
$months[2]="3月";
$months[3]="4月";
$months[4]="5月";
$months[5]="6月";
$months[6]="7月";
$months[7]="8月";
$months[8]="9月";
$months[9]="10月";
$months[10]="11月";
$months[11]="12月";

$full_days[0] = "日曜日";
$full_days[1] = "月曜日";
$full_days[2] = "火曜日";
$full_days[3] = "水曜日";
$full_days[4] = "木曜日";
$full_days[5]= "金曜日";
$full_days[6] = "土曜日";
$strLoadTime = '生成';
$strDescription = '記述';



$lang_modules['addressbook'] = "アドレス帳";
$cmdShowAll = 'すべて表示';
$cmdSearch = '検索';

//translation required
$cmdYesToAll = 'すべてに「はい」';
$cmdNoToAll = 'すべてに「いいえ」';

$short_months[0]="1月";
$short_months[1]="2月";
$short_months[2]="3月";
$short_months[3]="4月";
$short_months[4]="5月";
$short_months[5]="6月";
$short_months[6]="7月";
$short_months[7]="8月";
$short_months[8]="9月";
$short_months[9]="10月";
$short_months[10]="11月";
$short_months[11]="12月";

$strToday = '本日';

$cmdPrint = '印刷';
$strFirstName = '名前';
$strLastName = '名字';
$strInitials = 'イニシャル';
$strSex = '性別';
$strBirthday = '誕生日';
$strSexes['M'] = '男性';
$strSexes['F'] = '女性';

$strTitle = 'タイトル';
$strBeginning = 'Beginning';
$strMiddleName = 'ミドルネーム';
$sir_madam['M'] = 'Mr.';
$sir_madam['F'] = 'Ms.';
$strModifiedAt = '編集';
$strCreatedAt = '作成';

$htmlarea_language_name = 'ja';
$strLocalization = 'ローカライゼーション';


//look up the language in controls/jscalendar/lang
$jscalendar_language_name = 'calendar-ja.js';


//2.01 translations

//look up language in 'classes/phpmailer/language'
$php_mailer_lang = 'ja';
$strEmailFail = 'メールが送信されませんでした';

//for people that want to use ktmlarea
$ktml_language = 'japanese';

$ml_host_unreachable = ' は届きませんでした。 E-mailの自動チェックは無効になっています。';
$strRegion = 'リージョン';

//2.05
$cmdReset = 'リセット';
$strMyName = '名前';

//2.06
$strDeleteRow = 'この行を本当に削除しますか?';
$strAddressNo = '部屋番号';
$strWorkAddressNo = '職場の部屋番号';

$strPostAddress = 'Post address';
$strPostAddressNo = 'Post number of house';
$strPostCity = 'Post city';
$strPostState = 'Post state';
$strPostCountry = 'Post country';
$strPostZip = 'Post zip';
$strVisitAddress = 'Visit address';
$strPostAddress = 'Post address';



//modules
$lang_modules['scheduler'] = 'スケジューラ';
$lang_modules['calendar'] = "カレンダー";
$lang_modules['filesystem'] = 'ファイル';
$lang_modules['email'] = 'メール';
$lang_modules['projects'] = 'プロジェクト';
$lang_modules['search'] = '検索';
$lang_modules['contacts'] = "アドレス帳";
$lang_modules['cms'] = "ウェブサイト";
$lang_modules['notes'] = "ノート";
$lang_modules['bookmarks'] = "ブックマーク";
$lang_modules['summary'] = "概要";
$lang_modules['todos'] = "タスク";
$lang_modules['opentts'] = "ヘルプデスク";
$lang_modules['products'] = "ソフトウェアショップ";

$cmdVcard = 'vCard';

//2.07
$strNotEnoughLicenses = '以下のセクションを使用するライセンスがありません'.
												' Group-Office. Contact sales@intermesh.nl if you want '.
												'to purchase additional licenses.';
												
//2.08
$alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O',
									'P','Q','R','S','T','U','V','W','X','Y','Z');
									
$strContact = '連絡先';
$strRelatedTo = '関係者';
$strProject = 'プロジェクト';

$strNewProject = '新規プロジェクト';
$strNewEvent = '新規イベント';
$strNewTodo = '新規タスク';
$strNewNote = '新規メモ';

$strActivities = '活動';
$strFile = 'ファイル';

//2.10
$cmdCopy = 'コピー';
$strProjects = 'プロジェクト';
$strFiles = 'ファイル';
$cmdSaveNew = '新規保存';
$strAvailability = '利用権限';
$strCheckAvailabilityFor = '利用権限をチェック';
$cmdPleaseSelect = '選択してください';

//2.11
$strNewEmail = '新規メール';
$strConsistencyCheck = "整合性検査";
$strSearchAll = 'すべてのフィールド';
$sync_download_clients = '同期クライアントをダウンロード';

//2.12
$strNoData = 'データがありません';
//2.13
$strSynchronization = '同期';
$strSnooze = 'Snooze';
$strDismiss = 'Dismiss';

//2.14
$strUpload = 'ファイルアップロード';
$cmdInsert = '挿入';
$strText = 'テキスト';
$strType = 'タイプ';

$strNoItemSelected = 'アイテムが選択されていません';
$strSelectedItem = '選択アイテム';

$strKeyword = 'キーワード';

$strNoActivities = '無効なアイテム';

$strCopy = 'コピー'; //not a verb

//2.15
$strBank = '銀行';
$strBankNo = '口座番号';
$strSelectedItems = 'アイテムを選択';

$strMonth = '月';
$strYear = '年';
$strDay = '日';
$strHour = '時';
$strMinute = '分';

$strTwelveHourFormat = '12時間フォーマット';
$strTwentyfourHourFormat = '24時間フォーマット';

//2.16
$strCreateLink = 'リンクを作成';
$strCancelLink = 'リンクを取消';
$strLinkText = 'リンクは有効になりました。リンクされたアイテムを閲覧して \'リンクを作成\' ボタンをクリックして \'%s\' へのリンクを作成してください。 リンクが有効なアイテムを作成する場合、自動でリンクされるようになります。';
$strLinks = 'リンク';
$strUnlinkPrefix = 'リンクを取り消してもよろしいですか ';
$strUnlinkSuffix = '?';
$cmdUnlink = '非リンク';
?>

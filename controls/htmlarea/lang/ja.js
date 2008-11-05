// I18N constants

// LANG: "ja", ENCODING: UTF-8
// Author: Takeru Nomoto, http://monochrome-ash.myvnc.com

// FOR TRANSLATORS:
//
//   1. PLEASE PUT YOUR CONTACT INFO IN THE ABOVE LINE
//      (at least a valid email address)
//
//   2. PLEASE TRY TO USE UTF-8 FOR ENCODING;
//      (if this is not possible, please include a comment
//       that states what encoding is necessary.)

HTMLArea.I18N = {

	// the following should be the filename without .js extension
	// it will be used for automatically load plugin language.
	lang: "ja",

	tooltips: {
		bold:           "太字",
		italic:         "斜体",
		underline:      "下線",
		strikethrough:  "打ち消し線",
		subscript:      "下付き添え字",
		superscript:    "上付き添え字",
		justifyleft:    "左寄せ",
		justifycenter:  "中央寄せ",
		justifyright:   "右寄せ",
		justifyfull:    "均等割付",
		orderedlist:    "番号付き箇条書き",
		unorderedlist:  "記号付き箇条書き",
		outdent:        "インデント解除",
		indent:         "インデント設定",
		forecolor:      "文字色",
		hilitecolor:    "背景色",
		horizontalrule: "水平線",
		createlink:     "リンク挿入",
		insertimage:    "画像挿入／編集",
		inserttable:    "テーブル挿入",
		htmlmode:       "HTML表示切替",
		popupeditor:    "エディタ拡大",
		about:          "このエディタについて",
		showhelp:       "ヘルプ",
		textindicator:  "現在のスタイル",
		undo:           "元に戻す",
		redo:           "繰り返す",
		cut:            "選択場所を切り取り",
		copy:           "選択場所をコピー",
		paste:          "クリップボードから貼り付け",
		lefttoright:    "左から右",
		righttoleft:    "右から左"
	},

	buttons: {
		"ok":           "OK",
		"cancel":       "取り消し"
	},

	msg: {
		"Path":         "パス",
		"TEXT_MODE":    "現在はテキストモードです。[<>]ボタンを使ってWYSIWYGモードに切り替えてください。",

		"IE-sucks-full-screen" :
		// translate here
		"フルスクリーンモードはインターネットエクスプローラに問題を起こさせます。 " +
		"ブラウザのバグのため回避できませんでした。Windows 9x をご利用の場合、不愉快な思いをさせてしまことでしょう。 " +
		"表示、エディタ機能の不足 または突然のブラウザクラッシュのいずれか、またはすべての現象に会うかもしれません。 " +
		"例えば「一般保護違反」が表示され、再起動を余儀なくされることもあります。\n\n" +
		"それでもフルスクリーンエディタを使用する場合、よく注意して「OK」を押してください。",

		"Moz-Clipboard" :
		"特別なスクリプトは、切り取り・コピー・貼り付けをすることができません。 " +
		"これはセキュリティを配慮しての措置です。 OKをクリックしてmozilla.orgのテクニカルノートを見て " +
		"スクリプトをクリップボードにアクセスさせる方法を読んでください。"
	},

	dialogs: {
		"Cancel"                                            : "取り消し",
		"Insert/Modify Link"                                : "リンク挿入・編集",
		"New window (_blank)"                               : "新規ウィンドウ (_blank)",
		"None (use implicit)"                               : "なし (use implicit)",
		"OK"                                                : "OK",
		"Other"                                             : "その他",
		"Same frame (_self)"                                : "同じフレーム (_self)",
		"Target:"                                           : "ターゲット:",
		"Title (tooltip):"                                  : "タイトル (tooltip):",
		"Top frame (_top)"                                  : "トップフレーム (_top)",
		"URL:"                                              : "URL:",
		"You must enter the URL where this link points to"  : "このリンクをポイントしたときのURLを入力してください。"
	}
};

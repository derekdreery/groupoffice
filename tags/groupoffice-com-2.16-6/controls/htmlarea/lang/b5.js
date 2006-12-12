// I18N constants

// LANG: "en", ENCODING: UTF-8 | ISO-8859-1
// Author: Mihai Bazon, http://dynarch.com/mishoo

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
	lang: "b5",

	tooltips: {
		bold:           "粗體",
		italic:         "斜體",
		underline:      "底線",
		strikethrough:  "刪除線",
		subscript:      "下標",
		superscript:    "上標",
		justifyleft:    "靠左",
		justifycenter:  "置中",
		justifyright:   "靠右",
		justifyfull:    "左右平等",
	orderedlist:    "編號順序",
	unorderedlist:  "符號順序",
		outdent:        "減少縮排",
		indent:         "增加縮排",
		forecolor:      "文字顏色",
		hilitecolor:    "背景顏色",
	horizontalrule:  "水平線",
		createlink:     "插入連結",
		insertimage:    "插入/變更圖像",
		inserttable:    "插入表格",
		htmlmode:       "切換HTML原始碼",
		popupeditor:    "放大",
		about:          "關於這個編輯器",
		showhelp:       "使用編輯器說明",
		textindicator:  "當前風格",
		undo:           "復原你/妳最後的動作",
		redo:           "重做你/妳最後的動作",
		cut:            "剪下",
		copy:           "複製",
		paste:          "貼上",
		lefttoright:    "從左到右",
		righttoleft:    "從右到左"
	},

	buttons: {
		"ok":           "確定",
		"cancel":       "取消"
	},

	msg: {
		"Path":         "路徑",
		"TEXT_MODE":    "你現正在文字模式，請使用[<>]按鈕切換到所見即所得模式。",

		"IE-sucks-full-screen" :
		// translate here
		"The full screen mode is known to cause problems with Internet Explorer, " +
		"due to browser bugs that we weren't able to workaround.  You might experience garbage " +
		"display, lack of editor functions and/or random browser crashes.  If your system is Windows 9x " +
		"it's very likely that you'll get a 'General Protection Fault' and need to reboot.\n\n" +
		"You have been warned.  Please press OK if you still want to try the full screen editor."
	},

	dialogs: {
		"Cancel"                                            : "取消l",
		"Insert/Modify Link"                                : "插入/修改連結",
		"New window (_blank)"                               : "新的視窗(_blank)",
		"None (use implicit)"                               : "沒有(use implicit)",
		"OK"                                                : "確定",
		"Other"                                             : "其他",
		"Same frame (_self)"                                : "同一窗格 (_self)",
		"Target:"                                           : "目標:",
		"Title (tooltip):"                                  : "標題 (tooltip):",
		"Top frame (_top)"                                  : "頂窗格 (_top)",
		"URL:"                                              : "URL:",
		"You must enter the URL where this link points to"  : "你/妳必須輸入連結的目標URL"
	}
};

// I18N constants

// LANG: "cz", ENCODING: UTF-8 | ISO-8859-2
// Author: Jiri Löw, <jirilow@jirilow.com>

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
	lang: "cz",

	tooltips: {
		bold:           "Tučně",
		italic:         "Kurzíva",
		underline:      "Podtržení",
		strikethrough:  "Přeškrtnutí",
		subscript:      "Dolní index",
		superscript:    "Horní index",
		justifyleft:    "Zarovnat doleva",
		justifycenter:  "Na střed",
		justifyright:   "Zarovnat doprava",
		justifyfull:    "Zarovnat do stran",
		orderedlist:    "Seznam",
		unorderedlist:  "Odrážky",
		outdent:        "Předsadit",
		indent:         "Odsadit",
		forecolor:      "Barva písma",
		hilitecolor:    "Barva pozadí",
		horizontalrule: "Vodorovná čára",
		createlink:     "Vložit odkaz",
		insertimage:    "Vložit obrázek",
		inserttable:    "Vložit tabulku",
		htmlmode:       "Přepnout HTML",
		popupeditor:    "Nové okno editoru",
		about:          "O této aplikaci",
		showhelp:       "Nápověda aplikace",
		textindicator:  "Zvolený styl",
		undo:           "Vrátí poslední akci",
		redo:           "Opakuje poslední akci",
		cut:            "Vyjmout",
		copy:           "Kopírovat",
		paste:          "Vložit"
	},

	buttons: {
		"ok":           "OK",
		"cancel":       "Zrušit"
	},

	msg: {
		"Path":         "Cesta",
		"TEXT_MODE":    "Jste v TEXTOVÉM REŽIMU.  Použijte tlačítko [<>] pro přepnutí do WYSIWIG.",

		"IE-sucks-full-screen" :
		// translate here
		"The full screen mode is known to cause problems with Internet Explorer, " +
		"due to browser bugs that we weren't able to workaround.  You might experience garbage " +
		"display, lack of editor functions and/or random browser crashes.  If your system is Windows 9x " +
		"it's very likely that you'll get a 'General Protection Fault' and need to reboot.\n\n" +
		"You have been warned.  Please press OK if you still want to try the full screen editor.",

		"Moz-Clipboard" :
		"Unprivileged scripts cannot access Cut/Copy/Paste programatically " +
		"for security reasons.  Click OK to see a technical note at mozilla.org " +
		"which shows you how to allow a script to access the clipboard."
	},

	dialogs: {
		"Cancel"                                            : "Zrušit",
		"Insert/Modify Link"                                : "Vlož/změň odkaz",
		"New window (_blank)"                               : "Nové okno (_blank)",
		"None (use implicit)"                               : "Nedefinováné (použil implicitní)",
		"OK"                                                : "OK",
		"Other"                                             : "Ostatní",
		"Same frame (_self)"                                : "Tento frame (_self)",
		"Target:"                                           : "Cíl (target):",
		"Title (tooltip):"                                  : "Titulek (tooltip):",
		"Top frame (_top)"                                  : "Top frame (_top)",
		"URL:"                                              : "URL:",
		"You must enter the URL where this link points to"  : "Musíte zadat URL pro odkaz"
	}
};

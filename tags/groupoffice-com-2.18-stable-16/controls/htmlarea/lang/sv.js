// I18N constants

// LANG: "sv", ENCODING: UTF-8 | ISO-8859-1
// Author: Mihai Bazon, http://dynarch.com/mishoo

// FOR TRANSLATORS:
// Calendar SV language (Swedish, svenska)
// Translator: Johannes Petersson <na>
// Last translator: Johannes Petersson <na>
// Encoding: utf-8
// Distributed under the same terms as the calendar itself.
//   1. PLEASE PUT YOUR CONTACT INFO IN THE ABOVE LINE
//      (at least a valid email address)
//
//   2. PLEASE TRY TO USE UTF-8 FOR ENCODING;
//      (if this is not possible, please include a comment
//       that states what encoding is necessary.)

HTMLArea.I18N = {

	// the following should be the filename without .js extension
	// it will be used for automatically load plugin language.
	lang: "sv",

	tooltips: {
		bold:           "Fet",
		italic:         "Italic",
		underline:      "Underlinjerat",
		strikethrough:  "Genomstruket",
		subscript:      "Subscript",
		superscript:    "Superscript",
		justifyleft:    "Justera Vänster",
		justifycenter:  "Justera Centrerat",
		justifyright:   "Justera Höger",
		justifyfull:    "Justera Fullt",
		orderedlist:    "Ordnad Lista",
		unorderedlist:  "Bullet Lista",
		outdent:        "Minska Indentering",
		indent:         "Öka Indentering",
		forecolor:      "Font Färg",
		hilitecolor:    "Bakgrundsfärg",
		horizontalrule: "Horisontell Linje",
		createlink:     "Lägg till Webb Länk",
		insertimage:    "Lägg till/Modifiera Bild",
		inserttable:    "Lägg till Tabell",
		htmlmode:       "Toggla HTML Källa",
		popupeditor:    "Förstora Editorn",
		about:          "Om editorn",
		showhelp:       "Hjälp med editorn",
		textindicator:  "Nuvarande stil",
		undo:           "Ångra föregående kommando",
		redo:           "Gör om föregående kommando",
		cut:            "Klipp ut markering",
		copy:           "Kopiera markering",
		paste:          "Klistra in från klippbordet",
		lefttoright:    "Riktning vänster till höger",
		righttoleft:    "Riktning höger till vänster"
	},

	buttons: {
		"ok":           "OK",
		"cancel":       "Avbryt"
	},

	msg: {
		"Path":         "Sökväg",
		"TEXT_MODE":    "Du är i TEXT LÄGE.  Använd [<>] knappen för att byta tillbaka till WYSIWYG.",

		"IE-sucks-full-screen" :
		// translate here
		"Fullskärms läge kan orsaka problem med Internet Explorer, " +
		"beroende på buggar som vi inte kunde komma runt.  Du kan erfara skräp " +
		"tecken, avsaknad av editor funktioner och/eller krasher.  Om du använder Windows 9x " +
		"är det högst troligt att du får ett 'General Protection Fault' och måste boota om.\n\n" +
		"Du har blivit varnad.  Tryck OK om du fortfarande vill använda fullskärms editorn.",

		"Moz-Clipboard" :
		"Oprivilegerade skript kan inte komma åt Klipp ut/Kopiera/Klistra in " +
		"av säkerhetssjäl.  Klicka OK för att se en teknisk anteckning på mozilla.org " +
		"som visar dig hur du kan tillåta ett skript att få tillgång till clipboardet."
	},

	dialogs: {
		"Cancel"                                            : "Avbryt",
		"Insert/Modify Link"                                : "Lägg till/Modifiera Länk",
		"New window (_blank)"                               : "Nytt fönster (_blank)",
		"None (use implicit)"                               : "None (use implicit)",
		"OK"                                                : "OK",
		"Other"                                             : "Annan",
		"Same frame (_self)"                                : "Samma ram (_self)",
		"Target:"                                           : "Mål:",
		"Title (tooltip):"                                  : "Titel (tooltip):",
		"Top frame (_top)"                                  : "Top ram (_top)",
		"URL:"                                              : "URL:",
		"You must enter the URL where this link points to"  : "Du måste skriva in URLen som denna länk pekar på"
	}
};

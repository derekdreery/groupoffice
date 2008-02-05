// I18N constants

// LANG: "ca", ENCODING: UTF-8
// Author: Jordi Sanfeliu, <jordi@fibranet.com>

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
	lang: "ca",

	tooltips: {
		bold:           "Negreta",
		italic:         "Cursiva",
		underline:      "Subratllat",
		strikethrough:  "Barrat",
		subscript:      "Subíndex",
		superscript:    "Superíndex",
		justifyleft:    "Justifica a l'esquerra",
		justifycenter:  "Centra",
		justifyright:   "Justifica a la dreta",
		justifyfull:    "Justifica",
		orderedlist:    "Llista ordenada",
		unorderedlist:  "Llista de pics",
		outdent:        "Redueix la sagna",
		indent:         "Augmenta la sagna",
		forecolor:      "Color de lletra",
		hilitecolor:    "Color de fons",
		horizontalrule: "Línia horitzontal",
		createlink:     "Insereix enllaç de web",
		insertimage:    "Insereix/Modifica la imatge",
		inserttable:    "Insereix taula",
		htmlmode:       "Mostra el codi HTML",
		popupeditor:    "Amplia l'editor",
		about:          "Quant aquest editor",
		showhelp:       "Mostra ajuda de l'editor",
		textindicator:  "Estil actual",
		undo:           "Desfés la darrera acció",
		redo:           "Refés la darrera acció",
		cut:            "Retalla la selecció",
		copy:           "Copia la selecció",
		paste:          "Enganxa del porta-retalls",
		lefttoright:    "D'esquerra a dreta",
		righttoleft:    "De dreta a esquerra"
	},

	buttons: {
		"ok":           "D'acord",
		"cancel":       "Cancel·la"
	},

	msg: {
		"Path":         "Camí",
		"TEXT_MODE":    "Esteu en Mode Text. Feu servir el botó [<>] per canviar a mode WYSIWYG.",

		"IE-sucks-full-screen" :
		// translate here
		"El mode de pantalla completa se sap que pot donar problemes amb el navegador Internet Explorer, " +
		"degut a errors de programari que no podem solucionar nosaltres. Per tant, és possible que veieu brossa per la pantalla, " +
		"manca de funcions a l'editor i/o fallades aleatòries del propi navegador. Si el vostre sistema és Windows 9x " +
		"és molt possible que rebeu una 'Fallada de Protecció General' i hàgiu de reiniciar l'ordinador.\n\n" +
		"Un cop avisat, premeu D'acord si encara voleu provar l'editor en mode de pantalla completa.",

		"Moz-Clipboard" :
		"Els scripts sense privilegis no poden accedir programàticament a Retalla/Copia/Enganxa " +
		"per raons de seguretat.  Feu clic a D'acord per veure una nota tècnica a mozilla.org " +
		"la qual us mostrarà com permetre a l'script l'accés al porta-retalls."
	},

	dialogs: {
		"Cancel"                                            : "Cancel·la",
		"Insert/Modify Link"                                : "Insereix/Modifica l'enllaç",
		"New window (_blank)"                               : "Nova finestra (_buida)",
		"None (use implicit)"                               : "Cap (useu implicitament)",
		"OK"                                                : "D'acord",
		"Other"                                             : "Un altre",
		"Same frame (_self)"                                : "El mateix marc (_mateix)",
		"Target:"                                           : "Objectiu:",
		"Title (tooltip):"                                  : "Títol (rètol indicador de funció):",
		"Top frame (_top)"                                  : "Marc superior (_superior)",
		"URL:"                                              : "URL:",
		"You must enter the URL where this link points to"  : "Heu d'entrar la URL on ha d'apuntar aquest enllaç"
	}
};

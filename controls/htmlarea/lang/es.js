// I18N constants

// LANG: "en", ENCODING: UTF-8
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
		bold:           "Negrita",
		italic:         "Cursiva",
		underline:      "Subrayado",
		strikethrough:  "Tachado",
		subscript:      "Subíndice",
		superscript:    "Superíndice",
		justifyleft:    "Alinear a la izquierda",
		justifycenter:  "Centrar",
		justifyright:   "Alinear a la derecha",
		justifyfull:    "Justificar",
		orderedlist:    "Lista ordenada",
		unorderedlist:  "Lista no ordenada",
		outdent:        "Aumentar sangría",
		indent:         "Disminuir sangría",
		forecolor:      "Color del texto",
		hilitecolor:    "Color de fondo",
		horizontalrule: "Línea horizontal",
		createlink:     "Insertar enlace web",
		insertimage:    "Insertar/Modificar imagen",
		inserttable:    "Insertar tabla",
		htmlmode:       "Ver documento en HTML",
		popupeditor:    "Apliar editor",
		about:          "Acerca del edtor",
		showhelp:       "Ayuda",
		textindicator:  "Estilo actual",
		undo:           "Deshacer",
		redo:           "Rehacer",
		cut:            "Cortar selección",
		copy:           "Copiar selección",
		paste:          "Pegar desde el portapapeles",
		lefttoright:    "De izquierda a derecha",
		righttoleft:    "De derecha a izquierda"
	},

	buttons: {
		"ok":           "Aceptar",
		"cancel":       "Cancelar"
	},

	msg: {
		"Path":         "Ruta",
		"TEXT_MODE":    "Esta en Modo Texto. Use el botón [<>] para cambiar a WYSIWYG.",

		"IE-sucks-full-screen" :
		// translate here
		"El modo de pantalla completa puede causar problemas con el Internet Explorer, " +
		"debido a errores de programación que nosotros no podemos solucionar.  Es posible que vea basura en la pantalla, " +
		"falta de funciones en el editor y/o fallos aleatorios del propio navegador.  Si su sistema es Windows 9x " +
		"es muy posible que reciba un 'Fallo de Protección General' y tenga que reiniciar el ordenador.\n\n" +
		"Una vez avisado, por favor pulse Aceptar si todavía quiere probar el editor en modo de pantalla completa.",

		"Moz-Clipboard" :
		"Los scripts sin privilegios no pueden acceder a Cortar/Copiar/Pegar " +
		"por razones de seguridad.  Clique Aceptar para ver una nota técnica en mozilla.org " +
		"la cual le enseñará como permitir al script el acceso al portapapeles."
	},

	dialogs: {
		"Cancel"                                            : "Cancelar",
		"Insert/Modify Link"                                : "Insertar/Modifar enlace",
		"New window (_blank)"                               : "Nueva ventana (_vacío)",
		"None (use implicit)"                               : "Ningún (uso implícito)",
		"OK"                                                : "Aceptar",
		"Other"                                             : "Otro",
		"Same frame (_self)"                                : "El mismo marco (_mismo)",
		"Target:"                                           : "Objetivo:",
		"Title (tooltip):"                                  : "Título (indicador de funcion):",
		"Top frame (_top)"                                  : "Marco superior (_superior)",
		"URL:"                                              : "URL:",
		"You must enter the URL where this link points to"  : "Debe introducir la URL adonde apunta este enlace"
	}
};

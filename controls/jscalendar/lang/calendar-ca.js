// ** I18N

// Calendar CA language
// Author: Jordi Sanfeliu, <jordi@fibranet.com>
// Encoding: UTF-8
// Distributed under the same terms as the calendar itself.

// For translators: please use UTF-8 if possible.  We strongly believe that
// Unicode is the answer to a real internationalized world.  Also please
// include your contact information in the header, as can be seen above.

// full day names
Calendar._DN = new Array
("Diumenge",
 "Dilluns",
 "Dimarts",
 "Dimecres",
 "Dijous",
 "Divendres",
 "Dissabte",
 "Diumenge");

// Please note that the following array of short day names (and the same goes
// for short month names, _SMN) isn't absolutely necessary.  We give it here
// for exemplification on how one can customize the short day names, but if
// they are simply the first N letters of the full name you can simply say:
//
//   Calendar._SDN_len = N; // short day name length
//   Calendar._SMN_len = N; // short month name length
//
// If N = 3 then this is not needed either since we assume a value of 3 if not
// present, to be compatible with translation files that were written before
// this feature.

// short day names
Calendar._SDN = new Array
("Diu",
 "Dil",
 "Dmt",
 "Dmc",
 "Dij",
 "Div",
 "Dis",
 "Diu");

// First day of the week. "0" means display Sunday first, "1" means display
// Monday first, etc.
Calendar._FD = 1;

// full month names
Calendar._MN = new Array
("Gener",
 "Febrer",
 "Març",
 "Abril",
 "Maig",
 "Juny",
 "Juliol",
 "Agost",
 "Setembre",
 "Octubre",
 "Novembre",
 "Desembre");

// short month names
Calendar._SMN = new Array
("Gen",
 "Feb",
 "Mar",
 "Abr",
 "Mai",
 "Jun",
 "Jul",
 "Ago",
 "Set",
 "Oct",
 "Nov",
 "Des");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "Quant al calendari";

Calendar._TT["ABOUT"] =
"Selector de data i hora en DHTML\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this this ;-)
"Per la darrera versió visiteu: http://www.dynarch.com/projects/calendar/\n" +
"Distribuït sota GNU LGPL.  Per més detalls vegeu http://gnu.org/licenses/lgpl.html" +
"\n\n" +
"Selecció de data:\n" +
"- Feu servir els botons \xab i \xbb per seleccionar l'any\n" +
"- Feu servir els botons " + String.fromCharCode(0x2039) + " i " + String.fromCharCode(0x203a) + " per seleccionar el mes\n" +
"- Manteniu el botó del ratolí en qualsevol dels anteriors botons per una selecció més ràpida.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Selecció d'hora:\n" +
"- Feu clic sobre qualsevol part de l'hora per augmentar-la\n" +
"- o majúscules-clic reduir-la\n" +
"- o feu clic i arrossegueu per una selecció més ràpida.";

Calendar._TT["PREV_YEAR"] = "Any anterior (manteniu apretat per menú)";
Calendar._TT["PREV_MONTH"] = "Mes anterior (manteniu apretat per menú)";
Calendar._TT["GO_TODAY"] = "Vés a avui";
Calendar._TT["NEXT_MONTH"] = "Mes següent (manteniu apretat per menú)";
Calendar._TT["NEXT_YEAR"] = "Any següent (manteniu apretat per menú)";
Calendar._TT["SEL_DATE"] = "Selecciona data";
Calendar._TT["DRAG_TO_MOVE"] = "Arrossega per moure";
Calendar._TT["PART_TODAY"] = " (avui)";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "Mostra %s primer";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["CLOSE"] = "Tanca";
Calendar._TT["TODAY"] = "Avui";
Calendar._TT["TIME_PART"] = "Feu (Shift-)Clic or arrossegueu per canviar el valor";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
Calendar._TT["TT_DATE_FORMAT"] = "%a, %b %e";

Calendar._TT["WK"] = "set";
Calendar._TT["TIME"] = "Hora:";

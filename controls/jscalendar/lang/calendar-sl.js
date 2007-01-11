// ** I18N

// Calendar EN language
// Author: Mihai Bazon, <mishoo@infoiasi.ro>
// Encoding: any
// Distributed under the same terms as the calendar itself.

// For translators: please use UTF-8 if possible.  We strongly believe that
// Unicode is the answer to a real internationalized world.  Also please
// include your contact information in the header, as can be seen above.

// full day names
Calendar._DN = new Array
("Nedelja",
 "Ponedeljek",
 "Torek",
 "Sreda",
 "Èetrtek",
 "Petek",
 "Sobota",
 "Nedelja");

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
("Ned",
 "Pon",
 "Tor",
 "Sre",
 "Èet",
 "Pet",
 "Sob",
 "Ned");

// full month names
Calendar._MN = new Array
("Januar",
 "Februar",
 "Marec",
 "April",
 "Maj",
 "Junij",
 "Julij",
 "Avgust",
 "September",
 "Oktober",
 "November",
 "December");

// short month names
Calendar._SMN = new Array
("Jan",
 "Feb",
 "Mar",
 "Apr",
 "Maj",
 "Jun",
 "Jul",
 "Aug",
 "Sep",
 "Okt",
 "Nov",
 "Dec");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "O koledarju";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2003\n" + // don't translate this this ;-)
"Za zadnjo razlièico obi¹èite: http://dynarch.com/mishoo/calendar.epl\n" +
"Izdan pod licenco GNU LGPL. Za podrobnosti poglej http://gnu.org/licenses/lgpl.html." +
"\n\n" +
"Izbira datuma:\n" +
"- Uporabi \xab, \xbb gumbe za izbiro leta\n" +
"- Uporabi " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " gumbe za izbiro meseca\n" +
"- Dr¾i mi¹kin gumb ali katerikoli zgornji gumb za hitrej¹e izbiranje.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Izbira èasa:\n" +
"- Klikni na katerikoli del èasa za njegovo poveèavo\n" +
"- ali Shift-klik za zmanj¹anje\n" +
"- ali klikni in potegni za hitrej¹e izbiranje.";

Calendar._TT["PREV_YEAR"] = "Prej. leto (hold for menu)";
Calendar._TT["PREV_MONTH"] = "Prej. mesec (hold for menu)";
Calendar._TT["GO_TODAY"] = "Danes";
Calendar._TT["NEXT_MONTH"] = "Neslednji mesec (obdr¾i za meni)";
Calendar._TT["NEXT_YEAR"] = "Naslednje leto (obdr¾i za meni)";
Calendar._TT["SEL_DATE"] = "Izberi datum";
Calendar._TT["DRAG_TO_MOVE"] = "Poteg za premik";
Calendar._TT["PART_TODAY"] = " (danes)";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "Naprej poka¾i %s";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["CLOSE"] = "Zapri";
Calendar._TT["TODAY"] = "Danes";
Calendar._TT["TIME_PART"] = "(Shift-)Klikni ali potegni za spremembo vrednosti";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%d-%m-%Y";
Calendar._TT["TT_DATE_FORMAT"] = "%a, %b %e";

Calendar._TT["WK"] = "wk";
Calendar._TT["TIME"] = "Èas:";

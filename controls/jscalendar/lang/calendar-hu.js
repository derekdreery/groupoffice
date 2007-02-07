// ** I18N

// Calendar HU language
// Author: ???
// Modifier: KARASZI Istvan, <jscalendar@spam.raszi.hu>
// Encoding: any
// Distributed under the same terms as the calendar itself.

// For translators: please use UTF-8 if possible.  We strongly believe that
// Unicode is the answer to a real internationalized world.  Also please
// include your contact information in the header, as can be seen above.

// full day names
Calendar._DN = new Array
("Vas&#225rnap",
 "H&#233tf&#337",
 "Kedd",
 "Szerda",
 "Cs&#252t&#246rt&#246k",
 "P&#233ntek",
 "Szombat",
 "Vas&#225rnap");

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
("v",
 "h",
 "k",
 "sze",
 "cs",
 "p",
 "szo",
 "v");

// full month names
Calendar._MN = new Array
("janu&#225r",
 "febru&#225r",
 "m&#225rcius",
 "&#225prilis",
 "m&#225jus",
 "j&#250nius",
 "j&#250lius",
 "augusztus",
 "szeptember",
 "okt&#243ber",
 "november",
 "december");

// short month names
Calendar._SMN = new Array
("jan",
 "feb",
 "m&#225r",
 "&#225pr",
 "m&#225j",
 "j&#250n",
 "j&#250l",
 "aug",
 "sze",
 "okt",
 "nov",
 "dec");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "A kalend&#225riumr&#243l";

Calendar._TT["ABOUT"] =
"DHTML d&#225tum/id&#337 kiv&#225laszt&#243\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this this ;-)
"a legfrissebb verzi&#243 megtal&#225lhat&#243: http://www.dynarch.com/projects/calendar/\n" +
"GNU LGPL alatt terjesztve.  L&#225sd a http://gnu.org/licenses/lgpl.html oldalt a r&#233szletekhez." +
"\n\n" +
"D&#225tum v&#225laszt&#225s:\n" +
"- haszn&#225lja a \xab, \xbb gombokat az &#233v kiv&#225laszt&#225s&#225hoz\n" +
"- haszn&#225lja a " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " gombokat a h&#243nap kiv&#225laszt&#225s&#225hoz\n" +
"- tartsa lenyomva az eg&#233rgombot a gyors v&#225laszt&#225shoz.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Id&#337 v&#225laszt&#225s:\n" +
"- kattintva n&#246velheti az id&#337t\n" +
"- shift-tel kattintva cs&#246kkentheti\n" +
"- lenyomva tartva &#233s h&#250zva gyorsabban kiv&#225laszthatja.";

Calendar._TT["PREV_YEAR"] = "El&#337z&#337 &#233v (tartsa nyomva a men&#252h&#246z)";
Calendar._TT["PREV_MONTH"] = "El&#337z&#337 h&#243nap (tartsa nyomva a men&#252h&#246z)";
Calendar._TT["GO_TODAY"] = "Mai napra ugr&#225s";
Calendar._TT["NEXT_MONTH"] = "K&#246v. h&#243nap (tartsa nyomva a men&#252h&#246z)";
Calendar._TT["NEXT_YEAR"] = "K&#246v. &#233v (tartsa nyomva a men&#252h&#246z)";
Calendar._TT["SEL_DATE"] = "V&#225lasszon d&#225tumot";
Calendar._TT["DRAG_TO_MOVE"] = "H&#250zza a mozgat&#225shoz";
Calendar._TT["PART_TODAY"] = " (ma)";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "%s legyen a h&#233t els&#337 napja";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["CLOSE"] = "Bez&#225r";
Calendar._TT["TODAY"] = "Ma";
Calendar._TT["TIME_PART"] = "(Shift-)Klikk vagy h&#250z&#225s az &#233rt&#233k v&#225ltoztat&#225s&#225hoz";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
Calendar._TT["TT_DATE_FORMAT"] = "%b %e, %a";

Calendar._TT["WK"] = "h&#233t";
Calendar._TT["TIME"] = "id&#337:";

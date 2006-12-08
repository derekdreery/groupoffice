<?php
require_once('../../Group-Office.php');
?>
// ** I18N

// Calendar EN language
// Author: Mihai Bazon, <mishoo@infoiasi.ro>
// Encoding: any
// Distributed under the same terms as the calendar itself.

// For translators: please use UTF-8 if possible.  We strongly believe that
// Unicode is the answer to a real internationalized world.  Also please
// include your contact information in the header, as can be seen above.

//Modified for Group-Office by Merijn Schering

// full day names
Calendar._DN = new Array
("<?php echo $full_days[0]; ?>",
 "<?php echo $full_days[1]; ?>",
 "<?php echo $full_days[2]; ?>",
 "<?php echo $full_days[3]; ?>",
 "<?php echo $full_days[4]; ?>",
 "<?php echo $full_days[5]; ?>",
 "<?php echo $full_days[6]; ?>",
 "<?php echo $full_days[0]; ?>");

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
("<?php echo $days[0]; ?>",
 "<?php echo $days[1]; ?>",
 "<?php echo $days[2]; ?>",
 "<?php echo $days[3]; ?>",
 "<?php echo $days[4]; ?>",
 "<?php echo $days[5]; ?>",
 "<?php echo $days[6]; ?>",
 "<?php echo $days[0]; ?>");

// full month names
Calendar._MN = new Array
("<?php echo $months[0]; ?>",
 "<?php echo $months[1]; ?>",
 "<?php echo $months[2]; ?>",
 "<?php echo $months[3]; ?>",
 "<?php echo $months[4]; ?>",
 "<?php echo $months[5]; ?>",
 "<?php echo $months[6]; ?>",
 "<?php echo $months[7]; ?>",
 "<?php echo $months[8]; ?>",
 "<?php echo $months[9]; ?>",
 "<?php echo $months[10]; ?>",
 "<?php echo $months[11]; ?>");

// short month names
Calendar._SMN = new Array
("<?php echo $short_months[0]; ?>",
 "<?php echo $short_months[1]; ?>",
 "<?php echo $short_months[2]; ?>",
 "<?php echo $short_months[3]; ?>",
 "<?php echo $short_months[4]; ?>",
 "<?php echo $short_months[5]; ?>",
 "<?php echo $short_months[6]; ?>",
 "<?php echo $short_months[7]; ?>",
 "<?php echo $short_months[8]; ?>",
 "<?php echo $short_months[9]; ?>",
 "<?php echo $short_months[10]; ?>",
 "<?php echo $short_months[11]; ?>");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "";

Calendar._TT["ABOUT"] = "JSCalendar for Group-Office";
Calendar._TT["ABOUT_TIME"] = "";

Calendar._TT["PREV_YEAR"] = "<?php echo $cal_prev_year; ?>";
Calendar._TT["PREV_MONTH"] = "<?php echo $cal_prev_month; ?>";
Calendar._TT["GO_TODAY"] = "<?php echo $cal_go_today; ?>";
Calendar._TT["NEXT_MONTH"] = "<?php echo $cal_next_month; ?>";
Calendar._TT["NEXT_YEAR"] = "<?php echo $cal_next_year; ?>";
Calendar._TT["SEL_DATE"] = "<?php echo $cal_select_date; ?>";
Calendar._TT["DRAG_TO_MOVE"] = "<?php echo $cal_drag_to_move; ?>";
Calendar._TT["PART_TODAY"] = " <?php echo $cal_part_today; ?>";
Calendar._TT["MON_FIRST"] = "<?php echo $cal_monday_first; ?>";
Calendar._TT["SUN_FIRST"] = "<?php echo $cal_sunday_first; ?>";
Calendar._TT["CLOSE"] = "<?php echo $cmdClose; ?>";
Calendar._TT["TODAY"] = "<?php echo $strToday; ?>";
Calendar._TT["TIME_PART"] = "(Shift-)Click or drag to change value";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
Calendar._TT["TT_DATE_FORMAT"] = "%a, %b %e";

Calendar._TT["WK"] = "<?php echo $strShortWeek; ?>";
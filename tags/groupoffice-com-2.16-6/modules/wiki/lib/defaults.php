<?php
// $Id: defaults.php,v 1.1 2004/01/12 22:14:05 comsubvie Exp $

//**********************************************************************
// DO NOT EDIT THIS FILE.
//
// This file contains configuration defaults for 'Tavi that are over-
// written on installation.  Instead, you should edit 'config.php' to
// re-set these options, or use the 'configure.pl' script to create a
// 'config.php' for yourself.
//
// If you see options in here that are not present in 'config.php',
// you can safely copy them to 'config.php' and set them to a new
// value.  This will override the default set here.
//**********************************************************************

// The following variables establish the format for WikiNames in this wiki.
$UpperPtn = "[A-Z\xc0-\xde]";
$LowerPtn = "[a-z\xdf-\xfe]";
$AlphaPtn = "[A-Za-z\xc0-\xfe]";
$LinkPtn = $UpperPtn . $AlphaPtn . '*' . $LowerPtn . '+' .
           $UpperPtn . $AlphaPtn . '*(?:(?:\\/' . $UpperPtn . $AlphaPtn . '*)+)?';

// $UrlPtn establishes the format for URLs in this wiki.
$UrlPtn  = "(?:https?:|mailto:|ftp:|gopher:|news:|file:)" .
           "(?:[^ |\\/\"\']*\\/)*[^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_]";

// $InterWikiPtn establishes the format for InterWiki links in this wiki.
$InterwikiPtn = "([A-Za-z0-9]+):" .
                "((?:[^ |\\/\"\']*\\/)*[^ |\\t\\n\\/\"\']*[\\/=&~A-Za-z0-9])";
// Note: To avoid side effect of using parentheses in both $LinkPtn, $UrlPtn and
//       $InterwikiPtn the special syntax (?: is used. This hides the parentheses
//       from respectively parse_wikinames, parse_hyperlinkXXX and parse_interwiki
//       in parse/transforms.php

// $ImgPtn is used in html_url() and check_images() to detect
// image-links. Usually tied against the end of the text
$ImgPtn = ".jpe?g|.png|.gif|.bmp";

// $ExtRef contains the strings to be used around external references.
$ExtRef = array ('[', ']');
// $ExtRef = array ('', ''); // Use this if you don't want anything

// !!!WARNING!!!
// If $AdminEnabled is set to 1, the script admin/index.php will be accessible.
//   This allows administrators to lock pages and block IP addresses.  If you
//   want to use this feature, YOU SHOULD FIRST BLOCK ACCESS TO THE admin/
//   DIRECTORY BY OTHER MEANS, such as Apache's authorization directives.
//   If you do not do so, any visitor to your wiki will be able to lock pages
//   and block others from accessing the wiki.
// If $AdminEnabled is set to 0, administrator control will be disallowed.
$AdminEnabled = 0;

// Old versions of pages will be deleted after $ExpireLen days.  If $ExpireLen
//   is set to 0, old versions of pages (and pages set to empty) will never
//   be removed from the database.
$ExpireLen = 14;

// Set $Charset to indicate the character set used for storage, editing,
//   and display in your wiki.  The default is "ISO-8859-1" (Latin-1).
//   "utf-8" is supported, and is recommended for international text;
//   however you should be cautioned that Netscape does not behave correctly
//   when editing utf-8 text.  Hence, "utf-8" is not currently the default.
$Charset = 'ISO-8859-1';

// $SeparateTitleWords determines whether spaces should be inserted in page
//   titles.  If nonzero, the page title (but not header) of WikiName would
//   show 'Wiki Name' instead.  Pages that have free link titles will not
//   be changed.
$SeparateTitleWords = 1;

// $SeparateHeaderWords determines whether spaces should be inserted in page
//   headers.  If nonzero, the page header of WikiName would show 'Wiki Name'
//   instead.  Pages that have free link names would not have changed headers.
$SeparateHeaderWords = 0;

// $SeparateLinkWords determines whether spaces should be inserted in links
//   to pages.  If nonzero, all links to pages such as WikiName would display
//   as 'Wiki Name'.  Pages that have free link names would not have changed
//   links.
$SeparateLinkWords = 0;

// $CookieName determines the name of the cookie that browser preferences
//   (like user name, etc.) are stored in.
$CookieName = 'prefs';

// $EditRows and $EditCols determine the default dimensions of the wiki edit
//   box for users that have not set their preferences.
$EditRows = 20;
$EditCols = 65;

// Initialize the default user name to empty.
$UserName = '';

// Default time zone offset (in minutes) for visitors who haven't yet set their
//   preferences.
$TimeZoneOff = 0;

// $AuthorDiff indicates whether history pages should show a diff for the last
//   edit (zero), or for all edits made by the same author (not zero).  The
//   default here is used if the user has not set their preferences.
$AuthorDiff = 1;

// $DayLimit determines how many days worth of changes show in a category list.
//   This default is used if the user has not set their preferences.
$DayLimit = 14;

// $MinEntries determines the absolute minimum size of a category list (unless
//   there are fewer pages *in* the category).  This default is used if the
//   user has not set their preferences.
$MinEntries = 20;

// $HistMax determines the maximum number of entries on a page's history list.
//   This default is used if the user has not set their preferences.
$HistMax = 8;

// $RatePeriod determines how many seconds of time to record a visitor's access
//   to the site.  If it is set to zero, ALL RATE CHECKING AND IP ADDRESS
//   BLOCKING WILL BE DISABLED.
$RatePeriod = 300;

// $RateView determines how many pages a visitor can view in $RatePeriod
//   amount of time.
$RateView   = 100;

// $RateSearch determines how many processor-intensive operations (search,
//   diff, etc.) a visitor can perform in $RatePeriod amount of time.
$RateSearch = 50;

// $RateEdit determines how many edits a visitor can make in $RatePeriod
//   amount of time.
$RateEdit   = 20;

// $TempDir determines the location of temp files used for computing diffs.
// The default makes a choice based on the environment
$TempDir = isset($_ENV['TMP'])  ? $_ENV['TMP'] :
             isset($_ENV['TEMP']) ? $_ENV['TEMP'] : "/tmp";

// $DiffCmd determines what command to run to compute diffs.
$DiffCmd = '/usr/bin/diff';

// $MaxPostLen determines the size, in bytes, of the largest edit allowed.
$MaxPostLen = 204800;

// $MaxNesting determines the maximum allowed nesting of lists.
$MaxNesting = 20;

// $MaxHeading determines the maximum allowed heading level in headings.
$MaxHeading = 6;

// $EnableWikiLinks determines if WikiLinks are available
$EnableWikiLinks = 1;

// $EnableFreeLinks determines if ((free links)) are available
$EnableFreeLinks = 1;

// $EnableTextEnhance determines if the enhanced text markers for bold, 
// italic, super-/sub-script, ins/del are available
$EnableTextEnhance = 1;

// $ParseEngine indicates what parsing rules will be run when displaying a
//   wiki page.  To disable a particular rule, you can place a comment at the
//   beginning of its line.  The order of this list is important.
// Note that free links and wiki names are disabled above, using config
//   variables.  This is because wiki names are parsed in other places than
//   just the wiki page.
// Raw HTML parsing is turned off by default, since this is a potential
//   security hole.
$ParseEngine = array(
//                 'parse_raw_html',
                 'parse_htmlisms',
                 'parse_nowiki',
                 'parse_hyperlink_ref',
                 'parse_hyperlink_description',
                 'parse_hyperlink',
                 'parse_macros',
                 'parse_transclude',
                 'parse_freelink',
                 'parse_interwiki',
                 'parse_wikiname',
                 'parse_textenhance',
                 'parse_bold',
                 'parse_italic',
                 'parse_teletype',
                 'parse_heading',
                 'parse_table',
                 'parse_horiz',
                 'parse_indents',
                 'parse_newline',
                 'parse_elements'
               );

// $DiffEngine indicates what parsing rules will be run to display differences
//   between versions.  This should be a shorter list than $ParseEngine,
//   since we just want minimal things like bold and italic and wiki links.
$DiffEngine = array(
                'parse_elem_flag',
                'parse_diff_skip',
                'parse_diff_message',
                'parse_diff_color',
                'parse_htmlisms',
                'parse_nowiki',
                'parse_hyperlink_ref',
                'parse_hyperlink_description',
                'parse_hyperlink',
                'parse_freelink',
                'parse_interwiki',
                'parse_wikiname',
                'parse_textenhance',
                'parse_bold',
                'parse_italic',
                'parse_teletype',
                'parse_newline',
                'parse_elements'
              );

// $DisplayEngine indicates what functions will be used to translate wiki
//   markup elements into actual HTML.  See parse/html.php
$DisplayEngine = array(
                   'bold_start'   => 'html_bold_start',
                   'bold_end'     => 'html_bold_end',
                   'italic_start' => 'html_italic_start',
                   'italic_end'   => 'html_italic_end',
                   'superscript_start' => 'html_superscript_start', 
                   'superscript_end' => 'html_superscript_end', 
                   'subscript_start' => 'html_subscript_start', 
                   'subscript_end'   => 'html_subscript_end', 
                   'del_start'       => 'html_del_start', 
                   'del_end'         => 'html_del_end', 
                   'ins_start'       => 'html_ins_start', 
                   'ins_end'         => 'html_ins_end', 
                   'tt_start'     => 'html_tt_start',
                   'tt_end'       => 'html_tt_end',
                   'head_start'   => 'html_head_start',
                   'head_end'     => 'html_head_end',
                   'newline'      => 'html_newline',
                   'paragraph_start'    => 'html_paragraph_start',
                   'paragraph_end'      => 'html_paragraph_end',
                   'ref'          => 'html_ref',
                   'url'          => 'html_url',
                   'interwiki'    => 'html_interwiki',
                   'raw'          => 'html_raw',
                   'code'         => 'html_code',
                   'phpcode'      => 'html_phpcode',
                   'hr'           => 'html_hr',
                   'nowiki'       => 'html_nowiki',
                   'anchor'       => 'html_anchor',
                   'bullet_list_start'   => 'html_ul_start',
                   'bullet_list_end'     => 'html_ul_end',
                   'bullet_item_start'   => 'html_li_start',
                   'bullet_item_end'     => 'html_li_end',
                   'indent_list_start'   => 'html_dl_start',
                   'indent_list_end'     => 'html_dl_end',
                   'indent_item_start'   => 'html_dd_start',
                   'indent_item_end'     => 'html_dd_end',
                   'term_item_start'     => 'html_dt_start',
                   'term_item_end'       => 'html_dt_end',
                   'numbered_list_start' => 'html_ol_start',
                   'numbered_list_end'   => 'html_ol_end',
                   'numbered_item_start' => 'html_li_start',
                   'numbered_item_end'   => 'html_li_end',
                   'diff_old_start'      => 'html_diff_old_start',
                   'diff_old_end'        => 'html_diff_end',
                   'diff_new_start'      => 'html_diff_new_start',
                   'diff_new_end'        => 'html_diff_end',
                   'diff_change'         => 'html_diff_change',
                   'diff_add'            => 'html_diff_add',
                   'diff_delete'         => 'html_diff_delete',
                   'reflist'             => 'html_reflist'
                 );

// $ViewMacroEngine determines what macro names will be processed when
//   displaying a page.  For each name, a function must be provided.
//   See parse/macros.php
$ViewMacroEngine = array(
                     '!'             => 'view_macro_category',
                     'Anchor'        => 'view_macro_anchor',
                     'Transclude'    => 'view_macro_transclude',
                     'PageSize'      => 'view_macro_pagesize',
                     'LinkTable'     => 'view_macro_linktab',
                     'OrphanedPages' => 'view_macro_orphans',
                     'WantedPages'   => 'view_macro_wanted',
                     'TitleSearch'   => 'view_macro_titlesearch',
                     'PageLinks'     => 'view_macro_outlinks',
                     'PageRefs'      => 'view_macro_refs',
                     'RefList'       => 'view_macro_reflist'
                   );

// $SaveMacroEngine determines what save macros will be called after a
//   page is saved.  See parse/save.php
$SaveMacroEngine = array(
                     'parse_define_interwiki',
                     'parse_define_sisterwiki',
                     'parse_define_links'
                   );
?>
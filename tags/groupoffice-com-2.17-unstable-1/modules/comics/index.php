<?php
/*
 * Displays the daily garfield strip from ucomics.com
 *
 * Author: Markus Schabel <markus.schabel@tgm.ac.at>
 *
 * TODO add support for multiple comics
 */

// Require main configuration file
require_once( "../../Group-Office.php" );

// Check if a user is logged in. If not try to login via cookies. If that
// also fails then show the login-screen.
$GO_SECURITY->authenticate();

// Check if the user is allowed to access this module.
$GO_MODULES->authenticate( 'comics' );

// Load the comics module class
require_once($GO_MODULES->class_path.'comics.class.inc');
$comics = new comics();

// Load language data.
require_once( $GO_LANGUAGE->get_language_file( 'comics' ) );

// This is the title of this page. Needed in header.inc for displaying the
// correct title in the titlebar of the browser.
$page_title = "comics - garfield";

// Require theme-header, most times this will be the navigation with some
// design issues.
require_once( $GO_THEME->theme_path."header.inc" );

// Find out if we got a date (a unix timestamp to be exact) as parameter, and
// if not find out the current time.
$date = isset( $_REQUEST['date'] ) ? $_REQUEST['date'] : time();

echo "<table border='0' cellpadding='0' cellspacing='0' width='100%'>";

$url = $comics->get_valid_url( $date, "Garfield" );
if ( $url ) {
  echo "<tr><td align='center'>";
  echo "<img src='$url'>";
  echo "</td></tr>";
}
$url = $comics->get_valid_url( $date, "Calvin & Hobbes" );
if ( $url ) {
  echo "<tr><td align='center'>";
  echo "<img src='$url'>";
  echo "</td></tr>";
}
$url = $comics->get_valid_url( $date, "Adam@Home" );
if ( $url ) {
  echo "<tr><td align='center'>";
  echo "<img src='$url'>";
  echo "</td></tr>";
}
// Display links for last, next and todays strip.
echo "<tr><td align='center' width='150'>";
echo "<a href='".$_SERVER['PHP_SELF']."?date=".($date-60*60*24)."'>".
    $cmdPrevious."</a>&nbsp;";
echo "<a href='".$_SERVER['PHP_SELF']."?date=".time()."'>".
    $comics_today."</a>&nbsp;";
echo "<a href='".$_SERVER['PHP_SELF']."?date=".($date+60*60*24)."'>".
    $cmdNext."</a></td></tr>";

// Since all our output goes into a table we have to close the following tags
echo "</table>";

// Load theme-footer, this is probably some kind of "Group-Office Version..."
require_once( $GO_THEME->theme_path."footer.inc" );

// That's it, we've printed what the user wanted to do and can now exit.
// Maybe that would be the correct place to close database connections...

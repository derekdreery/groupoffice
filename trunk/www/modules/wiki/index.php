<?php
/*
 * index page of a new Group-Office module based on Wikki Tikki Tavi 0.25
 * which is available at http://tavi.sourceforge.net
 *
 * Group-Office Module Author: Markus Schabel <markus.schabel@tgm.ac.at>
 */

// Require main configuration file
require_once( "../../Group-Office.php" );

// Check if a user is logged in. If not try to login via cookies. If that
// also fails then show the login-screen.
$GO_SECURITY->authenticate();

// Check if the user is allowed to access this module.
$GO_MODULES->authenticate( 'wiki' );

// This is the title of this page. Needed in header.inc for displaying the
// correct title in the titlebar of the browser.
$page_title = "wiki";

// Require theme-header, most times this will be the navigation with some
// design issues.
require_once( $GO_THEME->theme_path."header.inc" );

// All output should be aligned in a table to have correct distances
// between the window-borders and our output.
echo "<table border='0' cellpadding='10' width='100%'>";
echo "<tr><td align='center'>";

require_once('lib/main.php');

// Since all our output goes into a table we have to close the following tags
echo "</td></tr></table>";

// Load theme-footer, this is probably some kind of "Group-Office Version..."
require_once( $GO_THEME->theme_path."footer.inc" );

// That's it, we've printed what the user wanted to do and can now exit.
// Maybe that would be the correct place to close database connections...

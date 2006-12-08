<?php
/*
   Copyright Intermesh 2003
   Author: Merijn Schering <mschering@intermesh.nl>
   Version: 1.0 Release date: 08 July 2003
   Version: 2.0 Release date: 31 May 2004

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.


   How to create a typical Group-Office module

   To see this example working open Group-office go to the menu: administrator->modules
   select the module named: 'example' and click at the 'save' button.


//first add some info:

Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003
Version: 2.0 Release date: 31 May 2004

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.

require the Group-Office.php file
This creates:

$GO_CONFIG - configuration settings - see GroupOffice.php
$GO_SECURITY - security class - see classes/base/*.security.class.inc
$GO_USERS - user management class see classes/base/*.users.class.inc
$GO_GROUPS - user group management class see classes/base/*.groups.class.inc
$GO_LANGUAGE - language class - Handles the multi lingual functions
$GO_THEME - theme class - Loads themes

 */
require_once("../../Group-Office.php");

//authenticate the user
//if $GO_SECURITY->authenticate(true); is used the user needs admin permissons

$GO_SECURITY->authenticate();

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('example');

//If you want to know if the user is allowed to manage this module
//check write permissions
if($GO_MODULES->write_permission)
{
  //This user is allowed to manage the example module
}

//require the header file. This should file should always be included before output
//to the client starts.

//some of the controls require some code in the <head></head> part of the HTML page.

//You can use $GO_HEADER['head'] for this.

//The HTMLArea control requires a header:
$htmlarea = new htmlarea();
$GO_HEADER['head'] = $htmlarea->get_header('content', -70, -240, 25);

//The JSCalendar control requires a header too:
$datepicker = new date_picker();
$GO_HEADER['head'] .= $datepicker->get_header();

//several control classes are available from classes/base/controls/

//First we create a tabbed window. (From classes/base/controls/tabtable.class.inc)
//We only create the object here but we actually print it later after the header.inc
//file is included
$tabtable = new tabtable('example_tabtable', 'Example tab window title', '100%', '400');
$tabtable->add_tab('welcome', 'Welcome');
$tabtable->add_tab('controls', 'Some controls');
$tabtable->add_tab('acl_demo', 'ACL');
$tabtable->add_tab('htmlarea', 'HTMLArea');
$tabtable->add_tab('jscalendar', 'JSCalendar');

//You can set <body> arguments too. This is required for HTMLArea:
//but only if the htmlarea is in this page so we have to check if
//we are in the htmlarea tab
if($tabtable->get_active_tab_id() == 'htmlarea')
{
  $GO_HEADER['body_arguments'] = 'onload="initEditor()"';
}
//now that the header is prepared we can actually include the header.inc file.
require_once($GO_THEME->theme_path."header.inc");

//Print out the heading of the tabbed table.
$tabtable->print_head();

//Determine what to display 
switch($tabtable->get_active_tab_id())
{
  case 'welcome':

    echo '<h1>Welcome to the example module for starting Group-Office developers</h1>';


    //how do you get user info

    //the current user is stored in:
    echo '<p>Your user ID is "'.$GO_SECURITY->user_id.'"<br />';
    //Get the user using the user management object
    $user = $GO_USERS->get_user($GO_SECURITY->user_id);

    //If you want info about the current logge din user then you can also use the session.
    echo 	'Your first name is "'.$user['first_name'].'"<br /><br />'.
      'Information about the current logged in user is also stored in $_SESSION[\'GO_SESSION\'].<br />'.
      'See classes/base/base.users.class.inc for the values that are stored in this array.</p>';

    break;

  case 'controls':
    //the $cmdOk var comes from language/common/your_language.inc. Take a look at that file.
    //the strings from the file can be used everywhere inside Group-Office

    //button control
    $button = new button($cmdOk, "javascript:alert('You clicked on a Group-Office button!')");

    echo '<br /><br />';

    //dropbox control
    //always declare vars in Group-Office and don't use registered globals!
    $dropdown = isset($_POST['dropdown']) ? $_POST['dropdown'] : '2';
    $dropbox = new dropbox();
    $dropbox->add_value('1', 'one');
    $dropbox->add_value('2', 'two');
    $dropbox->add_value('3', 'three');
    $dropbox->print_dropbox('dropdown', $dropdown);

    echo '<br /><br />';

    //or direct database link:
    //this is how you should load a class:
    //this function gets all users
    $GO_USERS->get_users();
    //we can now pass the users object to the dropdown box and add all the users to it
    //declare the user var
    $user = isset($_POST['user']) ? $_POST['user'] : '0';
    $dropbox = new dropbox();
    //add the users class, use 'id' for value and 'name' for text
    $dropbox->add_sql_data('GO_USERS', 'id', 'first_name');
    //print the dropbox
    $dropbox->print_dropbox('user',$user);

    echo '<br /><br />';

    //statusbar control
    $statusbar = new statusbar();
    $statusbar->info_text = 'Group-Office usage';
    $statusbar->turn_red_point = 90;
    $statusbar->print_bar(75, 100);

    break;

  case 'acl_demo':

    //You can secure an object by giving it an ACL (Access Control List). When the user
    //you logged in with was created it also got an ACL. This acl is used to protect your personal profile.
    //We already got the user information in the above example so the user acl = stored in $user['acl_id'].
    //So if we want to set the permissions this can be done really easily with the acl control

    echo '<p>You are visible to:</p>';
    $user = $GO_USERS->get_user($GO_SECURITY->user_id);

    print_acl($user['acl_id']);

    //When you are creating your own secured objects you just call th e function: $GO_SECURITY->get_new_acl()
    //to create a new ACL. (See classes/base/*.security.class.inc)
    break;

  case 'htmlarea':
    $content = isset($_POST['content']) ? $_POST['content'] : '<h1>Html editing in Group-Office</h1>';
    $htmlarea->print_htmlarea($content);
    break;

  case 'jscalendar':
    //print date picker using your preference setting date_format.
    //Note that get_time() uses your timezone and Daylight Saving preferences.
    $today = date($_SESSION['GO_SESSION']['date_format'], get_time());
    $datepicker->print_date_picker('date', $_SESSION['GO_SESSION']['date_format'], $today);

    //You can also print a non popup calendar
    ?>
      <div id="date_picker1_container" style="width: 220px;padding-top:20px;"></div>
      <script type="text/javascript">
      function date_picker_callback_function(calendar) {
	// Beware that this function is called even if the end-user only
	// changed the month/year.  In order to determine if a date was
	// clicked you can use the dateClicked property of the calendar:
	if (calendar.dateClicked) {
	  // OK, a date was clicked, redirect to /yyyy/mm/dd/index.php
	  var y = calendar.date.getFullYear();
	  var m = calendar.date.getMonth()+1;     // integer, 0..11
	  var d = calendar.date.getDate();      // integer, 1..31
	  alert('Day: '+d+' Month: '+m+' Year: '+y);
	}
      };
    </script>
      <?php
      $datepicker->print_date_picker('date_picker1', '',$today, 'date_picker1_container', 'date_picker_callback_function');
    break;


}
$tabtable->print_foot();

//Always require the footer file after you're done outputting to the 
//client and you have included the header.inc file.
require_once($GO_THEME->theme_path."footer.inc");
?>

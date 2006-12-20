<?php
/*
 * NNTP-Interface in weblog-style
 *
 * Author: Markus Schabel <markus.schabel@tgm.ac.at>
 *
 * TODO add support for multiple servers
 * TODO remove hard-coded servers
 * TODO add multiple language code
 */

// Require main configuration file
require_once( "../../Group-Office.php" );

// Include NNTP class, all communication to the NNTP server is done over
// this class. It also includes a class for a nntpMessage, so that each
// message has the same format.
require_once( "nntp.class.inc" );

// Get language-file for this module...
// TODO create language-file for this module
// require_once( $GO_LANGUAGE->get_language_file( 'news' ) );

// Check if a user is logged in. If not try to login via cookies. If that
// also fails then show the login-screen.
$GO_SECURITY->authenticate();

// Check if the user is allowed to access this module.
$GO_MODULES->authenticate( 'news' );

// This is the title of this page. Needed in header.inc for displaying the
// correct title in the titlebar of the browser.
$page_title = "news";

// Require theme-header, most times this will be the navigation with some
// design issues.
require_once( $GO_THEME->theme_path."header.inc" );

// If this module is loaded without any parameters we display all groups
// we have access to. So we set the default task to show_groups if it has
// not been set before.
$task = isset( $_REQUEST['task'] ) ? $_REQUEST['task'] : 'show_groups';

// This file implements the navigation of this module. So if you need more
// functions in this module you probably have to add them there.
require_once( "tasks.inc" );

// All output should be aligned in a table to have correct distances
// between the window-borders and our output.
echo "<table border='0' cellpadding='10'><tr><td>";

// Initialize the NNTP class. As parameters give the server we want to connect
// to, the current user and the password he used to login.
// TODO in the future this will be probably be configurable and stored in
// the database... This will probably break authentication on some public
// NNTP servers!
$nntp = new nntp(
                "dot.tgm.ac.at",
                $_SESSION['GO_SESSION']["username"],
                $_SESSION['GO_SESSION']["user_auth_id"]
                );

// Open the connection to the newsserver. We also could open a connection
// each time we query the server for data, but that would be a performance
// impact, so we decided to open the connection once and keep it open.
$nntp->open();

// Decide what output we should generate...
switch( $task ) {
  // Display all groups the user is allowed to see or the specific group
  // the user likes to see if he is allowed to.
  case 'show_groups':
    // If the user requested a newsgroup we will only display this one.
    // Since the implementation in showMessages needs an array as parameter
    // we convert the newsgroup to an array.
    if ( isset( $_REQUEST['newsgroup'] ) ) {
      // Generate a new array.
      $list = array();
      // Put the newsgroup in this array.
      array_push( $list, $_REQUEST['newsgroup'] );
    } else {
      // If the user didn't request a newsgroup we clear the array and
      // display all groups this user is able to see.
      $list = 0;
    }
    // Get all messages of the groups that are listed in $list. If $list
    // is 0 then return all messages.
    $msgs = $nntp->getMessages( $list );
    // If we got any messages we'll sort them and print them in reverse
    // (that means newest first) order. If we got no messages we do not
    // need to do anything.
    if ( count( $msgs ) > 0 ) {
      // Sort and reverse the list of messages (sort by timestamp).
	$omsg = $msgs;
      // Proceed for each message:
      foreach( $omsg as $msg ) {
        // and print it in commenting-enabled view.
	$msg->show( "comment" );
        // Since the messages will be put directly one after another we'll
        // provide an additional space between them.
        echo "<br>";
      }
    }
    break;

  // Post the new generated message to the server. No need to break after
  // this becase we can print the thread after the mail got posted.
  case 'post':
    // To post a new message to a newsgroup we need to know to which
    // group we should post it. So first we check if we know something
    // about the destination newsgroup...
    if ( isset( $_POST['newsgroup'] ) ) {
      // Next thing we check if $message is set, which means that the new
      // message is a answer to an old message.
      if ( isset( $_POST['message'] ) ) {
        // So we are answering a message. To fill the References Headers
        // we need to find out details of the original message...
        $msg = $nntp->getMessage( $_POST['newsgroup'], $_POST['message'] );
	// The field that is interesting is the id of the original message.
        $msgid = $msg->long_id;
      } else {
        // Ok, we are generating a new message, so there is no message id
	// of an old message and we set the id to 0.
        $msgid = 0;
      }
      // Next step is to find out who we are to generate the correct
      // from address...
      $sender = $_SESSION['GO_SESSION']["name"]." <".
                $_SESSION['GO_SESSION']["email"].">";
      // Now we have all information we need to post the new message. So
      // we can do it.
      // TODO add some checks if subject and body is valid.
      // TODO $retval should include the message id of the new message so
      // we can display the correct thread if we just started a new one.
      $retval = $nntp->postMessage(
                                  $_POST['newsgroup'],
                                  $sender,
                                  $msgid,
                                  smart_stripslashes( $_POST['news_subj'] ),
                                  smart_stripslashes( $_POST['news_body'] )
                                  );
      // If the message was posted successfully we can inform the user
      // about this.
      // TODO also inform the user if sending failed
      if ( $retval ) {
        // TODO replace hardcoded message with variable in language-file
        echo "<h1>Nachricht erfolgreich gesendet.</h1>\n";
      }
    }
    // We do not break here because we like to see the thread to which the
    // posted message belongs. For the user this is an additional check if
    // the message was posted correctly...

  // Display only one thread of a specific newsgroup.
  case 'show_thread':
    // The thread can only be identified if we know a message of this
    // thread and to which newsgroup it belongs. So first we check if
    // we know some details...
    if ( isset( $_REQUEST['message'] ) && isset( $_REQUEST['newsgroup'] ) ) {
      // OK, we know which thread we should display. So we query this
      // thread from the server and put it into a variable.
      $thread = $nntp->getThread(
                                $_REQUEST['newsgroup'],
                                $_REQUEST['message']
                                );
      // Thread is an array of messages. We proceed for each message
      // (that means we split this array to seperate messages and print
      // each message for it's own)
      foreach ( $thread as $msg ) {
        // We print this message in a table, this is the easiest way to
	// enable a threaded view, because we can add clean cells left
	// of the message to shift the message a bit to the right.
        echo "<table border='0' width='100%' cellspacing='0' cellpadding='1'";
        echo " bgcolor='#FFFFFF'><tr>\n";
	// The eigth value of the message (which is also represented by
	// an array) is the number of messages it references, that means
	// the value we have to shift the message to the right.
	for ( $i=1; $i<$msg->depth; $i++ ) {
	  echo "  <td width=20>&nbsp;</td>\n";
	}
	// This is the cell where we print the content of the message.
	// Since we are still in a table we have to add the <td> tags.
        echo "  <td>\n";
	// Print the message. Use the function show of the message, so we
        // do not care about the implementation here.
        $msg->show();
	// Close the cell with the message content...
        echo "  </td>\n";
	// Of course we have to close the table ;-)
	echo "</tr></table>\n";
      }
    }
    break;

  // Answer to an existing message or generate a new message. This depends
  // on weather a specific message is known to answer to, and if not we
  // decide the user likes to create a new.
  case 'new_message':
  case 'answer_msgs':
    // Find out if we should generate a new message or answer to an existing
    // and set the status variable $mode depending on this.
    $mode = ( isset( $_REQUEST['newsgroup'] ) &&
              isset( $_REQUEST['message'] ) ) ? "answer" : "new";
    // Since the user should write his message in the webinterface we must
    // provide a form.
    echo "<form name='sendform' method='post' action='";
    echo $_SERVER['PHP_SELF']."'>\n";
    // Depending on the mode we print a drop-box with all accessible groups
    // or the specific group we are answering to.
    if ( $mode == "answer" ) {
      // Find out to which message we are answering. This is needed for
      // correct settings of the References and Follow-Up headers.
      $msg = $nntp->getMessage( $newsgroup, $message );
      // Print the message we are anwering to in a hidden field.
      echo "<input type='hidden' name='message' value='";
      echo $_REQUEST['message']."'/>\n";
      // Print the name of the group we are answering.
      echo "<input type='hidden' name='newsgroup' value='";
      echo $_REQUEST['newsgroup']."'/>\n";
    } else {
      // We generate a new message. So first we find out which groups are
      // present on the server.
      $list = $nntp->getGroups();
      // Since $list is a complex array and we only like to know about the
      // names of the groups we must split them and fill a new array. First
      // create a new array without data in it.
      $ngroups = array();
      // Now we proceed for each group
      foreach ( $list as $ng ) {
        // Get the name of this group and append the name to the array.
        $ngroups[] = substr( $ng->name, strpos( $ng->name, "}")+1 );
      }
      // Sort the array, the user will be happy to see the groups in
      // alphabetical order.
      sort( $ngroups );
      // Now we create our dropbox with all the groups.
      $dropbox = new dropbox();
      // Fill the dropbox with data (add each groupname to it), but prcess
      // the groups to enable a threaded view of them
      foreach ( $ngroups as $ng ) {
        // We say that a group is not a subgroup of another group.
        $indent = "";
        // Since the groups are sorted, we can compare the actual group with
        // the group we processed last, and all fields that are equal in this
        // group and the last group indicate that we can indent this group.
        // We can only do this if we already processed the first group.
        if ( is_array( $last ) ) {
          // Fill the array with the seperate parts of the name of the group.
          $parts = explode( ".", $ng );
          // We process each part of the shorter group.
          for ( $i=1; ($i<count($parts)) && ($i<count($last)); $i++ ) {
            // If the actual processed part is equal in this group and tha
            // last one, we can indent it.
            if ( $parts[$i] == $last[$i] )
              $indent = $indent."&nbsp;&nbsp;&nbsp;";
          }
        }
        // Text and value of dropbox entry should be the same: the name
        // of the group.
        $dropbox->add_value( $ng, $indent.$ng );
        // We finished with processing this group. Now we update this variable
        // To be able to compare the next group with this one.
        $last = explode( ".", $ng );
      }
      // Print the dropbox. It's called "newsgroup" without any parameters.
      $dropbox->print_dropbox('newsgroup','','');
      echo "<br/>";
    }
    // Now we print an input field for the subject of the message.
    echo "<input type='text' class='textbox' name='news_subj' size='110' ";
    echo "value='";
    // If we are answering to an message we print the old subject with an
    // additional "Re: " in front of it:
    if ( $mode == "answer" ) {
      // Print an "Re: " and the encoded subject of the old message.
      echo "Re: ".utf8_decode( imap_utf8( $msg->subject ) );
    }
    // Close the input field. If we're not answering the value of the input
    // field will be clear and the user has to choose a subject.
    echo "'><br/>\n";
    // Print a textbox for the body of the new message
    echo "<textarea class='textbox' name='news_body' cols='110' rows='12'>";
    // If we are answering a message we print the old body quoted in the box.
    if ( $mode == "answer" ) {
      // Print the quoted text.
      echo quote( $msg->body );
    }
    // And close the message-body textbox again.
    echo "</textarea><br/>";
    // Add a hidden field to tell us that the user is posting a new message.
    echo "<input type='hidden' name='task' value='post'/>";
    // Print the "send" button
    $button = new button( "send", "document.sendform.submit();" );
    // Close the form.
    echo "</form>";
    break;

  // This really should not happen since our default-action is show_groups!
  default:
    break;
}

// Since all our output goes into a table we have to close the following tags
echo "</td></tr></table>";

// Load theme-footer, this is probably some kind of "Group-Office Version..."
require_once( $GO_THEME->theme_path."footer.inc" );

// That's it, we've printed what the user wanted to do and can now exit.
// Maybe that would be the correct place to close database connections...

// We're finished and able to close the connection to the newsserver,
$nntp->close();

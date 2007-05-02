<?php
/*
 * NNTP-Abstraction class
 *
 * Author: Markus Schabel <markus.schabel@tgm.ac.at>
 * 
 * TODO check if the calls of imap_*() have finished without problems and
 * do something if an error  occoured.
 * 
 * TODO re-implement the postMessage function to use the imap functions
 * and to return the message-id of the posted message so that we are able
 * to display a thread if we just started one...
 *
 * TODO document nntp->postMessage() and the class nntpMessage
 */

class nntpMessage {
  // This variable is an array which stores the name and the email address
  // of the sender of the message.
  var $sender;
  var $subject;
  var $server;
  var $groups;
  var $id;
  var $long_id;
  var $timestamp;
  var $lines;
  var $references;
  var $in_reply_to;
  var $content;
  var $body;

  var $answers;
  var $depth;

  function nntpMessage( $nntp, $id ) {
    $this->id = $id;
    $head = imap_fetchheader( $nntp, $id, FT_UID|FT_PREFETCHTEXT );
    if ( $head == "" ) {
      // Message does not exist on the server!
      $this = NULL;
      return NULL;
    }
    $this->body = quoted_printable_decode( chop( imap_fetchbody( $nntp, $id, '1', FT_UID ) ) );
    $head = str_replace( "\r\n ", "", $head );
    $headers = explode( "\r\n", $head );
    unset( $head );
    foreach ( $headers as $header ) {
      $headerinfo = explode( ": ", $header, 2 );
      if ( count( $headerinfo ) == 2 ) {
        if ( $headerinfo[0] == "From" ) {
	  $from = preg_split( "/[<@>]/", $headerinfo[1] );
	  if ( count( $from ) < 4 ) {
	    $this->sender["name"] = chop( $from[0] );
	    $this->sender["mail"] = $from[0]."@".$from[1];
	  } else {
	    $this->sender["name"] = chop( $from[0] );
	    $this->sender["mail"] = $from[1]."@".$from[2];
	  }
        } else if ( $headerinfo[0] == "Xref" ) {
	  // This is usually "server group:id"
	  $xref = explode( " ", $headerinfo[1], 2 );
	  $this->server = $xref[0];
	  $xref = explode( " ", $xref[1] );
	  foreach ( $xref as $ref ) {
	    $group = explode( ":", $ref );
	    $this->groups[] = $group;
	  }
        } else if ( $headerinfo[0] == "Subject" ) {
          $this->subject = $headerinfo[1] ;
        } else if ( $headerinfo[0] == "Date" ) {
	  $this->timestamp  = strtotime( $headerinfo[1] );
        } else if ( $headerinfo[0] == "Lines" ) {
	  $this->lines = $headerinfo[1];
	} else if ( $headerinfo[0] == "Message-ID" ) {
	  $this->long_id = $headerinfo[1];
	} else if ( $headerinfo[0] == "References" ) {
	  $this->references = explode( " ", $headerinfo[1] );
	} else if ( $headerinfo[0] == "In-Reply-To" ) {
	  $this->in_reply_to = split( " ", $headerinfo[1] );
	} else if ( $headerinfo[0] == "Content-Type" ) {
	  $ctype = explode( "; ", $headerinfo[1] );
	  foreach ( $ctype as $detail ) {
	    if ( ( strpos( $detail, "charset=" ) === 0 ) ) {
	      $this->content["charset"] = substr( $detail, 8 );
	    } else if ( ( strpos( $detail, "format=" ) === 0 ) ) {
	      $this->content["format"] = substr( $detail, 7 );
	    } else {
	      $this->content["type"] = $detail;
	    }
	  }
	} else if ( $headerinfo[0] == "Content-Transfer-Encoding" ) {
	  $this->content["transfer-encoding"] = $headerinfo[1];
        } else if ( in_array( $headerinfo[0], array( "Path", "Newsgroups", "Organization", "NNTP-Posting-Host", "X-Trace", "X-Complaints-To", "NNTP-Posting-Date", "Mime-Version", "User-Agent", "X-Accept-Language" ) ) ) {
	  // IGNORE THESE
	} else {
          $head[$headerinfo[0]] = $headerinfo[1];
	}
      }
    }
    if ( isset( $this->content ) ) {
      if ( isset( $this->content["charset"] ) ) {
        $this->subject = utf8_decode( imap_utf8( $this->subject ) );
      }
    }
  }

  function show( $mode='' ) {
    echo "<table border='0' width='100%' cellspacing='0' cellpadding='1'";
    echo " bgcolor='#000000'><tr><td>\n";
    echo "<table border='0' width='100%' cellspacing='5' bgcolor='#FFFFFF'>";
    echo "<tr><td>\n";
    echo "<b><a href='".$_SERVER['PHP_SELF']."?task=show_groups&newsgroup=";
    echo $this->groups[0][0]."'>".$this->groups[0][0]."</a>: &nbsp;";
    echo $this->subject;
    echo "</b>";
    echo "<pre>";
    echo htmlspecialchars( $this->body );
    echo "</pre>";
    echo "<hr noshade size='1'>";
    echo "<p align='center'>";
    echo "Von <a href='mailto:".$this->sender["mail"]."'>";
    echo $this->sender["name"]."</a>, ";
    echo date("l dS of F Y H:i:s", $this->timestamp )."<br>";
    echo " [ ";
    if ( $mode == "comment" ) {
      if ( $this->answers == 0 ) {
        echo "0 Kommentare";
      } else {
        echo "<a href='".$_SERVER['PHP_SELF'];
	echo "?task=show_thread&newsgroup=".$this->groups[0][0];
	echo "&message=".$this->groups[0][1]."'>";
        echo $this->answers." Kommentar";
        if ( $this->answers != 1 ) {
          echo "e";
        }
        echo "</a>";
      }
      echo " | ";
    }
    echo "<a href='".$_SERVER['PHP_SELF'];
    echo "?task=answer_msgs&newsgroup=".$this->groups[0][0];
    echo "&message=".$this->groups[0][1]."'>";
    echo "Antworten";
    echo "</a>";
    echo " ] ";
    echo "</p>";
    echo "</td></tr></table>\n";
    echo "</td></tr></table>\n";
  }

  function cmp_timestamp( $a, $b ) {
    if ( $a->timestamp == $b->timestamp ) return 0;
    return ( $a->timestamp < $b->timestamp ) ? +1 : -1;
  }
}

class nntp {
  // This variable stores the hostname of the server we are connecting
  // to. It is set in the constructor.
  var $host = "";
  // This two variables store the account information we use to connect
  // to the nntp server. They are set in the constructor.
  var $user = "";
  var $pass = "";
  // Here we store our connection to the server. Since the actual version
  // of PHP has no destructors we need to take care that our code closes
  // the connection before exiting.
  var $nntp = NULL;
  // Here we store a list of all available groups. It is initialized when
  // we open the connection to the server. It is an array of objects with
  // the following attributes:
  //   name       => contains the name of the group. This is an extended
  //                 string, that means a lot of information we usually
  //                 do not need: {hostname:port/nntp}group
  //             eg. "{dot.tgm.ac.at:119/nntp}tgm.dot.bugs"
  //   attributes =>
  //             eg. int(0)
  //   delimiter  =>
  //             eg. "."
  var $list = NULL;
  // This variable contains the name of the group we're connected at the
  // moment. So we do not need to rebind each time we fetch a message from
  // the same group if we definitely know that we are already connected to
  // this group.
  var $group = NULL;

  // This is the constructor of this class. It initializes the variables
  // that are used to connect to the server. The parameters user and pass
  // are optional, since we are able to bind anonymously.
  function nntp( $host, $user="", $pass="" ) {
    // We just initialize the following variables and that's it.
    $this->host = $host;
    $this->user = $user;
    $this->pass = $pass;
  }

  // Open a connection to the server and get a list of available groups.
  function open() {
    // First we check if we already have an open connection. If we do not
    // have we open a new one. With the current implementation it is not
    // possible to access multiple servers at the same time with one object
    // of this class, so we do not open a new connection if there still
    // exists an open one.
    if ( !$this->nntp ) {
      // Ok, we have no open connections and are able to open a new one.
      // As parameters we support the "imap"-string which represents our
      // server. We also provide user and passwort. OP_HALFOPEN means that
      // we open a connection but do not specify a group.
      $this->nntp = @imap_open(
                              "{".$this->host.":119/nntp}",
                              $this->user, $this->pass,
                              OP_HALFOPEN
                              );
      // Get a list of newsgroups from the server. As server we support
      // the connection id we just got from imap_open. As reference we
      // support the "root" of the server and as filter an asterix to get
      // all available groups.
      $this->list = @imap_getmailboxes(
                                      $this->nntp,
                                      "{".$this->host.":119/nntp}",
                                      "*"
                                      );
      // Set the group we're connected to to NULL because we have no group
      // selected at the moment.
      $this->group = NULL;
    }
  }

  // Close the connection to the server.
  function close() {
    // First we check if there is an open connection. If we have one then
    // we can close it. If we are not connected to a server we cannot
    // close it ;)
    if ( $this->nntp ) {
      // We found a connection so we can close it.
      @imap_close( $this->nntp );
    }
    // Reset the connection variable.
    $this->nntp = NULL;
  }

  // Return a list of all avaliable groups.
  function getGroups() {
    // We only can return a list of groups if we are connected to the
    // server. So check if there is an open connection...
    if ( $this->nntp ) {
      // There's an open connection so we can return the list of groups
      // the server has. This list is queried when we open the connection
      // to the server.
      return $this->list;
    } else {
      // If there is no connection we have no groups and return false.
      return false;
    }
  }

  // Try to find out the full name of the group (that means with the
  // server the port and so on, this is needed each time we reopen the
  // connection to a specific group.
  function getFullNameOfGroup( $fromgroup ) {
    // First we check if we have an open connection. No need to do
    // something if we have no connections opened.
    if ( $this->nntp ) {
      // Ok, there is an open connection.
      // We need to find out the complete name of the group we got as
      // parameter. So we proceed for each entry in the list of all groups
      // and compare each one with the name we got as parameter.
      foreach ( $this->list as $ng ) {
        // We split the complex name ({dot.tgm.ac.at:119/nntp}tgm.dot.bugs)
	// into a simpler array with the following regular expression: {}:/
	// so we get the following seperate values in our array:
	//   [1] => dot.tgm.ac.at
	//   [2] => 119
	//   [3] => nntp
	//   [4] => tgm.dot.bugs
        $group = preg_split ("/[{}:\/]+/", $ng->name );
	// Now we compare if the group we got as parameter is the same as
	// the last part of the complex name in our list.
        if ( $fromgroup == $group[4] ) {
	  // It is the same, so we initialize the variable ngroup with the
	  // full name. It is needed again when we re-open our connection.
          return $ng;
        }
      }
    }
    // If we reach this point, we have not found the group we were searching
    // for and can indicate that the search failed.
    return false;
  }

  // Get a message from a group. As parameters the group and the message
  // id must be supported.
  function getMessage( $fromgroup, $num ) {
    if ( $this->nntp ) {
      // Find out what the long identifier of this group is, that means the
      // long string that is needed to open/reopen a connection to the server.
      $ngroup = $this->getFullNameOfGroup( $fromgroup );
      // If we were able to find out to which group the message belongs, then
      // we can fetch all interesting data of this msg.
      if ( $ngroup ) {
        // We check if we are connected to the correct group, and if not we
        // reconnect to the right one.
        if ( $this->group != $ngroup->name ) {
          // We reopen the connection, and connect to the correct group.
          @imap_reopen( $this->nntp, $ngroup->name );
          // Now we set the group we are connected to to this one.
          $this->group = $ngroup->name;
        }
        // Query the server for this message and return it to the calling
        // function. This will generate a new object with all data of the
        // message in it, and is therefore a standardized way to represent
        // our messages.
        return new nntpMessage( $this->nntp, $num );
      }
    }
    // We only reach this point if we have not found message before, that
    // means we have not found the corresponding group or have no open
    // connection to the server. So we inform the caller that we failed.
    return false;
  }

  // This is a slightly more complex function which queries all messages
  // from the supplied groups. If we do not supply a list of groups this
  // function will return all messages of all available groups.
  function getMessages( $fromgroups='', $sort=1 ) {
    if ( $this->nntp ) {
      $thread_arr = array();
      $thread_dep = 0;

      // For all groups we try to find out if we should query the group.
      foreach ( $this->list as $ng ) {
        // The same as in the function getMessage, we split the name of
        // the group into something we can easily read.
        $group = preg_split ("/[{}:\/]+/", $ng->name );
        // First we think that the current processed will not be processed
        // and then we check if we have to.
        $process = false;
        // If we have no list of groups as parameter we have to process all
        // groups.
        if ( $fromgroups == '' ) {
          // Ok, we process all groups, so we set the variable to true.
          $process = true;
        } else {
          // We got something as parameter and we need to check if it is an
          // array (a list of groups) or a string (a single group).
          if ( is_array( $fromgroups ) ) {
            // We have an array as parameter. So we have to check if the
            // current processed newsgroup is an element of the array. If it
            // is we should process this group.
            if ( in_array( $group[4], $fromgroups ) ) {
              // It is element of the array, so we process this group.
              $process = true;
            }
          } else {
            // The parameter is no array. So it must be a string and we
            // compare the parameter and the actual group.
            if ( $fromgroups == $group[4] ) {
              // They are the same, so we process this group.
              $process = true;
            }
          }
        }
        // We checked all possibilities if we have to process the group
        // or not. If we need to process it, then we do it now.
        if ( $process ) {
          // First we reopen the connection to bind to the specific group
          // we are processing at the moment. We don't check if we are
          // already connected to this group, because most times this code
          // runs it will have to rebind since this function usually
          // processes more than one group.
          imap_reopen( $this->nntp, $ng->name );
          // We connected to a specific group, so we can set the status
          // variable to this group. We could also do this at the end of this
          // function, but it doesn't need much processing time, and setting
          // the variable each time we change our selected group is the clean
          // way.
          $this->group = $ng->name;
          // We check if there are postings in this group available. If there
          // are no postings, it is not needed to process this group.
          // Check if there is at least one message available.
          if ( imap_num_msg( $this->nntp ) > 0 ) {
            // There are messages on the server in this group available.
            // Get all headers of this group. With the current implementation
            // of the imap functions in PHP it is not possible to get only
            // some messages, it will always return _all_ messages!
            $headers = @imap_headers( $this->nntp );
            // Get the threads of the current group. This returns a somewhat
            // strange array. Since this function is not documented at the
            // moment it was complicated to find out what it really does...
            // The index of the array is always a string that consists of two
            // parts seperated by a dot: the fist part is an integer that is
            // only used to group the second part to messages, we can ignore
            // it at the moment (and probably at any time). The second part
            // is one of the following strings: num, next and branch.
            // We can ignore "next", this should be the next message in the
            // thread, but I'm not sure of that. If it is "num" it indicates
            // that have to move a level deeper in the thread, and if it is
            // "branch" we go up one level again. Each time "branch" is set,
            // "next" should be 0, but I'm really not sure of thet.
            // The second part of the array (the value) contains the mesg-id
            // of that message. In fact this is the number we're using to
            // fetch the message from the server.
            // The following is a short example of the return value of this
            // function:
            // array(18) {
            //  ["0.num"]    => int(1)
            //  ["0.next"]   => int(1)
            //  ["1.num"]    => int(2)    o 1
            //  ["1.next"]   => int(0)    |--o 2 
            //  ["1.branch"] => int(2)    |--o 3
            //  ["2.num"]    => int(3)       |--o 4
            //  ["2.next"]   => int(3)          |--o 5
            //  ["3.num"]    => int(4)          |--o 6
            //  ["3.next"]   => int(4)
            //  ["4.num"]    => int(5)
            //  ["4.next"]   => int(0)
            //  ["4.branch"] => int(5)
            //  ["5.num"]    => int(6)
            //  ["5.next"]   => int(0)
            //  ["5.branch"] => int(0)
            //  ["3.branch"] => int(0)
            //  ["2.branch"] => int(0)
            //  ["0.branch"] => int(0)
            // }
            $threads = @imap_thread( $this->nntp, SE_UID );
            // For each entry of the array we got from imap_threads we split
            // it into a key and a value pair (the key is the index in the
            // array and the value is the value of that element in the array).
            // The key is the identifier of the depth in the thread and the
            // value is the message id.
            while ( list( $key, $val ) = each( $threads ) ) {
              // We split the current index into a new array. The first value
              // contains an id and the second tells us what we need to do: If
              // it is "num" then we increment the thread_depth (which means
              // we leave the root of the thread and process to answers in the
              // thread. And if it is "branch" we decrement thread_depth which
              // means we tend to the root of the thread.
              $tree = explode( ".", $key );
              // Now we do this.
              if ( $tree[1] == "num" ) {
                // If the depth in the thread is zero we have a new thread.
                if ( $thread_dep == 0 ) {
                  // Since this is a new thread there are no answers now. This
                  // variable is used to store the number of answers in this
                  // thread.
                  $thread_num = 0;
                  // Fetch the current message from the server.
                  $message = new nntpMessage( $this->nntp, $val );
                } else {
                  // We already are in a thread, so we increment the number of
                  // messages that are in this thread.
                  $thread_num++;
                }
//              if ( $message )
                  $thread_dep++;
              // Now we have a branch, that means the current thread (to be
              // exact the actual subtree of this thread) ends here. So we
              // decrement the depth in this thread and if it reaches 0 we 
              // can say that this thread is finished.
              } else if ($tree[1] == "branch") {
                // As we said before, we decrement the depth in this thread.
                $thread_dep--;
                // Now we check if we already reached the top-level (that
                // means this thread ends here).
                if ( $thread_dep == 0 ) {
                  // Check if the message exists. If the message is deleted
                  // from the server, this variable will be NULL.
                  if ( $message ) {
                    // $message is the first message of this thread. We now
                    // know the number of answers to this message and set the
                    // corresponding attribute.
                    $message->answers = $thread_num;
                    // Append the message to the array of all messages.
                    $thread_arr[] = $message;
                    // unset message, so it really will be NULL if the next
                    // message we fetch from the server is deleted.
                    unset( $message );
                  }
                }
              // We can ignore all other cases (e.g. "next").
              }
            }
          }
        }
      }
      // If we should sort the messages, then do it.
      if ( $sort == 1 ) {
        // The class nntpMessage has a function (cmp_timestamp()) that
        // compares the timestamps of two messages.
        usort( $thread_arr, array( "nntpMessage", "cmp_timestamp" ) );
      }
      // Return the list of messages we fetched.
      return $thread_arr;
    }
    // We only reach this point if there is no connection to the server
    // available. So we indicate that we weren't able to process the
    // request.
    return false;
  }

  function getThread( $fromgroup, $message ) {
    // First we have to check if we are connected to the server. We can just
    // fail if we are not connected.
    if ( $this->nntp ) {
      // Find out what the long identifier of this group is, that means the
      // long string that is needed to open/reopen a connection to the server.
      $ngroup = $this->getFullNameOfGroup( $fromgroup );
      // If we were able to find out the exact name of the group we are
      // searching for, we can proceed.
      if ( $ngroup ) {
        // Now we find out if we are already connected to this group, and if
        // not we connect to it.
        if ( $this->group != $ngroup->name ) {
          // We reconnect to this specific group.
          imap_reopen( $this->nntp, $ngroup->name );
          // And set our status variable to this group.
          $this->group = $ngroup->name;
        }
        // We are now definitly connected to the correct group.
        // Now we find out how many messages are in this group. If there are
        // no messages posted, we do not need to get the thread ;-)
        if ( imap_num_msg( $this->nntp ) > 0 ) {
          // Since there are messages in this group, we get the thread
          // information from this server. The syntax of the array this
          // function returns is explained in the corresponding code fragment
          // of the function getMessages().
          $threads = @imap_thread($this->nntp, SE_UID);
          // We do not know in which thread our message id is, so we have to
          // process all available threads till we find our message. At the
          // beginning we can say that we haven't found it.
          $found = false;
          // We are at the top-level of the messages in the group, that means
          // we are flat and not in any thrad.
          $thread_dep = 0;
          // A thread is an array of messages. To use the function [] of this
          // array we have to say PHP that it is an array.
          $thread = array();
          // Process each entry we got from imap_threads(). What we are doing
          // here is also explaind in the code of getMessages().
          while ( list( $key, $val ) = each( $threads ) ) {
            // We split the current index in the array into two parts, the
            // first part is the id (for internal use only, we can ignore it)
            // and the second part tells us what we have to do. "num" is a
            // message in a thread, so we have to increase the depth, and
            // branch is the end of a thread so we have to decrease depth.
            $tree = explode(".", $key);
            // Do it. If it is num we have to increase the depth.
            if ($tree[1] == "num") {
              // First we check if we found our message and set the variable
              // to exit after when we processed the complete thread.
              if ( $val == $message )
	        $found = true;
              // We've said we have to increase the depth in the thread.
	      $thread_dep++;
              // Now get the message from the server. This is standardized to
              // simplify adding new features.
              $message = new nntpMessage( $this->nntp, $val );
              // Tell the message in which depth in the thread we found it.
              // This is needed for a correct threaded display.
	      $message->depth = $thread_dep;
              // Add this message to the thread.
	      $thread[] = $message;
            // If the second part of the result from imap_thread() is "branch"
            // then we go a level lower, that means decrease depth.
            } else if ($tree[1] == "branch") {
              // Decrease depth.
              $thread_dep--;
              // If we are at top-level again, we have finished this thread.
              if ( $thread_dep == 0 ) {
                // Now we have two possibilities: if we found the message,
                // then we can return the thread.
	        if ( $found ) {
                  return $thread;
                // and if we haven't found the message yet, we can reset
                // the thread and process with the next.
                } else {
                  $thread = array();
                }
              }
	    }
	  }
	}
      }
    }
    // If we haven't returned till now, we haven't found the thread and can
    // exit with an error.
    return false;
  }

  function postMessage( $togroup, $sender, $references, $subject, $message ) {
    if ( !$stream = fsockopen("dot.tgm.ac.at",119,$errno,$errstr) ) {
      echo "server:".$errstr."($errno)<br>\n";
    }
    // This should be "200 dot.tgm.ac.at InterNetNews NNRP server INN 2.4.0 ready (posting ok)."
    $answer = fgets($stream);
    fwrite($stream, "post\r\n");
    // This should be "340 Ok, recommended ID"
    $answer = fgets($stream);
    fwrite($stream, "From: ".$sender."\r\n");
    fwrite($stream, "Subject: ".$subject."\r\n");
    fwrite($stream, "Newsgroups: ".$togroup."\r\n");
    fwrite($stream, "References: ".$references."\r\n");
    fwrite($stream, "In-Reply-To: ".$references."\r\n");
    fwrite($stream, "\r\n");

    if (!strstr("\n", "\r"))
      $text = str_replace("\r", '', $message);
    $lines = explode( "\n", $text );
    echo "<pre>";
    foreach ( $lines as $key => $line ) {
      $line = wordwrap( $line );
      fwrite($stream, $line."\r\n");
    }
    echo "</pre>";
    fwrite($stream, "\n.\n");
    // This should be "240 Article posted <ID>"
    $answer = fgets($stream)."<br>";
    fclose($stream);

    if ( substr( $answer, 0, 3 ) == "240" ) {
      // We need to get the message id out of this string.
      return true;
    } else {
      return false;
    }
  }
}

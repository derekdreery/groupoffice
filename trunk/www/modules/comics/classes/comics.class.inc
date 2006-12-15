<?php

class comics {

  function create_url( $date, $what='Garfield' ) {
    switch( $what ) {
    case 'Garfield':
	// The URL will be something like this:
        // http://images.ucomics.com/comics/ga/2003/ga031203.gif
        $url = "http://images.ucomics.com/comics/ga/".date('Y',$date).
	       "/ga".date('ymd',$date).".gif";
	break;
    case 'Calvin & Hobbes':
	// The URL will be something like this:
	// http://images.ucomics.com/comics/ch/1993/ch931203.gif
	// change the year because Calvin & Hobbes is from the past
	$chyear = date( 'Y', $date )-11;
	$shortchyear = $chyear-1900;
	$url = "http://images.ucomics.com/comics/ch/".$chyear.
	       "/ch".$shortchyear.date( 'md', $date ).".gif";
	break;
    case 'Adam@Home':
	// The URL will be somethin like this:
	// http://images.ucomics.com/comics/ad/2005/ad050307.gif
	$url = "http://images.ucomics.com/comics/ad/".date('Y',$date).
	       "/ad".date('ymd',$date).".gif";
	break;
    }
    return $url;
  }

  function get_valid_url( $date, $what='Garfield' ) {
    $url = $this->create_url( $date, $what );
    $file = @fopen( $url, "r" );

    // Test if we were able to open this file. If not decrement date until we can
    // fetch a file or tested 30 files.
    if ( !$file ) {
      // We were not able to fetch the file. So we initialies our test-counter.
      $tries = 0;
      // Try to read some url's.
      do {
	// Decrement date, so that we fetch the comic of $date-one day.
	$date -= 60*60*24;
	// Increment tries, so that we can give an error if we weren't able to
	// fetch 30 days in the past, this prevents an infinite loop if e.g. the
	// ucomics server is down.
	$tries++;
	// Fetch the comic that was released at $date.
	$url = $this->create_url( $date, $what );
	$file = @fopen( $url, "r" );
	// Do this till you find a file or tried 30 times.
      } while ( !$file && $tries < 30 );
    }

    // If we found a file (either in the beginning or in the loop) then we should
    // close this again, and return the URL.
    if ( $file ) {
      // Close the file.
      fclose( $file );
      // We found a valid url
      return $url;
    }
    // We found no valid url.
    return false;
  }
  
	function delete_user($user_id)
  {
  	return true;
  }

}
?>

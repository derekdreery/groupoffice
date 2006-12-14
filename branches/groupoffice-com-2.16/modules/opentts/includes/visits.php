<?php
$expiredate1 = time() + 3600 * 24 * 365;
$expiredate2 = time() + 600;

// update LastVisit cookie. This cookie is updated each time auth.php runs
$cookiedomain=$cookiepath=$cookiesecure='';
setcookie("tts_LastVisit", time(), $expiredate1,  $cookiepath, $cookiedomain, $cookiesecure);

// set LastVisitTemp cookie, which only gets the time from the LastVisit
// cookie if it does not exist yet
// otherwise, it gets the time from the LastVisitTemp cookie

if (!isset($HTTP_COOKIE_VARS["tts_LastVisitTemp"])) {
	if (isset($HTTP_COOKIE_VARS["tts_LastVisit"])){
	    $temptime = $HTTP_COOKIE_VARS["tts_LastVisit"];
	}else{
		$temptime = time();
	}
} else {
    $temptime = $HTTP_COOKIE_VARS["tts_LastVisitTemp"];
}

// set cookie.

setcookie("tts_LastVisitTemp", $temptime ,$expiredate2, $cookiepath, $cookiedomain, $cookiesecure);
?>

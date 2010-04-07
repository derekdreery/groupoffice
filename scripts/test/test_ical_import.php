<?php
require('../../www/Group-Office.php');

require_once ($GO_MODULES->modules['calendar']['class_path']."calendar.class.inc.php");
require_once ($GO_CONFIG->class_path."ical2array.class.inc");
$ical = new ical2array();
$cal = new calendar();


$ical_str='BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Nexthaus Corporation//SyncJe for Outlook(11.0)//EN
BEGIN:VTIMEZONE
TZID:West-Europa (standaardtijd)
BEGIN:DAYLIGHT
DTSTART:19700329T020000
RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=3
TZOFFSETFROM:+0000
TZOFFSETTO:+0100
TZNAME:West-Europa (standaardtijd)
END:DAYLIGHT
BEGIN:STANDARD
DTSTART:19701025T030000
RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=10
TZOFFSETFROM:+0100
TZOFFSETTO:+0000
TZNAME:West-Europa (standaardtijd)
END:STANDARD
END:VTIMEZONE
BEGIN:VEVENT
DESCRIPTION:A50 richting Nijmegen\, op knooppunt Bankhoef de A326 richting
Nijmegen\, neem na 6\,1 km de tweede afslag op de rotonde richting "Nijmege
n"\, neem na 2\,4 km de tweede afslag richting Nijmegen. Dan na 400 meter d
e eerste afslag op de rotonde\, en ga na 1\,7 km rechtsaf de Neerbosscheweg
op en na 500 meter zie je een  hoog  gebouw met spiegelramen. Daar is het.
Je kunt voor het gebouw parkeren (gratis) en in de parkeergarage (2 euro pe
r uur).\n\nZodra je in het gebouw bent ga je met de lift of trap naar de be
gane grond. Daar meld je je bij de balie van de receptie en krijg je een ka
artje voor de lift. Neem de lift naar de 17e verdieping en hou je kaartje w
eer bij de deur om bij onze receptie te komen. Als je je daar meldt\, kom i
k je ophalen. Succes en tot dinsdag!\n\nTenders Media Holding BV\n52 Degree
s Building\nJonkerbosplein 52  - Floor 17 Room 14
DTSTART;TZID="West-Europa (standaardtijd)":20090304T080000
DTEND;TZID="West-Europa (standaardtijd)":20090304T090000
CLASS:PUBLIC
STATUS:TENTATIVE
SUMMARY:test
TRANSP:TRANSPARENT
SEQUENCE:0
PRIORITY:2
DTSTAMP:20090304T075743Z
LAST-MODIFIED:20090304T075743Z
UID:0000GK
END:VEVENT
END:VCALENDAR';

//$vcalendar = $ical->parse_file('/home/mschering/jos.ics');
//
$vcalendar = $ical->parse_icalendar_string($ical_str);
var_dump($vcalendar);
exit();

while($object = array_shift($objects))
{
		var_dump($object);
		$event = $cal->get_event_from_ical_object($object);
		
		echo 'Name: '.$event['name']."\n";
		echo 'Start time: '.date('Ymd G:i', $event['start_time'])."\n";
		echo 'End time: '.date('Ymd G:i', $event['end_time'])."\n";
		echo "\n------------\n\n";
		
		//var_dump($event);
}
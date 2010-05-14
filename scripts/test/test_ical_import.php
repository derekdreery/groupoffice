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


$ical_str='BEGIN:VCALENDAR
PRODID:-//Microsoft Corporation//Outlook 11.0 MIMEDIR//EN
VERSION:2.0
METHOD:REQUEST
BEGIN:VEVENT
ATTENDEE;ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:egil@digibrev.net
ORGANIZER:MAILTO:test@test.nl
DTSTART:20100513T060000Z
DTEND:20100513T063000Z
TRANSP:OPAQUE
SEQUENCE:0
UID:040000008200E00074C5B7101A82E00800000000E0B6AC5FD9F2CA010000000000000000100
 00000D80A7AFDF6D84A44BCA09A118B3CB142
DTSTAMP:20100513T181756Z
DESCRIPTION:Tidspunkt: 13. mai 2010 08:00-08:30 (GMT+01:00) Amsterdam\,
  Berlin\, Bern\, Oslo\, Roma\, Stockholm\,
  Wien.\n\n*~*~*~*~*~*~*~*~*~*\n\nHello!\n\nNice to ……..\n\n\nTommy
  Christiansen\n\n
SUMMARY:Budget meeting
PRIORITY:5
X-MICROSOFT-CDO-IMPORTANCE:1
CLASS:PUBLIC
BEGIN:VALARM
TRIGGER:-PT15M
ACTION:DISPLAY
DESCRIPTION:Reminder
END:VALARM
END:VEVENT
END:VCALENDAR';

$ical_str='BEGIN:VCALENDAR
PRODID:-//Ximian//NONSGML Evolution Calendar//EN
VERSION:2.0
METHOD:PUBLISH
BEGIN:VTIMEZONE
TZID:/freeassociation.sourceforge.net/Tzfile/Europe/Amsterdam
X-LIC-LOCATION:Europe/Amsterdam
BEGIN:STANDARD
TZNAME:CET
DTSTART:19701031T030000
RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
END:STANDARD
BEGIN:DAYLIGHT
TZNAME:CEST
DTSTART:19700328T020000
RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=3
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
END:DAYLIGHT
END:VTIMEZONE
BEGIN:VEVENT
UID:20100514T080516Z-4354-1000-1-0@Intermesh-1
DTSTAMP:20100514T080516Z
DTSTART;TZID=/freeassociation.sourceforge.net/Tzfile/Europe/Amsterdam:
 20100514T113000
DTEND;TZID=/freeassociation.sourceforge.net/Tzfile/Europe/Amsterdam:
 20100514T140000
TRANSP:OPAQUE
SEQUENCE:2
SUMMARY:Bla die bla
DESCRIPTION:\nOver Intermesh\nWelkom bij Intermesh software
 ontwikkeling\n\nIntermesh is een in 2003 opgericht IT bedrijf dat zich
 heeft gespecialiseerd in het maken van Internet applicaties en websites.
 Op basis van uw eisen realiseren we in korte tijd een betrouwbaar product
 dat direct inzetbaar is. Hierbij wordt gebruik gemaakt van de nieuwste
 technologieën en innovatieve software. Intermesh heeft de kennis en
 flexibiliteit in huis om voor elk automatiseringsprobleem een oplossing
 te bieden.\n\nMaatwerk gaat altijd gepaard met goed overleg met de klant.
 Daarom is een prettige samenwerking met de klant voor ons erg belangrijk.
 Uw wensen en de kennis van Intermesh vormen samen de perfecte
 samenwerking.\n\nIntermesh is een kleine organisatie en dat willen we
 graag zo houden. Wij geloven in een klein team van gedreven mensen met
 elk hun eigen expertise. Een klein team houdt de lijnen kort\, kan snel
 schakelen en kan de klant persoonlijke aandacht bieden. Zo ontwikkelen we
 betere en goedkopere producten. Om uitval van teamleden op te vangen
 heeft Intermesh een netwerk met betrouwbare mensen om zich heen gebouwd
 waarop teruggevallen kan worden.\n
CLASS:PUBLIC
CREATED:20100514T080538Z
LAST-MODIFIED:20100514T080538Z
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
<?php
require('../../www/Group-Office.php');

require_once ($GLOBALS['GO_MODULES']->modules['calendar']['class_path']."calendar.class.inc.php");
require_once ($GLOBALS['GO_CONFIG']->class_path."ical2array.class.inc");
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


$ical_str='BEGIN:VCALENDAR
CALSCALE:GREGORIAN
PRODID:-//Ximian//NONSGML Evolution Calendar//EN
VERSION:2.0
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
UID:20101008T112712Z-26890-1000-26849-2@Intermesh-1
DTSTAMP:20101008T112712Z
DTSTART;TZID=/freeassociation.sourceforge.net/Tzfile/Europe/Amsterdam:
 20101008T133000
DTEND;TZID=/freeassociation.sourceforge.net/Tzfile/Europe/Amsterdam:
 20101008T163000
TRANSP:OPAQUE
SEQUENCE:2
SUMMARY:test met alarm
DESCRIPTION:test
CLASS:PUBLIC
CREATED:20101008T112734Z
LAST-MODIFIED:20101008T112734Z
BEGIN:VALARM
X-EVOLUTION-ALARM-UID:20101008T112734Z-26895-1000-1-28@Intermesh-1
DESCRIPTION:test met alarm
ACTION:DISPLAY
TRIGGER;VALUE=DURATION;RELATED=START:-PT15M
END:VALARM
END:VEVENT
END:VCALENDAR';


$ical_str='BEGIN:VCALENDAR
VERSION:1.0
TZ:+0100
DAYLIGHT:TRUE;+0200;20100328T020000;20101031T030000;;
DAYLIGHT:TRUE;+0200;20110327T020000;20111030T030000;;
BEGIN:VEVENT
X-FUNAMBOL-FOLDER:DEFAULT_FOLDER
X-FUNAMBOL-ALLDAY:0
DTSTART:20101207T150000Z
DTEND:20101207T153000Z
X-MICROSOFT-CDO-BUSYSTATUS:BUSY
CATEGORIES:
DESCRIPTION:
LOCATION:
PRIORITY:2
STATUS:0
X-MICROSOFT-CDO-REPLYTIME:
SUMMARY:Wekelijks
CLASS:PUBLIC
AALARM:20101207T144500Z;;0;
RRULE:W1 TU 20110208T160000 #10
EXDATE:
RDATE:
X-FUNAMBOL-BILLINGINFO:
X-FUNAMBOL-COMPANIES:
X-FUNAMBOL-MILEAGE:
X-FUNAMBOL-NOAGING:0
END:VEVENT
END:VCALENDAR
';

$ical_str='BEGIN:VCALENDAR
METHOD:REPLY
PRODID:Microsoft CDO for Microsoft Exchange
VERSION:2.0
BEGIN:VTIMEZONE
TZID:(GMT+01.00) Sarajevo/Warsaw/Zagreb
X-MICROSOFT-CDO-TZID:2
BEGIN:STANDARD
DTSTART:16010101T030000
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
RRULE:FREQ=YEARLY;WKST=MO;INTERVAL=1;BYMONTH=10;BYDAY=-1SU
END:STANDARD
BEGIN:DAYLIGHT
DTSTART:16010101T020000
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
RRULE:FREQ=YEARLY;WKST=MO;INTERVAL=1;BYMONTH=3;BYDAY=-1SU
END:DAYLIGHT
END:VTIMEZONE
BEGIN:VEVENT
DTSTAMP:20110106T112844Z
DTSTART;TZID="(GMT+01.00) Sarajevo/Warsaw/Zagreb":20110119T122800
SUMMARY:Zugesagt: Invitation: test outlook
UID:0954974d-288a-5b29-9611-e3376fd4ec1c
ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=ACCEPTED;RSVP=TRUE;CN="Gamma Ramon M
 TGE":MAILTO:Ramon.Gamma@mt.com
ORGANIZER:MAILTO:ramon.gamma@oagr.ch
LOCATION:
DTEND;TZID="(GMT+01.00) Sarajevo/Warsaw/Zagreb":20110119T132800
SEQUENCE:0
PRIORITY:5
CLASS:
CREATED:20110106T112934Z
LAST-MODIFIED:20110106T112934Z
STATUS:TENTATIVE
TRANSP:OPAQUE
X-MICROSOFT-CDO-BUSYSTATUS:BUSY
X-MICROSOFT-CDO-INSTTYPE:0
X-MICROSOFT-CDO-REPLYTIME:20110106T112933Z
X-MICROSOFT-CDO-INTENDEDSTATUS:BUSY
X-MICROSOFT-CDO-ALLDAYEVENT:FALSE
X-MICROSOFT-CDO-IMPORTANCE:1
X-MICROSOFT-CDO-OWNERAPPTID:-1
X-MICROSOFT-CDO-APPT-SEQUENCE:0
X-MICROSOFT-CDO-ATTENDEE-CRITICAL-CHANGE:20110106T112933Z
X-MICROSOFT-CDO-OWNER-CRITICAL-CHANGE:20110106T112844Z
END:VEVENT
END:VCALENDAR';


$ical_str="BEGIN:VCALENDAR
CALSCALE:GREGORIAN
PRODID:-//Ximian//NONSGML Evolution Calendar//EN
VERSION:2.0
METHOD:REQUEST
BEGIN:VTIMEZONE
TZID:/freeassociation.sourceforge.net/Tzfile/Europe/Amsterdam
X-LIC-LOCATION:Europe/Amsterdam
BEGIN:STANDARD
TZNAME:CET
DTSTART:19701030T030000
RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
END:STANDARD
BEGIN:DAYLIGHT
TZNAME:CEST
DTSTART:19700327T020000
RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=3
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
END:DAYLIGHT
END:VTIMEZONE
BEGIN:VEVENT
UID:20110512T084134Z-2128-1000-1-3@Intermesh-1
DTSTAMP:20110512T084341Z
DTSTART;TZID=/freeassociation.sourceforge.net/Tzfile/Europe/Amsterdam:
 20110514T120000
DTEND;TZID=/freeassociation.sourceforge.net/Tzfile/Europe/Amsterdam:
 20110514T123000
TRANSP:OPAQUE
SEQUENCE:4
SUMMARY:evo2
CLASS:PUBLIC
ORGANIZER;CN=Merijn Schering:MAILTO:admin@intermesh.dev
CREATED:20110512T084155Z
LAST-MODIFIED:20110512T084242Z
RECURRENCE-ID;
 TZID=/freeassociation.sourceforge.net/Tzfile/Europe/Amsterdam:
 20110514T103000
ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=ACCEPTED;
 RSVP=TRUE;CN=Merijn Schering;LANGUAGE=en:MAILTO:admin@intermesh.dev
ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=ACCEPTED;
 RSVP=TRUE;LANGUAGE=en:MAILTO:test@intermesh.dev
END:VEVENT
END:VCALENDAR";

$ical_str='BEGIN:VCALENDAR
PRODID:-//Mozilla.org/NONSGML Mozilla Calendar V1.1//EN
VERSION:2.0
BEGIN:VEVENT
LAST-MODIFIED:20110513T091807Z
DTSTAMP:20110513T091807Z
UID:e107c9a6-bf05-5163-b16f-9b558156ddfd
SUMMARY:test elke dag
ORGANIZER;CN="Administrator, Group-Office":mailto:admin@intermesh.dev
ATTENDEE;RSVP=FALSE;CN="Administrator, Group-Office";PARTSTAT=ACCEPTED;CUT
 YPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;LANGUAGE=en:mailto:admin@intermesh.dev
 
RRULE:FREQ=DAILY
EXDATE:20110519T082500Z
EXDATE:20110518T082500Z
EXDATE:20110517T082500Z
DTSTART:20110509T082500Z
DTEND:20110509T092500Z
SEQUENCE:3
TRANSP:OPAQUE
CLASS:PUBLIC
X-MOZ-GENERATION:10
END:VEVENT
END:VCALENDAR';


$ical_str='BEGIN:VCALENDAR
PRODID:-//Sun/Calendar Server//EN
METHOD:PUBLISH
VERSION:2.0
X-S1CS-EXPORTVERSION:6.0
BEGIN:VEVENT
UID:000000000000000000000000000000004b1f939b00005d27000009a8000061bc
DTSTAMP:20110518T012414Z
SUMMARY:CBS issues of GHB : Updated status
DTSTART:20091211T060000Z
DTEND:20091211T090000Z
CREATED:20091209T121003Z
LAST-MODIFIED:20091221T113514Z
PRIORITY:0
SEQUENCE:0
DESCRIPTION:Please note : postpone the meeting from 15:00 to 13:00 at same
  meeting room
CLASS:PUBLIC
LOCATION:Crystal Room\, 7th fl.\, YIT office
ORGANIZER;CN="crystal_room"
 ;SENT-BY="mailto:piyapol.ch@yipintsoi.com"
 ;X-NSCP-ORGANIZER-UID=piyapoch@yipintsoi.com
 ;X-NSCP-ORGANIZER-SENT-BY-UID=piyapoch@yipintsoi.com
 ;X-S1CS-EMAIL=piyapol.ch@yipintsoi.com
 :crystal_room@yipintsoi.com
STATUS:CONFIRMED
TRANSP:OPAQUE
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Akaradej Ketruskul"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=akaradej.ke@yipintsoi.com
 :akaradke@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Anake Ruttanai"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=anake.ru@yipintsoi.com
 :anakeru@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Anake Srivilas"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=anake.sr@yipintsoi.com
 :anakesr@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Anurat Ratnumnoy"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=anurat.ra@yipintsoi.com
 :anuratra@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Danai Wangsiri"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=danai.wa@yipintsoi.com
 :danaiwa@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Piyapol Churnratanakul"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=piyapol.ch@yipintsoi.com
 :piyapoch@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Prapan Charasshutshawankul"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=prapan.ch@yipintsoi.com
 :prapanch@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Prasarn Lerkumnueychok"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=prasarn.le@yipintsoi.com
 :prasarle@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=ACCEPTED;CN="Pravit Komthongchuskul"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=pravit.ko@yipintsoi.com
 :pravitko@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Saraphol Kittiwarakarn"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=saraphol.ki@yipintsoi.com
 :saraphki@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Satchaporn Rattanavanit"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=satchaporn.ra@yipintsoi.com
 :satchara@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Sirin Piya-O-Lan"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=sirin.pi@yipintsoi.com
 :sirinpi@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=DECLINED;CN="Somchai Kanjanapattana"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=somchai.ka@yipintsoi.com
 :somchaka@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Suksan Mongkoljuthar"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=0
 ;X-S1CS-EMAIL=suksan.mo@yipintsoi.com
 :suksanmo@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Supak Lailert"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=0
 ;X-S1CS-EMAIL=supak.la@yipintsoi.com
 :supak@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Taned Sangowingul"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=0
 ;X-S1CS-EMAIL=taned.sa@yipintsoi.com
 :tanedsa@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Tanongsak Gulyanon"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=0
 ;X-S1CS-EMAIL=tanongsak.gu@yipintsoi.com
 :tanonggu@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=ACCEPTED;CN="Teera Sumatawattana"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=teera.su@yipintsoi.com
 :teerasu@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Thitipong Limudomsuk"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=0
 ;X-S1CS-EMAIL=thitipong.li@yipintsoi.com
 :thitipli@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Vicha Upariputtipong"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=0
 ;X-S1CS-EMAIL=vicha.up@yipintsoi.com
 :vichaup@yipintsoi.com
X-S1CS-GROUP-ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=GROUP
 ;X-S1CS-GROUP-EXPAND=TRUE
 ;PARTSTAT=NEEDS-ACTION;CN="ghb-support"
 ;RSVP=FALSE
 ;X-NSCP-ATTENDEE-GSE-STATUS=0
 ;X-S1CS-EMAIL=ghb-support@yipintsoi.com
 :ghb-support@yipintsoi.com
X-NSCP-ORIGINAL-DTSTART:20091211T060000Z
X-NSCP-LANGUAGE:en
X-NSCP-DTSTART-TZID:Asia/Bangkok
X-NSCP-TOMBSTONE:0
X-NSCP-ONGOING:0
X-NSCP-ORGANIZER-EMAIL:piyapol.ch@yipintsoi.com
X-NSCP-GSE-COMPONENT-STATE;X-NSCP-GSE-COMMENT="REPLY-DECLINED":262208
END:VEVENT
BEGIN:VEVENT
UID:000000000000000000000000000000004b2b539f00000de900000ac0000061bc
DTSTAMP:20110518T012414Z
SUMMARY:GHB : Meeting for update any issues of GHB
DTSTART:20091221T070000Z
DTEND:20091221T090000Z
CREATED:20091218T100415Z
LAST-MODIFIED:20091221T113456Z
PRIORITY:0
SEQUENCE:0
DESCRIPTION:อาเจ็กขอเรียกประชุมเ
 พื่ออัพเดท issues ต่างๆ ของ GHB เ
 ช่น CBS issues\, M5000 เพื่อช่วยงาน LOS 
 เป็นต้น
CLASS:PUBLIC
LOCATION:Crystal Room
ORGANIZER;CN="crystal_room"
 ;SENT-BY="mailto:piyapol.ch@yipintsoi.com"
 ;X-NSCP-ORGANIZER-UID=piyapoch@yipintsoi.com
 ;X-NSCP-ORGANIZER-SENT-BY-UID=piyapoch@yipintsoi.com
 ;X-S1CS-EMAIL=piyapol.ch@yipintsoi.com
 :crystal_room@yipintsoi.com
STATUS:CONFIRMED
TRANSP:OPAQUE
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Akaradej Ketruskul"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=akaradej.ke@yipintsoi.com
 :akaradke@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Anake Ruttanai"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=anake.ru@yipintsoi.com
 :anakeru@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Anake Srivilas"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=anake.sr@yipintsoi.com
 :anakesr@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Anurat Ratnumnoy"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=anurat.ra@yipintsoi.com
 :anuratra@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Danai Wangsiri"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=danai.wa@yipintsoi.com
 :danaiwa@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Piyapol Churnratanakul"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=piyapol.ch@yipintsoi.com
 :piyapoch@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Prapan Charasshutshawankul"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=prapan.ch@yipintsoi.com
 :prapanch@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Prasarn Lerkumnueychok"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=prasarn.le@yipintsoi.com
 :prasarle@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=ACCEPTED;CN="Pravit Komthongchuskul"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=pravit.ko@yipintsoi.com
 :pravitko@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Saraphol Kittiwarakarn"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=saraphol.ki@yipintsoi.com
 :saraphki@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Satchaporn Rattanavanit"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=satchaporn.ra@yipintsoi.com
 :satchara@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Sirin Piya-O-Lan"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=sirin.pi@yipintsoi.com
 :sirinpi@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=DECLINED;CN="Somchai Kanjanapattana"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=2
 ;X-S1CS-EMAIL=somchai.ka@yipintsoi.com
 :somchaka@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Suksan Mongkoljuthar"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=0
 ;X-S1CS-EMAIL=suksan.mo@yipintsoi.com
 :suksanmo@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Supak Lailert"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=0
 ;X-S1CS-EMAIL=supak.la@yipintsoi.com
 :supak@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Taned Sangowingul"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=0
 ;X-S1CS-EMAIL=taned.sa@yipintsoi.com
 :tanedsa@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Tanongsak Gulyanon"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=0
 ;X-S1CS-EMAIL=tanongsak.gu@yipintsoi.com
 :tanonggu@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Teera Sumatawattana"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=0
 ;X-S1CS-EMAIL=teera.su@yipintsoi.com
 :teerasu@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Thitipong Limudomsuk"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=0
 ;X-S1CS-EMAIL=thitipong.li@yipintsoi.com
 :thitipli@yipintsoi.com
ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=INDIVIDUAL
 ;PARTSTAT=NEEDS-ACTION;CN="Vicha Upariputtipong"
 ;MEMBER="ghb-support@yipintsoi.com"
 ;RSVP=TRUE
 ;X-NSCP-ATTENDEE-GSE-STATUS=0
 ;X-S1CS-EMAIL=vicha.up@yipintsoi.com
 :vichaup@yipintsoi.com
X-S1CS-GROUP-ATTENDEE;ROLE=REQ-PARTICIPANT;CUTYPE=GROUP
 ;X-S1CS-GROUP-EXPAND=TRUE
 ;PARTSTAT=NEEDS-ACTION;CN="ghb-support"
 ;RSVP=FALSE
 ;X-NSCP-ATTENDEE-GSE-STATUS=0
 ;X-S1CS-EMAIL=ghb-support@yipintsoi.com
 :ghb-support@yipintsoi.com
X-NSCP-ORIGINAL-DTSTART:20091221T070000Z
X-NSCP-LANGUAGE:en
X-NSCP-DTSTART-TZID:Asia/Bangkok
X-NSCP-TOMBSTONE:0
X-NSCP-ONGOING:0
X-NSCP-ORGANIZER-EMAIL:piyapol.ch@yipintsoi.com
X-NSCP-GSE-COMPONENT-STATE;X-NSCP-GSE-COMMENT="REPLY-DECLINED":262208
END:VEVENT
BEGIN:VEVENT
UID:000000000000000000000000000000004b08f4a6000074db000006c9000061bc
DTSTAMP:20110518T012414Z
SUMMARY:YIT :: ITIL v.3
DTSTART:20091222T010000Z
DTEND:20091222T100000Z
CREATED:20091122T082158Z
LAST-MODIFIED:20091122T082158Z
PRIORITY:0
SEQUENCE:0
DESCRIPTION:ITIL v.3
CLASS:PUBLIC
ORGANIZER;CN="Somchai Kanjanapattana"
 ;X-S1CS-EMAIL=somchai.ka@yipintsoi.com
 :somchaka@yipintsoi.com
STATUS:CONFIRMED
TRANSP:OPAQUE
X-NSCP-ORIGINAL-DTSTART:20091222T010000Z
X-NSCP-LANGUAGE:en
BEGIN:VALARM
ACTION:EMAIL
TRIGGER;VALUE=DATE-TIME:20091221T010000Z
SUMMARY:Reminder: YIT :: ITIL v.3
DESCRIPTION:Reminder: YIT :: ITIL v.3
ATTENDEE:MAILTO:somchaka@yipintsoi.com
END:VALARM
X-NSCP-DTSTART-TZID:Asia/Bangkok
X-NSCP-TOMBSTONE:0
X-NSCP-ONGOING:0
X-NSCP-ORGANIZER-EMAIL:somchai.ka@yipintsoi.com
X-NSCP-GSE-COMPONENT-STATE;X-NSCP-GSE-COMMENT="PUBLISH-COMPLETED":65538
END:VEVENT
BEGIN:VEVENT
UID:000000000000000000000000000000004b08f4a900002db3000006cb000061bc
DTSTAMP:20110518T012414Z
SUMMARY:YIT :: ITIL v.3
DTSTART:20091223T010000Z
DTEND:20091223T100000Z
CREATED:20091122T082201Z
LAST-MODIFIED:20091122T082203Z
PRIORITY:0
SEQUENCE:1
DESCRIPTION:ITIL v.3
CLASS:PUBLIC
ORGANIZER;CN="Somchai Kanjanapattana"
 ;X-S1CS-EMAIL=somchai.ka@yipintsoi.com
 :somchaka@yipintsoi.com
STATUS:CONFIRMED
TRANSP:OPAQUE
X-NSCP-ORIGINAL-DTSTART:20091222T010000Z
X-NSCP-LANGUAGE:en
BEGIN:VALARM
ACTION:EMAIL
TRIGGER;VALUE=DATE-TIME:20091222T010000Z
SUMMARY:Reminder: YIT :: ITIL v.3
DESCRIPTION:Reminder: YIT :: ITIL v.3
ATTENDEE:MAILTO:somchaka@yipintsoi.com
END:VALARM
X-NSCP-DTSTART-TZID:Asia/Bangkok
X-NSCP-TOMBSTONE:0
X-NSCP-ONGOING:0
X-NSCP-ORGANIZER-EMAIL:somchai.ka@yipintsoi.com
X-NSCP-GSE-COMPONENT-STATE;X-NSCP-GSE-COMMENT="PUBLISH-COMPLETED":65538
END:VEVENT
END:VCALENDAR';


//$vcalendar = $ical->parse_file('/home/mschering/jos.ics');
//
$vcalendar = $ical->parse_icalendar_string($ical_str);
var_dump($vcalendar);
exit();

while($object = array_shift($vcalendar[0]['objects'])) {
	if($object['type'] == 'VEVENT' || $object['type'] == 'VTODO') {
		var_dump($object);
		$event = $cal->get_event_from_ical_object($object);
		
		echo 'Name: '.$event['name']."\n";
		echo 'Start time: '.date('Ymd G:i', $event['start_time'])."\n";
		echo 'End time: '.date('Ymd G:i', $event['end_time'])."\n";
		echo "\n------------\n\n";
		
		var_dump($event);
	}
}
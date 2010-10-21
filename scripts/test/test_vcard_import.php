<?php
require('../../www/Group-Office.php');

require_once ($GO_MODULES->modules['addressbook']['class_path']."vcard.class.inc.php");
require_once ($GO_CONFIG->class_path."ical2array.class.inc");


$vcard_text='BEGIN:VCARD
VERSION:2.1
N:Schering;Merijn;;Title;
BDAY:19450325
NOTE:Notes
TEL;WORK;FAX:312123
TEL;VOICE;WORK:34254234
TEL;VOICE;WORK:
TEL;CAR;VOICE:
CATEGORIES:
TEL;WORK;PREF:
FN:Schering, Merijn
EMAIL;INTERNET:email1@intermesh.nl
EMAIL;INTERNET;HOME:mschering@intermesh.nl
EMAIL;INTERNET;WORK:email2@intermesh.nl
TITLE:Job title
TEL;VOICE;HOME:123213
TEL;VOICE;HOME:
TEL;HOME;FAX:fax123
URL;HOME:
PRIORITY:1
TEL;CELL:123123
NICKNAME:
TEL;FAX:
TEL;VOICE:
TEL;PAGER:
TEL;PREF;VOICE:
ROLE:
CLASS:PUBLIC
URL:
ORG:Intermesh;;
ADR;HOME:;;Hesselsstraat 97b;Den Bosch;State;ZIP;Country
ADR:;;;;;;
ADR;WORK;ENCODING=QUOTED-PRINTABLE;CHARSET=UTF-8:;;Address 1=0D=0Aadres 2;plaats;state;123134;Nederland
PHOTO:
X-ANNIVERSARY:
X-FUNAMBOL-BILLINGINFO:
TEL;X-FUNAMBOL-CALLBACK:
X-FUNAMBOL-CHILDREN:
X-FUNAMBOL-COMPANIES:
X-FUNAMBOL-FOLDER:DEFAULT_FOLDER
X-FUNAMBOL-GENDER:0
X-FUNAMBOL-HOBBIES:
EMAIL;INTERNET;HOME;X-FUNAMBOL-INSTANTMESSENGER:
X-FUNAMBOL-INITIALS:M.S.
X-FUNAMBOL-LANGUAGES:
X-MANAGER:
X-FUNAMBOL-MILEAGE:
X-FUNAMBOL-ORGANIZATIONALID:
TEL;X-FUNAMBOL-RADIO:
X-SPOUSE:
X-FUNAMBOL-SUBJECT:Merijn Schering
TEL;X-FUNAMBOL-TELEX:
X-FUNAMBOL-YOMICOMPANYNAME:
X-FUNAMBOL-YOMIFIRSTNAME:
X-FUNAMBOL-YOMILASTNAME:
X-GO-COMPANY-BANK-NO:
X-GO-COMPANY-EMAIL:
X-GO-COMPANY-FAX:
X-GO-COMPANY-POST-ADDRESS:
X-GO-COMPANY-POST-ADDRESS-NO:
X-GO-COMPANY-POST-CITY:
X-GO-COMPANY-POST-COUNTRY:
X-GO-COMPANY-POST-STATE:
X-GO-COMPANY-POST-ZIP:
X-GO-COMPANY-TEL:
X-GO-COMPANY-VAT-NO:
X-GO-SALUTATION:
END:VCARD';

$vcard = new vcard();
$record = $vcard->vcf_to_go($vcard_text);
var_dump($record);

//$vcard->export_contact(3);
//echo $vcard->vcf;


<?php
require('../../www/Group-Office.php');

require_once ($GO_MODULES->modules['addressbook']['class_path']."vcard.class.inc.php");
require_once ($GO_CONFIG->class_path."ical2array.class.inc");


$vcard_text='BEGIN:VCARD
VERSION:2.1
N:Schering;Merijn;;Title;
TEL;VOICE;HOME:12345
TEL;VOICE;WORK:#1+33242423
TEL;PAGER:424213
TEL;FAX;WORK:2342
TEL;CELL:12334567
TEL;VOICE:23
ADR;HOME:;2;Reitscheweg 37;Den Bosch;State;ZIP;Country
ADR;WORK:;2;Work address;City;State;ZIP;Country
ADR:;;;;;;
BDAY:1945-03-25
EMAIL;INTERNET:
EMAIL;INTERNET;HOME:mschering@intermesh.nl
EMAIL;INTERNET;WORK:
TITLE:Job title
URL:
ORG:Intermesh
NOTE:Notes
PHOTO:
CATEGORIES:
END:VCARD';

$vcard = new vcard();
//$record = $vcard->vcf_to_go($vcard_text);
//var_dump($record);

$vcard->export_contact(1);
echo $vcard->vcf;


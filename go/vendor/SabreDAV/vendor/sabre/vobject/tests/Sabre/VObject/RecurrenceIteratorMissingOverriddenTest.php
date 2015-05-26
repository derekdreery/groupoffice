<?php

namespace Sabre\VObject;

use
    DateTime,
    DateTimeZone;

class RecurrenceIteratorMissingOverriddenTest extends \PHPUnit_Framework_TestCase {

    function testExpand() {

        $input = <<<ICS
BEGIN:VCALENDAR
VERSION:2.0
BEGIN:VEVENT
UID:foo
DTSTART:20130727T120000Z
DURATION:PT1H
RRULE:FREQ=DAILY;COUNT=2
SUMMARY:A
END:VEVENT
BEGIN:VEVENT
RECURRENCE-ID:20130728T120000Z
UID:foo
DTSTART:20140101T120000Z
DURATION:PT1H
SUMMARY:B
END:VEVENT
END:VCALENDAR
ICS;

        $vcal = Reader::read($input);
        $this->assertInstanceOf('Sabre\\VObject\\Component\\VCalendar', $vcal);

        $vcal->expand(new DateTime('2011-01-01'), new DateTime('2015-01-01'));

        $result = $vcal->serialize();

        $output = <<<ICS
BEGIN:VCALENDAR
VERSION:2.0
BEGIN:VEVENT
UID:foo
DTSTART;VALUE=DATE-TIME:20130727T120000Z
DURATION:PT1H
SUMMARY:A
END:VEVENT
BEGIN:VEVENT
RECURRENCE-ID:20130728T120000Z
UID:foo
DTSTART:20140101T120000Z
DURATION:PT1H
SUMMARY:B
END:VEVENT
END:VCALENDAR

ICS;
        $this->assertEquals($output, str_replace("\r","",$result));
    
    }

}

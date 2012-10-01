<?php

class Sabre_VObject_RecurrenceIteratorInfiniteLoopProblemTest extends PHPUnit_Framework_TestCase {

    /**
     * This bug came from a Fruux customer. This would result in a never-ending
     * request.
     */
    function testFastForwardTooFar() {

        $ev = Sabre_VObject_Component::create('VEVENT');
        $ev->DTSTART = '20090420T180000Z';
        $ev->RRULE = 'FREQ=WEEKLY;BYDAY=MO;UNTIL=20090704T205959Z;INTERVAL=1';

        $this->assertFalse($ev->isInTimeRange(new DateTime('2012-01-01 12:00:00'),new DateTime('3000-01-01 00:00:00')));

    }

    /**
     * Different bug, also likely an infinite loop.
     */
    function testYearlyByMonthLoop() {

        $ev = Sabre_VObject_Component::create('VEVENT');
        $ev->UID = 'uuid';
        $ev->DTSTART = '20120101T154500';
        $ev->DTSTART['TZID'] = 'Europe/Berlin';
        $ev->RRULE = 'FREQ=YEARLY;INTERVAL=1;UNTIL=20120203T225959Z;BYMONTH=2;BYSETPOS=1;BYDAY=SU,MO,TU,WE,TH,FR,SA';
        $ev->DTEND = '20120101T164500';
        $ev->DTEND['TZID'] = 'Europe/Berlin';

        // This recurrence rule by itself is a yearly rule that should happen
        // every february.
        //
        // The BYDAY part expands this to every day of the month, but the
        // BYSETPOS limits this to only the 1st day of the month. Very crazy
        // way to specify this, and could have certainly been a lot easier.
        $cal = Sabre_VObject_Component::create('VCALENDAR');
        $cal->add($ev);

        $it = new Sabre_VObject_RecurrenceIterator($cal,'uuid');
        $it->fastForward(new DateTime('2012-01-29 23:00:00', new DateTimeZone('UTC')));

        $collect = array();

        while($it->valid()) {
            $collect[] = $it->getDTSTART();
            if ($it->getDTSTART() > new DateTime('2013-02-05 22:59:59', new DateTimeZone('UTC'))) {
                break;
            }
            $it->next();

        }

        $this->assertEquals(
            array(new DateTime('2012-02-01 15:45:00', new DateTimeZone('Europe/Berlin'))),
            $collect
        );

    }


}

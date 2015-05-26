<?php

namespace Sabre\VObject\ITip;

use Sabre\VObject\Component\VCalendar;
use Sabre\VObject\DateTimeParser;
use Sabre\VObject\Reader;
use Sabre\VObject\Recur\EventIterator;

/**
 * The ITip\Broker class is a utility class that helps with processing
 * so-called iTip messages.
 *
 * iTip is defined in rfc5546, stands for iCalendar Transport-Independent
 * Interoperability Protocol, and describes the underlying mechanism for
 * using iCalendar for scheduling for for example through email (also known as
 * IMip) and CalDAV Scheduling.
 *
 * This class helps by:
 *
 * 1. Creating individual invites based on an iCalendar event for each
 *    attendee.
 * 2. Generating invite updates based on an iCalendar update. This may result
 *    in new invites, updates and cancellations for attendees, if that list
 *    changed.
 * 3. On the receiving end, it can create a local iCalendar event based on
 *    a received invite.
 * 4. It can also process an invite update on a local event, ensuring that any
 *    overridden properties from attendees are retained.
 * 5. It can create a accepted or declined iTip reply based on an invite.
 * 6. It can process a reply from an invite and update an events attendee
 *     status based on a reply.
 *
 * @copyright Copyright (C) 2011-2015 fruux GmbH (https://fruux.com/).
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class Broker {

    /**
     * This setting determines whether the rules for the SCHEDULE-AGENT
     * parameter should be followed.
     *
     * This is a parameter defined on ATTENDEE properties, introduced by RFC
     * 6638. This parameter allows a caldav client to tell the server 'Don't do
     * any scheduling operations'.
     *
     * If this setting is turned on, any attendees with SCHEDULE-AGENT set to
     * CLIENT will be ignored. This is the desired behavior for a CalDAV
     * server, but if you're writing an iTip application that doesn't deal with
     * CalDAV, you may want to ignore this parameter.
     *
     * @var bool
     */
    public $scheduleAgentServerRules = true;

    /**
     * The broker will try during 'parseEvent' figure out whether the change
     * was significant.
     *
     * It uses a few different ways to do this. One of these ways is seeing if
     * certain properties changed values. This list of specified here.
     *
     * This list is taken from:
     * * http://tools.ietf.org/html/rfc5546#section-2.1.4
     *
     * @var string[]
     */
    public $significantChangeProperties = array(
        'DTSTART',
        'DTEND',
        'DURATION',
        'DUE',
        'RRULE',
        'RDATE',
        'EXDATE',
        'STATUS',
    );

    /**
     * This method is used to process an incoming itip message.
     *
     * Examples:
     *
     * 1. A user is an attendee to an event. The organizer sends an updated
     * meeting using a new iTip message with METHOD:REQUEST. This function
     * will process the message and update the attendee's event accordingly.
     *
     * 2. The organizer cancelled the event using METHOD:CANCEL. We will update
     * the users event to state STATUS:CANCELLED.
     *
     * 3. An attendee sent a reply to an invite using METHOD:REPLY. We can
     * update the organizers event to update the ATTENDEE with its correct
     * PARTSTAT.
     *
     * The $existingObject is updated in-place. If there is no existing object
     * (because it's a new invite for example) a new object will be created.
     *
     * If an existing object does not exist, and the method was CANCEL or
     * REPLY, the message effectively gets ignored, and no 'existingObject'
     * will be created.
     *
     * The updated $existingObject is also returned from this function.
     *
     * If the iTip message was not supported, we will always return false.
     *
     * @param Message $itipMessage
     * @param VCalendar $existingObject
     * @return VCalendar|null
     */
    public function processMessage(Message $itipMessage, VCalendar $existingObject = null) {

        // We only support events at the moment.
        if ($itipMessage->component !== 'VEVENT') {
            return false;
        }

        switch($itipMessage->method) {

            case 'REQUEST' :
                return $this->processMessageRequest($itipMessage, $existingObject);

            case 'CANCEL' :
                return $this->processMessageCancel($itipMessage, $existingObject);

            case 'REPLY' :
                return $this->processMessageReply($itipMessage, $existingObject);

            default :
                // Unsupported iTip message
                return null;

        }

        return $existingObject;

    }

    /**
     * This function parses a VCALENDAR object and figure out if any messages
     * need to be sent.
     *
     * A VCALENDAR object will be created from the perspective of either an
     * attendee, or an organizer. You must pass a string identifying the
     * current user, so we can figure out who in the list of attendees or the
     * organizer we are sending this message on behalf of.
     *
     * It's possible to specify the current user as an array, in case the user
     * has more than one identifying href (such as multiple emails).
     *
     * It $oldCalendar is specified, it is assumed that the operation is
     * updating an existing event, which means that we need to look at the
     * differences between events, and potentially send old attendees
     * cancellations, and current attendees updates.
     *
     * If $calendar is null, but $oldCalendar is specified, we treat the
     * operation as if the user has deleted an event. If the user was an
     * organizer, this means that we need to send cancellation notices to
     * people. If the user was an attendee, we need to make sure that the
     * organizer gets the 'declined' message.
     *
     * @param VCalendar|string $calendar
     * @param string|array $userHref
     * @param VCalendar|string $oldCalendar
     * @return array
     */
    public function parseEvent($calendar = null, $userHref, $oldCalendar = null) {

        if ($oldCalendar) {
            if (is_string($oldCalendar)) {
                $oldCalendar = Reader::read($oldCalendar);
            }
            if (!isset($oldCalendar->VEVENT)) {
                // We only support events at the moment
                return array();
            }

            $oldEventInfo = $this->parseEventInfo($oldCalendar);
        } else {
            $oldEventInfo = array(
                'organizer' => null,
                'significantChangeHash' => '',
                'attendees' => array(),
            );
        }

        $userHref = (array)$userHref;

        if (!is_null($calendar)) {

            if (is_string($calendar)) {
                $calendar = Reader::read($calendar);
            }
            if (!isset($calendar->VEVENT)) {
                // We only support events at the moment
                return array();
            }
            $eventInfo = $this->parseEventInfo($calendar);
            if (!$eventInfo['attendees'] && !$oldEventInfo['attendees']) {
                // If there were no attendees on either side of the equation,
                // we don't need to do anything.
                return array();
            }
            if (!$eventInfo['organizer'] && !$oldEventInfo['organizer']) {
                // There was no organizer before or after the change.
                return array();
            }

            $baseCalendar = $calendar;

            // If the new object didn't have an organizer, the organizer
            // changed the object from a scheduling object to a non-scheduling
            // object. We just copy the info from the old object.
            if (!$eventInfo['organizer'] && $oldEventInfo['organizer']) {
                $eventInfo['organizer'] = $oldEventInfo['organizer'];
                $eventInfo['organizerName'] = $oldEventInfo['organizerName'];
            }

        } else {
            // The calendar object got deleted, we need to process this as a
            // cancellation / decline.
            if (!$oldCalendar) {
                // No old and no new calendar, there's no thing to do.
                return array();
            }

            $eventInfo = $oldEventInfo;

            if (in_array($eventInfo['organizer'], $userHref)) {
                // This is an organizer deleting the event.
                $eventInfo['attendees'] = array();
                // Increasing the sequence, but only if the organizer deleted
                // the event.
                $eventInfo['sequence']++;
            } else {
                // This is an attendee deleting the event.
                foreach($eventInfo['attendees'] as $key=>$attendee) {
                    if (in_array($attendee['href'], $userHref)) {
                        $eventInfo['attendees'][$key]['instances'] = array('master' =>
                            array('id'=>'master', 'partstat' => 'DECLINED')
                        );
                    }
                }
            }
            $baseCalendar = $oldCalendar;

        }

        if (in_array($eventInfo['organizer'], $userHref)) {
            return $this->parseEventForOrganizer($baseCalendar, $eventInfo, $oldEventInfo);
        } elseif ($oldCalendar) {
            // We need to figure out if the user is an attendee, but we're only
            // doing so if there's an oldCalendar, because we only want to
            // process updates, not creation of new events.
            foreach($eventInfo['attendees'] as $attendee) {
                if (in_array($attendee['href'], $userHref)) {
                    return $this->parseEventForAttendee($baseCalendar, $eventInfo, $oldEventInfo, $attendee['href']);
                }
            }
        }
        return array();

    }

    /**
     * Processes incoming REQUEST messages.
     *
     * This is message from an organizer, and is either a new event
     * invite, or an update to an existing one.
     *
     *
     * @param Message $itipMessage
     * @param VCalendar $existingObject
     * @return VCalendar|null
     */
    protected function processMessageRequest(Message $itipMessage, VCalendar $existingObject = null) {

        if (!$existingObject) {
            // This is a new invite, and we're just going to copy over
            // all the components from the invite.
            $existingObject = new VCalendar();
            foreach($itipMessage->message->getComponents() as $component) {
                $existingObject->add(clone $component);
            }
        } else {
            // We need to update an existing object with all the new
            // information. We can just remove all existing components
            // and create new ones.
            foreach($existingObject->getComponents() as $component) {
                $existingObject->remove($component);
            }
            foreach($itipMessage->message->getComponents() as $component) {
                $existingObject->add(clone $component);
            }
        }
        return $existingObject;

    }

    /**
     * Processes incoming CANCEL messages.
     *
     * This is a message from an organizer, and means that either an
     * attendee got removed from an event, or an event got cancelled
     * altogether.
     *
     * @param Message $itipMessage
     * @param VCalendar $existingObject
     * @return VCalendar|null
     */
    protected function processMessageCancel(Message $itipMessage, VCalendar $existingObject = null) {

        if (!$existingObject) {
            // The event didn't exist in the first place, so we're just
            // ignoring this message.
        } else {
            foreach($existingObject->VEVENT as $vevent) {
                $vevent->STATUS = 'CANCELLED';
                $vevent->SEQUENCE = $itipMessage->sequence;
            }
        }
        return $existingObject;

    }

    /**
     * Processes incoming REPLY messages.
     *
     * The message is a reply. This is for example an attendee telling
     * an organizer he accepted the invite, or declined it.
     *
     * @param Message $itipMessage
     * @param VCalendar $existingObject
     * @return VCalendar|null
     */
    protected function processMessageReply(Message $itipMessage, VCalendar $existingObject = null) {

        // A reply can only be processed based on an existing object.
        // If the object is not available, the reply is ignored.
        if (!$existingObject) {
            return null;
        }
        $instances = array();
        $requestStatus = '2.0';

        // Finding all the instances the attendee replied to.
        foreach($itipMessage->message->VEVENT as $vevent) {
            $recurId = isset($vevent->{'RECURRENCE-ID'})?$vevent->{'RECURRENCE-ID'}->getValue():'master';
            $attendee = $vevent->ATTENDEE;
            $instances[$recurId] = $attendee['PARTSTAT']->getValue();
            if (isset($vevent->{'REQUEST-STATUS'})) {
                $requestStatus = $vevent->{'REQUEST-STATUS'}->getValue();
                list($requestStatus) = explode(';', $requestStatus);
            }
        }

        // Now we need to loop through the original organizer event, to find
        // all the instances where we have a reply for.
        $masterObject = null;
        foreach($existingObject->VEVENT as $vevent) {
            $recurId = isset($vevent->{'RECURRENCE-ID'})?$vevent->{'RECURRENCE-ID'}->getValue():'master';
            if ($recurId==='master') {
                $masterObject = $vevent;
            }
            if (isset($instances[$recurId])) {
                $attendeeFound = false;
                if (isset($vevent->ATTENDEE)) {
                    foreach($vevent->ATTENDEE as $attendee) {
                        if ($attendee->getValue() === $itipMessage->sender) {
                            $attendeeFound = true;
                            $attendee['PARTSTAT'] = $instances[$recurId];
                            $attendee['SCHEDULE-STATUS'] = $requestStatus;
                            // Un-setting the RSVP status, because we now know
                            // that the attende already replied.
                            unset($attendee['RSVP']);
                            break;
                        }
                    }
                }
                if (!$attendeeFound) {
                    // Adding a new attendee. The iTip documentation calls this
                    // a party crasher.
                    $attendee = $vevent->add('ATTENDEE', $itipMessage->sender, array(
                        'PARTSTAT' => $instances[$recurId]
                    ));
                    if ($itipMessage->senderName) $attendee['CN'] = $itipMessage->senderName;
                }
                unset($instances[$recurId]);
            }
        }

        if(!$masterObject) {
            // No master object, we can't add new instances.
            return null;
        }
        // If we got replies to instances that did not exist in the
        // original list, it means that new exceptions must be created.
        foreach($instances as $recurId=>$partstat) {

            $recurrenceIterator = new EventIterator($existingObject, $itipMessage->uid);
            $found = false;
            $iterations = 1000;
            do {

                $newObject = $recurrenceIterator->getEventObject();
                $recurrenceIterator->next();

                if (isset($newObject->{'RECURRENCE-ID'}) && $newObject->{'RECURRENCE-ID'}->getValue()===$recurId) {
                    $found = true;
                }
                $iterations--;

            } while($recurrenceIterator->valid() && !$found && $iterations);

            // Invalid recurrence id. Skipping this object.
            if (!$found) continue;

            unset(
                $newObject->RRULE,
                $newObject->EXDATE,
                $newObject->RDATE
            );
            $attendeeFound = false;
            if (isset($newObject->ATTENDEE)) {
                foreach($newObject->ATTENDEE as $attendee) {
                    if ($attendee->getValue() === $itipMessage->sender) {
                        $attendeeFound = true;
                        $attendee['PARTSTAT'] = $partstat;
                        break;
                    }
                }
            }
            if (!$attendeeFound) {
                // Adding a new attendee
                $attendee = $newObject->add('ATTENDEE', $itipMessage->sender, array(
                    'PARTSTAT' => $partstat
                ));
                if ($itipMessage->senderName) {
                    $attendee['CN'] = $itipMessage->senderName;
                }
            }
            $existingObject->add($newObject);

        }
        return $existingObject;

    }

    /**
     * This method is used in cases where an event got updated, and we
     * potentially need to send emails to attendees to let them know of updates
     * in the events.
     *
     * We will detect which attendees got added, which got removed and create
     * specific messages for these situations.
     *
     * @param VCalendar $calendar
     * @param array $eventInfo
     * @param array $oldEventInfo
     * @return array
     */
    protected function parseEventForOrganizer(VCalendar $calendar, array $eventInfo, array $oldEventInfo) {

        // Merging attendee lists.
        $attendees = array();
        foreach($oldEventInfo['attendees'] as $attendee) {
            $attendees[$attendee['href']] = array(
                'href' => $attendee['href'],
                'oldInstances' => $attendee['instances'],
                'newInstances' => array(),
                'name' => $attendee['name'],
                'forceSend' => null,
            );
        }
        foreach($eventInfo['attendees'] as $attendee) {
            if (isset($attendees[$attendee['href']])) {
                $attendees[$attendee['href']]['name'] = $attendee['name'];
                $attendees[$attendee['href']]['newInstances'] = $attendee['instances'];
                $attendees[$attendee['href']]['forceSend'] = $attendee['forceSend'];
            } else {
                $attendees[$attendee['href']] = array(
                    'href' => $attendee['href'],
                    'oldInstances' => array(),
                    'newInstances' => $attendee['instances'],
                    'name' => $attendee['name'],
                    'forceSend' => $attendee['forceSend'],
                );
            }
        }

        $messages = array();

        foreach($attendees as $attendee) {

            // An organizer can also be an attendee. We should not generate any
            // messages for those.
            if ($attendee['href']===$eventInfo['organizer']) {
                continue;
            }

            $message = new Message();
            $message->uid = $eventInfo['uid'];
            $message->component = 'VEVENT';
            $message->sequence = $eventInfo['sequence'];
            $message->sender = $eventInfo['organizer'];
            $message->senderName = $eventInfo['organizerName'];
            $message->recipient = $attendee['href'];
            $message->recipientName = $attendee['name'];

            if (!$attendee['newInstances']) {

                // If there are no instances the attendee is a part of, it
                // means the attendee was removed and we need to send him a
                // CANCEL.
                $message->method = 'CANCEL';

                // Creating the new iCalendar body.
                $icalMsg = new VCalendar();
                $icalMsg->METHOD = $message->method;
                $event = $icalMsg->add('VEVENT', array(
                    'UID' => $message->uid,
                    'SEQUENCE' => $message->sequence,
                ));
                if (isset($calendar->VEVENT->SUMMARY)) {
                    $event->add('SUMMARY', $calendar->VEVENT->SUMMARY->getValue());
                }
                $event->add(clone $calendar->VEVENT->DTSTART);
                $org = $event->add('ORGANIZER', $eventInfo['organizer']);
                if ($eventInfo['organizerName']) $org['CN'] = $eventInfo['organizerName'];
                $event->add('ATTENDEE', $attendee['href'], array(
                    'CN' => $attendee['name'],
                ));
                $message->significantChange = true;

            } else {

                // The attendee gets the updated event body
                $message->method = 'REQUEST';

                // Creating the new iCalendar body.
                $icalMsg = new VCalendar();
                $icalMsg->METHOD = $message->method;

                foreach($calendar->select('VTIMEZONE') as $timezone) {
                    $icalMsg->add(clone $timezone);
                }

                // We need to find out that this change is significant. If it's
                // not, systems may opt to not send messages.
                //
                // We do this based on the 'significantChangeHash' which is
                // some value that changes if there's a certain set of
                // properties changed in the event, or simply if there's a
                // difference in instances that the attendee is invited to.

                $message->significantChange =
                    $attendee['forceSend'] === 'REQUEST' ||
                    array_keys($attendee['oldInstances']) != array_keys($attendee['newInstances']) ||
                    $oldEventInfo['significantChangeHash']!==$eventInfo['significantChangeHash'];

                foreach($attendee['newInstances'] as $instanceId => $instanceInfo) {

                    $currentEvent = clone $eventInfo['instances'][$instanceId];
                    if ($instanceId === 'master') {

                        // We need to find a list of events that the attendee
                        // is not a part of to add to the list of exceptions.
                        $exceptions = array();
                        foreach($eventInfo['instances'] as $instanceId=>$vevent) {
                            if (!isset($attendee['newInstances'][$instanceId])) {
                                $exceptions[] = $instanceId;
                            }
                        }

                        // If there were exceptions, we need to add it to an
                        // existing EXDATE property, if it exists.
                        if ($exceptions) {
                            if (isset($currentEvent->EXDATE)) {
                                $currentEvent->EXDATE->setParts(array_merge(
                                    $currentEvent->EXDATE->getParts(),
                                    $exceptions
                                ));
                            } else {
                                $currentEvent->EXDATE = $exceptions;
                            }
                        }

                        // Cleaning up any scheduling information that
                        // shouldn't be sent along.
                        unset($currentEvent->ORGANIZER['SCHEDULE-FORCE-SEND']);
                        unset($currentEvent->ORGANIZER['SCHEDULE-STATUS']);

                        foreach($currentEvent->ATTENDEE as $attendee) {
                            unset($attendee['SCHEDULE-FORCE-SEND']);
                            unset($attendee['SCHEDULE-STATUS']);

                            // We're adding PARTSTAT=NEEDS-ACTION to ensure that
                            // iOS shows an "Inbox Item"
                            if (!isset($attendee['PARTSTAT'])) {
                                $attendee['PARTSTAT'] = 'NEEDS-ACTION';
                            }

                        }

                    }

                    $icalMsg->add($currentEvent);

                }

            }

            $message->message = $icalMsg;
            $messages[] = $message;

        }

        return $messages;

    }

    /**
     * Parse an event update for an attendee.
     *
     * This function figures out if we need to send a reply to an organizer.
     *
     * @param VCalendar $calendar
     * @param array $eventInfo
     * @param array $oldEventInfo
     * @param string $attendee
     * @return Message[]
     */
    protected function parseEventForAttendee(VCalendar $calendar, array $eventInfo, array $oldEventInfo, $attendee) {

        if ($this->scheduleAgentServerRules && $eventInfo['organizerScheduleAgent']==='CLIENT') {
            return array();
        }

        // Don't bother generating messages for events that have already been
        // cancelled.
        if ($eventInfo['status']==='CANCELLED') {
            return array();
        }

        $instances = array();
        foreach($oldEventInfo['attendees'][$attendee]['instances'] as $instance) {

            $instances[$instance['id']] = array(
                'id' => $instance['id'],
                'oldstatus' => $instance['partstat'],
                'newstatus' => null,
            );

        }
        foreach($eventInfo['attendees'][$attendee]['instances'] as $instance) {

            if (isset($instances[$instance['id']])) {
                $instances[$instance['id']]['newstatus'] = $instance['partstat'];
            } else {
                $instances[$instance['id']] = array(
                    'id' => $instance['id'],
                    'oldstatus' => null,
                    'newstatus' => $instance['partstat'],
                );
            }

        }

        // We need to also look for differences in EXDATE. If there are new
        // items in EXDATE, it means that an attendee deleted instances of an
        // event, which means we need to send DECLINED specifically for those
        // instances.
        // We only need to do that though, if the master event is not declined.
        if (isset($instances['master']) && $instances['master']['newstatus'] !== 'DECLINED') {
            foreach($eventInfo['exdate'] as $exDate) {

                if (!in_array($exDate, $oldEventInfo['exdate'])) {
                    if (isset($instances[$exDate])) {
                        $instances[$exDate]['newstatus'] = 'DECLINED';
                    } else {
                        $instances[$exDate] = array(
                            'id' => $exDate,
                            'oldstatus' => null,
                            'newstatus' => 'DECLINED',
                        );
                    }
                }

            }
        }

        // Gathering a few extra properties for each instance.
        foreach($instances as $recurId=>$instanceInfo) {

            if (isset($eventInfo['instances'][$recurId])) {
                $instances[$recurId]['dtstart'] = clone $eventInfo['instances'][$recurId]->DTSTART;
            } else {
                $instances[$recurId]['dtstart'] = $recurId;
            }

        }

        $message = new Message();
        $message->uid = $eventInfo['uid'];
        $message->method = 'REPLY';
        $message->component = 'VEVENT';
        $message->sequence = $eventInfo['sequence'];
        $message->sender = $attendee;
        $message->senderName = $eventInfo['attendees'][$attendee]['name'];
        $message->recipient = $eventInfo['organizer'];
        $message->recipientName = $eventInfo['organizerName'];

        $icalMsg = new VCalendar();
        $icalMsg->METHOD = 'REPLY';

        $hasReply = false;

        foreach($instances as $instance) {

            if ($instance['oldstatus']==$instance['newstatus'] && $eventInfo['organizerForceSend'] !== 'REPLY') {
                // Skip
                continue;
            }

            $event = $icalMsg->add('VEVENT', array(
                'UID' => $message->uid,
                'SEQUENCE' => $message->sequence,
            ));
            $summary = isset($calendar->VEVENT->SUMMARY)?$calendar->VEVENT->SUMMARY->getValue():'';
            // Adding properties from the correct source instance
            if (isset($eventInfo['instances'][$instance['id']])) {
                $instanceObj = $eventInfo['instances'][$instance['id']];
                $event->add(clone $instanceObj->DTSTART);
                if (isset($instanceObj->SUMMARY)) {
                    $event->add('SUMMARY', $instanceObj->SUMMARY->getValue());
                } elseif ($summary) {
                    $event->add('SUMMARY', $summary);
                }
            } else {
                // This branch of the code is reached, when a reply is
                // generated for an instance of a recurring event, through the
                // fact that the instance has disappeared by showing up in
                // EXDATE
                $dt = DateTimeParser::parse($instance['id'], $eventInfo['timezone']);
                // Treat is as a DATE field
                if (strlen($instance['id']) <= 8) {
                    $recur = $event->add('DTSTART', $dt, array('VALUE' => 'DATE'));
                } else {
                    $recur = $event->add('DTSTART', $dt);
                }
                if ($summary) {
                    $event->add('SUMMARY', $summary);
                }
            }
            if ($instance['id'] !== 'master') {
                $dt = DateTimeParser::parse($instance['id'], $eventInfo['timezone']);
                // Treat is as a DATE field
                if (strlen($instance['id']) <= 8) {
                    $recur = $event->add('RECURRENCE-ID', $dt, array('VALUE' => 'DATE'));
                } else {
                    $recur = $event->add('RECURRENCE-ID', $dt);
                }
            }
            $organizer = $event->add('ORGANIZER', $message->recipient);
            if ($message->recipientName) {
                $organizer['CN'] = $message->recipientName;
            }
            $attendee = $event->add('ATTENDEE', $message->sender, array(
                'PARTSTAT' => $instance['newstatus']
            ));
            if ($message->senderName) {
                $attendee['CN'] = $message->senderName;
            }
            $hasReply = true;

        }

        if ($hasReply) {
            $message->message = $icalMsg;
            return array($message);
        } else {
            return array();
        }

    }

    /**
     * Returns attendee information and information about instances of an
     * event.
     *
     * Returns an array with the following keys:
     *
     * 1. uid
     * 2. organizer
     * 3. organizerName
     * 4. attendees
     * 5. instances
     *
     * @param VCalendar $calendar
     * @return array
     */
    protected function parseEventInfo(VCalendar $calendar = null) {

        $uid = null;
        $organizer = null;
        $organizerName = null;
        $organizerForceSend = null;
        $sequence = null;
        $timezone = null;
        $status = null;
        $organizerScheduleAgent = 'SERVER';

        $significantChangeHash = '';

        // Now we need to collect a list of attendees, and which instances they
        // are a part of.
        $attendees = array();

        $instances = array();
        $exdate = array();

        foreach($calendar->VEVENT as $vevent) {

            if (is_null($uid)) {
                $uid = $vevent->UID->getValue();
            } else {
                if ($uid !== $vevent->UID->getValue()) {
                    throw new ITipException('If a calendar contained more than one event, they must have the same UID.');
                }
            }

            if (!isset($vevent->DTSTART)) {
                throw new ITipException('An event MUST have a DTSTART property.');
            }

            if (isset($vevent->ORGANIZER)) {
                if (is_null($organizer)) {
                    $organizer = $vevent->ORGANIZER->getNormalizedValue();
                    $organizerName = isset($vevent->ORGANIZER['CN'])?$vevent->ORGANIZER['CN']:null;
                } else {
                    if ($organizer !== $vevent->ORGANIZER->getNormalizedValue()) {
                        throw new SameOrganizerForAllComponentsException('Every instance of the event must have the same organizer.');
                    }
                }
                $organizerForceSend =
                    isset($vevent->ORGANIZER['SCHEDULE-FORCE-SEND']) ?
                    strtoupper($vevent->ORGANIZER['SCHEDULE-FORCE-SEND']) :
                    null;
                $organizerScheduleAgent =
                    isset($vevent->ORGANIZER['SCHEDULE-AGENT']) ?
                    strtoupper((string)$vevent->ORGANIZER['SCHEDULE-AGENT']) :
                    'SERVER';
            }
            if (is_null($sequence) && isset($vevent->SEQUENCE)) {
                $sequence = $vevent->SEQUENCE->getValue();
            }
            if (isset($vevent->EXDATE)) {
                foreach ($vevent->select('EXDATE') as $val) {
                    $exdate = array_merge($exdate, $val->getParts());
                }
                sort($exdate);
            }
            if (isset($vevent->STATUS)) {
                $status = strtoupper($vevent->STATUS->getValue());
            }

            $recurId = isset($vevent->{'RECURRENCE-ID'})?$vevent->{'RECURRENCE-ID'}->getValue():'master';
            if ($recurId==='master') {
                $timezone = $vevent->DTSTART->getDateTime()->getTimeZone();
            }
            if(isset($vevent->ATTENDEE)) {
                foreach($vevent->ATTENDEE as $attendee) {

                    if ($this->scheduleAgentServerRules &&
                        isset($attendee['SCHEDULE-AGENT']) &&
                        strtoupper($attendee['SCHEDULE-AGENT']->getValue()) === 'CLIENT'
                    ) {
                        continue;
                    }
                    $partStat =
                        isset($attendee['PARTSTAT']) ?
                        strtoupper($attendee['PARTSTAT']) :
                        'NEEDS-ACTION';

                    $forceSend =
                        isset($attendee['SCHEDULE-FORCE-SEND']) ?
                        strtoupper($attendee['SCHEDULE-FORCE-SEND']) :
                        null;


                    if (isset($attendees[$attendee->getNormalizedValue()])) {
                        $attendees[$attendee->getNormalizedValue()]['instances'][$recurId] = array(
                            'id' => $recurId,
                            'partstat' => $partStat,
                            'force-send' => $forceSend,
                        );
                    } else {
                        $attendees[$attendee->getNormalizedValue()] = array(
                            'href' => $attendee->getNormalizedValue(),
                            'instances' => array(
                                $recurId => array(
                                    'id' => $recurId,
                                    'partstat' => $partStat,
                                ),
                            ),
                            'name' => isset($attendee['CN'])?(string)$attendee['CN']:null,
                            'forceSend' => $forceSend,
                        );
                    }

                }
                $instances[$recurId] = $vevent;

            }

            foreach($this->significantChangeProperties as $prop) {
                if (isset($vevent->$prop)) {
                    $propertyValues = $vevent->select($prop);

                    $significantChangeHash.=$prop.':';

                    if ($prop === 'EXDATE') {

                        $significantChangeHash.= implode(',', $exdate).';';

                    } else {

                        foreach($propertyValues as $val) {
                            $significantChangeHash.= $val->getValue().';';
                        }

                    }
                }
            }

        }
        $significantChangeHash = md5($significantChangeHash);

        return compact(
            'uid',
            'organizer',
            'organizerName',
            'organizerScheduleAgent',
            'organizerForceSend',
            'instances',
            'attendees',
            'sequence',
            'exdate',
            'timezone',
            'significantChangeHash',
            'status'
        );

    }

}

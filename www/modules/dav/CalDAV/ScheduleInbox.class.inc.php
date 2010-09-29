<?php

/**
 * This object represents a CalDAV calendar.
 *
 * A calendar can contain multiple TODO and or Events. These are represented
 * as Sabre_CalDAV_CalendarObject objects.
 *
 * @package Sabre
 * @subpackage CalDAV
 * @copyright Copyright (C) 2007-2010 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/)
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class GO_CalDAV_ScheduleInbox implements Sabre_DAV_ICollection, Sabre_DAV_IProperties {

    /**
     * This is an array with calendar information
     *
     * @var array
     */
    private $calendarInfo;

    /**
     * CalDAV backend
     *
     * @var Sabre_CalDAV_Backend_Abstract
     */
    private $caldavBackend;

    /**
     * Authentication backend
     *
     * @var Sabre_DAV_Auth_Backend_Abstract
     */
    private $authBackend;

    /**
     * Constructor
     *
     * @param Sabre_CalDAV_Backend_Abstract $caldavBackend
     * @param array $calendarInfo
     * @return void
     */
    public function __construct($principalUri) {
		$this->principalUri = $principalUri;
	}

    /**
     * Returns the name of the calendar
     *
     * @return string
     */
    public function getName() {

        return $this->calendarInfo['uri'];

    }

   /*
     * Returns the list of properties
     *
     * @param array $properties
     * @return array
     */
    public function getProperties($requestedProperties) {

        $response = array();

        if (!$this->hasPrivilege()) return array();

        foreach($requestedProperties as $prop) switch($prop) {

            case '{DAV:}resourcetype' :
                $response[$prop] =  new Sabre_DAV_Property_ResourceType(array('{urn:ietf:params:xml:ns:caldav}schedule-inbox','{DAV:}collection'));
                break;            

        }
        return $response;

    }


    /**
     * Returns the last modification date as a unix timestamp.
     *
     * @return void
     */
    public function getLastModified() {

        return null;

    }



}

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
class GO_CalDAV_ScheduleOutbox extends Sabre_DAV_Node implements Sabre_DAV_ICollection, Sabre_DAV_IProperties {

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

        return 'outbox';

    }

   /*
     * Returns the list of properties
     *
     * @param array $properties
     * @return array
     */
    public function getProperties($requestedProperties) {

        $response = array();

        //if (!$this->hasPrivilege()) return array();

        foreach($requestedProperties as $prop) switch($prop) {

            case '{DAV:}resourcetype' :
                $response[$prop] =  new Sabre_DAV_Property_ResourceType(array('{urn:ietf:params:xml:ns:caldav}schedule-outbox','{DAV:}collection'));
                break;

        }
        return $response;

    }

	 /**
     * Updates this principals properties.
     *
     * Currently this is not supported
     *
     * @param array $properties
     * @see Sabre_DAV_IProperties::updateProperties
     * @return bool|array
     */
    public function updateProperties($properties) {

        return false;

    }


    /**
     * Returns the last modification date as a unix timestamp.
     *
     * @return void
     */
    public function getLastModified() {

        return null;

    }


	/**
     * Returns a single calendar, by name
     *
     * @param string $name
     * @todo needs optimizing
     * @return Sabre_CalDAV_Calendar
     */
    public function getChild($name) {

        foreach($this->getChildren() as $child) {
            if ($name==$child->getName())
                return $child;

        }
        throw new Sabre_DAV_Exception_FileNotFound('Calendar with name \'' . $name . '\' could not be found');

    }


	public function getChildren(){
		return array();
	}

	/**
     * Creates a new directory
     *
     * We actually block this, as subdirectories are not allowed in calendars.
     *
     * @param string $name
     * @return void
     */
    public function createDirectory($name) {

       throw new Sabre_DAV_Exception_Forbidden('Permission denied to access this special folder');

    }

    /**
     * Creates a new file
     *
     * The contents of the new file must be a valid ICalendar string.
     *
     * @param string $name
     * @param resource $calendarData
     * @return void
     */
    public function createFile($name,$calendarData = null) {

        throw new Sabre_DAV_Exception_Forbidden('Permission denied for this special folder');

    }



}

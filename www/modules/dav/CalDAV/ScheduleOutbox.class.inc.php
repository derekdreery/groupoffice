<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
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

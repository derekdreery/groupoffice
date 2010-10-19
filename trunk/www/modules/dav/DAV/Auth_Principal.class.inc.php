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
class GO_DAV_Auth_Principal extends Sabre_DAV_Auth_Principal implements Sabre_DAV_ICollection, Sabre_DAV_IProperties {

    /**
     * Full uri for this principal resource
     *
     * @var string
     */
    protected $principalUri;

    /**
     * Struct with principal information.
     *
     * @var array
     */
    protected $principalProperties;

    /**
     * Creates the principal object
     *
     * @param string $principalUri Full uri to the principal resource
     * @param array $principalProperties
     */
    public function __construct($principalUri,array $principalProperties = array()) {

        $this->principalUri = $principalUri;
        $this->principalProperties = $principalProperties;

    }

    /**
     * Returns the name of the element
     *
     * @return void
     */
    public function getName() {

        list(, $name) = Sabre_DAV_URLUtil::splitPath($this->principalUri);
        return $name;

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
        throw new Sabre_DAV_Exception_FileNotFound('Principal with name \'' . $name . '\' could not be found');

    }

	 /**
     * Checks is a child-node exists.
     *
     * It is generally a good idea to try and override this. Usually it can be optimized.
     *
     * @param string $name
     * @return bool
     */
    public function childExists($name) {

        try {

            $this->getChild($name);
            return true;

        } catch(Sabre_DAV_Exception_FileNotFound $e) {

            return false;

        }

    }


	public function getChildren(){
		return array(new GO_CalDAV_ScheduleInbox($this->principalUri), new GO_CalDAV_ScheduleOutbox($this->principalUri));
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

    /**
     * Returns the name of the user
     *
     * @return void
     */
    public function getDisplayName() {

        if (isset($this->principalProperties['{DAV:}displayname'])) {
            return $this->principalProperties['{DAV:}displayname'];
        } else {
            return $this->getName();
        }

    }

    /**
     * Returns a list of properties
     *
     * @param array $requestedProperties
     * @return void
     */
    public function getProperties($requestedProperties) {

        if (!count($requestedProperties)) {

            // If all properties were requested
            // we will only returns properties from this list
            $requestedProperties = array(
                '{DAV:}resourcetype',
                '{DAV:}displayname',
            );

        }

        // We need to always return the resourcetype
        // This is a bug in the core server, but it is easier to do it this way for now
        $newProperties = array(
            '{DAV:}resourcetype' => new Sabre_DAV_Property_ResourceType('{DAV:}principal')
        );
        foreach($requestedProperties as $propName) switch($propName) {

            case '{DAV:}alternate-URI-set' :
                if (isset($this->principalProperties['{http://sabredav.org/ns}email-address'])) {
                    $href = 'mailto:' . $this->principalProperties['{http://sabredav.org/ns}email-address'];
                    $newProperties[$propName] = new Sabre_DAV_Property_Href($href);
                } else {
                    $newProperties[$propName] = null;
                }
                break;
            case '{DAV:}group-member-set' :
            case '{DAV:}group-membership' :
                $newProperties[$propName] = null;
                break;

            case '{DAV:}principal-URL' :
                $newProperties[$propName] = new Sabre_DAV_Property_Href($this->principalUri);
                break;

            case '{DAV:}displayname' :
                $newProperties[$propName] = $this->getDisplayName();
                break;

            default :
                if (isset($this->principalProperties[$propName])) {
                    $newProperties[$propName] = $this->principalProperties[$propName];
                }
                break;

        }

        return $newProperties;


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

}

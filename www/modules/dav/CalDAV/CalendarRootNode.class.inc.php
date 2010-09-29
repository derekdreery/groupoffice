<?php

/**
 * Users collection
 *
 * This object is responsible for generating a collection of users.
 *
 * @package Sabre
 * @subpackage CalDAV
 * @copyright Copyright (C) 2007-2010 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/)
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class GO_CalDAV_CalendarRootNode extends Sabre_DAV_Directory {

    /**
     * Authentication Backend
     *
     * @var Sabre_DAV_Auth_Backend_Abstract
     */
    protected $authBackend;

    /**
     * CalDAV backend
     *
     * @var Sabre_CalDAV_Backend_Abstract
     */
    protected $caldavBackend;

    /**
     * Constructor
     *
     * This constructor needs both an authentication and a caldav backend.
     *
     * @param Sabre_DAV_Auth_Backend_Abstract $authBackend
     * @param Sabre_CalDAV_Backend_Abstract $caldavBackend
     */
    public function __construct(Sabre_DAV_Auth_Backend_Abstract $authBackend,Sabre_CalDAV_Backend_Abstract $caldavBackend) {

        $this->authBackend = $authBackend;
        $this->caldavBackend = $caldavBackend;

    }

    /**
     * Returns the name of the node
     *
     * @return string
     */
    public function getName() {

        return Sabre_CalDAV_Plugin::CALENDAR_ROOT;

    }

	/**
     * Returns a child object, by its name.
     *
     * This method makes use of the getChildren method to grab all the child nodes, and compares the name.
     * Generally its wise to override this, as this can usually be optimized
     *
     * @param string $name
     * @throws Sabre_DAV_Exception_FileNotFound
     * @return Sabre_DAV_INode
     */
    public function getChild($name) {

		go_debug("crn:getChild($name)");

		$user = $this->authBackend->getUser($name);

		if($user)
			return new GO_CalDAV_UserCalendars($this->authBackend, $this->caldavBackend, $user['uri']);
        
        throw new Sabre_DAV_Exception_FileNotFound('File not found: ' . $name);

    }

    /**
     * Returns the list of users as Sabre_CalDAV_User objects.
     *
     * @return array
     */
    public function getChildren() {
		
		go_debug('crn:getChildren()');
        $users = $this->authBackend->getUsers();
        $children = array();
        foreach($users as $user) {

            $children[] = new GO_CalDAV_UserCalendars($this->authBackend, $this->caldavBackend, $user['uri']);

        }
        return $children;

    }

}

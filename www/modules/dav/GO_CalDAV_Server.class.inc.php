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

class GO_CalDAV_Server extends Sabre_DAV_Server {

    /**
     * Sets up the object. A PDO object must be passed to setup all the backends.
     *
     * @param PDO $pdo
     */
    public function __construct() {

        /* Backends */
        $authBackend = new GO_DAV_Auth_Backend();
        $calendarBackend = new GO_CalDAV_Calendars_Backend();
		$tasklistsBackend = new GO_CalDAV_Tasklists_Backend();

        /* Directory structure */
        $root = new Sabre_DAV_SimpleDirectory('root');
		//$calendars = new Sabre_CalDAV_UserCalendars($authBackend, $calendarBackend, $_SESSION['GO_SESSION']['username']);
		//$root->addChild($calendars);
		
        $principals = new Sabre_DAV_Auth_PrincipalCollection($authBackend);
        $root->addChild($principals);

        $calendars = new Sabre_CalDAV_CalendarRootNode($authBackend, $calendarBackend);
		$root->addChild($calendars);

		$tasklists = new GO_CalDAV_TasklistsRootNode($authBackend, $tasklistsBackend);
		$root->addChild($tasklists);
        

        $objectTree = new Sabre_DAV_ObjectTree($root);

		

        /* Initializing server */
        parent::__construct($objectTree);


        /* Server Plugins */
        $authPlugin = new Sabre_DAV_Auth_Plugin($authBackend,'Group-Office');
        $this->addPlugin($authPlugin);

        $caldavPlugin = new Sabre_CalDAV_Plugin();
        $this->addPlugin($caldavPlugin);

    }

}

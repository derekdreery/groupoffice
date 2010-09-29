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
class GO_CalDAV_TasklistsRootNode extends Sabre_CalDAV_CalendarRootNode {
    
    /**
     * Returns the name of the node
     *
     * @return string
     */
    public function getName() {
        return 'tasklists';
    }
}

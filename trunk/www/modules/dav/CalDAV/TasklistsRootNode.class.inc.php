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
class GO_CalDAV_TasklistsRootNode extends GO_CalDAV_CalendarRootNode {
    
    /**
     * Returns the name of the node
     *
     * @return string
     */
    public function getName() {
        return 'tasklists';
    }
}

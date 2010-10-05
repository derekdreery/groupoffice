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
class GO_CalDAV_UserCalendars extends Sabre_CalDAV_UserCalendars{
     /**
     * Returns a single calendar, by name
     *
     * @param string $name
     * @todo needs optimizing
     * @return Sabre_CalDAV_Calendar
     */
    public function getChild($name) {

		$calendar = $this->caldavBackend->getCalendar($this->userUri, $name);

		return new Sabre_CalDAV_Calendar($this->authBackend, $this->caldavBackend, $calendar);
    }
}
?>

<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserCalendars
 *
 * @author mschering
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

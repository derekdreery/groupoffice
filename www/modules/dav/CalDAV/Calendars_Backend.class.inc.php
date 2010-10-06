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
class GO_CalDAV_Calendars_Backend extends Sabre_CalDAV_Backend_Abstract {
	/**
	 * Creates the backend
	 *
	 * @param PDO $pdo
	 */
	public function __construct() {

		$this->cal = new calendar();

		$this->exporter = new go_ical('2.0', true);
		$this->exporter->dont_use_quoted_printable = true;


		$this->importer = new ical2array();
	}

	private function get_user_id($principalUri) {
		global $GO_USERS;

		if (!isset($_SESSION['GO_SESSION']['dav']['principaluri_map']))
			$_SESSION['GO_SESSION']['dav']['principaluri_map'] = array();

		if (!isset($_SESSION['GO_SESSION']['dav']['principaluri_map'][$principalUri])) {
			$user = $GO_USERS->get_user_by_username($principalUri);
			$_SESSION['GO_SESSION']['dav']['principaluri_map'][$principalUri] = $user['id'];
		}

		return $_SESSION['GO_SESSION']['dav']['principaluri_map'][$principalUri];
	}

	/**
	 * Returns a list of calendars for a principal
	 *
	 * @param string $userUri
	 * @return array
	 */
	public function getCalendarsForUser($principalUri) {

		go_debug("c:getCalendarsForUser($principalUri)");

		$this->cal->get_writable_calendars($this->get_user_id($principalUri));		

		$calendars = array();
		while ($gocal = $this->cal->next_record()) {
			$calendar = $this->recordToDAVCalendar($gocal, $principalUri);
			$calendars[] = $calendar;
		}

		return $calendars;
	}

	public function getCalendar($principalUri, $calendarUri){
		go_debug("c:getCalendar($principalUri, $calendarUri)");

		//$calendarUri = rawurldecode($calendarUri);

		$calendar=false;

		preg_match('/-([0-9]+)$/', $calendarUri, $matches);


		if($matches[1]){
			$calendar = $this->cal->get_calendar($matches[1]);
		}
		
		
		if(!$calendar)
			throw new Sabre_DAV_Exception_FileNotFound('File not found: ' . $calendarUri);
		
		global $GO_SECURITY;
		if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_id'])<GO_SECURITY::WRITE_PERMISSION)
				throw new Sabre_DAV_Exception_Forbidden ('Access denied for '.$calendarUri);

		return $this->recordToDAVCalendar($calendar, $principalUri);
	}

	private function recordToDAVCalendar($gocal, $principalUri){

		$db = new db();
		$db->query("SELECT max(mtime) AS mtime, COUNT(*) AS count FROM cal_events WHERE calendar_id=?", 'i', $gocal['id']);
		$r = $db->next_record();

		return array(
					'id' => $gocal['id'],
					'uri' => preg_replace('/[^\w-]*/', '', (strtolower(str_replace(' ', '-', $gocal['name'])))).'-'.$gocal['id'],
					//'uri' => $gocal['name'],
					'principaluri' => 'principals/'.$principalUri,
					'size'=> $r['count'],
					'mtime'=>$r['mtime'],
					'{' . Sabre_CalDAV_Plugin::NS_CALENDARSERVER . '}getctag' => $r['count'] . ':' . $r['mtime'],
					'{' . Sabre_CalDAV_Plugin::NS_CALDAV . '}supported-calendar-component-set' => new Sabre_CalDAV_Property_SupportedCalendarComponentSet(array('VEVENT')),
					'{DAV:}displayname' => $gocal['name'],
					'{urn:ietf:params:xml:ns:caldav}calendar-description' => 'User calendar',
					'{urn:ietf:params:xml:ns:caldav}calendar-timezone' => date_default_timezone_get(),
					'{http://apple.com/ns/ical/}calendar-order' => '0',
					'{http://apple.com/ns/ical/}calendar-color' => ''
			);
	}

	/**
	 * Creates a new calendar for a principal.
	 *
	 * If the creation was a success, an id must be returned that can be used to reference
	 * this calendar in other methods, such as updateCalendar
	 *
	 * @param string $principalUri
	 * @param string $calendarUri
	 * @param array $properties
	 * @return mixed
	 */
	public function createCalendar($principalUri, $calendarUri, array $properties) {

		throw new Sabre_DAV_Exception_Forbidden();
	}

	/**
	 * Updates a calendars properties
	 *
	 * The properties array uses the propertyName in clark-notation as key,
	 * and the array value for the property value. In the case a property
	 * should be deleted, the property value will be null.
	 *
	 * This method must be atomic. If one property cannot be changed, the
	 * entire operation must fail.
	 *
	 * If the operation was successful, true can be returned.
	 * If the operation failed, false can be returned.
	 *
	 * Deletion of a non-existant property is always succesful.
	 *
	 * Lastly, it is optional to return detailed information about any
	 * failures. In this case an array should be returned with the following
	 * structure:
	 *
	 * array(
	 *   403 => array(
	 *      '{DAV:}displayname' => null,
	 *   ),
	 *   424 => array(
	 *      '{DAV:}owner' => null,
	 *   )
	 * )
	 *
	 * In this example it was forbidden to update {DAV:}displayname.
	 * (403 Forbidden), which in turn also caused {DAV:}owner to fail
	 * (424 Failed Dependency) because the request needs to be atomic.
	 *
	 * @param string $calendarId
	 * @param array $properties
	 * @return bool|array
	 */
	public function updateCalendar($calendarId, array $properties) {

		throw new Sabre_DAV_Exception_Forbidden();
	}

	/**
	 * Delete a calendar and all it's objects
	 *
	 * @param string $calendarId
	 * @return void
	 */
	public function deleteCalendar($calendarId) {

		throw new Sabre_DAV_Exception_Forbidden();
	}

	/**
	 * Returns all calendar objects within a calendar object.
	 *
	 * @param string $calendarId
	 * @return array
	 */
	public function getCalendarObjects($calendarId) {

		go_debug("c:getCalendarObjects($calendarId)");

		$objects = array();
		$this->cal->get_events(array($calendarId),0, Date::date_add(time(), 0, -1));
		while ($event = $this->cal->next_record()) {

			if (empty($event['uuid'])) {
				$db = new db();
				$event['uuid'] = $ue['uuid'] = UUID::create('event', $event['id']);
				$ue['id'] = $event['id'];
				$db->update_row('cal_events', 'id', $ue);
			}

			$objects[] = array(
					'id' => $event['id'],
					'uri' => $event['uuid'],
					'calendardata' => $this->exporter->export_event($event['id']),
					'calendarid' => $calendarId,
					'lastmodified' => date('Ymd H:i:s', $event['mtime'])
			);
		}

		return $objects;
	}

	/**
	 * Get's an array with free busy info for a given time period.
	 *
	 * @param String $email
	 * @param int $start Unix time stamp of the time period
	 * @param int $end Unix time stamp of the time period
	 * @return Array Free busy information
	 */

	public function getFreeBusy($email, $start, $end){
		global $GO_USERS;

		$user = $GO_USERS->get_user_by_email($email);

		if(!$user)
			return false;

		$events = $this->cal->get_events_in_array(0, $user['id'], $start, $end,true);
		
		$fb=array();

		while ($event = array_shift($events)) {
			$fb[]=array('start'=>$event['start_time'],'end'=>$event['end_time'], 'busyType'=>'BUSY');
		}
		return $fb;
	}

	/**
	 * Returns information from a single calendar object, based on it's object uri.
	 *
	 * @param string $calendarId
	 * @param string $objectUri
	 * @return array
	 */
	public function getCalendarObject($calendarId, $objectUri) {

		go_debug("c:getCalendarObject($calendarId,$objectUri)");

		/*
		 * When a client adds or updates an event, the server must return the
		 * data identical to what the client sent. That's why we store the
		 * client data in a separate table and if the mtime's match we use that.
		 */

		$sql = "SELECT e.uuid,e.id,e.mtime, d.mtime AS client_mtime, d.data  FROM cal_events e LEFT JOIN dav_events d ON d.id=e.id WHERE e.uuid=?";
		$this->cal->query($sql, 's', $objectUri);
		$event = $this->cal->next_record();

		$data = ($event['mtime']==$event['client_mtime']) ? $event['data'] : $this->exporter->export_event($event['id']);

		//$event = $this->cal->get_event_by_uuid($objectUri);
		if ($event) {
			$object = array(
					'id' => $event['id'],
					'uri' => $event['uuid'],
					'calendardata' => $data,
					'calendarid' => $calendarId,
					'lastmodified' => date('Ymd H:i:s', $event['mtime'])
			);
			go_debug($object);
			return $object;
		}
		return false;
	}

	/**
	 * Creates a new calendar object.
	 *
	 * @param string $calendarId
	 * @param string $objectUri
	 * @param string $calendarData
	 * @return void
	 */
	public function createCalendarObject($calendarId, $objectUri, $calendarData) {

		go_debug("createCalendarObject($calendarId,$objectUri,$calendarData)");

		$event = $this->cal->get_event_from_ical_string($calendarData);
		if (!$event)
			return false;

		$event['uuid']=$objectUri;
		$event['calendar_id'] = $calendarId;
	
		$event['mtime']=$dav_event['mtime']=time();
		
		//store calendar data because we need to reply with the exact client
		//data
		$dav_event['id']=$this->cal->add_event($event);
		$dav_event['data']=$calendarData;
		$this->cal->insert_row('dav_events', $dav_event);
	}



	/**
	 * Updates an existing calendarobject, based on it's uri.
	 *
	 * @param string $calendarId
	 * @param string $objectUri
	 * @param string $calendarData
	 * @return void
	 */
	public function updateCalendarObject($calendarId, $objectUri, $calendarData) {

		go_debug("updateCalendarObject($calendarId,$objectUri,$calendarData)");

		$event = $this->cal->get_event_from_ical_string($calendarData);
		if (!$event)
			return false;

		$goevent = $this->cal->get_event_by_uuid($objectUri);

		$event['id'] = $dav_event['id']= $goevent['id'];
		$event['calendar_id'] = $calendarId;

		$event['mtime']=$dav_event['mtime']=time();
		$dav_event['data']=$calendarData;

		$this->cal->update_row('dav_events', 'id', $dav_event);

		$this->cal->update_event($event);
	}

	/**
	 * Deletes an existing calendar object.
	 *
	 * @param string $calendarId
	 * @param string $objectUri
	 * @return void
	 */
	public function deleteCalendarObject($calendarId, $objectUri) {
		go_debug("deleteCalendarObject($calendarId,$objectUri)");

		$goevent = $this->cal->get_event_by_uuid($objectUri);
		$this->cal->delete_event($goevent['id']);
	}

}

?>

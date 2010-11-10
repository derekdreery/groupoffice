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


		$this->tasks = new tasks();

		$this->tasks_exporter = new export_tasks('2.0', false);
		$this->tasks_exporter->dont_use_quoted_printable = true;


		$this->importer = new ical2array();
	}

	private function get_user_id($principalUri) {

		global $GO_CONFIG;

		$principalUri = str_replace('principals/', '', $principalUri);

		require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();

		$user = $GO_USERS->get_user_by_username($principalUri);
		return $user['id'];
	}

	/**
	 * Returns a list of calendars for a principal
	 *
	 * @param string $userUri
	 * @return array
	 */
	public function getCalendarsForUser($principalUri) {

		go_debug("c:getCalendarsForUser($principalUri)");

		$user_id = $this->get_user_id($principalUri);

		$this->cal->get_writable_calendars($user_id);

		$calendars = array();
		while ($gocal = $this->cal->next_record()) {
			$calendar = $this->recordToDAVCalendar($gocal, $principalUri);
			$calendars[] = $calendar;
		}


		/*$this->tasks->get_authorized_tasklists('read','', $user_id);

		while ($gocal = $this->tasks->next_record()) {
			$tasklist = $this->tasklistRecordToDAVCalendar($gocal, $principalUri);
			$calendars[] = $tasklist;
		}*/

		return $calendars;
	}

	public function getCalendar($principalUri, $calendarUri){
		go_debug("c:getCalendar($principalUri, $calendarUri)");

		//$calendarUri = rawurldecode($calendarUri);

		$calendar=false;

		preg_match('/-([0-9]+)$/', $calendarUri, $matches);

		$id=$matches[1];

		//if(substr($calendarUri,0,8)=='calendar'){
			$calendar = $this->cal->get_calendar($id);
				
		
			if(!$calendar)
				throw new Sabre_DAV_Exception_FileNotFound('File not found: ' . $calendarUri);

			global $GO_SECURITY;
			if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $calendar['acl_id'])<GO_SECURITY::WRITE_PERMISSION)
					throw new Sabre_DAV_Exception_Forbidden ('Access denied for '.$calendarUri);

			return $this->recordToDAVCalendar($calendar, $principalUri);
		/*}else
		{
			$tasklist = $this->tasks->get_tasklist($id);
			if(!$tasklist)
				throw new Sabre_DAV_Exception_FileNotFound('File not found: ' . $calendarUri);

			global $GO_SECURITY;
			if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $tasklist['acl_id'])<GO_SECURITY::WRITE_PERMISSION)
					throw new Sabre_DAV_Exception_Forbidden ('Access denied for '.$calendarUri);

			return $this->tasklistRecordToDAVCalendar($tasklist, $principalUri);
		}*/
	}

	private function getTimezone(){
		if(!isset($this->timezone)){
			$this->timezone=$this->exporter->export_timezone();
		}
		return $this->timezone;
	}

	private function recordToDAVCalendar($gocal, $principalUri){

		$db = new db();
		$db->query("SELECT max(mtime) AS mtime, COUNT(*) AS count FROM cal_events WHERE calendar_id=?", 'i', $gocal['id']);
		$r = $db->next_record();

		$supportedComponents = array('VEVENT');

		if($gocal['tasklist_id']>0)
			$supportedComponents[]='VTODO';

		return array(
					'id' => $gocal['id'],
					'uri' => preg_replace('/[^\w-]*/', '', (strtolower(str_replace(' ', '-', $gocal['name'])))).'-'.$gocal['id'],
					//'uri' => $gocal['name'],
					'principaluri' => $principalUri,
					'size'=> $r['count'],
					'mtime'=>$r['mtime'],
					'{' . Sabre_CalDAV_Plugin::NS_CALENDARSERVER . '}getctag' => $r['count'] . ':' . $r['mtime'],
					'{' . Sabre_CalDAV_Plugin::NS_CALDAV . '}supported-calendar-component-set' => new Sabre_CalDAV_Property_SupportedCalendarComponentSet($supportedComponents),
					'{DAV:}displayname' => $gocal['name'],
					'{urn:ietf:params:xml:ns:caldav}calendar-description' => 'User calendar',
					'{urn:ietf:params:xml:ns:caldav}calendar-timezone' => $this->getTimezone(),
					'{http://apple.com/ns/ical/}calendar-order' => $gocal['id'],
					'{http://apple.com/ns/ical/}calendar-color' => '#2952A3'
			);
	}

	/*private function tasklistRecordToDAVCalendar($gocal, $principalUri){
		$db = new db();
		$db->query("SELECT max(mtime) AS mtime, COUNT(*) AS count FROM ta_tasks WHERE tasklist_id=?", 'i', $gocal['id']);
		$r = $db->next_record();

		//$components = explode(',',$row['components']);

		$tasklist = array(
				'id' => 't:'.$gocal['id'],
				'uri' =>
				'principaluri' => $principalUri,
				'size'=> $r['count'],
				'mtime'=>$r['mtime'],
				'{' . Sabre_CalDAV_Plugin::NS_CALENDARSERVER . '}getctag' => $r['count'] . ':' . $r['mtime'],
				'{' . Sabre_CalDAV_Plugin::NS_CALDAV . '}supported-calendar-component-set' => new Sabre_CalDAV_Property_SupportedCalendarComponentSet(array('VTODO')),
				'{DAV:}displayname' => '[T] '.$gocal['name'],
				'{urn:ietf:params:xml:ns:caldav}calendar-description' => 'User Tasklist',
				'{urn:ietf:params:xml:ns:caldav}calendar-timezone' => $this->getTimezone(),
				'{http://apple.com/ns/ical/}calendar-order' => $gocal['id']+100,
				'{http://apple.com/ns/ical/}calendar-color' => '#2952A3'
		);

		return $tasklist;
	}*/

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


		$calendar = $this->cal->get_calendar($calendarId);

		$this->cal->get_events(array($calendarId),0, Date::date_add(time(), 0, -1));
		while ($event = $this->cal->next_record()) {

			if (empty($event['uuid'])) {

				if(!isset($db))
					$db = new db();

				$event['uuid'] = $ue['uuid'] = UUID::create('event', $event['id']);
				$ue['id'] = $event['id'];
				$db->update_row('cal_events', 'id', $ue);
			}


			$objects[] = array(
					'id' => $event['id'],
					'uri' => $event['uuid'],
					'calendardata' => $this->exporter->export_event($event['id']),
					'calendarid' => 'c:'.$calendarId,
					'lastmodified' => date('Ymd H:i:s', $event['mtime'])
			);
		}

		if($calendar['tasklist_id']>0)
		{
			$sql = "SELECT * FROM ta_tasks WHERE tasklist_id=? AND (completion_time=0 OR due_time>?)";
			$this->tasks->query($sql, 'ii', array($calendar['tasklist_id'], Date::date_add(time(),0,-1)));
			while ($task = $this->tasks->next_record()) {

				if (empty($task['uuid'])) {

					if(!isset($db))
						$db = new db();

					$task['uuid'] = $ue['uuid'] = UUID::create('task', $task['id']);
					$ue['id'] = $task['id'];
					$db->update_row('ta_tasks', 'id', $ue);
				}

				$objects[] = array(
						'id' => $task['id'],
						'uri' => $task['uuid'],
						'calendardata' => $this->tasks_exporter->export_task($task['id']),
						'calendarid' => 't:'.$calendarId,
						'lastmodified' => date('Ymd H:i:s', $task['mtime'])
				);
			}
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

		global $GO_CONFIG;

		require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
		$GO_USERS = new GO_USERS();

		go_debug('getting freebusy info for: '.$email);

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

		$objectUri = File::strip_extension($objectUri);

		/*
		 * When a client adds or updates an event, the server must return the
		 * data identical to what the client sent. That's why we store the
		 * client data in a separate table and if the mtime's match we use that.
		 */



		$sql = "SELECT e.uuid,e.id,e.mtime, d.mtime AS client_mtime, d.data  FROM cal_events e LEFT JOIN dav_events d ON d.id=e.id WHERE e.uuid=?";
		$this->cal->query($sql, 's', $objectUri);
		$event = $this->cal->next_record();

		go_debug($event);

		$data = ($event['mtime']==$event['client_mtime'] && !empty($event['data'])) ? $event['data'] : $this->exporter->export_event($event['id']);

		//$event = $this->cal->get_event_by_uuid($objectUri);
		if ($event) {

			go_debug('Found event');

			$object = array(
					'id' => $event['id'],
					'uri' => $event['uuid'],
					'calendardata' => $data,
					'calendarid' => $calendarId,
					'lastmodified' => date('Ymd H:i:s', $event['mtime'])
			);
			//go_debug($object);
			return $object;
		} else {

			$sql = "SELECT e.*, d.mtime AS client_mtime, d.data  FROM ta_tasks e LEFT JOIN dav_tasks d ON d.id=e.id WHERE e.uuid=?";
			$this->tasks->query($sql, 's', $objectUri);
			$task = $this->tasks->next_record();

			go_debug($task);

			$data = ($task['mtime']==$task['client_mtime'] && !empty($task['data'])) ? $task['data'] : $this->tasks_exporter->export_task($task);

			//$task = $this->tasks->get_task_by_uuid($objectUri);
			if ($task) {
				go_debug('Found task');
				$object = array(
						'id' => $task['id'],
						'uri' => $task['uuid'],
						'calendardata' => $data,
						'calendarid' => $calendarId,
						'lastmodified' => date('Ymd H:i:s', $task['mtime'])
				);
				//go_debug($object);
				return $object;
			}
		}
		throw new Sabre_DAV_Exception_FileNotFound('File not found');
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

		$objectUri = File::strip_extension($objectUri);

		go_debug("createCalendarObject($calendarId,$objectUri,$calendarData)");

		if(strpos($calendarData, 'VEVENT')!==false){

			go_debug('item is an event');

			$event = $this->cal->get_event_from_ical_string($calendarData);

			go_debug($event);
			
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
		}else
		{
			$calendar = $this->cal->get_calendar($calendarId);
			go_debug('item is a task');
			$task = $this->tasks->get_task_from_ical_string($calendarData);

			go_debug($task);
			
			if (!$task)
				throw new Sabre_CalDAV_Exception_InvalidICalendarObject ();

			$task['uuid']=$objectUri;
			$task['tasklist_id'] = $calendar['tasklist_id'];

			$task['mtime']=$dav_task['mtime']=time();

			//store Tasklist data because we need to reply with the exact client
			//data
			$dav_task['id']=$this->tasks->add_task($task);
			$dav_task['data']=$calendarData;
			$this->tasks->insert_row('dav_tasks', $dav_task);
		}
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

		$objectUri = File::strip_extension($objectUri);


		if(strpos($calendarData,'VEVENT')!==false){

			go_debug('item is an event');

			$event = $this->cal->get_event_from_ical_string($calendarData);

			go_debug($event);
			
			if (!$event)
				return false;

			$goevent = $this->cal->get_event_by_uuid($objectUri);

			$event['id'] = $dav_event['id']= $goevent['id'];
			$event['calendar_id'] = $calendarId;

			$event['mtime']=$dav_event['mtime']=time();
			$dav_event['data']=$calendarData;

			$this->cal->update_row('dav_events', 'id', $dav_event);

			$this->cal->update_event($event);
		}else
		{
			go_debug('item is a task');

			$task = $this->tasks->get_task_from_ical_string($calendarData);

			go_debug($task);

			if (!$task)
				return false;

			$gotask = $this->tasks->get_task_by_uuid($objectUri);

			$task['id'] = $dav_task['id']= $gotask['id'];
			//$task['tasklist_id'] = $id;

			$task['mtime']=$dav_task['mtime']=time();
			$dav_task['data']=$calendarData;

			$this->tasks->update_row('dav_tasks', 'id', $dav_task);
			$this->tasks->update_task($task);
		}
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

		$objectUri = File::strip_extension($objectUri);

		$goevent = $this->cal->get_event_by_uuid($objectUri);
		if($goevent){
			$this->cal->delete_event($goevent['id']);
		}else{
			$gotask = $this->tasks->get_task_by_uuid($objectUri);
			$this->tasks->delete_task($gotask['id']);
		}
	}
}
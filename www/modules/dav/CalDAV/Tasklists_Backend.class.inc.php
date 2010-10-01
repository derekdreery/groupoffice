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
class GO_CalDAV_Tasklists_Backend extends Sabre_CalDAV_Backend_Abstract {
	/**
	 * Creates the backend
	 *
	 * @param PDO $pdo
	 */
	public function __construct() {

		$this->tasks = new tasks();

		$this->exporter = new export_tasks('2.0', true);
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
	 * Returns a list of Tasklists for a principal
	 *
	 * @param string $userUri
	 * @return array
	 */
	public function getCalendarsForUser($principalUri) {

		go_debug("t:getCalendarsForUser($principalUri)");

		$this->tasks->get_authorized_tasklists('read','', $this->get_user_id($principalUri));
		$db = new db();

		$tasklists = array();
		while ($gocal = $this->tasks->next_record()) {

			$db->query("SELECT max(mtime) AS mtime, COUNT(*) AS count FROM ta_tasks WHERE tasklist_id=?", 'i', $gocal['id']);
			$r = $db->next_record();

			//$components = explode(',',$row['components']);

			$tasklist = array(
					'id' => $gocal['id'],
					'uri' => preg_replace('/[^\w]*/', '', (strtolower(str_replace(' ', '-', $gocal['name'])))),
					'principaluri' => $principalUri,
					'size'=> $r['count'],
					'mtime'=>$r['mtime'],
					'{' . Sabre_CalDAV_Plugin::NS_CALENDARSERVER . '}getctag' => $r['count'] . ':' . $r['mtime'],
					'{' . Sabre_CalDAV_Plugin::NS_CALDAV . '}supported-calendar-component-set' => new Sabre_CalDAV_Property_SupportedCalendarComponentSet(array('VTODO')),
					'{DAV:}displayname' => $gocal['name'],
					'{urn:ietf:params:xml:ns:caldav}calendar-description' => 'User Tasklist',
					'{urn:ietf:params:xml:ns:caldav}calendar-timezone' => date_default_timezone_get(),
					'{http://apple.com/ns/ical/}calendar-order' => '0',
					'{http://apple.com/ns/ical/}calendar-color' => ''
			);

			$tasklists[] = $tasklist;
		}

		return $tasklists;
	}

	/**
	 * Creates a new Tasklist for a principal.
	 *
	 * If the creation was a success, an id must be returned that can be used to reference
	 * this Tasklist in other methods, such as updateTasklist
	 *
	 * @param string $principalUri
	 * @param string $tasklistUri
	 * @param array $properties
	 * @return mixed
	 */
	public function createCalendar($principalUri, $tasklistUri, array $properties) {

		throw new Sabre_DAV_Exception_Forbidden();
	}

	/**
	 * Updates a Tasklists properties
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
	 * @param string $tasklistId
	 * @param array $properties
	 * @return bool|array
	 */
	public function updateCalendar($tasklistId, array $properties) {

		throw new Sabre_DAV_Exception_Forbidden();
	}

	/**
	 * Delete a Tasklist and all it's objects
	 *
	 * @param string $tasklistId
	 * @return void
	 */
	public function deleteCalendar($tasklistId) {

		throw new Sabre_DAV_Exception_Forbidden();
	}

	/**
	 * Returns all Tasklist objects within a Tasklist object.
	 *
	 * @param string $tasklistId
	 * @return array
	 */
	public function getCalendarObjects($tasklistId) {

		go_debug("getTasklistObjects($tasklistId)");

		$objects = array();
		$lists=array($tasklistId);
		$this->tasks->get_tasks($lists,
			0,
			true,
			'due_time',
			'ASC',
			0,
			0,
			true,
			'',
			'',
            array(),
			'',
			''
				);
		while ($task = $this->tasks->next_record()) {

			if (empty($task['uuid'])) {
				$db = new db();
				$task['uuid'] = $ue['uuid'] = UUID::create('task', $task['id']);
				$ue['id'] = $task['id'];
				$db->update_row('ta_tasks', 'id', $ue);
			}

			$objects[] = array(
					'id' => $task['id'],
					'uri' => $task['uuid'],
					'calendardata' => $this->exporter->export_task($task['id']),
					'calendarid' => $tasklistId,
					'lastmodified' => date('Ymd H:i:s', $task['mtime'])
			);
		}

		go_debug($objects);

		return $objects;
	}

	/**
	 * Returns information from a single Tasklist object, based on it's object uri.
	 *
	 * @param string $tasklistId
	 * @param string $objectUri
	 * @return array
	 */
	public function getCalendarObject($tasklistId, $objectUri) {

		go_debug("getCalendarObject($tasklistId,$objectUri)");

		/*
		 * When a client adds or updates an task, the server must return the
		 * data identical to what the client sent. That's why we store the
		 * client data in a separate table and if the mtime's match we use that.
		 */

		$sql = "SELECT e.*, d.mtime AS client_mtime, d.data  FROM ta_tasks e LEFT JOIN dav_tasks d ON d.id=e.id WHERE e.uuid=?";
		$this->tasks->query($sql, 's', $objectUri);
		$task = $this->tasks->next_record();

		go_debug($task);

		$data = ($task['mtime']==$task['client_mtime']) ? $task['data'] : $this->exporter->export_task($task);

		//$task = $this->tasks->get_task_by_uuid($objectUri);
		if ($task) {
			$object = array(
					'id' => $task['id'],
					'uri' => $task['uuid'],
					'calendardata' => $data,
					'calendarid' => $tasklistId,
					'lastmodified' => date('Ymd H:i:s', $task['mtime'])
			);
			go_debug($object);
			return $object;
		}
		return false;
	}

	/**
	 * Creates a new Tasklist object.
	 *
	 * @param string $tasklistId
	 * @param string $objectUri
	 * @param string $tasklistData
	 * @return void
	 */
	public function createCalendarObject($tasklistId, $objectUri, $tasklistData) {

		go_debug("createCalendarObject($tasklistId,$objectUri,$tasklistData)");

		$task = $this->tasks->get_task_from_ical_string($tasklistData);
		if (!$task)
			throw new Sabre_CalDAV_Exception_InvalidICalendarObject ();

		$task['uuid']=$objectUri;
		$task['tasklist_id'] = $tasklistId;
	
		$task['mtime']=$dav_task['mtime']=time();

		go_debug($task);
		
		//store Tasklist data because we need to reply with the exact client
		//data
		$dav_task['id']=$this->tasks->add_task($task);
		$dav_task['data']=$tasklistData;
		$this->tasks->insert_row('dav_tasks', $dav_task);
	}



	/**
	 * Updates an existing Tasklistobject, based on it's uri.
	 *
	 * @param string $tasklistId
	 * @param string $objectUri
	 * @param string $tasklistData
	 * @return void
	 */
	public function updateCalendarObject($tasklistId, $objectUri, $tasklistData) {

		go_debug("updateTasklistObject($tasklistId,$objectUri,$tasklistData)");

		$task = $this->tasks->get_task_from_ical_string($tasklistData);
		if (!$task)
			return false;

		$gotask = $this->tasks->get_task_by_uuid($objectUri);

		$task['id'] = $dav_task['id']= $gotask['id'];
		$task['Tasklist_id'] = $tasklistId;

		$task['mtime']=$dav_task['mtime']=time();
		$dav_task['data']=$tasklistData;

		$this->tasks->update_row('dav_tasks', 'id', $dav_task);
		$this->tasks->update_task($task);
	}

	/**
	 * Deletes an existing Tasklist object.
	 *
	 * @param string $tasklistId
	 * @param string $objectUri
	 * @return void
	 */
	public function deleteCalendarObject($tasklistId, $objectUri) {
		go_debug("deleteTasklistObject($tasklistId,$objectUri)");

		$gotask = $this->tasks->get_task_by_uuid($objectUri);
		$this->tasks->delete_task($gotask['id']);
	}

}

?>

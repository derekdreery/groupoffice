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

class GO_CalDAV_Backend extends Sabre_CalDAV_Backend_Abstract {
	/**
     * pdo
     *
     * @var PDO
     */
    private $pdo;

    /**
     * List of CalDAV properties, and how they map to database fieldnames
     *
     * Add your own properties by simply adding on to this array
     *
     * @var array
     */
    public $propertyMap = array(
        '{DAV:}displayname'                          => 'name',
        '{urn:ietf:params:xml:ns:caldav}calendar-description' => 'name',
        '{urn:ietf:params:xml:ns:caldav}calendar-timezone'    => 'timezone',
        '{http://apple.com/ns/ical/}calendar-order'  => 'calendarorder',
        '{http://apple.com/ns/ical/}calendar-color'  => 'calendarcolor',
    );

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

	private function get_user_id($principalUri){
		global $GO_USERS;

		if(!isset($_SESSION['GO_SESSION']['dav']['principaluri_map']))
			$_SESSION['GO_SESSION']['dav']['principaluri_map']=array();
		
		if(!isset($_SESSION['GO_SESSION']['dav']['principaluri_map'][$principalUri])){
			$user = $GO_USERS->get_user_by_username($principalUri);
			$_SESSION['GO_SESSION']['dav']['principaluri_map'][$principalUri]=$user['id'];
		}

		return $_SESSION['GO_SESSION']['dav']['principaluri_map'][$principalUri];

	}

	private function get_calendar_id($calendarUri){
		global $GO_USERS;

		if(!isset($_SESSION['GO_SESSION']['dav']['principaluri_map']))
			$_SESSION['GO_SESSION']['dav']['principaluri_map']=array();

		if(!isset($_SESSION['GO_SESSION']['dav']['principaluri_map'][$principalUri])){
			$user = $GO_USERS->get_user_by_username($principalUri);
			$_SESSION['GO_SESSION']['dav']['principaluri_map'][$principalUri]=$user['id'];
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


		$this->cal->get_writable_calendars($this->get_user_id($principalUri));

		$db = new db();


        $calendars = array();
        while($gocal = $this->cal->next_record()) {

			$db->query("SELECT max(mtime) AS mtime, COUNT(*) AS count FROM cal_events WHERE calendar_id=?",'i', $gocal['id']);
			$r=$db->next_record();

            //$components = explode(',',$row['components']);

            $calendar = array(
                'id' => $gocal['id'],
                'uri' => preg_replace('/[^\w]*/','',(strtolower(str_replace(' ','-',$gocal['name'])))),
                'principaluri' => $principalUri,
                '{' . Sabre_CalDAV_Plugin::NS_CALENDARSERVER . '}getctag' => $r['count'].':'.$r['mtime'],
                '{' . Sabre_CalDAV_Plugin::NS_CALDAV . '}supported-calendar-component-set' => new Sabre_CalDAV_Property_SupportedCalendarComponentSet(array('VEVENT')),
				'{DAV:}displayname'                          => $gocal['name'],
				'{urn:ietf:params:xml:ns:caldav}calendar-description' => 'User calendar',
				'{urn:ietf:params:xml:ns:caldav}calendar-timezone'    => date_default_timezone_get(),
				'{http://apple.com/ns/ical/}calendar-order'  => '0',
				'{http://apple.com/ns/ical/}calendar-color'  => ''
            );

            $calendars[] = $calendar;

        }

        return $calendars;

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
    public function createCalendar($principalUri,$calendarUri, array $properties) {

        return false;

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

         return false;

    }

    /**
     * Delete a calendar and all it's objects
     *
     * @param string $calendarId
     * @return void
     */
    public function deleteCalendar($calendarId) {

        return false;
    }

    /**
     * Returns all calendar objects within a calendar object.
     *
     * @param string $calendarId
     * @return array
     */
    public function getCalendarObjects($calendarId) {

		go_debug("getCalendarObjects($calendarId)");

		$objects = array();
		$this->cal->get_events(array($calendarId));
		while($event = $this->cal->next_record()){
			$objects[]=array(
				'id'=>$event['id'],
				'uri'=>$event['id'],
				'calendardata'=>$this->exporter->export_event($event['id']),
				'calendarid'=>$calendarId,
				'lastmodified'=>date('Ymd H:i:s', $event['mtime'])
			);
		}

		go_debug($objects);

		return $objects;
    }

    /**
     * Returns information from a single calendar object, based on it's object uri.
     *
     * @param string $calendarId
     * @param string $objectUri
     * @return array
     */
    public function getCalendarObject($calendarId,$objectUri) {

		go_debug("getCalendarObject($calendarId,$objectUri)");

		//go_debug($_SESSION['GO_SESSION']['dav']['objectUriMap']);
		
		if(isset($_SESSION['GO_SESSION']['dav']['objectUriMap'][$objectUri])){
			$objectUri=$_SESSION['GO_SESSION']['dav']['objectUriMap'][$objectUri];
		}

        $event = $this->cal->get_event($objectUri);
		if($event){
			$object = array(
				'id'=>$event['id'],
				'uri'=>$event['id'],
				'calendardata'=>$this->exporter->export_event($event['id']),
				'calendarid'=>$calendarId,
				'lastmodified'=>date('Ymd H:i:s', $event['mtime'])
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
    public function createCalendarObject($calendarId,$objectUri,$calendarData) {

		go_debug("createCalendarObject($calendarId,$objectUri,$calendarData)");


		

        $event = $this->cal->get_event_from_ical_string($calendarData);
		if(!$event)
			return false;

		//$event['id']=$objectUri;
		$event['calendar_id']=$calendarId;

		go_debug($event);

		$_SESSION['GO_SESSION']['dav']['objectUriMap'][$objectUri]=$this->cal->add_event($event);

    }

    /**
     * Updates an existing calendarobject, based on it's uri.
     *
     * @param string $calendarId
     * @param string $objectUri
     * @param string $calendarData
     * @return void
     */
    public function updateCalendarObject($calendarId,$objectUri,$calendarData) {

		go_debug("updateCalendarObject($calendarId,$objectUri,$calendarData)");

		$event = $this->cal->get_event_from_ical_string($calendarData);
		if(!$event)
			return false;

		$event['id']=$objectUri;
		$event['calendar_id']=$calendarId;

		$this->cal->update_event($event);

    }

    /**
     * Deletes an existing calendar object.
     *
     * @param string $calendarId
     * @param string $objectUri
     * @return void
     */
    public function deleteCalendarObject($calendarId,$objectUri) {
		go_debug("deleteCalendarObject($calendarId,$objectUri)");
        $this->cal->delete_event($objectUri);
    }

}
?>

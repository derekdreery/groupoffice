<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GO_CalDAV_Freebusy
 *
 * @author mschering
 */
class GO_CalDAV_FreebusyPlugin extends Sabre_DAV_ServerPlugin {

	protected $authBackend;
	protected $calendarBackend;

	public function __construct($authBackend, $calendarBackend) {
		$this->authBackend = $authBackend;
		$this->calendarBackend = $calendarBackend;
	}

	/**
	 * Initializes the plugin
	 *
	 * @param Sabre_DAV_Server $server
	 * @return void
	 */
	public function initialize(Sabre_DAV_Server $server) {
		$this->server = $server;

		$this->server->subscribeEvent('unknownMethod', array($this, 'httpPOSTHandler'));

		//$server->xmlNamespaces[Sabre_CalDAV_Plugin::NS_CALDAV] = 'C';
	}

	/**
	 * Returns a list of features for the DAV: HTTP header.
	 *
	 * @return array
	 */
	public function getFeatures() {

		return array('calendar-schedule');
	}

	/**
	 * Handles POST requests for tree operations
	 *
	 * This method is not yet used.
	 *
	 * @param string $method
	 * @return bool
	 */
	public function httpPOSTHandler($method) {

		go_debug("httpPOSTHandler($method)");

		$body = $this->server->httpRequest->getBody(true);

		go_debug($body);

	
		$node = $this->server->tree->getNodeForPath($this->server->getRequestUri());

		$dom = new DOMDocument('1.0', 'utf-8');
		$scheduleResponse = $dom->createElement('C:schedule-response');
		$dom->appendChild($scheduleResponse);

		// Adding in default namespaces
		foreach ($this->server->xmlNamespaces as $namespace => $prefix) {
			$scheduleResponse->setAttribute('xmlns:' . $prefix, $namespace);
		}
		$scheduleResponse->setAttribute('xmlns:C', Sabre_CalDAV_Plugin::NS_CALDAV);



		$response = $dom->createElement('C:response');
		$scheduleResponse->appendChild($response);

		$importer = new ical2array();
		$ical = $importer->parse_icalendar_string($body);

		$start = $importer->parse_date($ical[0]['DTSTART']['value']);
		$end = $importer->parse_date($ical[0]['DTEND']['value']);

		foreach ($ical[0]['ATTENDEES'] as $attendee) {
			$email = str_replace('mailto:', '', $attendee['value']);
			$fb = $this->calendarBackend->getFreeBusy($email, $start, $end);
			if (!$fb) {
				//<C:request-status>3.7;Invalid calendar user</C:request-status>
			} else {

				$recipient = $dom->createElement('C:recipient');
				$response->appendChild($recipient);

				$href = $dom->createElement('d:href', $attendee['value']);
				$recipient->appendChild($href);

				$status = $dom->createElement('C:request-status', '2.0;Success');
				$response->appendChild($status);

				$timeFormat = 'Ymd\THis\Z';

				$data = 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Intermesh Group-Office//CalDAV Server//EN
METHOD:REPLY
BEGIN:VFREEBUSY
UID:'.$ical[0]['UID']['value'].'
DTSTAMP:' . gmdate($timeFormat) . '
DTSTART:' . gmdate($timeFormat, $start) . '
DTEND:' . gmdate($timeFormat, $end) . '
ORGANIZER:' . $ical[0]['ORGANIZER']['value'] . '
ATTENDEE:mailto:' . $email . '
';

				foreach ($fb as $fbt) {
					$data .="FREEBUSY;FBTYPE=BUSY:" . gmdate($timeFormat, $fbt['start']) . "/" . gmdate($timeFormat, $fbt['end']) . "\n";
				}

//FREEBUSY;FBTYPE=BUSY:20101004T110000Z/20101004T120000Z
//FREEBUSY;FBTYPE=BUSY:20101004T170000Z/20101004T180000Z
				$data .= 'END:VFREEBUSY
END:VCALENDAR';

				$data = $dom->createElement('C:calendar-data', $data);


				$response->appendChild($data);
			}
		}

		$r = $dom->saveXML();

		go_debug($r);


		/* $r = '<?xml version="1.0" encoding="utf-8" ?>
		  <C:schedule-response xmlns:D="DAV:" xmlns:C="urn:ietf:params:xml:ns:caldav">
		  <C:response>
		  <C:recipient>
		  <D:href>mailto:mschering@intermesh.nl</D:href>
		  </C:recipient>
		  <C:request-status>2.0;Success</C:request-status>
		  <C:calendar-data>BEGIN:VCALENDAR
		  VERSION:2.0
		  PRODID:-//Example Corp.//CalDAV Server//EN
		  METHOD:REPLY
		  BEGIN:VFREEBUSY
		  UID:4FD3AD926350
		  DTSTAMP:200910042T200733Z
		  DTSTART:20091004T000000Z
		  DTEND:20090604T000000Z
		  ORGANIZER;CN="Merijn":mailto:mschering@intermesh.nl
		  ATTENDEE;CN="Merijn":mailto:mschering@intermesh.nl
		  FREEBUSY;FBTYPE=BUSY:20101004T110000Z/20101004T120000Z
		  FREEBUSY;FBTYPE=BUSY:20101004T170000Z/20101004T180000Z
		  END:VFREEBUSY
		  END:VCALENDAR
		  </C:calendar-data>
		  </C:response>
		  </C:schedule-response>'; */

		$this->server->httpResponse->setHeader('Content-Length', strlen($r));
		$this->server->httpResponse->sendStatus(200);

		$this->server->httpResponse->sendBody($r);


		return false;
	}

}

?>

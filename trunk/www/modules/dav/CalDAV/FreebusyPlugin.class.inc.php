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
    /**
     * Initializes the plugin
     *
     * @param Sabre_DAV_Server $server
     * @return void
     */
    public function initialize(Sabre_DAV_Server $server) {
        $this->server = $server;

		$this->server->subscribeEvent('unknownMethod',array($this,'httpPOSTHandler'));
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


        $body = $this->server->httpRequest->getBody();


        // First we'll do a check to see if the resource already exists
        try {

            $node = $this->server->tree->getNodeForPath($this->server->getRequestUri());

			$dom = new DOMDocument('1.0','utf-8');
			$scheduleResponse = $dom->createElement('C:schedule-response');
			$dom->appendChild($scheduleResponse);

			// Adding in default namespaces
			foreach($this->server->xmlNamespaces as $namespace=>$prefix) {
				$scheduleResponse->setAttribute('xmlns:' . $prefix,$namespace);
			}		

			return $dom->saveXML();


			$r = '<?xml version="1.0" encoding="utf-8" ?>
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
</C:schedule-response>';

            $this->server->httpResponse->setHeader('Content-Length',strlen($r));
            $this->server->httpResponse->sendStatus(200);

			$this->server->httpResponse->sendBody($r);

        } catch (Sabre_DAV_Exception_FileNotFound $e) {

            // If we got here, the resource didn't exist yet.
            //$this->createFile($this->getRequestUri(),$body);
            //$this->httpResponse->setHeader('Content-Length','0');
            //$this->httpResponse->sendStatus(201);

        }

        return false;

    }
}
?>

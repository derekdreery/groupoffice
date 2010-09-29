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

		$server->subscribeEvent('report',array($this,'report'));
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
     * This functions handles REPORT requests specific to CalDAV
     *
     * @param string $reportName
     * @param DOMNode $dom
     * @return bool
     */
    public function report($reportName,$dom) {

        switch($reportName) {
            case '{'.self::NS_CALDAV.'}free-busy-query' :
                
                return false;
        }
    }

}
?>

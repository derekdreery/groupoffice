<?php

namespace Sabre\DAV\Property;

use Sabre\DAV;

/**
 * ResponseList property
 *
 * This class represents multiple {DAV:}response XML elements.
 * This is used by the Server class to encode items within a multistatus
 * response.
 *
 * @copyright Copyright (C) 2007-2013 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/)
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class ResponseList extends DAV\Property {

    /**
     * Response objects.
     *
     * @var array
     */
    private $responses;

    /**
     * The only valid argument is a list of Sabre\DAV\Property\Response
     * objects.
     *
     * @param array $responses;
     */
    public function __construct($responses) {

        foreach($responses as $response) {
            if (!($response instanceof Response)) {
                throw new \InvalidArgumentException('You must pass an array of Sabre\DAV\Property\Response objects');
            }
        }
        $this->responses = $responses;

    }

    /**
     * serialize
     *
     * @param DAV\Server $server
     * @param \DOMElement $dom
     * @return void
     */
    public function serialize(DAV\Server $server,\DOMElement $dom) {

        foreach($this->responses as $response) {
            $response->serialize($server, $dom);
        }

    }

}

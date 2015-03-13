<?php

namespace Sabre\VObject;

/**
 * This issue was pointed out in Issue 55. \r should be stripped completely 
 * when encoding property values.
 */
class SlashRTest extends \PHPUnit_Framework_TestCase {

    function testEncode() {

        $prop = new \Sabre\VObject\Property('test', "abc\r\ndef");
        $this->assertEquals("TEST:abc\\ndef\r\n", $prop->serialize());

    }


}

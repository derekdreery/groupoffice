<?php

namespace Sabre\DAV\Browser;

use Sabre\DAV;

require_once 'Sabre/DAV/AbstractServer.php';
class GuessContentTypeTest extends DAV\AbstractServer {

    function setUp() {

        parent::setUp();
        file_put_contents(SABRE_TEMPDIR . '/somefile.jpg','blabla');
        file_put_contents(SABRE_TEMPDIR . '/somefile.hoi','blabla');

    }

    function tearDown() {

        unlink(SABRE_TEMPDIR . '/somefile.jpg');
        parent::tearDown();
    }

    function testGetProperties() {

        $properties = array(
            '{DAV:}getcontenttype',
        );
        $result = $this->server->getPropertiesForPath('/somefile.jpg',$properties);
        $this->assertArrayHasKey(0,$result);
        $this->assertArrayHasKey(404,$result[0]);
        $this->assertArrayHasKey('{DAV:}getcontenttype',$result[0][404]);

    }

    /**
     * @depends testGetProperties
     */
    function testGetPropertiesPluginEnabled() {

        $this->server->addPlugin(new GuessContentType());
        $properties = array(
            '{DAV:}getcontenttype',
        );
        $result = $this->server->getPropertiesForPath('/somefile.jpg',$properties);
        $this->assertArrayHasKey(0,$result);
        $this->assertArrayHasKey(200,$result[0]);
        $this->assertArrayHasKey('{DAV:}getcontenttype',$result[0][200]);
        $this->assertEquals('image/jpeg',$result[0][200]['{DAV:}getcontenttype']);

    }

    /**
     * @depends testGetPropertiesPluginEnabled
     */
    function testGetPropertiesUnknown() {

        $this->server->addPlugin(new GuessContentType());
        $properties = array(
            '{DAV:}getcontenttype',
        );
        $result = $this->server->getPropertiesForPath('/somefile.hoi',$properties);
        $this->assertArrayHasKey(0,$result);
        $this->assertArrayHasKey(404,$result[0]);
        $this->assertArrayHasKey('{DAV:}getcontenttype',$result[0][404]);

    }
}

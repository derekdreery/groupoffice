<?php
/**
 * @copyright Copyright &copy; Intermesh 2003
 * @version $Revision: 1.4 $ $Date: 2006/05/31 09:41:46 $
 * 
 * @author Markus Schabel <markus.schabel@tgm.ac.at>

   This file is part of Group-Office.

   Group-Office is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Group-Office is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Group-Office; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 * @package Framework
 * @subpackage Usermanagement
 * @category Authentication
 */

/**
 * Test for the Implementation of LDAP Authentication.
 * 
 * This class provides a test for the login-function for an LDAP based
 * authentication functions.
 * 
 * @package Framework
 * @subpackage Usermanagement
 * @category Authentication
 * 
 * @access protected
 * 
 * @see ldap.auth.class.inc
 */
class ButtonTest extends PHPUnit_TestCase {
	function ButtonTest( $name ) {
		$this->PHPUnit_TestCase( $name );
	}

	function setUp() {
		// TODO initialize $GO_LDAP variable from framework, and fill a test
		// LDAP server with data we can use for testing.
	}

	function tearDown() {
		// Remove test data from test LDAP server.
	}

	/**
	 * Test the function which finds the DN of a user in LDAP.
	 * 
	 * This function should return a string that represents the DN under which
	 * the user is stored in LDAP.
	 * 
	 * @todo create multiple functions to test different expected results from
	 * this function, e.g. existing user, unknown user, XSS-attacks, problems
	 * with the connection to the directory server, ...
	 */
	function test_getDNfromUsername() {
	}

	/**
	 * Test the function which authenticates a user against a LDAP directory.
	 * 
	 * This function should return the userid number of the given user, when
	 * the user is found in LDAP and the given password is invalid, or null
	 * otherwise.
	 * 
	 * @todo create multiple functions to test different expected results from
	 * this function, eg. existing user, unknown user, valid password, invalid
	 * password, XSS-attacks, no connection to the directory server, ...
	 */
	function test_authenticate() {
	}


  // {{{ void checkValidMouseOver( $htmlcode, $error )
  function checkValidMouseOver( $htmlcode, $error ) {
    $this->assertRegExp(
        '/^.*\sonmouseover=(["\'])javascript:this.className=(["\'])button_mo(\\2);(\\1)\s.*$/',
        $htmlcode, $error );
    $this->assertNotRegExp(
        '/^.*\sonmouseover=(["\'])javascript:this.className=(\\1).*$/',
        $htmlcode, $error );
    $this->assertRegExp(
        '/^.*\sonmouseout=(["\'])javascript:this.className=(["\'])button(\\2);(\\1)\s.*$/',
        $htmlcode, $error );
    $this->assertNotRegExp(
        '/^.*\sonmouseout=(["\'])javascript:this.className=(\\1).*$/',
        $htmlcode, $error );
  }
  // }}}

  // {{{ void test_returnsValidInputfield_Constructor()
  function test_returnsValidInputfield_Constructor() {
    $errormsg = "Constructor does not return valid inputfield HTML code.";
    $result = $this->constructor2stdout( "text", "action", 20 );
    $this->checkValidInputfield( $result, $errormsg );
  }
}

/* {{{ VIM-Config-Statements
 * vim600:  et sw=2 ts=2 fdm=marker
 * vim<600: et sw=2 ts=2
 * }}} */
?>

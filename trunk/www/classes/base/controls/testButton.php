<?php
require_once "button.class.inc";

class ButtonTest extends PHPUnit_TestCase {

  // {{{ ButtonTest( $name )
  function ButtonTest( $name ) {
    $this->PHPUnit_TestCase( $name );
  }
  // }}}

  // {{{ void setUp()
  function setUp() {
  }
  // }}}
  // {{{ void tearDown()
  function tearDown() {
  }
  // }}}

  // {{{ string constructor2stdout( $text, $action, $size=false )
  function constructor2stdout( $text, $action, $size=false ) {
    ob_start();
    if ( $size == false ) {
      $button = new button( $text, $action );
    } else {
      $button = new button( $text, $action, $size );
    }
    return ob_get_clean();
  }
  // }}}

  // {{{ void checkValidInputfield( $htmlcode, $error )
  function checkValidInputfield( $htmlcode, $error ) {
    // Check if we got a valid Inputfield of type button that contains no
    // other tags (exactly: does not contain the characters '<' and '>').
    $this->assertRegExp(
        '/^<input type=(["\'])button(\\1)\s([^<>]+)\/>$/i',
        $htmlcode, $error );
  }
  // }}}
  // {{{ void checkValidClass( $htmlcode, $error )
  function checkValidClass( $htmlcode, $error ) {
    $this->assertRegExp(
        '/^.*\sclass=(["\'])button(\\1)\s.*$/i',
        $htmlcode, $error );
  }
  // }}}
  // {{{ void checkValidWidth( $htmlcode, $width, $error )
  function checkValidWidth( $htmlcode, $width, $error ) {
    $this->assertRegExp(
        '/^.*\sstyle=(["\'])width:\s?'.$width.'\s?px;(\\1)\s.*$/i',
        $htmlcode, $error );
  }
  // }}}
  // {{{ void checkValidValue( $htmlcode, $value, $error )
  function checkValidValue( $htmlcode, $value, $error ) {
    $this->assertRegExp(
        '/^.*\svalue=(["\'])'.$value.'(\\1)\s.*$/',
        $htmlcode, $error );
  }
  // }}}
  // {{{ void checkValidOnclick( $htmlcode, $action, $error )
  function checkValidOnclick( $htmlcode, $action, $error ) {
    $this->assertRegExp(
        '/^.*\sonclick=(["\'])'.$action.'(\\1)\s.*$/',
        $htmlcode, $error );
  }
  // }}}
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
  // }}}
  // {{{ void test_returnsValidInputfield_getButton()
  function test_returnsValidInputfield_getButton() {
    $errormsg = "get_button() does not return valid inputfield HTML code.";
    $button = new button();
    $result = $button->get_button( "text", "action", 20 );
    $this->checkValidInputfield( $result, $errormsg );
  }
  // }}}

  // {{{ void test_returnsValidClass_Constructor()
  function test_returnsValidClass_Constructor() {
    $errormsg = "Constructor does not return valid/correct CSS-class.";
    $result = $this->constructor2stdout( "text", "action", 20 );
    $this->checkValidClass( $result, $errormsg );
  }
  // }}}
  // {{{ void test_returnsValidClass_getButton()
  function test_returnsValidClass_getButton() {
    $errormsg = "get_button() does not return valid/correct CSS-class.";
    $button = new button();
    $result = $button->get_button( "text", "action", 20 );
    $this->checkValidClass( $result, $errormsg );
  }
  // }}}
  
  // {{{ void test_returnsValidDefaultWidth_Constructor()
  function test_returnsValidDefaultWidth_Constructor() {
    $errormsg = "Constructor does not return correct default size.";
    $result = $this->constructor2stdout( "text", "action" );
    $this->checkValidWidth( $result, 100, $errormsg );
  }
  // }}}
  // {{{ void test_returnsValidDefaultWidth_getButton()
  function test_returnsValidDefaultWidth_getButton() {
    $errormsg = "get_button() does not return correct default size.";
    $button = new button();
    $result = $button->get_button( "text", "action" );
    $this->checkValidWidth( $result, 100, $errormsg );
  }
  // }}}

  // TODO add checks what happens if the given size is not valid.
  // {{{ void test_returnsValidWidth_Constructor()
  function test_returnsValidWidth_Constructor() {
    $errormsg = "Constructor does not return correct size.";
    $result = $this->constructor2stdout( "text", "action", 20 );
    $this->checkValidWidth( $result, 20, $errormsg );
  }
  // }}}
  // {{{ void test_returnsValidWidth_getButton()
  function test_returnsValidWidth_getButton() {
    $errormsg = "get_button() does not return correct size.";
    $button = new button();
    $result = $button->get_button( "text", "action", 20 );
    $this->checkValidWidth( $result, 20, $errormsg );
  }
  // }}}

  // TODO add checks if value contains ' or ".
  // {{{ void test_returnsValidValue_Constructor()
  function test_returnsValidValue_Constructor() {
    $errormsg = "Constructor does not return correct value.";
    $result = $this->constructor2stdout( "text", "action", 20 );
    $this->checkValidValue( $result, "text", $errormsg );
  }
  // }}}
  // {{{ void test_returnsValidValue_getButton()
  function test_returnsValidValue_getButton() {
    $errormsg = "get_button() does not return correct value.";
    $button = new button();
    $result = $button->get_button( "text", "action", 20 );
    $this->checkValidValue( $result, "text", $errormsg );
  }
  // }}}

  // TODO add checks if action contains ' or ".
  // {{{ void test_returnsValidOnclick_Constructor()
  function test_returnsValidOnclick_Constructor() {
    $errormsg = "Constructor does not return correct action.";
    $result = $this->constructor2stdout( "text", "action", 20 );
    $this->checkValidOnclick( $result, "action", $errormsg );
  }
  // }}}
  // {{{ void test_returnsValidOnclick_getButton()
  function test_returnsValidOnclick_getButton() {
    $errormsg = "get_button() does not return correct action.";
    $button = new button();
    $result = $button->get_button( "text", "action", 20 );
    $this->checkValidOnclick( $result, "action", $errormsg );
  }
  // }}}

  // {{{ void test_returnsValidMouseOver_Constructor()
  function test_returnsValidMouseOver_Constructor() {
    $errormsg = "Constructor does not return valid mouseover-scripts.";
    $result = $this->constructor2stdout( "text", "action", 20 );
    $this->checkValidMouseOver( $result, $errormsg );
  }
  // }}}
  // {{{ void test_returnsValidMouseOver_getButton()
  function test_returnsValidMouseOver_getButton() {
    $errormsg = "get_button() does not return valid mouseover-scripts.";
    $button = new button();
    $result = $button->get_button( "text", "action", 20 );
    $this->checkValidMouseOver( $result, $errormsg );
  }
  // }}}
}

/* {{{ VIM-Config-Statements
 * vim600:  et sw=2 ts=2 fdm=marker
 * vim<600: et sw=2 ts=2
 * }}} */

<?php
ini_set( 'error_reporting', E_ALL );

require_once( 'PHPUnit.php');
require_once( 'PHPUnit/GUI/HTML.php' );
require_once( 'PHPUnit/GUI/SetupDecorator.php' );

?>
<html><head><title>PHP-Unit Tests for Group-Office</title>
<style type="text/css">
include( "phpunit/stylesheet.css" );
</style></head><body>

<?php
class GO_CONFIG {
	var $class_path = '../classes';
	
	function GO_CONFIG() {
		$this->class_path = $this->class_path.'/';
	}
}

$GO_CONFIG = new GO_CONFIG();

class PHPUnit_GUI_SetupDecorator_GO extends PHPUnit_GUI_SetupDecorator {
	function PHPUnit_GUI_SetupDecorator_GO( &$gui ) {
		$this->_gui = $gui;
	}

	function getSuitesFromDir( $dir, $filenamePattern = '', $exclude = array() ) {
		if ( $dir{strlen( $dir ) - 1} == DIRECTORY_SEPARATOR ) {
			$dir = substr( $dir, 0, -1 );
		}

		$files = $this->_getFiles( $dir, $filenamePattern, $exclude, $dir );
		asort( $files );

		foreach ( $files as $className => $aFile ) {
			include_once( $aFile );
			$openedfile = file( $aFile );
			foreach ( $openedfile as $line_num => $line ) {
				$output = array();
				preg_match_all( '/class[\s]+([\w]+)[\s]+extends[\s]+PHPUnit_TestCase[\s]+{/', $line, $output );
				if ( count( $output[1] ) == 1 ) {
					$cName = $output[1][0];
					if ( class_exists( $cName ) ) {
						$suites[] =& new PHPUnit_TestSuite( $cName );
					} else {
						trigger_error( "$cName could not be found in $aFile!" );
					}
				}
			}
		}

		$this->_gui->addSuites( $suites );
	}
}

$gui = new PHPUnit_GUI_SetupDecorator_GO( new PHPUnit_GUI_HTML() );
$gui->getSuitesFromDir( dirname(__FILE__).'/classes/base/controls', '^test.*\.php$' );
$gui->getSuitesFromDir( dirname(__FILE__).'/classes/base', '^.*\.test\.php$' );
$gui->show();

echo "</body>";
?>

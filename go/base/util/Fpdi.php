<?php
require_once(GO::config()->root_path."go/vendor/tcpdf/tcpdf.php");

//TODO FPDI is not using strict standards
$oldLvl = error_reporting(E_ALL & ~E_STRICT);
require_once(GO::config()->root_path.'go/vendor/fpdi/fpdi.php');
error_reporting($oldLvl);

class GO_Base_Util_Fpdi extends FPDI {
	
	/**
	 * Pass error message in Exception
	 *
	 * @param string $msg  Error-Message
	 */
	function error($msg) {
		throw new Exception('<b>FPDI Error:</b> ' . $msg);	
	}		
	
}
?>

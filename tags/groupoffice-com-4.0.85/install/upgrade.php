<?php
require('header.php');

if($_SERVER['REQUEST_METHOD']=="POST")
		redirect('finished.php');

printHead();

?>
<h1>Upgrading</h1>
<?php

$mc = new GO_Core_Controller_Maintenance();
$mc->run("upgrade",array(),false);

continueButton();

printFoot();
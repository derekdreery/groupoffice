<?php
require('header.php');
if($_SERVER['REQUEST_METHOD']=='POST'){
	redirect("configFile.php");
}


printHead();

?>
<h1>System test</h1>
<?php

require('gotest.php');

if(!output_system_test())
{
	echo '<p style="color: red;">Because of a fatal error in your system setup the installation can\'t continue. Please fix the errors above first.</p>';
}else
{
	echo continueButton();
}

printFoot();
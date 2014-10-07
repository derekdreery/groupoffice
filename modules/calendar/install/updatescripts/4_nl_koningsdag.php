<?php
$holiday = new GO_Base_Model_Holiday();
for ($year=2014;$year<2031;$year++)
	$holiday->deleteHolidays($year,'nl');
?>

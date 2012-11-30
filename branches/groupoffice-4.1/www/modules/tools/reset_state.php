<?php

require('../../Group-Office.php');
$db = new db();
$db->query("DELETE FROM go_state WHERE name!='summary-active-portlets'");

echo 'State was reset';
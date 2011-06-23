<?php

require('../../Group-Office.php');
$db = new db();
$db->query('TRUNCATE go_state');

echo 'State was reset';
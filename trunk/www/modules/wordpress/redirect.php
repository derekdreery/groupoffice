<?php
//start session
require('../../Group-Office.php');

header('Location: /wordpress/wp-admin/?GO_SID='.session_id());
?>
<?php
//start session
require('../../Group-Office.php');

header('Location: /wordpress/?GO_SID='.session_id());
?>
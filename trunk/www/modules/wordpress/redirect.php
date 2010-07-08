<?php
//start session
require('../../Group-Office.php');

header('Location: '.$GO_CONFIG->get_setting('wp_url').'?GO_SID='.session_id());
?>
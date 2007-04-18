<?php
require('Group-Office.php');
$url = isset($_SESSION['GO_SESSION']['help_url']) ? $_SESSION['GO_SESSION']['help_url'] : 'http://docs.group-office.com';

header('Location: '.$url);
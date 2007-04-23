<?php
session_start();
$url = isset($_SESSION['GO_SESSION']['help_url']) ? $_SESSION['GO_SESSION']['help_url'] : 'http://docs.group-office.com';

header('Location: '.$url);
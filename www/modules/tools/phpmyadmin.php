<?php
require('../../Group-Office.php');

/* Need to have cookie visible from parent directory */
session_set_cookie_params(0, '/', '', 0);
/* Create signon session */


//session_start();
/* Store there credentials */
$_SESSION['PMA_single_signon_user'] = GO::config()->db_user;
$_SESSION['PMA_single_signon_password'] = GO::config()->db_pass;
$_SESSION['PMA_single_signon_host'] = GO::config()->db_host;
$id = session_id();
/* Close that session */
session_write_close();
/* Redirect to phpMyAdmin (should use absolute URL here!) */
header('Location: '.GO::config()->phpMyAdminUrl.'?db='.GO::config()->db_name);
?>
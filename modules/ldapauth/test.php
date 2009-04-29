<?php
require('../../Group-Office.php');

require_once($GO_MODULES->modules['ldapauth']['class_path'].'ldapauth.class.inc.php');

ldapauth::before_login('test', 'test');

?>
#!/usr/bin/php
<?php
chdir(dirname(__FILE__));
require('../../www/cli-functions.inc.php');

$args = parse_cli_args($argv);

require('../../www/Group-Office.php');

var_dump($args);
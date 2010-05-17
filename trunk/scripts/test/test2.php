#!/usr/bin/php
<?php
require('../../www/classes/cryptastic.class.inc.php');
$c = new cryptastic();
$enc = $c->encrypt('Secret message', 'mysecretkey', true);
echo $c->decrypt($enc, 'mysecretkey', true);
echo "\n";





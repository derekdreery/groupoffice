<?php

require('../../www/Group-Office.php');

$ns=UUID::v4();

echo UUID::v5($ns, 1)."\n";
echo UUID::v5($ns, 2)."\n";


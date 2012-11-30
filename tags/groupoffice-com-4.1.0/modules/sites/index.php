<?php
//Copy this file to the root of your website
//adjust the $path_to_gos file to the location of groupoffice/modules/sites/components/GOS.php
$path_to_gos = dirname(__FILE__).'/components/GOS.php';
require($path_to_gos);
GOS::launch()->run();

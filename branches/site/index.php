<?php
define('GO_CONFIG_FILE','/var/www/groupoffice-4.1/config.php'); //TODO!!!

require(dirname(__FILE__).'/../../GO.php');
require('components/Site.php');
Site::launch();
?>
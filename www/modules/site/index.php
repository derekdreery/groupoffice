<?php
define('GO_CONFIG_FILE', '/etc/groupoffice/go41.loc/config.php');
require(dirname(__FILE__).'/../../GO.php');
require('components/Site.php');
Site::launch();
?>
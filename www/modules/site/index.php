<?php
//If the config.php file can't be found add this to the Apache configuration:
//SetEnv GO_CONFIG /etc/groupoffice/config.php
require(dirname(__FILE__).'/../../GO.php');
require(GO::config()->root_path.'modules/site/components/Site.php');
Site::launch();
?>
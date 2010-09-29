<?php
function GO_Sabre_autoload($className) {

    if(strpos($className,'GO_')===0) {

		$className = substr($className,3);
		$className = String::replace_once('_', '/', $className);

        include dirname(__FILE__) . '/' . $className . '.class.inc.php';

    }
}

spl_autoload_register('GO_Sabre_autoload');
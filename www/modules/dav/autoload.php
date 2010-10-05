<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

function GO_Sabre_autoload($className) {

    if(strpos($className,'GO_')===0) {

		$className = substr($className,3);
		$className = String::replace_once('_', '/', $className);

        include dirname(__FILE__) . '/' . $className . '.class.inc.php';

    }
}

spl_autoload_register('GO_Sabre_autoload');
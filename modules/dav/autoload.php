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

	global $GO_MODULES;

    if(strpos($className,'GO_')===0) {

		$className = substr($className,3);
		$className = String::replace_once('_', '/', $className);

		if(strpos($className, 'CalDAV')!==false)
			include $GO_MODULES->modules['caldav']['path'].str_replace('CalDAV/','', $className).'.class.inc.php';
		else
			include $GO_MODULES->modules['dav']['path'] . $className . '.class.inc.php';

    }
}

spl_autoload_register('GO_Sabre_autoload');
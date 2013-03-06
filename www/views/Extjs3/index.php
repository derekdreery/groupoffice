<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

header('Content-Type: text/html; charset=UTF-8');
require_once("Group-Office.php");

global $GO_CONFIG, $GO_INCLUDES, $GO_MODULES, $GO_SECURITY, $GO_LANGUAGE, $GO_EVENTS, $GO_THEME, $lang;



require_once($GLOBALS['GO_CONFIG']->class_path.'base/theme.class.inc.php');
$GO_THEME = new GO_THEME();


require_once($GLOBALS['GO_THEME']->theme_path."layout.inc.php");
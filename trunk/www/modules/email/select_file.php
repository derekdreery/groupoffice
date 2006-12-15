<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.7 $ $Date: 2005/07/01 10:11:12 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once('../../Group-Office.php');
require_once($GO_LANGUAGE->get_language_file('filesystem'));
$module = $GO_MODULES->get_module('email');
$GO_HANDLER = $module['url'].'attach_online.php';
$GO_CONFIG->window_mode = 'popup';
$target_frame = '_self';
$module = $GO_MODULES->get_module('filesystem');
require_once($module['path'].'index.php');
?>

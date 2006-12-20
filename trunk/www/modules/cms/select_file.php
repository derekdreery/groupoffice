<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.2 $ $Date: 2006/09/06 08:24:44 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once('../../Group-Office.php');
require_once($GO_LANGUAGE->get_language_file('filesystem'));

$GO_HANDLER = $GO_MODULES->modules['cms']['url'].'insert_link.php';
$GO_CONFIG->window_mode = 'popup';
$target_frame = '_self';
$treeview=false;
require_once($GO_MODULES->modules['filesystem']['path'].'index.php');

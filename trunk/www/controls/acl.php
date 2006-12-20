<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.12 $ $Date: 2006/11/21 16:25:35 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once("../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_HEADER['nomessages'] = true;
$GO_HEADER['body_arguments'] = 'class="acl"';
require_once($GO_THEME->theme_path."header.inc");

$acl_id = isset($_REQUEST['acl_id']) ? $_REQUEST['acl_id'] : 0;
load_basic_controls();
load_control('acl');
$form = new form('acl_form');
$form->add_html_element(new input('hidden', 'acl_id', $acl_id));
$form->add_html_element(new acl($acl_id, 'acl_form'));

echo $form->get_html();

require_once($GO_THEME->theme_path."footer.inc");

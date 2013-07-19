<?php

/**
 * @copyright Intermesh 2007
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1615 $ $Date: 2008-03-26 14:07:35 +0100 (wo, 26 mrt 2008) $3
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
require_once("../../Group-Office.php");
$GLOBALS['GO_SECURITY']->json_authenticate('customfields');

/*
 * License checking for pro module
 */
require_once($GLOBALS['GO_CONFIG']->root_path . 'modules/professional/check.php');
if (check_license() != 'dvwes8sd689d67h23jdwd78hkdwaw') {
	die('license error in customfields');
}

require_once ($GLOBALS['GO_MODULES']->modules['customfields']['class_path'] . "customfields.class.inc.php");
//require_once ($GLOBALS['GO_LANGUAGE']->get_language_file('projects'));
$customfields = new customfields();

//ini_set('display_errors','off');
//we are unsuccessfull by default
$response = array('success' => false);

try {

	switch ($_REQUEST['task']) {

		case 'bulk_edit':
//				$cf2 = new customfields();
//				$customfields->get_categories(6);
//				$fields = array();
//				while ($cat = $customfields->next_record()) {
//					$cf2->get_fields($cat['id']);
//					while ($field = $cf2->next_record()) {
//						$fields['col_'.$field['id']] = $field;
//					}
//				}

				// collect changes
				$changes = array();
				foreach ($_POST as $k => $v) {
					if (substr($k,0,4)=='col_' && substr($k,-8)!='_checked' && !empty($_POST[$k.'_checked'])) {
//						if ($fields[$k]['datatype']=='date')
//							$changes[$k] = Date::to_db_date($v);
//						else
							$changes[$k] = $v;
					}
				}

				$selected_file_ids = json_decode($_POST['selected_file_ids']);

				// apply changes to selection
				foreach ($selected_file_ids as $file_id) {
					//$customfields->update_row('cf_6', 'link_id', array_merge(array('link_id'=>$file_id),$changes));
					$customfields->update_fields($GLOBALS['GO_SECURITY']->user_id, $file_id, 6, $changes,false,true);
				}
				$response['success'] = true;
			break;

	

	}
} catch (Exception $e) {
	$response['feedback'] = $e->getMessage();
	$response['success'] = false;
}

echo json_encode($response);
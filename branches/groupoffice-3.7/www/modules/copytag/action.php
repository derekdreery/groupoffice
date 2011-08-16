<?php
/** 
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: action.php 4569 2011-08-15 010:45:54Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate('copytag');
require_once ($GO_MODULES->modules['copytag']['class_path']."copytag.class.inc.php");
$copytag = new copytag();

try {
	switch($_REQUEST['task']) {

		case 'save_tag_grid':
      $gridData = json_decode($_POST['gridData']); // every row of the grid
      
      if($copytag->clearTable()){
        foreach($gridData as $row){
          if(!empty($row->tag)&&!empty($row->userid)) {
            $copytag->addTag($row->userid, $row->tag);
          }
        }
      }
      
			$response['success']=true;
			break;

/* {TASKSWITCH} */
	}
}
catch(Exception $e) {
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);

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
require_once("../../Group-Office.php");
$GLOBALS['GO_SECURITY']->json_authenticate('comments');
require_once ($GLOBALS['GO_MODULES']->modules['comments']['class_path']."comments.class.inc.php");
//require_once ($GLOBALS['GO_LANGUAGE']->get_language_file('comments'));
$comments = new comments();
try{
	switch($_REQUEST['task'])
	{
		case 'save_comment':
			$comment_id=$comment['id']=isset($_POST['comment_id']) ? ($_POST['comment_id']) : 0;
			$comment['comments']=$_POST['comments'];
			if($comment['id']>0)
			{
				$comments->update_comment($comment);
				$response['success']=true;
			}else
			{
				$comment['link_id']=$_POST['link_id'];
				$comment['link_type']=$_POST['link_type'];			
				$comment['user_id']=$GLOBALS['GO_SECURITY']->user_id;
				
				$comment_id= $comments->add_comment($comment);
				
				$response['comment_id']=$comment_id;
				$response['success']=true;
			}
			break;
			/* {TASKSWITCH} */
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);

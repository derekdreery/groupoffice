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
 * @version $Id: download_oo_template.php 2657 2008-07-22 13:53:07Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require_once("../../Group-Office.php");
$GO_SECURITY->json_authenticate('addressbook');
require_once($GO_LANGUAGE->get_language_file('addressbook'));
require($GO_MODULES->modules['mailings']['class_path'].'templates.class.inc.php');
$tp = new templates;

$feedback = null;

$template_id = isset($_REQUEST['template_id']) ? smart_addslashes($_REQUEST['template_id']) : 0;

try
{
	if($template_id)
	{
		$contact_id = isset($_REQUEST['contact_id']) ? smart_addslashes($_REQUEST['contact_id']) : 0;
		$company_id = isset($_REQUEST['company_id']) ? smart_addslashes($_REQUEST['company_id']) : 0;
		
		$browser = detect_browser();
		$existing_template = $tp->get_template($template_id);
		
		if($existing_template)
 		{
	 		if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $existing_template['acl_write']))
	 		{
	 			throw new AccessDeniedException();
	 		}
	 		
 			$file = $tp->get_template($template_id);

 			if($file)
			{
				if ($browser['name'] == 'MSIE') {
					header('Content-Type: application/octet-stream');
				
					header('Content-Disposition: attachment; filename="'.$existing_template['name'].'.odt"');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
				} else {
					//header('Content-Type: application/vnd.sun.xml.writer');
					header('Content-Type: application/octet-stream');
					header('Pragma: no-cache');
					header('Content-Disposition: attachment; filename="'.$existing_template['name'].'.odt"');
				}
				header('Content-Transfer-Encoding: binary');
		
				$tmp_dir = $GO_CONFIG->tmpdir.$GO_SECURITY->user_id;
				while (file_exists($tmp_dir)) {
					$tmp_dir .= '0';
				}
				$tmp_dir .= '/';
				mkdir($tmp_dir, $GO_CONFIG->create_mode);
		
				require_once ($GO_CONFIG->class_path.'filesystem.class.inc');
				$fs = new filesystem(true);
		
				$zip_file = $tmp_dir.$file['name'].'.odt';

				file_put_contents($zip_file, $file['content']);

				if($contact_id > 0 || $company_id > 0)
				{
					chdir($tmp_dir);
			
					exec('unzip "'.$zip_file.'"');
					$content_file = $tmp_dir.'content.xml';
					$content='';
					if (file_exists($content_file)) 
					{
						//get the content.xml						
						$content = file_get_contents($content_file);						
					}
				}
				
				if($contact_id > 0)
				{
					$content = $tp->replace_contact_data_fields($content, $contact_id);
				} 
				
				if($company_id > 0) {
					$content = $tp->replace_company_data_fields($content, $company_id);
				}
				
				if($contact_id > 0 || $company_id > 0)
				{
					file_put_contents($content_file, $content);		
					@ unlink($zip_file);					
					exec('zip -r "'.$zip_file.'" *');

					readfile($zip_file);					

				} else {
					echo $file['content'];
				}	
				exec('rm -Rf '.$tmp_dir);
 			}			
			
 		}
	}
}

catch(Exception $e)
{
/*
	$response['feedback']=$e->getMessage();
 	$response['success']=false;
 	
 	go_log(LOG_DEBUG, json_encode($response));
 	
 	echo json_encode($response);
	*/
}

?>
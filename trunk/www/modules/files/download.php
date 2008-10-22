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
 
//Server and client send the session ID in the URL
if(isset($_REQUEST['sid']))
{
	session_id($_REQUEST['sid']);
}

require_once("../../Group-Office.php");
//load file management class

//$GO_SECURITY->html_authenticate('files');

require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc');
$fs = new files();

$path = $GO_CONFIG->file_storage_path.smart_stripslashes($_REQUEST['path']);

$mode = isset($_REQUEST['mode'])  ? $_REQUEST['mode'] : 'download';

if ($fs->has_read_permission($GO_SECURITY->user_id, $path) || $fs->has_write_permission($GO_SECURITY->user_id, $path))
{
	/*if($GO_LOGGER->enabled)
	{
		$link_id=$fs->get_link_id_by_path(addslashes($path));
		$GO_LOGGER->log('filesystem', 'VIEW '.$path, $link_id);
	}*/
	
	$browser = detect_browser();

	$filename = utf8_basename($path);
	$extension = File::get_extension($filename);

	header('Content-Length: '.filesize($path));
	//header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
	header('Content-Transfer-Encoding: binary');
	
	$last_modified_time = filemtime($file);
	$etag = md5_file($file);
	
	header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
	header("Etag: $etag");
	header('Cache-Control: private, pre-check=0, post-check=0, max-age=1080');

	if ($browser['name'] == 'MSIE')
	{
		header('Content-Type: application/download');
		if($mode == 'download')
		{
			header('Content-Disposition: attachment; filename="'.$filename.'"');
		}else
		{
			header('Content-Disposition: inline; filename="'.$filename.'"');
		}
		//header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		//header('Pragma: public');
	}else
	{
		if($mode == 'download')
		{
			//$finfo = finfo_open(FILEINFO_MIME);
			//$mime = finfo_file($finfo, $path);

			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
		}else
		{
			header('Content-Type: '.mime_content_type($path));
			header('Content-Disposition: inline; filename="'.$filename.'"');
		}
		//header('Pragma: no-cache');
	}


	$fd = fopen($path,'rb');
	while (!feof($fd)) {
		print fread($fd, 32768);
	}
	fclose($fd);

}else
{
	exit($lang['common']['accessDenied']);
}
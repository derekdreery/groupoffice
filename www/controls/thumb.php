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

/**
 * Generates thumbnail.
 *
 * 3 parameters can be passed:
 *
 * w = width
 * h = height
 * zc = 0 or 1. When set to 1 thumbnail will zoom in to the center and keep
 * aspect ratio.
 *
 * You should pass the filemtime of a file too so the browser will refresh the
 * thumbnail when this changes because this script will instruct the browser
 * to cache the thumbnail for one year.
 * 
 */
require('../Group-Office.php');

session_write_close();

$path = $_REQUEST['src'];

if(File::path_leads_to_parent($path))
	die('Invalid request');


$w = isset($_REQUEST['w']) ? intval($_REQUEST['w']) : 0;
$h = isset($_REQUEST['h']) ? intval($_REQUEST['h']) : 0;
$zc = !empty($_REQUEST['zc']) && !empty($w) && !empty($h);

$full_path = $GO_CONFIG->file_storage_path.$path;

$cache_dir = $GO_CONFIG->file_storage_path.'thumbcache';
if(!is_dir($cache_dir)){
	mkdir($cache_dir, 0755, true);
}
$filename = basename($path);
$file_mtime = filemtime($full_path);

$cache_filename = str_replace(array('/','\\'),'_', dirname($path)).'_'.$w.'_'.$h.'_';
if($zc)
{
	$cache_filename .= 'zc_';
}
$cache_filename .= $filename;


if(!empty($_REQUEST['nocache']) || !file_exists($cache_dir.'/'.$cache_filename) || filemtime($cache_dir.'/'.$cache_filename)<$file_mtime){
	$image = new Image($full_path);
	if($zc){
		$image->zoomcrop($w, $h);		
	}else
	{
		if($w && $h){
			$image->resize($w, $h);
		}elseif($w){
			$image->resizeToWidth($w);
		}else
		{
			$image->resizeToHeight($h);
		}
	}

	$image->save($cache_dir.'/'.$cache_filename);
}

$browser = detect_browser();

header("Expires: " . date("D, j M Y G:i:s ", time()+(86400*365)) . 'GMT');//expires in 1 year
header('Cache-Control: cache');
header('Pragma: cache');
$mime = File::get_mime($full_path);
header('Content-Type: '.$mime);
header('Content-Disposition: inline; filename="'.$cache_filename.'"');
header('Content-Transfer-Encoding: binary');

readfile($cache_dir.'/'.$cache_filename);

<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: download.php 2549 2009-05-21 12:10:19Z mschering $
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

$path = $_REQUEST['src'];
$w = isset($_REQUEST['w']) ? intval($_REQUEST['w']) : 0;
$h = isset($_REQUEST['h']) ? intval($_REQUEST['h']) : 0;
$zc = !empty($_REQUEST['zc']) && !empty($w) && !empty($h);

$full_path = $GO_CONFIG->file_storage_path.$path;

$cache_dir = $GO_CONFIG->file_storage_path.'thumbcache/'.dirname($path);
if(!is_dir($cache_dir)){
	mkdir($cache_dir, 0755, true);
}
$filename = basename($path);
$file_mtime = filemtime($full_path);

$cache_filename = $w.'_'.$h.'_';
if($zc)
{
	$cache_filename .= 'zc_';
}
$cache_filename .= File::strip_extension($filename).'.jpg';


if(!file_exists($cache_dir.'/'.$cache_filename) || filemtime($cache_dir.'/'.$cache_filename)<$file_mtime){
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
header('Content-Type: image/jpg');
header('Content-Disposition: inline; filename="'.$cache_filename.'"');
header('Content-Transfer-Encoding: binary');

readfile($cache_dir.'/'.$cache_filename);

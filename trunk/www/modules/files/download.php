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

require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
$files = new files();
$fs = new filesystem();



if(!empty($_REQUEST['id']))
{
    $file = $files->get_file($_REQUEST['id']);
    $path = $files->build_path($file['folder_id']).'/'.$file['name'];
}else
{
    $path = $_REQUEST['path'];
    $versioning = substr($path,0,10)=='versioning';

    if($versioning)
    {
        $path_parts = explode('/', $path);
        $file_id = $path_parts[1];
        $file = $files->get_file($file_id);
        $file['name']=utf8_basename($path);
    }else
    {       
        $file = $files->resolve_path($_REQUEST['path']);
    }
}
$public = substr($path,0,6)=='public';

$path = $GO_CONFIG->file_storage_path.$path;

$mode = isset($_REQUEST['mode'])  ? $_REQUEST['mode'] : 'download';

if(!$file || !file_exists($path))
{
    die('File not found: '.$path);
}

/*
 * Enable browser caching for public files. They expire in one day.
 */


/*
//add timestamp for caching
if(!isset($_REQUEST['mtime']))
{
    header('Location: '.$_SERVER['PHP_SELF'].'?path='.urlencode($_REQUEST['path']).'&mode='.$mode.'&mtime='.filemtime($path));
    exit();
}*/
if ($public || $files->has_read_permission($GO_SECURITY->user_id, $file['folder_id']))
{
    /*
     * Remove new_filelink
     */
    if(!$public)
    {
        $files->delete_new_filelink($file['id'], $GO_SECURITY->user_id);
    }

    $browser = detect_browser();

    $extension = File::get_extension($file['name']);

    header('Content-Length: '.filesize($path));
    header('Content-Transfer-Encoding: binary');

    header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime($path))." GMT");
    header("ETag: ".md5_file($path));


    if($public)
    {
        header("Expires: " . date("D, j M Y G:i:s ", time()+86400) . 'GMT');//expires in 1 day
        header('Cache-Control: cache');
        header('Pragma: cache');
    }

    if ($browser['name'] == 'MSIE')
    {
        header('Content-Type: application/download');
        if($mode == 'download')
        {
            header('Content-Disposition: attachment; filename="'.$file['name'].'"');
        }else
        {
            header('Content-Disposition: inline; filename="'.$file['name'].'"');
        }
        if(!$public)
        {
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
        }
    }else
    {
        header('Content-Type: '.File::get_mime($path));
        if($mode == 'download')
        {
            header('Content-Disposition: attachment; filename="'.$file['name'].'"');
        }else
        {
            header('Content-Disposition: inline; filename="'.$file['name'].'"');
        }
        if(!$public)
        {
            header('Pragma: no-cache');
        }
    }

    readfile($path);

    /*$fd = fopen($path,'rb');
    if($fd)
    {
        while (!feof($fd)) {
            print fread($fd, 32768);
        }
        fclose($fd);
    }else
    {
        trigger_error('Could not open '.$path, E_USER_ERROR);
    }*/

}else
{
    exit($lang['common']['accessDenied']);
}
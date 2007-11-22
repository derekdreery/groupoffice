<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.2 $ $Date: 2006/12/01 13:58:22 $
 *
   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 */

/**
 * Creates a table that uses tabs to show multiple screens
 * 
 * @package Framework
  * 
 * @access public
 */


$user_id = isset($user_id) ? $user_id : $GO_SECURITY->user_id;

require_once($GO_LANGUAGE->get_language_file('filesystem'));
require_once($GO_CONFIG->class_path."filesystem.class.inc");
$fs = new filesystem();

if($task == 'save_filesystem_settings')
{
	$fs_settings['user_id']=$user_id; 
	$fs_settings['notify'] =  isset($_POST['notify']) ? '1' : '0';	
	$fs_settings['open_properties'] =  isset($_POST['open_properties']) ? '1' : '0';	
	$fs_settings['use_gota'] =  isset($_POST['use_gota']) ? '1' : '0';
	$fs_settings['show_files_on_summary'] =  isset($_POST['show_files_on_summary']) ? '1' : '0';
	$fs->update_settings($fs_settings);
}else
{
	$fs_settings = $fs->get_settings($user_id);
}

$cb = new checkbox('notify','notify','1', $fs_notify, $fs_settings['notify']);
$tabstrip->add_html_element($cb);
$tabstrip->innerHTML .= '<br />';
$cb = new checkbox('open_properties','open_properties','1', $fs_open_properties, $fs_settings['open_properties']);
$tabstrip->add_html_element($cb);
$tabstrip->innerHTML .= '<br />';

$cb = new checkbox('show_files_on_summary','show_files_on_summary','1', $fs_show_files_on_summary, $fs_settings['show_files_on_summary']);
$tabstrip->add_html_element($cb);
$tabstrip->innerHTML .= '<br />';


$disabled = !file_exists($GO_MODULES->modules['filesystem']['path'].'jnlp.php');

if($disabled)
{
	$input = new input('hidden','use_gota','1');
	$tabstrip->add_html_element($input);
}

$cb = new checkbox('use_gota','use_gota','1', $fs_enable_gota, $fs_settings['use_gota'],$disabled);
$tabstrip->add_html_element($cb);
$tabstrip->innerHTML .= '<br />';



$tabstrip->add_html_element(new button($cmdSave, "javascript:_save('save_filesystem_settings');"));

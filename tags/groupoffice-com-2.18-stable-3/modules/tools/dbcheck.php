<?php
/**
 * @copyright Copyright &copy; Intermesh 2006
 * @version $Revision: 1.4 $ $Date: 2006/04/24 08:07:16 $
 * 
 * @author Merijn Schering <mschering@intermesh.nl>

   This file is part of Group-Office.

   Group-Office is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Group-Office is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Group-Office; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   
   
 * This script will make sure admins and owners have access to all items.


 * @package Tools
 * @subpackage DB check
 */

require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('tools');

require($GO_THEME->theme_path.'header.inc');
echo 'Checking link ID\'s...<br />';
$db2 = new db();
$db3 = new db();

$db = new db();
$db->Halt_On_Error = 'no';


if($GO_MODULES->get_module('addressbook'))
{
	$tables[]='ab_contacts';
	$tables[]='ab_companies';
}
if($GO_MODULES->get_module('calendar'))
{
	$tables[]='cal_events';
}
if($GO_MODULES->get_module('projects'))
{
	$tables[]='pmProjects';
}
if($GO_MODULES->get_module('notes'))
{
	$tables[]='no_notes';
}
if($GO_MODULES->get_module('users'))
{
	$tables[]='users';
}

if($GO_MODULES->get_module('billing'))
{
	$tables[]='bs_orders';
}

if($GO_MODULES->get_module('shipping'))
{
	$tables[]='sh_jobs';
	$tables[]='sh_packages';
}

if($GO_MODULES->get_module('updateserver'))
{
	$tables[]='us_licenses';
}

$count=0;
foreach($tables as $table)
{
	$sql = "SELECT id FROM $table WHERE link_id=0 OR ISNULL(link_id)";
	$db->query($sql);

	while($db->next_record())
	{
		$count++;
		$ud['id']=$db->f('id');
		$ud['link_id']=$GO_LINKS->get_link_id();

		$db2->update_row($table,'id', $ud);
	}


	$sql = "SELECT link_id FROM `$table` group by link_id having count(*) > 1;";
	$db->query($sql);
	while($db->next_record())
	{
		$first=true;
		$db2->query("SELECT id FROM $table WHERE link_id=".$db->f('link_id')." ORDER BY id ASC");
		while($db2->next_record())
		{
			if($first)
			{
				$first=false;
			}else {
				$ud['id']=$db2->f('id');
				$ud['link_id']=$GO_LINKS->get_link_id();

				$db3->update_row($table,'id', $ud);
				echo 'Updating duplicate link<br />';
			}
		}
	}

}
echo $count.' links added<br /><br />';


echo 'Checking ACL...<br />';

$sql = "SELECT * FROM acl_items";

$db->query($sql);
while($db->next_record())
{
	if(!$GO_SECURITY->group_in_acl($GO_CONFIG->group_root, $db->f('id')))
	{
		echo 'Adding admin group to '.$db->f('id').'<br />';
		$GO_SECURITY->add_group_to_acl($GO_CONFIG->group_root, $db->f('id'));
	}
	if(!$GO_SECURITY->user_in_acl($db->f('user_id'), $db->f('id')))
	{
		echo 'Adding owner to '.$db->f('id').'<br />';
		$GO_SECURITY->add_user_to_acl($db->f('user_id'), $db->f('id'));
	}
}
echo 'Done<br /><br />';

echo 'Resetting DB sequence...<br />';

$db->query("SHOW TABLES");

$tables = array();

while($db->next_record())
{
	if($db->f(0) != 'db_sequence')
	{
		$db2->query("SHOW FIELDS FROM `".$db->f(0)."`");
		while($db2->next_record())
		{
			if($db2->f('Field')=='id')
			{
				$tables[]=$db->f(0);
				break;
			}
		}
	}
}

foreach($tables as $table)
{
	$max=0;
	$sql = "SELECT max(id) FROM `$table`";
	$db->query($sql);
	$db->next_record();
	$max = $db->f(0);

	if(!empty($max))
	{
		$sql = "REPLACE INTO db_sequence VALUES ('$table', '$max');";
		$db->query($sql);

		echo 'Setting '.$table.'='.$max.'<br />';
	}
}
echo 'Done<br /><br />';


echo 'Optimizing tables<br />';

$db->query("SHOW TABLES");

$tables = array();

while($db->next_record())
{
	echo 'Optimizing: '.$db->f(0).'<br />';
	$db2->query('OPTIMIZE TABLE `'.$db->f(0).'`');
}
echo 'Done<br /><br />';


echo 'Clearing search cache<br />';

$db->query('TRUNCATE TABLE se_cache');
$db->query('TRUNCATE TABLE se_last_sync_times');

echo 'Done<br /><br />';

if(isset($GO_MODULES->modules['cms']))
{
	echo 'Checking CMS folder permissions<br />';
	require_once($GO_CONFIG->class_path.'filesystem.class.inc');
	$fs = new filesystem();

	require($GO_MODULES->modules['cms']['class_path'].'cms.class.inc');
	$cms = new cms();
	$cms->get_templates();
	while($cms->next_record())
	{
		$template_file_path = $GO_CONFIG->local_path.'cms/templates/'.$cms->f('id').'/';
		if(is_dir($template_file_path) && !$fs->find_share($template_file_path))
		{
			$fs->add_share($cms->f('user_id'), $template_file_path, 'template', $cms->f('acl_read'), $cms->f('acl_write'));
			echo 'Adding share for '.$template_file_path.'<br />';
		}
	}

	$cms->get_sites();
	while($cms->next_record())
	{
		$site_file_path = $GO_CONFIG->local_path.'cms/sites/'.$cms->f('id').'/';
		if(is_dir($site_file_path) && !$fs->find_share($site_file_path))
		{
			$fs->add_share($cms->f('user_id'), $site_file_path, 'template', $cms->f('acl_read'), $cms->f('acl_write'));
			echo 'Adding share for '.$site_file_path.'<br />';
		}
	}
	echo 'Done<br /><br />';
}
if(isset($GO_MODULES->modules['filesystem']))
{

	echo 'Checking filesystem links...<br />';
	require_once($GO_CONFIG->class_path.'filesystem.class.inc');
	$fs = new filesystem(true);
	$fs2 = new filesystem(true);

	$fs->get_latest_files();

	while($fs->next_record())
	{
		if(file_exists($fs->f('path')))
		{
			$file['link_id']=$fs->f('link_id');
			$file['mtime']=filemtime($fs->f('path'));
			$file['ctime']=filectime($fs->f('path'));

			$fs2->update_row('fs_links','link_id',$file);
		}else {
			$fs2->query("DELETE FROM fs_links WHERE link_id=".$fs->f('link_id'));
		}
	}
	echo 'Done<br /><br />';
	
	
	
	echo 'Linking file structure...<br />';
	
	function link_files($path)
	{
		
		$fs = new filesystem(true);
		
		$files = $fs->get_files($path);
		
		foreach($files as $file)
		{
			$fs->get_link_id(addslashes($file['path']));
		}
		
		$folders = $fs->get_folders($path);
		
		foreach($folders as $folder)
		{
			link_files($folder['path']);
		}		
	}
	
	link_files($GO_CONFIG->file_storage_path);
	echo 'Done<br /><br />';
}

echo 'All Done!<br />';

load_basic_controls();
$button = new button($cmdClose, "javascript:document.location='index.php';");
echo $button->get_html();
require($GO_THEME->theme_path.'footer.inc');
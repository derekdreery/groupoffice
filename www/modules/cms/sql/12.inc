<?php
require_once($GO_MODULES->modules['cms']['class_path'].'cms.class.inc');
$cms = new cms();


//get the language file
require_once($GO_LANGUAGE->get_language_file('cms'));

$db1 = new db();
$db2 = new db();
$db2->Halt_On_Error = 'no';

$sql = "CREATE TABLE `cms_languages` (
  `id` int(11) NOT NULL default '0',
  `site_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `title` varchar(50) NOT NULL default '',
  `description` text NOT NULL,
  `keywords` text NOT NULL,
  `image_url` varchar(255) NOT NULL default '',
  `sort_order` int(11) NOT NULL default '0',
  `root_folder_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `site_id` (`site_id`)
) TYPE=MyISAM;";

$db2->query($sql);


$sql = "SELECT id FROM cms_templates";
$db1->query($sql);
while($db1->next_record())
{
	$sql = "SELECT id FROM cms_template_items WHERE template_id='".$db1->f('id')."' AND main='1'";
	$db2->query($sql);
	if($db2->next_record())
	{
		$cms->set_template_items($db1->f('id'), $db2->f('id'), 0);
	}
}

$sql = "SELECT * FROM cms_sites";
$db1->query($sql);
while($db1->next_record())
{	
	$language_id = $db2->nextid('cms_languages');	  	
	if($language_id > 0)
	{
		$sql = "INSERT INTO cms_languages (id, site_id, name, title, description, keywords, root_folder_id) ".
			"VALUES ('$language_id', '".$db1->f('id')."', '$cms_default', '".$db1->f('name')."', '".$db1->f('description')."', '".$db1->f('keywords')."', '".$db1->f('root_folder_id')."')";		  				
		$db2->query($sql);
	}
	$sql = "UPDATE cms_search_words SET site_id='$language_id' WHERE site_id='".$db1->f('id')."'";
	$db2->query($sql);
}

$db2->query('ALTER TABLE cms_templates DROP main');
$db2->query("ALTER TABLE `cms_search_words` CHANGE `site_id` `language_id` INT( 11 ) DEFAULT '0' NOT NULL");
$db2->query('ALTER TABLE `cms_sites` DROP `name` ,
DROP `description` ,
DROP `root_folder_id`,
DROP `keywords` ;');

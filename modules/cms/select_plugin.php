<?php
/*
   Copyright Intermesh 2003
   Author: Merijn Schering <mschering@intermesh.nl>
   Version: 1.0 Release date: 08 July 2003

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.
 */


require_once ("../../Group-Office.php");

load_basic_controls();

//authenticate the user
$GO_SECURITY->authenticate();

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('cms');

//get the language file
require_once ($GO_LANGUAGE->get_language_file('cms'));

	

require_once ($GO_MODULES->class_path.'cms.class.inc');
$cms = new cms();




require_once ($GO_THEME->theme_path."header.inc");

$form = new form('select_plugin_form');

$tabstrip = new tabstrip('select_plugin_tabstrip', $cms_insert_plugin);
$tabstrip->set_attribute('style','width:100%');

$plugins = $cms->get_plugins();
foreach($plugins as $plugin)
{
	$plugin_class = 'cms_'.$plugin;
	require_once($GO_MODULES->path.'plugins/'.$plugin.'.class.inc');
	
	if(file_exists($GO_MODULES->path.'plugins/dialog/'.$plugin.'.php'))
	{
		@$plugin_obj=new $plugin_class();
		if($plugin_obj->has_permission())
		{
			$link = new hyperlink('plugins/dialog/'.$plugin.'.php' ,$plugin_obj->get_name());
			$link->set_attribute('class','selectableItem');
			$tabstrip->add_html_element($link);
		}
	}
}

$form->add_html_element($tabstrip);

echo $form->get_html();
?>
<script type="text/javascript" language="javascript">

function _insertHyperlink(url, name)
{
  window.opener.SetUrl(url, "", "", name);
  window.close();
}

document.onblur = function() {
  setTimeout('self.focus()',100);
}

</script>
<?php
require_once ($GO_THEME->theme_path."footer.inc");

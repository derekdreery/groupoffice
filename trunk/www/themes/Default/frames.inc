<?php
/**
 * @copyright Copyright Intermesh 2006
 * @version $Revision: 1.2 $ $Date: 2006/11/23 11:34:44 $
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
      
 * @package Addressbook
 * @category Addressbook
 */


require($GO_THEME->theme_path.'page_header.inc');


$link = new html_element('link');
$link->set_attribute('rel','stylesheet');
$link->set_attribute('type','text/css');
$link->set_attribute('href',$GO_CONFIG->host.'ext/resources/css/ext-all.css');
$head->add_html_element($link);


$script = new html_element('script');
$script->set_attribute('type','text/javascript');
$script->set_attribute('src',$GO_CONFIG->host.'ext/adapter/yui/yui-utilities.js');
$head->add_html_element($script);

$script = new html_element('script');
$script->set_attribute('type','text/javascript');
$script->set_attribute('src',$GO_CONFIG->host.'ext/adapter/yui/ext-yui-adapter.js');
$head->add_html_element($script);

$script = new html_element('script');
$script->set_attribute('type','text/javascript');
$script->set_attribute('src',$GO_CONFIG->host.'ext/ext-all-debug.js');
$head->add_html_element($script);



$head->innerHTML .= '
	<style type="text/css">
	.x-layout-panel-north {
	    border:0px none;
	}
	#nav {
	}
	#autoTabs, #west {
	    padding:10px;
	}
	#north{
		padding:0px;
		background-color: #22437f;
		font:normal 8pt arial, helvetica;
	}
	#south{
	    font:normal 8pt arial, helvetica;
	    padding:4px;
	}
	.x-layout-panel-center p {
	    margin:5px;
	}
	#props-panel .x-grid-col-0{
	}
	#props-panel .x-grid-col-1{
	}
	</style>';

$script = new html_element('script');
$script->set_attribute('type','text/javascript');
$script->set_attribute('src',$GO_CONFIG->host.'groupoffice.js');
$head->add_html_element($script);


/*$script = new html_element('script');
$script->set_attribute('type','text/javascript');
$script->set_attribute('src',$GO_CONFIG->host.'search.js');
$head->add_html_element($script);*/




$containerdiv = new html_element('div');
$containerdiv->set_attribute('id','container');

$westdiv = new html_element('div');
$westdiv->set_attribute('id','west');
$westdiv->set_attribute('class','x-layout-inactive-content');


$northwestdiv = new html_element('div');
$northwestdiv->set_attribute('id','northwest');
$westdiv->add_html_element($northwestdiv);


$southwestdiv = new html_element('div');
$southwestdiv->set_attribute('id','southwest');

$toolbar = new html_element('div');
$toolbar->set_attribute('id','mainmenu-tb');
$toolbar->set_attribute('class','mainmenu');



foreach($GO_MODULES->modules as $module)
{
	$GO_THEME->load_module_theme($module['id']);
	$GO_THEME->images[$module['id']] = isset($GO_THEME->images[$module['id']]) ? $GO_THEME->images[$module['id']] : $GO_THEME->images['unknown_module'];

	//require language file to obtain module name in the right language
	$language_file = $GO_LANGUAGE->get_language_file($module['id']);
	if(file_exists($language_file))
	{
		require_once($language_file);
	}
	$lang_var = isset($lang_modules[$module['id']]) ? $lang_modules[$module['id']] : $module['id'];
	
	$img = new image($module['id']);
	$img->set_attribute('style','border:0px;width:22px;height:22px;margin-right:5px;');
	$img->set_attribute('align','absmiddle');

	//$link = new hyperlink('#', $lang_var);
	$link = new hyperlink($module['url'], $img->get_html().$lang_var);
	$link->set_attribute('target','mainframe');
	//$link->set_attribute('onclick','GroupOffice.setCenterUrl(\''.$module['url'].'\');');
	$link->set_attribute('ext:qtip','Some info about the module');
	$link->set_attribute('ext:qtitle',$module['id']);
	$link->set_attribute('class','menu-button');
	
	$toolbar->add_html_element($link);
	
}
$southwestdiv->add_html_element($toolbar);
$westdiv->add_html_element($southwestdiv);

$northdiv = new html_element('div');
$northdiv->set_attribute('id','north');

$img = new image('',$GO_CONFIG->host.'lib/go-icon.png');
$img->set_attribute('style','text-align:middle;vertical-align:middle;border:0px;padding:2px;');


$table = new table();
//$table->set_attribute('style','width:100%');
$table->set_attribute('id','headerTable');

$row = new table_row();
$cell = new table_cell($img->get_html().$strLoggedInAs.' '.htmlspecialchars($_SESSION['GO_SESSION']['name']));
$cell->set_attribute('style', 'width:15%');
$row->add_cell($cell);

$iframe = new html_element('iframe',' ');
$iframe->set_attribute('style','height:20px;width:100%;border:0;');
$iframe->set_attribute('frameborder','0');
$iframe->set_attribute('scrolling','no');
$iframe->set_attribute('name','checker');
$iframe->set_attribute('ALLOWTRANSPARENCY','true');
$iframe->set_attribute('src',$GO_CONFIG->host.'checker.php');

$cell = new table_cell($iframe->get_html());
$cell->set_attribute('style', 'text-align:right;width:70%');
$row->add_cell($cell);

$cell = new table_cell();
$cell->set_attribute('style', 'text-align:right;width:15%');

$input = new input('text','query',$cmdSearch.'...');
$input->set_attribute('id','query');
$input->set_attribute('onfocus',"javascript:this.value='';");
$input->set_attribute('onblur',"javascript:this.value='".$cmdSearch."...';");
$input->set_attribute('onkeypress',"return search(event);");

$img = new image('search');
$img->set_attribute('style','border:0px;margin-left:10px;margin-right:3px;');
$img->set_attribute('align','absmiddle');

$cell->add_html_element($img);
$cell->add_html_element($input);

$img = new image('configuration');
$img->set_attribute('style','border:0px;margin-right:3px;');
$img->set_attribute('align','absmiddle');

$link = new hyperlink($GO_CONFIG->host.'configuration/',$img->get_html().$menu_configuration);
$link->set_attribute('target','mainframe');

$cell->add_html_element($link);

$img = new image('help');
$img->set_attribute('style','border:0px;margin-right:3px;');
$img->set_attribute('align','absmiddle');

$link = new hyperlink($GO_CONFIG->host.'help.php',$img->get_html().$menu_help);
$link->set_attribute('target','_blank');

$cell->add_html_element($link);

$img = new image('logout');
$img->set_attribute('style','border:0px;margin-right:3px;');
$img->set_attribute('align','absmiddle');

$link = new hyperlink($GO_CONFIG->host.'index.php?task=logout',$img->get_html().$menu_logout);
$link->set_attribute('target','_top');

$cell->add_html_element($link);

$row->add_cell($cell);
$table->add_row($row);



$northdiv->add_html_element($table);


$centerdiv = new html_element('div');
$centerdiv->set_attribute('id','center');

$iframe = new html_element('iframe');
$iframe->set_attribute('id', 'mainframe');
$iframe->set_attribute('name', 'mainframe');
$iframe->set_attribute('frameborder', '0');
$iframe->set_attribute('src',$start_url);
$iframe->set_attribute('style', 'width:100%;height:100%');

$centerdiv->add_html_element($iframe);

$eastdiv = new html_element('div');
$eastdiv->set_attribute('id','east');


$containerdiv->add_html_element($northdiv);
$containerdiv->add_html_element($westdiv);
$containerdiv->add_html_element($eastdiv);
$containerdiv->add_html_element($centerdiv);

/*
$propsdiv = new html_element('div');
$propsdiv->set_attribute('id','props-panel');
$propsdiv->set_attribute('class','x-layout-inactive-content');
$propsdiv->set_attribute('style','width:200px;height:200px;overflow:hidden;');
//$containerdiv->add_html_element($propsdiv);*/

$body->add_html_element($containerdiv);

$dialogdiv = new html_element('div');
$dialogdiv->set_attribute('id','dialog');
$dialogdiv->set_attribute('style','visibility:hidden;height:100%;width:100%;');
/*
$subdiv = new html_element('div');
$subdiv->set_attribute('id','dialogbody');
$subdiv->set_attribute('style','height:100%;width:100%;');
$subdiv->set_attribute('class','x-dlg-bd');


$tabdiv = new html_element('div');
$tabdiv->set_attribute('id','dialogtab');
$tabdiv->set_attribute('style','height:100%;width:100%;');
$tabdiv->set_attribute('class','x-dlg-tab');


$iframe = new html_element('iframe');
$iframe->set_attribute('id', 'dialogframe');
$iframe->set_attribute('name', 'dialogframe');
$iframe->set_attribute('frameborder', '0');
$iframe->set_attribute('style', 'width:100%;height:100%');
//$tabdiv->add_html_element($iframe);

$subdiv->add_html_element($iframe);

$dialogdiv->add_html_element($subdiv);
*/
$iframe = new html_element('iframe');
$iframe->set_attribute('id', 'dialogframe');
$iframe->set_attribute('name', 'dialogframe');
$iframe->set_attribute('frameborder', '0');
$iframe->set_attribute('style', 'width:100%;height:100%');
//$dialogdiv->add_html_element($iframe);

$body->add_html_element($dialogdiv);


$body->innerHTML .= '
<script type="text/javascript">

function search(e)
{
	var keynum;	
	var input;
	input = Ext.get("query");
	
	if(window.event) // IE
	{
		keynum = e.keyCode
	}else if(e.which) // Netscape/Firefox/Opera
	{
		keynum = e.which
	}
	
	if(keynum==13)
	{
		GroupOffice.search(input.getValue());
	}
	return true;
}

</script>';

require($GO_THEME->theme_path.'page_footer.inc');

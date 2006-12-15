<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.2 $ $Date: 2006/11/21 16:25:35 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once("../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_HEADER['nomessages'] = true;
require_once($GO_THEME->theme_path."header.inc");

load_basic_controls();

$id = smart_stripslashes($_REQUEST['id']);
$name = smart_stripslashes($_REQUEST['name']);
$form_name = smart_stripslashes($_REQUEST['form_name']);
$max_cols=18;

$table = new table();
$table->set_attribute('style', 'border:1px solid black;background-color:#fff');
// $table->set_attribute('onblur', 'javascript:close_colors(\''.$attributes['id'] .'\');');

$row = new table_row();

$colors=array();
$colors[] = '000000';
$colors[] = '009900';
$colors[] = '00ff00';
$colors[] = '0000ff';
$colors[] = '00ffff';
$colors[] = '660033';
$colors[] = '660099';
$colors[] = '6666ff';
$colors[] = '66ff99';
$colors[] = 'cc0099';
$colors[] = 'cc99ff';
$colors[] = '996600';
$colors[] = '999900';
$colors[] = 'ff0000';
$colors[] = 'ff6600';
$colors[] = 'ffff00';
$colors[] = 'ff9966';
$colors[] = 'ff9900';
$colors[] = 'ffff99';
$colors[] = 'ffffff';
	


$cols=0;
$row = new table_row();  	

for($a=0;$a<count($colors);$a+=1)
{    	
	$cols++;
	$cell = new table_cell();

	$link = new hyperlink('javascript:select_color(\''.$id.'\', \''.$form_name.'\', \''.$name.'\', \''.$colors[$a].'\');', '&nbsp;');
	$link->set_attribute('style', 'display:block;border:1px solid;#ccccc;height:16px;width:16px;background-color:'.$colors[$a]);
	//$link->set_attribute('id', 'color_'.$attributes['id'].'_'.$colors[$a]);
	
	$cell->add_html_element($link);
	$row->add_cell($cell);	    		
	
	if($cols==$max_cols)
	{
		$table->add_row($row);
		$row = new table_row(); 
		$cols=0;
	}	    	
}

$row = new table_row();
$cell = new table_cell('&nbsp;');
$cell->set_attribute('colspan', '99');
$cell->set_attribute('style', 'height:10px;');
$row->add_cell($cell);
$table->add_row($row);


$row = new table_row();


$colors = array('00','33','66','99','cc','ff');   



$cols=0;
$row = new table_row();  	

for($a=0;$a<count($colors);$a+=1)
{
	for($b=0;$b<count($colors);$b+=1)
	{    		
		for($c=0;$c<count($colors);$c+=1)
		{
			$cols++;
			$color = ''.$colors[$a].$colors[$c].$colors[$b];
    		$cell = new table_cell();
    		
    		$link = new hyperlink('javascript:select_color(\''.$id.'\', \''.$form_name.'\', \''.$name.'\', \''.$color.'\');', '&nbsp;');
			$link->set_attribute('style', 'display:block;border:1px solid;#ccccc;height:16px;width:16px;background-color:#'.$color);
			
			$cell->add_html_element($link);
			$row->add_cell($cell);	    		
			
			if($cols==$max_cols)
			{
				$table->add_row($row);
				$row = new table_row(); 
				$cols=0;
			}
    	}
    }	    
}


echo $table->get_html();
?>
<script type="text/javascript">
function select_color(id, form_name, element_name, color)
{
	var link = opener.document.getElementById('color_link_'+id);
	opener.document.forms[form_name].elements[element_name].value=color;
	link.style.backgroundColor='#'+color;
	window.close();
}
</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
?>
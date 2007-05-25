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

require_once("Group-Office.php");
$GO_SECURITY->authenticate();
load_basic_controls();

$script='';
foreach($link_types as $link_type=>$name)
{
	$links = $GO_LINKS->get_links($_REQUEST['link_id'], $link_type);
	
	if(count($links))
	{
		$p = new html_element('h3',$name);
		echo $p->get_html();
		$div = new html_element('div');
		$div->set_attribute('id','link_type_'.$link_type);
		
		echo $div->get_html().' <hr>';
		$script .= "linkGrids.push(new GroupOffice.linksGrid('link_type_".$link_type."', {link_id: ".$_REQUEST['link_id'].", link_type: ".$link_type."}));";				
	}
}
?>
<script type="text/javascript">
var linkGrids = [];
<?php echo $script; ?>

for (var i = 0;i<linkGrids.length;i++)
{
	linkGrids[i].render();
}
Note.setLinkGrids(linkGrids);
</script>
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
for($link_type=1;$link_type<10;$link_type++)
{
	$links = $GO_LINKS->get_links($_REQUEST['link_id'], $link_type);
	
	if(count($links))
	{
		$p = new html_element('p','Type');
		$p->set_attribute('style','font-weight:bold');
		echo $p->get_html();
		$div = new html_element('div');
		$div->set_attribute('id','link_type_'.$link_type);
		
		echo $div->get_html();
		$script .= "linksGrid = new GroupOffice.linksGrid('link_type_".$link_type."', {link_id: ".$_REQUEST['link_id'].", link_type: ".$link_type."});linksGrid.render();";				
	}
}
?>
<script type="text/javascript">
<?php echo $script; ?>
</script>
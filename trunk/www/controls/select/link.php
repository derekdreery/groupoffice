<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.3 $ $Date: 2006/05/31 09:32:49 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once("../../Group-Office.php");

unset($_SESSION['GO_HANDLER']);
session_unregister('GO_HANDLER');

$charset = isset($charset) ? $charset : 'UTF-8';
header('Content-Type: text/html; charset='.$charset);


$values = isset($_POST['search_table']['selected']) ? $_POST['search_table']['selected'] : array();

$link_type=smart_stripslashes($_REQUEST['link_type']);
$link_id=smart_stripslashes($_REQUEST['link_id']);
$opener_action=smart_stripslashes($_REQUEST['opener_action']);


require_once($GO_CONFIG->class_path.'/base/search.class.inc');
$search = new search();

foreach($values as $selected_link_id)
{
	$search_result = $search->get_search_result($GO_SECURITY->user_id, $selected_link_id);
	
	$GO_LINKS->add_link($search_result['link_id'], $search_result['link_type'], $link_id, $link_type);
}


?>
<html>
<body onload="javascript:<?php if(!empty($opener_action)) echo base64_decode($opener_action); ?>window.close();">
</body>
</html>

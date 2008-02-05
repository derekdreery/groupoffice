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


$values = isset($_POST['global_select_table']['selected']) ? $_POST['global_select_table']['selected'] : array();

$control_name=smart_stripslashes($_REQUEST['name']);
$link_id=0;
$link_type=0;
$link_text='';
$form_name=smart_stripslashes($_REQUEST['form_name']);
if(isset($values[0]))
{
	require_once($GO_CONFIG->class_path.'/base/search.class.inc');
	$search = new search();

	$search_result = $search->get_search_result($GO_SECURITY->user_id, $values[0]);
	

	$link_id=$search_result['link_id'];
	$link_type=$search_result['link_type'];
	$link_text=$search_result['name'];
}
?>
<html>
<head>
<script type="text/javascript">
function set_link()
{
	var form = opener.document.forms['<?php echo $form_name; ?>'];
	form.elements['<?php echo $control_name; ?>[link_id]'].value='<?php echo $link_id; ?>';
	form.elements['<?php echo $control_name; ?>[link_type]'].value='<?php echo $link_type; ?>';
	form.elements['<?php echo $control_name; ?>[text]'].value='<?php echo addslashes($link_text); ?>';
	window.close();
}
</script>
</head>
<body onload="javascript:set_link();">
</body>
</html>

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
require_once("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('addressbook');

require_once($GO_MODULES->class_path."addressbook.class.inc");
$ab = new addressbook();

$contacts = isset($_POST['select_table']['selected']) ? $_POST['select_table']['selected'] : array();

for($i=0;$i<sizeof($contacts);$i++)
{
	$ab->add_contact_to_company($contacts[$i], $_REQUEST['company_id']);
}

?>
<html>
<body>
<script language="javascript" type="text/javascript">
        opener.document.company_form.submit();
					window.close();
</script>
</body>
</html>

<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.13 $ $Date: 2006/04/10 13:21:11 $

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

if($_POST['multiselect'] == 'true')
{
	$values = isset($_POST['select_table']['selected']) ? $_POST['select_table']['selected'] : array();

	$values = array_unique($values);
	$values_string = implode(',',$values);		
	?>
	<html>
	<body>
	<script language="javascript" type="text/javascript">
	        opener.<?php echo $_REQUEST['GO_FIELD']; ?>.value = '<?php echo $values_string; ?>';
	        window.close();
	</script>
	</body>
	</html>
	<?php
}else
{
	$value = $_POST['select_table']['selected'][0];
	
	switch($_REQUEST['search_type'])
	{		
		case 'user':
			if($user = $GO_USERS->get_user($value))
			{	
				$middle_name = $user['middle_name'] == '' ? '' : $user['middle_name'].' ';
				$name_field_value = $user['first_name'].' '.$middle_name.$user['last_name'];
			}
		break;
		
		case 'contact':
			$GO_MODULES->authenticate('addressbook');
			
			require_once($GO_MODULES->class_path."addressbook.class.inc");
			$ab = new addressbook();
		
			if($contact = $ab->get_contact($value))
			{	
				$middle_name = $contact['middle_name'] == '' ? '' : $contact['middle_name'].' ';
				$name_field_value = $contact['first_name'].' '.$middle_name.$contact['last_name'];
			}
		break;
		
		case 'company':
			$GO_MODULES->authenticate('addressbook');
			require_once($GO_MODULES->class_path."addressbook.class.inc");
			$ab = new addressbook();
		
			if($company = $ab->get_company($value))
			{		
				$name_field_value = $company['name'];
			}
		break;
		
		case 'project':
			$GO_MODULES->authenticate('projects');
			require_once($GO_MODULES->class_path."projects.class.inc");
			$projects = new projects();
		
			if($project = $projects->get_project($value))
			{
				$name_field_value = $project['description'] == '' ? $project['name'] : $project['name'].' ('.$project['description'].')';
			}
		break;
		
		case 'file':		
			$name_field_value = basename($value);
		break;
	}
	?>
	<html>
	<body>
	<script language="javascript" type="text/javascript">
		opener.<?php echo smart_stripslashes($_REQUEST['id_field']); ?>.value = '<?php echo $value; ?>';
		opener.<?php echo smart_stripslashes($_REQUEST['type_field']); ?>.value = '<?php echo $_REQUEST['search_type']; ?>';
		opener.<?php echo smart_stripslashes($_REQUEST['name_field']); ?>.value = "<?php echo $name_field_value; ?>";
		window.close();
	</script>
	</body>
	</html>
	<?php
}
?>

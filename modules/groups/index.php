<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 * @author Boy Wijnmaalen <bwijnmaalen@intermesh.nl>
 */

require_once("../../Group-Office.php");
$GLOBALS['GO_SECURITY']->html_authenticate('groups');
require_once($GLOBALS['GO_LANGUAGE']->get_language_file('groups'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

	<title>
		<?php echo $GLOBALS['GO_CONFIG']->title.' - '.$lang['groups']['name']; ?>
	</title>
	<?php	
		require($GLOBALS['GO_CONFIG']->root_path.'default_head.inc');
		require($GLOBALS['GO_CONFIG']->root_path.'default_scripts.inc');		
	?>
	
	
</head>
<body>
</body>
</html>  
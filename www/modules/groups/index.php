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
GO::security()->html_authenticate('groups');
require_once(GO::language()->get_language_file('groups'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

	<title>
		<?php echo GO::config()->title.' - '.$lang['groups']['name']; ?>
	</title>
	<?php	
		require(GO::config()->root_path.'default_head.inc');
		require(GO::config()->root_path.'default_scripts.inc');		
	?>
	
	
</head>
<body>
</body>
</html>  
<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @version $Id: invitation.php 7752 2011-07-26 13:48:43Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

//require_once(GO::config()->root_path."Group-Office.php");

extract($data);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
</head>
<body>

	<?php
	
	if($participant->status==GO_Calendar_Model_Participant::STATUS_ACCEPTED){	
		?>
		<p>You have accepted the event.</p>
		<?php
		if($event){
			?>
			<p>The event has been scheduled in your default calendar.</p>
			<?php
		}
	}else
	{
		?>
		<p>You have declined the event.</p>
		<?php
	}
	?>

</body>
</html>
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
	<head>
		<link href="<?php echo GO::config()->host; ?>views/Extjs3/themes/Default/external.css" type="text/css" rel="stylesheet" />
		<?php
		
		$theme = GO::user() ? GO::user()->theme : GO::config()->theme;
		
		if($theme!='Default'){
			?>
			<link href="<?php echo GO::config()->host; ?>views/Extjs3/themes/<?php echo $theme; ?>/external.css" type="text/css" rel="stylesheet" />
			<?php
		}
		if(!empty($GLOBALS['GO_CONFIG']->custom_css_url))
			echo '<link href="'.$GLOBALS['GO_CONFIG']->custom_css_url.'" type="text/css" rel="stylesheet" />';
		?>
		<title><?php echo GO::config()->title; ?></title>
	</head>
<body>
	<div id="container">
	<?php
	
	if($participant->status==GO_Calendar_Model_Participant::STATUS_ACCEPTED){	
		?>
		<p><?php echo GO::t('eventAccepted','calendar'); ?></p>		
	}else
	{
		?>
		<p><?php echo GO::t('eventDeclined','calendar'); ?></p>
		<?php
	}
	
	if($event){
		?>
		<p><?php echo sprintf(GO::t('eventScheduledIn','calendar'),$event->calendar->name, $participant->statusName); ?></p>
		<?php
	}
	?>
	</div>
</body>
</html>
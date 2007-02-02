<?php
/**
 * @copyright Intermesh 2004
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.52 $ $Date: 2006/11/22 09:35:30 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
require_once('Group-Office.php');
$GO_SECURITY->authenticate();
?>
<html>
<head>
<script language="javascript" type="text/javascript">
<!-- Centring window Javascipt mods By Muffin Research Labs//-->

function popup(url,w,h,target)
{
	var centered;
	x = (screen.availWidth - w) / 2;
	y = (screen.availHeight - h) / 2;
	centered =',width=' + w + ',height=' + h + ',left=' + x + ',top=' + y + ',scrollbars=yes,resizable=no,status=no';
	popup = window.open(url, target, centered);
	if (!popup.opener) popup.opener = self;
	popup.focus();
}
</script>
<title><?php echo $GO_CONFIG->title; ?>
</title>
<?php
echo '<meta http-equiv="refresh" content="'.$GO_CONFIG->refresh_rate.';url='.$_SERVER['PHP_SELF'].'?initiated=true">';
$height = 0;
//if user uses the calendar then check for events to remind
$calendar_module = isset($GO_MODULES->modules['calendar']) ? $GO_MODULES->modules['calendar'] : false;
if ($calendar_module && $GO_MODULES->modules['calendar']['read_permission'])
{
	require_once($calendar_module['class_path'].'calendar.class.inc');
	$cal = new calendar();
	if($remind_events = $cal->get_events_to_remind($GO_SECURITY->user_id))
	{
		$height += 200+(20*$remind_events);
	}
}else
{
	$remind_events = false;
}

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';


$_SESSION['notified_new_mail'] = isset($_SESSION['notified_new_mail']) ? $_SESSION['notified_new_mail'] : 0;
$_SESSION['new_mail'] = 0;
$remind_email = false;
if(($_SESSION['GO_SESSION']['start_module'] != 'summary' && $_SESSION['GO_SESSION']['start_module'] != 'email')
|| isset($_REQUEST['initiated']))
{
	//check for email
	if (isset($GO_MODULES->modules['email']) && $GO_MODULES->modules['email']['read_permission'])
	{
		require_once($GO_MODULES->modules['email']['class_path'].'email.class.inc');
		require_once($GO_CONFIG->class_path.'mail/imap.class.inc');
		$imap = new imap();
		$email1 = new email();
		$email2 = new email();

		$email1->get_accounts($GO_SECURITY->user_id);

		while($email1->next_record())
		{
			if ($email1->f('auto_check') == '1')
			{
				$account = $email1->Record;

				if ($imap->open($account['host'], $account['type'],$account['port'],$account['username'],$account['password'], '', 0, $account['use_ssl'], $account['novalidate_cert']))
				{
					if ($account['type'] == 'imap')
					{
						if($status = $imap->status('INBOX'))
						{
							if ($status->unseen > 0)
							{
								$_SESSION['new_mail'] += $status->unseen;
							}
						}
						$email2->get_subscribed($email1->f('id'));
						while($email2->next_record())
						{
							if($email2->f('name') != 'INBOX')
							{
								if($status = $imap->status($email2->f('name')))
								{
									if ($status->unseen > 0)
									{
										$_SESSION['new_mail'] += $status->unseen;
									}
								}
							}
						}
					}else
					{
						$status = $imap->status('INBOX');
						$_SESSION['unseen_in_mailbox'][$account['id']] = $status->unseen;
						$_SESSION['new_mail'] += $status->unseen;
					}
				}else
				{
					$email2->disable_auto_check($account['id']);
					echo '<script language="javascript" type="text/javascript">alert("'.$account['host'].' '.$ml_host_unreachable.'");</script>';
				}
			}
		}

		if ($_SESSION['new_mail'] > 0 && $_SESSION['new_mail'] > $_SESSION['notified_new_mail'])
		{
			$remind_email = true;
			$height += 120;
		}
		if($_SESSION['notified_new_mail'] >  $_SESSION['new_mail'])
		{
			$_SESSION['notified_new_mail'] =  $_SESSION['new_mail'];
		}
	}
}

if ($remind_events ||  $remind_email)
{
	if($height > 600) $height = 600;

	echo '<script language="javascript" type="text/javascript">'.
	'popup("'.$GO_CONFIG->control_url.'reminder.php", "600", "'.$height.'", "reminder");'.
	'</script>';
}
?>
<link rel="shortcut icon" href="<?php echo $GO_CONFIG->host; ?>lib/favicon.ico" />
</head>
<body></body>
</html>

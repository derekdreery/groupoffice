<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<meta name="robots" content="none,noindex,nofollow" />
<title><?php echo $lang['mailings']['unsubscribe']; ?></title>
<link href="template/stylesheet.css" rel="stylesheet" type="text/css" />
<link href="template/buttons.css" rel="stylesheet" type="text/css" />
<link href="template/tabs.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div class="main-container">    
	<div class="header">
		<img src="<?php echo $GLOBALS['GO_MODULES']->modules['mailings']['url'].'extern/template/images/groupoffice.png'; ?>" alt="Group-Office logo" />
	</div>

	<div class="hoofd-kader">		
		<div class="hoofd-kader-menu">		
			<div id="topmenu-item-center_81" class="topmenu-item-center topmenu-item-center_0"></div>		
		</div>
	<div class="hoofd-kader-top"></div>			
		<div class="hoofd-kader-center">
			<div class="subkader-big-top">
				<div class="subkader-big-bottom">
					<div class="subkader-big-center" id="ticket-messages-container">
<?php
	
	if(!$delete_success)
	{
		echo $lang['mailings']['r_u_sure'];
?>			
		<div id="msg-form">
		<form method="post" action="unsubscribe.php">
		<input type="hidden" name="hash" value="<?php echo $data['hash'];?>" />
		<input type="hidden" name="recipient_type" id="recipient_type" value="<?php echo $data['recipient_type'];?>" />
		<input type="hidden" name="recipient_id" id="recipient_id" value="<?php echo $data['recipient_id'];?>" />
		<input type="hidden" name="addresslist_id" id="addresslist_id" value="<?php echo $data['addresslist_id'];?>" /><br />
		<input type="submit" name="unsubscribe" value="<?php echo $lang['mailings']['unsubscribe']; ?>" />
		</form>
		</div>
<?php
} else {
?>
		<div id="delete-success">
			<?php echo $lang['mailings']['delete_success'] ?>
		</div>
<?php
}
?>
		</div>
	</div>
</div>	
									
		</div>
		<div class="hoofd-kader-bottom"></div>
	</div>
</div>
</body>
</html>
<?php
require('../../Group-Office.php');
$GO_SECURITY->authenticate();
$charset = isset($charset) ? $charset : 'UTF-8';
header('Content-Type: text/html; charset='.$charset);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset; ?>">
<script language="javascript" type="text/javascript" src="<?php echo $GO_CONFIG->host; ?>javascript/common.js"></script>
<?php require($GO_CONFIG->control_path.'fixpng.inc'); ?>
<title><?php echo $GO_CONFIG->title; ?>
</title>
<link href="<?php echo $GO_THEME->theme_url.'css/common.css'; ?>" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="<?php echo $GO_CONFIG->host; ?>lib/favicon.ico" />
</head>
<body style="padding:0px;margin:0px;" dir="<?php echo $htmldirection; ?>">
<form name="footer_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="active_module" value="<?php echo $_SESSION['GO_SESSION']['active_module']; ?>" />
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#F6F7F2">
<tr>
	<td width="100%" height="50" align="left">
	&nbsp;<a target="main" href="<?php echo $GO_CONFIG->host; ?>about.php" title="<?php echo $menu_about; ?>">
	<img src="<?php echo $GO_THEME->images['go_header']; ?>" border="0" />
	</a>
	</td>
</tr>
<tr height="25">
	<td colspan="2" class="HeaderBar">

			<a class="HeaderBar" href="<?php echo $GO_CONFIG->host; ?>configuration/" target="main">
			<img src="<?php echo $GO_THEME->images['configuration']; ?>" width="16" height="16" border="0" align="absmiddle" />
			<?php echo $menu_configuration; ?>
			</a>

      <a class="HeaderBar" href="javascript:popup('<?php echo $GO_CONFIG->host; ?>doc/index.php', 750, 500);" target="main">
				<img src="<?php echo $GO_THEME->images['help']; ?>" width="16" height="16" border="0" align="absmiddle" />
				<?php echo $menu_help; ?>
      </a>


			<a class="HeaderBar" href="<?php echo $GO_CONFIG->host; ?>index.php?task=logout" target="_parent">
			<img src="<?php echo $GO_THEME->images['logout']; ?>" width="16" height="16" border="0" align="absmiddle" />
			<?php echo $menu_logout; ?>
			</a>

	</td>
</tr>
</table>
</form>
</body>
</html>

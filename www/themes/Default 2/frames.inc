<html>
<head>
<title><?php echo $GO_CONFIG->title; ?>
</title>
<link rel="shortcut icon" href="<?php echo $GO_CONFIG->host; ?>lib/favicon.ico" />
</head>
<frameset rows="0,73,100%,20" frameborder="0" framespacing="0">
<frame name="checker" src="<?php echo $GO_CONFIG->host; ?>checker.php" scrolling="no" noresize>
<frame name="header" src="<?php echo $GO_THEME->theme_url; ?>header.php" scrolling="no" noresize>
	<frameset cols="120, 100%">
		<frame name="left" src="<?php echo $GO_THEME->theme_url; ?>left.php">
		<frame name="main" src="<?php echo $link; ?>">
	</frameset>
<frame name="footer" src="<?php echo $GO_THEME->theme_url; ?>footer.php" scrolling="no" noresize>
</frameset>
</html>
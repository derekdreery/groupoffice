<html>
<head>
<title><?php echo $GO_CONFIG->title; ?>
</title>
<link rel="shortcut icon" href="<?php echo $GO_CONFIG->host; ?>lib/favicon.ico" />
<script type="text/javascript">
function hidestatus(){
        window.status='Group-Office <?php echo $GO_CONFIG->version; ?>'
        return true
}

if (document.layers)
        document.captureEvents(Event.MOUSEOVER | Event.MOUSEOUT)

document.onmouseover=hidestatus
document.onmouseout=hidestatus
</script>
</head>
<frameset rows="0, 23,100%, 60" frameborder="0" framespacing="0">
<frame name="checker" src="<?php echo $GO_CONFIG->host; ?>checker.php" scrolling="no" noresize>
<frame name="header" src="<?php echo $GO_THEME->theme_url; ?>header.php" scrolling="no" noresize>
<frame name="main" src="<?php echo $link; ?>" noresize>
<frame name="footer" src="<?php echo $GO_THEME->theme_url; ?>footer.php" scrolling="no" noresize>
</frameset>
</html>
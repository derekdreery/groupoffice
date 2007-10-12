<script src="<?php echo $GO_CONFIG->host; ?>javascript/common.js" type="text/javascript"></script>
<?php
if(!empty($GO_MODULES->id))
{
	echo '<script src="'.$GO_CONFIG->host.'imagesjs.php?modules='.$GO_MODULES->id.'" type="text/javascript"></script>';
}else {
	echo '<script src="'.$GO_CONFIG->host.'imagesjs.php" type="text/javascript"></script>';
}
/*
<script src="<?php echo $GO_CONFIG->host; ?>ext/adapter/yui/yui-utilities.js" type="text/javascript"></script>
<script src="<?php echo $GO_CONFIG->host; ?>ext/adapter/yui/ext-yui-adapter.js" type="text/javascript"></script>
*/
?>
<script src="<?php echo $GO_CONFIG->host; ?>ext/adapter/ext/ext-base.js" type="text/javascript"></script>
<script src="<?php echo $GO_CONFIG->host; ?>ext/ext-all-debug.js" type="text/javascript"></script>
<script src="<?php echo $GO_CONFIG->host; ?>language/common/en.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $GO_CONFIG->host; ?>javascript/windows/loginDialog.js"></script>
<script type="text/javascript">
var BaseHref = '<?php echo $GO_CONFIG->host; ?>';
GOsettings=Ext.util.JSON.decode('<?php echo addslashes(json_encode($_SESSION['GO_SESSION'])); ?>');
GOmodules=Ext.util.JSON.decode('<?php echo addslashes(json_encode($GO_MODULES->modules)); ?>');
Ext.BLANK_IMAGE_URL = '<?php echo $GO_CONFIG->host; ?>ext/resources/images/default/s.gif';
</script>
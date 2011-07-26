<script type="text/javascript">
GO.moduleManager.on('languageLoaded',function(){
<?php
$file = $GLOBALS['GO_CONFIG']->file_storage_path.'users/admin/language/'.$GLOBALS['GO_LANGUAGE']->language.'.js';
if(!file_exists($file)){
	$file = $GLOBALS['GO_CONFIG']->file_storage_path.'users/admin/language/en.js';
}
include($file);
?>
});
</script>
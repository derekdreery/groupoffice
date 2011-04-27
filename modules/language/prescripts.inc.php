<script type="text/javascript">
GO.moduleManager.on('languageLoaded',function(){
<?php
$file = $GO_CONFIG->file_storage_path.'users/admin/language/'.$GO_LANGUAGE->language.'.js';
if(!file_exists($file)){
	$file = $GO_CONFIG->file_storage_path.'users/admin/language/en.js';
}
include($file);
?>
});
</script>
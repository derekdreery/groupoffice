<script type="text/javascript">
GO.moduleManager.on('languageLoaded',function(){
<?php
$file = GO::config()->file_storage_path.'users/admin/language/'.GO::language()->language.'.js';
if(!file_exists($file)){
	$file = GO::config()->file_storage_path.'users/admin/language/en.js';
}
include($file);
?>
});
</script>
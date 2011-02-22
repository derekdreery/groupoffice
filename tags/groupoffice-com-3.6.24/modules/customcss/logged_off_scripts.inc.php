<?php
if(file_exists($GO_CONFIG->file_storage_path.'customcss/style.css'))
		echo '<style>'.file_get_contents($GO_CONFIG->file_storage_path.'customcss/style.css').'</style>'."\n";

if(file_exists($GO_CONFIG->file_storage_path.'customcss/javascript.js'))
		echo '<script type="text/javascript">'.file_get_contents($GO_CONFIG->file_storage_path.'customcss/javascript.js').'</script>'."\n";
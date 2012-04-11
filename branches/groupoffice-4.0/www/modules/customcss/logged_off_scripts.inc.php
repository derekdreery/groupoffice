<?php
if(file_exists($GLOBALS['GO_CONFIG']->file_storage_path.'customcss/javascript.js'))
		echo '<script type="text/javascript">'.file_get_contents($GLOBALS['GO_CONFIG']->file_storage_path.'customcss/javascript.js').'</script>'."\n";
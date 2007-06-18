<?php
require('../../../../../Group-Office.php');

require($_SESSION['GO_SESSION']['go_autodata_definitions']);

header('Content-Type: text/xml;charset=UTF-8');
echo '<?xml version="1.0" encoding="utf-8" ?>';
echo "\r\n";
?>
<autodata>
	<?php 
	foreach($autodata as $option)
	{
		echo '<option text="'.htmlspecialchars($option['text']).' ('.htmlspecialchars($option['value']).')" value="'.htmlspecialchars($option['value']).'" />';
	}
	?>
</autodata>

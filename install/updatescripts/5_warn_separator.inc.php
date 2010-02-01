<?php
if(isset($GO_CONFIG->default_date_seperator))
{
	echo $line_break.$line_break.'########################'.$line_break.$line_break;
	
	echo 'WARNING! you must run install/index.php and correct the regional settings because there was a spelling error in the default_date_separator, default_thousands_separator and default_decimal_separator value.';
	echo $line_break.$line_break.'########################'.$line_break.$line_break;
}
?>
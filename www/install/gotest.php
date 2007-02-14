<h1 style="font-family: Arial, Helvetica;font-size: 18px;">Group-Office test script</h1>
<?php
require(dirname(__FILE__).'/install.inc');
require(dirname(__FILE__).'/test.inc');

if(isset($fatal_error))
{
	echo '<p style="font-family: Arial, Helvetica;font-size: 12px;color:red;">Fatal errors occured. Group-Office will not run properly with current system setup!</p>';
}else
{
	echo '<p style="font-family: Arial, Helvetica;font-size: 12px;">Passed, Group-Office should run on this machine</p>';
}

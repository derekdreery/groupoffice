<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.27 $ $Date: 2005/11/17 10:44:34 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('filesystem');
require_once($GO_LANGUAGE->get_language_file('email'));

$email_module = $GO_MODULES->get_module('email');
if (!$email_module)
{
	die($strDataError);
}
$task = '';
//load file management class
require_once($GO_CONFIG->class_path."filesystem.class.inc");
require_once($email_module['class_path'].'email.class.inc');
$email = new email();
$fs = new filesystem();

$attachments_size = 0;

if (isset($_SESSION['attach_array']))
{
	for($i=0;$i<sizeof($_SESSION['attach_array']);$i++)
	{
		$attachments_size += $_SESSION['attach_array'][$i]->file_size;
	}
}

$files = isset($_POST['fs_list']['selected']) ? $_POST['fs_list']['selected'] : array();

if(isset($_REQUEST['path']))
{
	$files[] = stripslashes($_REQUEST['path']);
}


for ($i=0;$i<count($files); $i++)
{
	$attachments_size += filesize(smart_stripslashes($files[$i]));
}
if ($attachments_size < $GO_CONFIG->max_attachment_size)
{
	while ($file = smart_stripslashes(array_shift($files)))
	{
		$tmp_file = $GO_CONFIG->tmpdir.md5(uniqid(time()));
		if (copy($file, $tmp_file))
		{
			$filename = utf8_basename($file);
			$email->register_attachment($tmp_file, $filename, filesize($file), mime_content_type($file), 'attachment');
		}
	}
}else
{
	$task = 'too_big';
}


$charset = isset($charset) ? $charset : 'UTF-8';
header('Content-Type: text/html; charset='.$charset);

if ($task == 'too_big')
{
?>
	<html>
	<body>
	<script type="text/javascript">
			alert("<?php echo $ml_file_too_big.format_size($GO_CONFIG->max_attachment_size)." (".number_format($GO_CONFIG->max_attachment_size, 0, $_SESSION['GO_SESSION']['decimal_seperator'], $_SESSION['GO_SESSION']['thousands_seperator'])." bytes)."; ?>");
			window.close();
	</script>
	</body>
	</html>
<?php
}else
{
	?>
	<html>
	<body>
	<script type="text/javascript">
			opener.document.location=opener.document.location;
			window.close();
	</script>
	</body>
	</html>
<?php

}

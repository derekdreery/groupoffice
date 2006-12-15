<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.1 $ $Date: 2006/06/12 14:55:09 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once('../../Group-Office.php');

$path = smart_addslashes($_REQUEST['path']);
$url = str_replace($GO_CONFIG->local_path, $GO_CONFIG->local_url, $path);
?>
<html>
<head>
<script type="text/javascript" language="javascript">
function _insertHyperlink(url, name)
{
  window.opener.SetUrl(url, "", "", name);
  window.close();
}
</script>
</head>
<body onload="javascript:_insertHyperlink('<?php echo $url; ?>','<?php echo basename($path); ?>');">
</body>
</html>

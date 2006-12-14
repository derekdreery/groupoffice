<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/
?>
<table border="0">
<tr>
        <td colspan="2"><br /><h3><?php echo $ac_login_info; ?></h3></td>
</tr>
<tr>
        <td><?php echo $ac_registration_time; ?>:</td>
        <td><?php echo date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], gmt_to_local_time($profile['registration_time'])); ?></td>
</tr>
<tr>
        <td><?php echo $ac_lastlogin; ?>:</td>
        <td><?php echo date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'],  gmt_to_local_time($_SESSION['GO_SESSION']['lastlogin'])); ?></td>
</tr>
<tr>
        <td><?php echo $ac_logins; ?>:</td>
        <td><?php echo number_format($profile["logins"], 0, $_SESSION['GO_SESSION']['decimal_seperator'], $_SESSION['GO_SESSION']['thousands_seperator']); ?></td>
</tr>
</table>

<?php
echo '<br />';
$button = new button($cmdClose, "javascript:document.location='".$return_to ."'");
?>

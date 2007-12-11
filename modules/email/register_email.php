<?php
require('../../Group-Office.php');

$GO_SECURITY->authenticate();

$filename='Group-Office_email.reg';
header('Content-Type: application/download');
header('Content-Disposition: attachment; filename="'.$filename.'"');
?>
REGEDIT4

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office]
@="Group-Office"

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office\Protocols]

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office\Protocols\mailto]
"URL Protocol"=""

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office\Protocols\mailto\shell]

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office\Protocols\mailto\shell\open]

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office\Protocols\mailto\shell\open\command]
@="rundll32.exe url.dll,FileProtocolHandler <?php echo $GO_MODULES->modules['email']['full_url']; ?>mailto.php?mail_to=%1"


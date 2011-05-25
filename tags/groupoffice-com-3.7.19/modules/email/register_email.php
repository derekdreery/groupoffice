<?php
require('../../Group-Office.php');

$filename='Group-Office_email.reg';

$data = 'Windows Registry Editor Version 5.00

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office]
@="Group-Office"

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office\Protocols]

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office\Protocols\mailto]
"URL Protocol"=""

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office\Protocols\mailto\shell]

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office\Protocols\mailto\shell\open]

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office\Protocols\mailto\shell\open\command]
@="rundll32.exe url.dll,FileProtocolHandler '.$GO_MODULES->modules['email']['full_url'].'mailto.php?mail_to=%1"

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail]
@="Group-Office"

[HKEY_CLASSES_ROOT\mailto\shell\open\command]
@="rundll32.exe url.dll,FileProtocolHandler '.$GO_MODULES->modules['email']['full_url'].'mailto.php?mail_to=%1"
';

header('Content-Type: application/download');
header('Content-Length: '.strlen($data));
header('Content-Transfer-Encoding: binary');
header("Last-Modified: ".gmdate("D, d M Y H:i:s", time()-86400)." GMT");
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
echo $data;
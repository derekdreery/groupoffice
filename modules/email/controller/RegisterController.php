<?php

class GO_Email_Controller_Register extends GO_Base_Controller_AbstractController {

	protected function actionDownload($params) {
		
		$url = GO::url('email/message/mailto', array('mailto'=>'-mailto-'), false, false, false);
		//this is necessary because we don't want %1 to be urlencoded.
		$url = str_replace('-mailto-','%1', $url);

		$data = 'Windows Registry Editor Version 5.00

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office]
@="Group-Office"

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office\Protocols]

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office\Protocols\mailto]
"URL Protocol"=""

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office\Protocols\mailto\shell]

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office\Protocols\mailto\shell\open]

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail\Group-Office\Protocols\mailto\shell\open\command]
@="rundll32.exe url.dll,FileProtocolHandler '.$url.'"

[HKEY_LOCAL_MACHINE\SOFTWARE\Clients\Mail]
@="Group-Office"

[HKEY_CLASSES_ROOT\mailto\shell\open\command]
@="rundll32.exe url.dll,FileProtocolHandler '.$url. '"
';
		
		GO_Base_Util_Http::downloadFile(new GO_Base_Fs_MemoryFile('Group-Office_email.reg', $data));
		
	}

}
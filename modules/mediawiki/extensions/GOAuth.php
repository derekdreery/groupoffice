<?php
function go_unserializesession($data) {
	$vars = preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/',
					$data, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	for ($i = 0; isset($vars[$i]); $i++)
		$result[$vars[$i++]] = unserialize($vars[$i]);
	return $result;
}

function go_getsession() {
	if (isset($_COOKIE['groupoffice'])) {

		$GO_SID=$_COOKIE['groupoffice'];

		$fname = session_save_path() . "/sess_" . $GO_SID;

		if (file_exists($fname)) {
			$data = file_get_contents($fname);
			$data = go_unserializesession($data);
			return $data['GO_SESSION'];
		}
	}
	return false;
}

function go_auth() {

	global $wgUser, $wgLanguageCode, $wgRequest, $wgOut;

	// For a few special pages, don't do anything.
	$title = $wgRequest->getVal('title');
	$lg = Language::factory($wgLanguageCode);
	if ($title == $lg->specialPage("Userlogout") || $title == $lg->specialPage("Userlogin")) {
		return true;
	}
	
	$data = go_getsession();

	if($wgUser->IsAnon() || ($data && $wgUser->getName() != $data['username'])) {

		if(isset($data['user_id'])) {

			$wgUser = User::newFromName( $data['username'] );
			// Create a new account if the user does not exists
			if ($wgUser->getID() == 0)
			{
				// Create the user
				$wgUser->addToDatabase();
				$wgUser->setRealName($data['username']);
				//$wgUser->setEmail($data['GO_SESSION']['email']);
				$wgUser->setPassword( md5($data['username'].'zout') ); // do something random
				$wgUser->setToken();
				$wgUser->saveSettings();

				// Update user count
				$ssUpdate = new SiteStatsUpdate(0,0,0,0,1);
				$ssUpdate->doUpdate();
			}

			$wgUser->setOption("rememberpassword", 1);
			$wgUser->setCookies();
			$wgOut->returnToMain();
		}

	}
	return true;
}
//$wgHooks['UserLoadFromSession'][] = 'go_auth';
$wgEditPageFrameOptions=false;
//$wgHooks['ApiBeforeMain'][] = 'go_auth';
$wgExtensionFunctions[]='go_auth';

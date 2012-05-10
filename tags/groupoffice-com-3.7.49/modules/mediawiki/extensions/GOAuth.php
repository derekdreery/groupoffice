<?php
function go_unserializesession($data) {
	$vars = preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/',
					$data, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	for ($i = 0; isset($vars[$i]); $i++)
		$result[$vars[$i++]] = unserialize($vars[$i]);
	return $result;
}

function go_auth() {

	global $wgUser, $wgRequest;


	// For a few special pages, don't do anything.
	$title = $wgRequest->getVal('title');
	if ($title == 'Special:Userlogout' || $title == 'Special:Userlogin') {
		return;
	}

	// wiki user need setting?
	if(!(isset($wgUser))) {
		$wgUser = new User();
		$wgUser->newFromSession();
		$wgUser->load();
	}

	if($wgUser->IsAnon()) {

		//import Group-Office session data
		$wg_user_id=0;
		$go_user_id=0;
		if (isset($_COOKIE['groupoffice'])) {

			$GO_SID=$_COOKIE['groupoffice'];

			$fname = session_save_path() . "/sess_" . $GO_SID;
			if (file_exists($fname)) {
				$data = file_get_contents($fname);
				$data = go_unserializesession($data);

				//$goUser = $GO_USERS->get_user();
				if(isset($data['GO_SESSION']['user_id'])) {

					$go_user_id=$data['GO_SESSION']['user_id'];

					$wgUser = User::newFromName( $data['GO_SESSION']['username'] );
					$wg_user_id = $wgUser->idForName();
				}
			}
		}

		if($go_user_id==0)
			$wgUser->logout();

		if ( 0 != $wg_user_id ) {
			// user exists in wiki
			$wgUser->setCookies();

		}elseif($go_user_id>0) {
			// create new wiki user
			// set properties
			$wgUser->mEmail       = $data['GO_SESSION']['email']; // Set Email Address.
			$wgUser->mRealName    = $data['GO_SESSION']['name'];
			$wgUser->addToDatabase();


			$wgUser->setToken();
			$wgUser->setCookies();

		}
		return true;
	}else {
		return false;
	}
}
//$wgHooks['UserLoadFromSession'][] = 'go_auth';

$wgExtensionFunctions[]='go_auth';

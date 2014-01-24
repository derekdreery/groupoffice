<?php

class GO_Phpbb3_Controller_Bridge extends GO_Base_Controller_AbstractController {

	protected function actionRedirect() {
		$tmpFile = GO_Base_Fs_File::tempFile();
		$tmpFile->putContents(GO::user()->id);
		
		if (empty(GO::config()->phpbb3_url)) {
			throw new Exception('You must configure phpbb3_url in your config.php file');
		}

		$url = GO::config()->phpbb3_url. '?goauth=' . base64_encode($tmpFile->path()) . '&sid=' . md5(uniqid(time()));
		header('Location: ' . $url);
		exit();
	}

}
<?php

class GO_Base_Fs_Zip {

	public static function create(GO_Base_Fs_File $archiveFile, GO_Base_Fs_Folder $workingFolder, $sources, $utf8=false) {
	
		if (class_exists("ZipArchive") && !$utf8) {
			$zip = new ZipArchive();
			$zip->open($archiveFile->path(), ZIPARCHIVE::CREATE);
			for ($i = 0; $i < count($sources); $i++) {
				if ($sources[$i]->isFolder()) {
					self::_zipDir($sources[$i], $zip, str_replace($workingFolder->path() . '/', '', $sources[$i]->path()) . '/');
				} else {
					$name = str_replace($workingFolder->path() . '/', '', $sources[$i]->path());
					$name = iconv('UTF-8', 'CP850', $name);
					$zip->addFile($sources[$i]->path(), $name);
				}
			}
			$zip->close();
		} else {
		
			if (!GO_Base_Util_Common::isWindows())
				putenv('LANG=en_US.UTF-8');

			chdir($workingFolder->path());

			$cmdSources = array();
			for ($i = 0; $i < count($sources); $i++) {
				$cmdSources[$i] = escapeshellarg(str_replace($workingFolder->path() . '/', '', $sources[$i]->path()));
			}

			$cmd = GO::config()->cmd_zip . ' -r ' . escapeshellarg($archiveFile->path()) . ' ' . implode(' ', $cmdSources);

			exec($cmd, $output, $ret);

			if ($ret!=0 || !$archiveFile->exists()) {
				throw new Exception('Command failed: ' . $cmd . "<br /><br />" . implode("<br />", $output));
			}
		}
	}

	private static function _zipDir(GO_Base_Fs_Folder $dir, $zip, $relative_path, $utf8) {
		
		$items = $dir->ls();
		foreach($items as $item){
			if ($item->isFile()) {
				$name = $relative_path . $item->name();
				$name = iconv('UTF-8', 'CP850', $name);

				$zip->addFile($dir . $item->name(), $name);
			} else{
				$this->_zipDir($item . $item->name(), $zip, $relative_path . $item->name() . '/');
			}
		}
		
	}

}
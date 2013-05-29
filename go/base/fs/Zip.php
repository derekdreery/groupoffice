<?php

/**
 * Copyright Intermesh BV
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @package GO.base.fs
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @copyright Copyright Intermesh BV.
 */

/**
 * Static function to create a ZIP archive
 * 
 * @package GO.base.fs
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @copyright Copyright Intermesh BV.
 */

class GO_Base_Fs_Zip {

	/**
	 * Create a ZIP archive encoded in CP850 so that Windows will understand
	 * foreign characters
	 * 
	 * @param GO_Base_Fs_File $archiveFile
	 * @param GO_Base_Fs_Folder $workingFolder
	 * @param GO_Base_Fs_Base[] $sources
	 * @param boolean $utf8 Set to true to use UTF8 encoding. This is not supported by Windows explorer.
	 * @throws Exception
	 */
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
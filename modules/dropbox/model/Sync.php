<?php

/**
 * Group-Office
 * 
 * Copyright Intermesh BV. 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @version $Id: Sync.php 20024 2014-11-27 16:09:32Z mdhart $
 * @copyright Copyright Intermesh BV.
 * @author mdhart@intermesh.nl
 */

namespace GO\Dropbox\Model;

use GO;
use GO\Dropbox\DropboxModule;
use GO\Files\Model\Folder;
use GO\Files\Model\File;

/**
 * Code for syncing Dropbox to GroupOffice
 *
 * @package GO.modules.dropbox.model
 * @property string $access_token
 * @property string $dropbox_user_id
 */
class Sync {

	protected static $client = array();
	
	/**
	 * 
	 */
	protected static function getClient(\GO\Base\Model\User $user = null) {

		if (!isset($user))
			$user = \GO::user();

		if (!isset(self::$client[$user->id])) {
			$dbxUser = User::model()->findByPk($user->id);
			if (!$dbxUser) {
				throw new \Exception("User isn't connected");
			}
			if (empty($dbxUser->access_token)) {
				throw new \Exception("Access token was cleared. Please reconnect.");
			}
			
			$client = new DropboxClient();
			$client->SetAccessToken(@unserialize($dbxUser->access_token));
			self::$client[$user->id] = $client;
		}
		return self::$client[$user->id];
	}

	/**
	 * 
	 * @param \GO\Base\Model\User $user
	 * @return Folder
	 */
	protected static function getHomeFolder(\GO\Base\Model\User $user = null) {
		if (!isset($user))
			$user = \GO::user();
		return Folder::model()->findByPath('users/' . $user->username . DropboxModule::GO_ROOT, true);
	}

	protected static function dbxToGoPath($dbxPath, \GO\Base\Model\User $user) {
		return 'users/' . $user->username . DropboxModule::GO_ROOT . '/' . substr($dbxPath, strlen(DropboxModule::DBX_ROOT));
	}

	protected static function goToDbxPath($goPath, \GO\Base\Model\User $user) {
		return DropboxModule::DBX_ROOT . substr($goPath, strlen('users/' . $user->username . DropboxModule::GO_ROOT . '/'));
	}

	protected static function getGroupOfficeSnapShot(\GO\Files\Model\Folder $folder, $sort = true) {

		$snapshot = array();
		$stmt = $folder->files();

		foreach ($stmt as $file) {
			$snapshot[strtolower($file->path)] = array('mtime' => $file->mtime, 'path' => $file->path);
		}

		$stmt = $folder->folders();
		foreach ($stmt as $childFolder) {
			$snapshot[strtolower($childFolder->path)] = array('mtime' => $childFolder->mtime, 'path' => $childFolder->path);
			$snapshot = array_merge($snapshot, self::getGroupOfficeSnapShot($childFolder, false));
		}

		if ($sort)
			ksort($snapshot);

		return $snapshot;
	}

	protected static function getDropboxSnapshot(\GO\Base\Model\User $user = null, $dropboxPath = DropboxModule::DBX_ROOT, $sort = true) {

		$snapshot = array();

		$dbxClient = self::getClient($user);
		$folderMetaData = $dbxClient->GetMetadata($dropboxPath, false, null, true);

		if (!isset($folderMetaData) && $dropboxPath == DropboxModule::DBX_ROOT) {
			$folderMetaData = $dbxClient->CreateFolder(DropboxModule::DBX_ROOT);

			if (!isset($folderMetaData))
				throw new \Exception("Could not create root folder " . DropboxModule::DBX_ROOT);
		}


		if (isset($folderMetaData->contents)) {
			foreach ($folderMetaData->contents as $item) {

				$snapshot[strtolower($item->path)] = array('mtime' => strtotime($item->modified), 'path' => $item->path);

				if ($item->is_dir) {
					$snapshot = array_merge($snapshot, self::getDropboxSnapshot($user, $item->path, false));
				}
			}
		}

		if ($sort)
			ksort($snapshot);

		return $snapshot;
	}

	public function start(\GO\Base\Model\User $user = null, $reset = false) {

		if ($user === null)
			$user = GO::user();

		GO::session()->runAsRoot();
		GO::setMaxExecutionTime(0);

		self::log("Dropbox sync for user " . $user->username);


		$dbxUser = User::model()->findByPk($user->id);
		if ($reset) {
			$dbxUser->delta_cursor = "";

			$folder = self::getHomeFolder($user);
			$folder->removeChildren();
		}

		$dbxClient = self::getClient($user);

		self::log("Getting Dropbox Changes");

		$hasMore = true;
		while ($hasMore) {

			$delta = $dbxClient->Delta(empty($dbxUser->delta_cursor) ? null : $dbxUser->delta_cursor );
//			var_dump($delta);
//			exit();
			if (!isset($delta)) {
				throw new \Exception("Could not get delta from Dropbox!");
			}
			$hasMore = $delta->has_more;

			foreach ($delta->entries as $entry) {
				flush();

				//$entry[1]['path'] = with case. Otherwise we just have a string to lowered path for deleting
				$dbxPath = isset($entry[1]->path) ? $entry[1]->path : $entry[0];
				$goPath = self::dbxToGoPath($dbxPath, $user);

				if (!isset($entry[1])) {
					//should be deleted


					$file = File::model()->findByPath($goPath, false);
					if ($file) {
						self::log("Deleting file on Group-Office " . $goPath);

						$file->delete();
					} else {
						$folder = Folder::model()->findByPath($goPath, false, array(), false);
						if ($folder) {
							self::log("Deleting folder on Group-Office " . $goPath);
							$folder->delete();
						} else {
							self::log("Could not find path for delete file on Group-Office " . $goPath);
						}
					}
				} else if ($entry[1]->is_dir) {
					self::log("Create folder on Group-Office " . $entry[1]->path . " -> " . $goPath);
					$folder = Folder::model()->findByPath($goPath, true);
				} else {

					self::log("Download from Dropbox " . $entry[1]->path . " -> " . $goPath);
					$folder = Folder::model()->findByPath(dirname($goPath), true, array(), false);

					$name = \GO\Base\Fs\File::utf8Basename($goPath);
					$path = $folder->fsFolder->createChild($name)->path();
					//$f = fopen($path, "w+b");
					$fileMetadata = $dbxClient->DownloadFile($entry[0], $path);
					//fclose($f);

					touch($path, strtotime($fileMetadata->modified));

					//todo needs optimize
					$folder->syncFilesystem();
				}
			}
		}


		$dbxUser->delta_cursor = $delta->cursor;
		$dbxUser->save();



		self::log("Applying Group-Office changes to Dropbox");

		$folder = self::getHomeFolder($user);
		$goSnapshot = self::getGroupOfficeSnapShot($folder);



		$dbxSnapshot = self::getDropboxSnapshot($user);

		foreach ($goSnapshot as $path => $props) {

			$dbxPath = self::goToDbxPath($props['path'], $user);
			$dbxPathToLower = strtolower($dbxPath);
			if (!isset($dbxSnapshot[$dbxPathToLower]) || $dbxSnapshot[$dbxPathToLower]['mtime'] < $props['mtime']) {
				if (is_file(GO::config()->file_storage_path . $props['path'])) {

					self::log("Upload to Dropbox " . $path . " -> " . $dbxPath);

					$localPath = GO::config()->file_storage_path . $props['path'];
					$meta = $dbxClient->UploadFile($localPath, $dbxPath, true);

					if (!isset($meta))
						throw new \Exception("Failed to create file '" . $dbxPath . "' on Dropbox");
				}elseif (!isset($dbxSnapshot[$dbxPathToLower])) {
					self::log("Create folder on Dropbox " . $path . " -> " . $dbxPath);

					$folderMetaData = $dbxClient->CreateFolder($dbxPath);

					if (!isset($folderMetaData))
						throw new \Exception("Failed to create folder '" . $dbxPath . "' on Dropbox");
				}
			}
		}

		//reverse sort for deleting so that deeper files are deleted first.
		krsort($dbxSnapshot);
		foreach ($dbxSnapshot as $path => $props) {
			$goPath = strtolower(self::dbxToGoPath($path, $user));
			if (!isset($goSnapshot[$goPath])) {
				self::log("Deleting on dropbox " . $path);

				if (!$dbxClient->Delete($path)) {
					throw new \Exception("Failed to delete '" . $path . "'");
				}
			}
		}

		//get delta again so we won't process our own changes next sync
		$delta = $dbxClient->Delta($dbxUser->delta_cursor);
		$dbxUser->delta_cursor = $delta->cursor;
		$dbxUser->save();

		self::log("Done!");
	}

	public static function log($text) {

		\GO::debug($text);

		if (!empty(\GO::config()->dropbox_log)) {
			$user = isset(\GO::session()->values['username']) ? \GO::session()->values['username'] : 'notloggedin';

			$text = "[$user] " . str_replace("\n", "\n[$user] ", $text);

			file_put_contents(\GO::config()->file_storage_path . 'log/dropbox.log', $text . "\n", FILE_APPEND);
		}
	}

}

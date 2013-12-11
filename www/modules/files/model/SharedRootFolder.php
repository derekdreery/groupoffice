<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */

/**
 * The GO_Files_Model_Folder model
 * 

 * @property int $user_id
 * @property int $folder_id
 */
class GO_Files_Model_SharedRootFolder extends \GO\Base\Db\ActiveRecord {

	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'fs_shared_root_folders';
	}

	public function primaryKey() {
		return array('user_id', 'folder_id');
	}

	public function relations() {
		return array(
				'folder' => array('type' => self::BELONGS_TO, 'model' => 'GO_Files_Model_Folder', 'field' => 'folder_id')
		);
	}

	/**
	 * Find all shared folders for the current user
	 * 
	 * @param \GO\Base\Db\FindParams $findParams
	 * @return \GO\Base\Db\ActiveStatement
	 */
	private function _findShares($user_id) {


		$findParams = new \GO\Base\Db\FindParams();

		$findParams->getCriteria()
						->addModel(\GO_Files_Model_Folder::model())
						->addCondition('visible', 1)
						->addCondition('user_id', $user_id, '!=');

		return \GO_Files_Model_Folder::model()->find($findParams);
	}

	private function _getLastMtime($user_id) {
		
		\GO_Files_Model_Folder::model()->addRelation('aclItem',array(
			"type"=>self::BELONGS_TO,
			"model"=>"\GO\Base\Model\Acl",
			"field"=>'acl_id'
		));
		
		$findParams = \GO\Base\Db\FindParams::newInstance()->debugSql()
						->select("max(a.mtime) AS mtime")
						->single()
						->joinModel(array(
								'model'=>"\GO\Base\Model\Acl",
								'localField'=>'acl_id',
								'tableAlias'=>'a'
						));
		
		$findParams->getCriteria()
						->addModel(\GO_Files_Model_Folder::model())
						->addCondition('visible', 1)
						->addCondition('user_id', $user_id, '!=');
		
		
		$result = \GO_Files_Model_Folder::model()->find($findParams);
		
		return $result->mtime;		
	}

	public function rebuildCache($user_id, $force=false) {
		
		$lastBuildTime = $force ? 0 : \GO::config()->get_setting('files_shared_cache_ctime', $user_id);
		if(!$lastBuildTime || $this->_getLastMtime($user_id)>$lastBuildTime){	

			\GO::debug("Rebuilding shared cache");
			$this->deleteByAttribute('user_id', $user_id);

			$stmt = $this->_findShares($user_id);
			//sort by path and only list top level shares
			$shares = array();
			while ($folder = $stmt->fetch()) {
				//$folder->checkFsSync();
				//sort by path and only list top level shares		
				$shares[$folder->path] = $folder;
			}
			ksort($shares);

			foreach ($shares as $path => $folder) {
				$isSubDir = isset($lastPath) && strpos($path . '/', $lastPath . '/') === 0;

				if (!$isSubDir) {


					$sharedRoot = new \GO_Files_Model_SharedRootFolder();
					$sharedRoot->user_id = $user_id;
					$sharedRoot->folder_id = $folder->id;
					$sharedRoot->save();

					$lastPath = $path;
				}
			}
			
			\GO::config()->save_setting('files_shared_cache_ctime',time(), $user_id);
			
			return time();
		}else
		{
			return $lastBuildTime;
		}
	}

}
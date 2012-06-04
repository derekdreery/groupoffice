<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: Shared_Directory.class.inc.php 7752 2011-07-26 13:48:43Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
class GO_Dav_Fs_SharedDirectory extends Sabre_DAV_FS_Directory implements Sabre_DAV_ICollection, Sabre_DAV_IQuota {

	public function __construct($path='') {		
		$this->path = $path;
	}

	public function getName() {
		return 'Shared';
	}

	public function getChild($name) {

		GO::debug('Shared::getChild('.$name.')');
		
		$folder = GO_Files_Model_Folder::model()->findShares(GO_Base_Db_FindParams::newInstance()->single()->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('name', $name)));
		
		if (!$folder)
			throw new Sabre_DAV_Exception_NotFound('File with name ' . $name . ' could not be located');

		return new GO_DAV_FS_Directory($folder->path);
	}

	/**
	 * Returns an array with all the child nodes
	 *
	 * @return Sabre_DAV_INode[]
	 */
	public function getChildren() {
		GO::debug('Shared::getChildren()');
		$stmt = GO_Files_Model_Folder::model()->findShares();

		$nodes = array();
		while($folder = $stmt->fetch()){
			$nodes[]=new GO_DAV_FS_Directory($folder->path);
		}

		return $nodes;
	}

	/**
	 * Creates a new file in the directory
	 *
	 * data is a readable stream resource
	 *
	 * @param string $name Name of the file
	 * @param resource $data Initial payload
	 * @return void
	 */
	public function createFile($name, $data = null) {

		throw new Sabre_DAV_Exception_Forbidden();
	}

	/**
	 * Creates a new subdirectory
	 *
	 * @param string $name
	 * @return void
	 */
	public function createDirectory($name) {

		throw new Sabre_DAV_Exception_Forbidden();
	}

	/**
	 * Deletes all files in this directory, and then itself
	 *
	 * @return void
	 */
	public function delete() {

		throw new Sabre_DAV_Exception_Forbidden();
	}

	/**
	 * Returns available diskspace information
	 *
	 * @return array
	 */
	public function getQuotaInfo() {

		return array(
				0,
				0
		);
	}

	/**
	 * Returns the last modification time, as a unix timestamp
	 *
	 * @return int
	 */
	public function getLastModified() {

		return false;
	}

}
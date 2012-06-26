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
 * @author Merijn Schering <mschering@intermesh.nl>
 */
class GO_DAV_Shared_Directory extends Sabre_DAV_FS_Directory implements Sabre_DAV_ICollection, Sabre_DAV_IQuota {

	public function __construct($path='') {		
		$this->path = $path;
	}

	public function getName() {
		return 'Shared';
	}

	public function getChild($name) {

		go_debug('Shared::getChild('.$name.')');

		global $files, $GO_SECURITY;

		$r= $files->get_cached_share($GO_SECURITY->user_id, $name);
		
		if (!$r)
			throw new Sabre_DAV_Exception_FileNotFound('File with name ' . $name . ' could not be located');

		return new GO_DAV_FS_Directory($r['path']);
	}

	/**
	 * Returns an array with all the child nodes
	 *
	 * @return Sabre_DAV_INode[]
	 */
	public function getChildren() {

		go_debug('Shared::geChildren');
		
		global $GO_SECURITY,$GO_CONFIG, $files;

		$count = $files->get_cached_shares($GO_SECURITY->user_id);
go_debug($count);
		$nodes = array();
		while($r=$files->next_record()){
			go_debug($r['path']);
			$nodes[]=new GO_DAV_FS_Directory($r['path']);
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
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
class GO_Dav_Fs_RootDirectory extends Sabre_DAV_FS_Directory implements Sabre_DAV_ICollection, Sabre_DAV_IQuota {

	public function __construct($path='') {		
		$this->path = $path;
	}

	public function getName() {
		return 'root';
	}

	

	/**
	 * Returns an array with all the child nodes
	 *
	 * @return Sabre_DAV_INode[]
	 */
	public function getChildren() {
		
		$children = array();
		$children[] = new GO_Dav_Fs_Directory('users/' . GO::user()->username);
		$children[] = new GO_Dav_Fs_SharedDirectory();
		$children[] = new GO_Dav_Fs_Directory('projects');
		$children[] = new GO_Dav_Fs_Directory('addressbook');


		return $children;
	}
	
	/**
     * Returns a specific child node, referenced by its name 
     * 
     * @param string $name 
     * @throws Sabre_DAV_Exception_NotFound
     * @return Sabre_DAV_INode 
     */
    public function getChild($name) {
			
			$children = $this->getChildren();
			
			foreach($children as $child)
			{
				if($child->getName()==$name)
					return $child;
			}
			return false;
			
//			if($name=='Shared')
//				return new GO_Dav_Fs_SharedDirectory();
//			elseif($name==GO::user()->username)
//				return new GO_Dav_Fs_Directory('users/' . GO::user()->username);
//			else
//				return false;
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
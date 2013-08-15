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
class GO_Dav_Fs_RootDirectory extends Sabre\DAV\FS\Directory{

	public function __construct($path="") {
		parent::__construct(GO::config()->file_storage_path);
	}
	public function getName() {
		return "root";
	}	

	/**
	 * Returns an array with all the child nodes
	 *
	 * @return Sabre\DAV\INode[]
	 */
	public function getChildren() {
		
		$children = array();
		$children[] = new GO_Dav_Fs_Directory('users/' . GO::user()->username);
		$children[] = new GO_Dav_Fs_SharedDirectory();
		
		if(GO::modules()->projects)
			$children[] = new GO_Dav_Fs_Directory('projects');
		
		if(GO::modules()->addressbook)
			$children[] = new GO_Dav_Fs_Directory('addressbook');


		return $children;
	}
	
	/**
     * Returns a specific child node, referenced by its name 
     * 
     * @param string $name 
     * @throws Sabre\DAV\Exception\NotFound
     * @return Sabre\DAV\INode 
     */
    public function getChild($name) {
			
			switch($name){
				case GO::user()->username:
					return new GO_Dav_Fs_Directory('users/' . GO::user()->username);
					break;
				
				case 'Shared':
						return new GO_Dav_Fs_SharedDirectory();
					break;
				case 'projects':
					if(GO::modules()->projects)
						return new GO_Dav_Fs_Directory('projects');
					break;
					
				case 'addressbook':
					if(GO::modules()->addressbook)
						return new GO_Dav_Fs_Directory('addressbook');
					break;
			}
			
			throw new Sabre\DAV\Exception\NotFound("$name not found in the root");
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

		throw new Sabre\DAV\Exception\Forbidden();
	}

	/**
	 * Creates a new subdirectory
	 *
	 * @param string $name
	 * @return void
	 */
	public function createDirectory($name) {

		throw new Sabre\DAV\Exception\Forbidden();
	}

	/**
	 * Deletes all files in this directory, and then itself
	 *
	 * @return void
	 */
	public function delete() {

		throw new Sabre\DAV\Exception\Forbidden();
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

		return filemtime(GO::config()->file_storage_path);
	}

}
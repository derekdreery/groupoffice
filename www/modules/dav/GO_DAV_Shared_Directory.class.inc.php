<?php
class GO_DAV_Shared_Directory extends Sabre_DAV_FS_Directory implements Sabre_DAV_ICollection, Sabre_DAV_IQuota {

	public function __construct($path='') {
		global $GO_CONFIG;
		$this->path = $path;


		global $GO_SECURITY,$GO_CONFIG;

		$nodes = array();

		$files = new files();
		$fs2 = new files();

		$share_count = $files->get_authorized_shares($GO_SECURITY->user_id);

		$nodes = array();

		$count = 0;
		while ($folder = $files->next_record()) {
			$path = $fs2->build_path($folder);
			$nodes[] = $path;
		}
		sort($nodes);

		//go_debug($nodes);

		$fs = new filesystem();

		$toplevel_nodes=array();

		foreach ($nodes as $path) {
			$is_sub_dir = isset($last_path) ? $fs->is_sub_dir($path, $last_path) : false;
			if (!$is_sub_dir) {
				$this->children[utf8_basename($path)]=new GO_DAV_FS_Directory($path);

				$last_path = $path;
			}
		}

		return $toplevel_nodes;


	}

	public function getName() {
		return 'Shared';
	}

	public function getChild($name) {

		if (!isset($this->children[$name]))
			throw new Sabre_DAV_Exception_FileNotFound('File with name ' . $name . ' could not be located');

		return $this->children[$name];
	}

	/**
	 * Returns an array with all the child nodes
	 *
	 * @return Sabre_DAV_INode[]
	 */
	public function getChildren() {

		global $GO_CONFIG;

		$nodes = array();
		foreach ($this->children as $node) {
			$nodes[] = $node;
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
				disk_total_space($this->path) - disk_free_space($this->path),
				disk_free_space($this->path)
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
<?php
class GO_DAV_ObjectTree extends Sabre_DAV_ObjectTree{
	/**
     * Moves a file from one location to another
     *
     * @param string $sourcePath The path to the file which should be moved
     * @param string $destinationPath The full destination path, so not just the destination parent node
     * @return int
     */
    public function move($sourcePath, $destinationPath) {
		$moveable = $this->getNodeForPath($sourcePath);

		$basePath = str_replace($sourcePath, '', $moveable->getServerPath());

        $moveable->move($basePath.$destinationPath);
    }
	
}
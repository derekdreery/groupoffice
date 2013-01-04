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
class GO_DAV_ObjectTree extends Sabre_DAV_ObjectTree{
	/**
     * Moves a file from one location to another
     *
     * @param string $sourcePath The path to the file which should be moved
     * @param string $destinationPath The full destination path, so not just the destination parent node
     * @return int
     */
    public function move($sourcePath, $destinationPath) {

		go_debug("ObjectTree::move($sourcePath, $destinationPath)");

		$moveable = $this->getNodeForPath($sourcePath);

		$destination = $this->getNodeForPath(dirname($destinationPath));
		$targetServerPath = $destination->getServerPath().'/'.utf8_basename($destinationPath);

        $moveable->move($targetServerPath);
    }
}
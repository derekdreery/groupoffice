<?php
class GO_DAV_ObjectTree extends Sabre_DAV_ObjectTree{

	private $cache=array();
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

	/**
     * Returns the INode object for the requested path
     *
     * @param string $path
     * @return Sabre_DAV_INode
     */
    public function getNodeForPath($path) {

        $path = trim($path,'/');

		go_debug ("getNodeForPath($path)");


		$path = trim($path,'/');

        //if (!$path || $path=='.') return $this->rootNode;
        $currentNode = $this->rootNode;
        $i=0;

		$currentPath = '';

        // We're splitting up the path variable into folder/subfolder components and traverse to the correct node..
        foreach(explode('/',$path) as $pathPart) {

            // If this part of the path is just a dot, it actually means we can skip it
            if ($pathPart=='.' || $pathPart=='') continue;

            if (!($currentNode instanceof Sabre_DAV_ICollection))
                throw new Sabre_DAV_Exception_FileNotFound('Could not find node at path: ' . $path);

			if($currentPath != '')
				$currentPath.='/';

			$currentPath .= $pathPart;
			
			//go_debug($this->cache);
			go_debug($pathPart);
			if(!isset($_SESSION['SabreDAV']['cache'][$currentPath])){
				$_SESSION['SabreDAV']['cache'][$currentPath]= $currentNode->getChild($pathPart);
				go_debug('Cached: '.$currentPath);
			}else
			{
				go_debug('Got from cache: '.$currentPath);
			}

            $currentNode = $_SESSION['SabreDAV']['cache'][$currentPath];
        }

        return $currentNode;
    }
	
}
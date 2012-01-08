<?php

/**
 * Directory class 
 * 
 * @package Sabre
 * @subpackage DAV
 * @copyright Copyright (C) 2007-2012 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/) 
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class Sabre_DAV_FS_Directory extends Sabre_DAV_FS_Node implements Sabre_DAV_ICollection, Sabre_DAV_IQuota {

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

        $newPath = $this->path . '/' . $name;
        file_put_contents($newPath,$data);

    }

    /**
     * Creates a new subdirectory 
     * 
     * @param string $name 
     * @return void
     */
    public function createDirectory($name) {

        $newPath = $this->path . '/' . $name;
        mkdir($newPath);

    }

    /**
     * Returns a specific child node, referenced by its name 
     * 
     * @param string $name 
     * @throws Sabre_DAV_Exception_FileNotFound
     * @return Sabre_DAV_INode 
     */
    public function getChild($name) {

        $path = $this->path . '/' . $name;

        if (!file_exists($path)) throw new Sabre_DAV_Exception_FileNotFound('File with name ' . $path . ' could not be located');

        if (is_dir($path)) {

            return new Sabre_DAV_FS_Directory($path);

        } else {

            return new Sabre_DAV_FS_File($path);

        }

    }

    /**
     * Returns an array with all the child nodes 
     * 
     * @return Sabre_DAV_INode[] 
     */
    public function getChildren() {

        $nodes = array();
        foreach(scandir($this->path) as $node) if($node!='.' && $node!='..') $nodes[] = $this->getChild($node);
        return $nodes;

    }

    /**
     * Checks if a child exists. 
     * 
     * @param string $name 
     * @return bool 
     */
    public function childExists($name) {

        $path = $this->path . '/' . $name;
        return file_exists($path);

    }

    /**
     * Deletes all files in this directory, and then itself 
     * 
     * @return void
     */
    public function delete() {

        foreach($this->getChildren() as $child) $child->delete();
        rmdir($this->path);

    }

    /**
     * Returns available diskspace information 
     * 
     * @return array 
     */
    public function getQuotaInfo() {

        return array(
            disk_total_space($this->path)-disk_free_space($this->path),
            disk_free_space($this->path)
            ); 

    }

}


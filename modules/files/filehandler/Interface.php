<?php
interface GO_Files_Filehandler_Interface{
	//public function supportedExtensions();
	
	/**
	 * @return string Name of the handler
	 */
	public function getName();
	
	/**
	 * Return true if it's the default handler for a file.
	 * 
	 * @param GO_Files_Model_File $file
	 * @return boolean
	 */
	public function isDefault(GO_Files_Model_File $file);
	
	/**
	 * Check if the file is supported by this handler
	 * 
	 * @param GO_Files_Model_File $file
	 * @return boolean
	 */
	public function fileIsSupported(GO_Files_Model_File $file);
	
	/**
	 * Return javascript that will be eval'd by the view to open a file.
	 * 
	 * @param GO_Files_Model_File $file
	 * @return string
	 */
	public function getHandler(GO_Files_Model_File $file);
}
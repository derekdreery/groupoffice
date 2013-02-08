<?php
interface GO_Files_Filehandler_Interface{
	public function supportedExtensions();
	public function getName();
	
	public function fileIsSupported(GO_Files_Model_File $file);
	
	public function getHandler(GO_Files_Model_File $file);
}
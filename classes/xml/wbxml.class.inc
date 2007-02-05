<?php
/**
* @copyright Intermesh 2004
* @author Merijn Schering <mschering@intermesh.nl>
* @version $Revision: 1.10 $ $Date: 2006/12/06 09:47:03 $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or (at your
* option) any later version.
**/

/**
* This class is used to parse and produce RFC822 formatted E-mail strings.
* It was written because imap_rfc822_parse_addr_list() does a terrible job.
*
* @package Framework
* @subpackage XML  
* @author   Merijn Schering <mschering@intermesh.nl>
* @since    Group-Office 2.10
*/

class wbxml
{
	/**
	* Temporary file for the WBXML data
	*
	* @var     String
	* @access  private
	*/
	var $wbxmlfile = '/tmp/tmp.wbxml';
	
	/**
	* Temporary file for the XML data
	*
	* @var     String
	* @access  private
	*/
	var $xmlfile = '/tmp/tmp.xml';	
	
	/**
	* Constructor. Set's temporary file names
	*
	* @access public
	* @return void
	*/
	function wbxml()
	{
		global $GO_CONFIG;
		
		$this->wbxmlfile = $GO_CONFIG->tmpdir.md5(uniqid(time())).'.wbxml';
		$this->xmlfile = $GO_CONFIG->tmpdir.md5(uniqid(time())).'.xml';
	}
	
	/**
	* Converts a WBXML string to XML
	*
	* @param	string	wbxml	The WBXML data
	* @access public
	* @return string XML
	*/
	function to_xml($wbxml)
	{		
		//create temp file
		$fp = fopen($this->wbxmlfile, 'w+');
		fwrite($fp, $wbxml);
		fclose($fp);
		//convert temp file
		exec('wbxml2xml -o '.$this->xmlfile.' '.$this->wbxmlfile);
		
		if(!file_exists($this->xmlfile))
		{
			go_log(LOG_DEBUG, 'Fatal error: wbxml2xml conversion failed');
			return false;
		}
		
		//read xml
		$wbxml = trim(file_get_contents($this->xmlfile));
		
		//remove temp files
		unlink($this->xmlfile);
		unlink($this->wbxmlfile);			
		return $wbxml;		
	}
	
	/**
	* Converts a XML string to WBXML
	*
	* @param	string	wbxml	The WBXML data
	* @access public
	* @return string WBXML
	*/
	function to_wbxml($xml)
	{
			//create temp file
		$fp = fopen($this->xmlfile, 'w+');
		fwrite($fp, $xml);
		fclose($fp);
		//convert temp file
		exec('xml2wbxml -v 1.2 -o '.$this->wbxmlfile.' '.$this->xmlfile);
		if(!file_exists($this->wbxmlfile))
		{
			go_log(LOG_DEBUG, 'Fatal error: xml2wbxml conversion failed');
			return false;
		}
		
		//read xml
		$xml = trim(file_get_contents($this->wbxmlfile));
		
		//remove temp files
		unlink($this->xmlfile);
		unlink($this->wbxmlfile);		
		return $xml;	
	}
}

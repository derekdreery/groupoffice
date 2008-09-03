<?php
/**
 * This file is part of Group-Office.
 *
 * Group-Office is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with Group-Office; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1615 $ $Date: 2008-04-25 16:18:36 +0200 (vr, 25 apr 2008) $
 * @copyright Copyright Intermesh
 * @package go.xml
 * @since Group-Office 2.10
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
		
		//$this->wbxmlfile = $GO_CONFIG->tmpdir.md5(uniqid(time())).'.wbxml';
		//$this->xmlfile = $GO_CONFIG->tmpdir.md5(uniqid(time())).'.xml';
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
		global $GO_CONFIG;
		
		$this->wbxmlfile = $GO_CONFIG->tmpdir.'wbxml2xml_'.md5(uniqid(time())).'.wbxml';
		$this->xmlfile = $GO_CONFIG->tmpdir.'wbxml2xml_'.md5(uniqid(time())).'.xml';
		
		//create temp file
		
		//file_put_contents did not work with nokia phones because the
		//line ends got mixed up somehow.
		$fp = fopen($this->wbxmlfile, 'w+');
		fwrite($fp, $wbxml);
		fclose($fp);
		//convert temp file
		exec($GO_CONFIG->cmd_wbxml2xml.' -o '.$this->xmlfile.' '.$this->wbxmlfile.' 2>/dev/null');
		
		if(!file_exists($this->xmlfile))
		{
			go_log(LOG_DEBUG, 'Fatal error: wbxml2xml conversion failed');
			return false;
		}
		
		//read xml
		$xml = trim(file_get_contents($this->xmlfile));
		
		//remove temp files
		unlink($this->xmlfile);
		unlink($this->wbxmlfile);			
		return $xml;		
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
		global $GO_CONFIG;
		
		$this->wbxmlfile = $GO_CONFIG->tmpdir.'xml2wbxml_'.md5(uniqid(time())).'.wbxml';
		$this->xmlfile = $GO_CONFIG->tmpdir.'xml2wbxml_'.md5(uniqid(time())).'.xml';
		
		//create temp file
		$fp = fopen($this->xmlfile, 'w+');
		fwrite($fp, $xml);
		fclose($fp);
		
		
		//convert temp file
		exec($GO_CONFIG->cmd_xml2wbxml.' -v 1.2 -o '.$this->wbxmlfile.' '.$this->xmlfile.' 2>/dev/null');
		if(!file_exists($this->wbxmlfile))
		{
			go_log(LOG_DEBUG, 'Fatal error: xml2wbxml conversion failed');
			return false;
		}
		
		//read xml
		$wbxml = trim(file_get_contents($this->wbxmlfile));
		
		//remove temp files
		unlink($this->xmlfile);
		unlink($this->wbxmlfile);		
		return $wbxml;	
	}
}

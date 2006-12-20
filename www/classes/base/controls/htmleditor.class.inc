<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.5 $ $Date: 2006/04/26 13:52:59 $
 *
   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Create a FCKeditor
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */

require($GO_CONFIG->control_path.'FCKeditor/fckeditor.php');

class htmleditor extends FCKeditor
{
	var $value;
	var $ForceTextMode=false;
	
	function htmleditor($name, $value='')
	{
		global $GO_CONFIG;
		
		$this->FCKeditor($name);
		$this->Config=array('CustomConfigurationsPath'=>$GO_CONFIG->control_url.'FCKeditor/go_fckconfig.js', 'LinkUpload','false');
		$this->BasePath	= $GO_CONFIG->control_url.'FCKeditor/';
		$this->Value		= $value;
		$this->Width		= '100%' ;
		$this->Height		= '100%' ;
		//$this->ToolbarSet='email';
	}
	
	function SetConfig($key, $value)
	{
		$this->Config[$key]=$value;	
	}
	function setImagePath($path)
	{
		$_SESSION['htmleditor_imagemanager_path'] = $path;
	}
	
	function setAutoDataDefinitionsPath($path)
	{
		$_SESSION['GO_SESSION']['go_autodata_definitions']=$path;
	}
	function ForceTextMode()
	{
		$this->ForceTextMode=true;
	}
	
	function IsCompatible()
	{
		global $HTTP_USER_AGENT ;
		
		if($this->ForceTextMode)
		{
			return false;
		}

		if ( isset( $HTTP_USER_AGENT ) )
			$sAgent = $HTTP_USER_AGENT ;
		else
			$sAgent = $_SERVER['HTTP_USER_AGENT'] ;

		if ( strpos($sAgent, 'MSIE') !== false && strpos($sAgent, 'mac') === false && strpos($sAgent, 'Opera') === false )
		{
			$iVersion = (float)substr($sAgent, strpos($sAgent, 'MSIE') + 5, 3) ;
			return ($iVersion >= 5.5) ;
		}
		else if ( strpos($sAgent, 'Gecko/') !== false )
		{
			$iVersion = (int)substr($sAgent, strpos($sAgent, 'Gecko/') + 6, 8) ;
			return ($iVersion >= 20030210) ;
		}
		else
			return false ;
	}
}
?>

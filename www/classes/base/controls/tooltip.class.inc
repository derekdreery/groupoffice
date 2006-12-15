<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.3 $ $Date: 2006/05/31 09:32:49 $

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Creates a mouseover popup box
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */

class tooltip
{
	var $show_command = '';
	var $hide_command = 'return nd();';
	
	function tooltip($text, $caption='', $options='')
	{
		if ($text != '')
    {
      $this->show_command = 'return overlib(\''.addslashes(htmlspecialchars($text)).'\'';

      if ($caption != '')
      {
				$this->show_command .= ',CAPTION, \''.addslashes(htmlspecialchars($caption)).'\'';
      }
      if ($options != '')
      {
				$this->show_command .= ','.$options;
      }
      $this->show_command .= ');';
    }
	}
	
  function get_header()
  {
    global $GO_CONFIG, $GO_THEME;

    return '<script type="text/javascript" src="'.$GO_CONFIG->control_url.'overlibmws/overlibmws.js"></script>'.
      "<script type=\"text/javascript\">OLpageDefaults(".
      "TEXTFONTCLASS,'olFont',FGCLASS,'olForeground',BGCLASS,'olBackground',CGCLASS,'olCBackground',".
      "CAPTIONFONTCLASS,'olCaptionFont', CLOSEFONTCLASS, 'olCaptionFont', HAUTO, VAUTO,DELAY, 250);</script>".
      '<link href="'.$GO_THEME->theme_url.'css/overlib.css" type="text/css" rel="stylesheet" />';
  }
}
?>

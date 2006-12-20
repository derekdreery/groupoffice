<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.8 $ $Date: 2005/05/30 12:59:30 $

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

class overlib
{
  function get_header()
  {
    global $GO_CONFIG, $GO_THEME;

    /*return '<script type="text/javascript" src="'.$GO_CONFIG->control_url.'overlib/overlib.js"></script>'.
      "<script type=\"text/javascript\">overlib_pagedefaults(CSSCLASS, ".
      "TEXTFONTCLASS,'olFont',FGCLASS,'olForeground', BGCLASS,'olBackground',".
      "CAPTIONFONTCLASS,'olCaptionFont', CLOSEFONTCLASS, 'olCaptionFont', HAUTO, VAUTO);</script>".
      '<link href="'.$GO_THEME->theme_url.'overlib.css" type="text/css" rel="stylesheet" />';*/
    return '<script type="text/javascript" src="'.$GO_CONFIG->control_url.'overlibmws/overlibmws.js"></script>'.
      "<script type=\"text/javascript\">OLpageDefaults(".
      "TEXTFONTCLASS,'olFont',FGCLASS,'olForeground',BGCLASS,'olBackground',CGCLASS,'olCBackground',".
      "CAPTIONFONTCLASS,'olCaptionFont', CLOSEFONTCLASS, 'olCaptionFont', HAUTO, VAUTO,DELAY, 250);</script>".
      '<link href="'.$GO_THEME->theme_url.'css/overlib.css" type="text/css" rel="stylesheet" />';
  }

  function print_overlib($text, $caption='', $options='')
  {
    if ($text != '')
    {
      $overlib = 'onmouseover="return overlib(\''.addslashes($text).'\'';

      if ($caption != '')
      {
	$overlib .= ',CAPTION, \''.addslashes($caption).'\'';
      }
      if ($options != '')
      {
	$overlib .= ','.$options;
      }
      $overlib .= ');" onmouseout="return nd();"';
      return $overlib;
    }
  }
}

<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.11 $ $Date: 2006/11/23 12:39:26 $

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Creates a javascript control to enter a date
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */

class date_picker
{
	var $outerHTML;
	
  function get_header()
  {
    global $GO_CONFIG, $GO_THEME, $jscalendar_language_name;

    $jscalendar_language_name = $jscalendar_language_name != '' ? $jscalendar_language_name : 'calendar-en.js';
    $headers  = '<script type="text/javascript" src="'.$GO_CONFIG->control_url.'jscalendar/calendar.js"></script>'.
    	'<script type="text/javascript" src="'.$GO_CONFIG->control_url.'jscalendar/lang/'.$jscalendar_language_name.'"></script>'.
   		'<script type="text/javascript" src="'.$GO_CONFIG->control_url.'jscalendar/calendar-setup.js"></script>'.
    	'<link href="'.$GO_THEME->theme_url.'css/jscalendar.css" type="text/css" rel="stylesheet" />';

    return $headers;
  }

  function print_date_picker($name, $date_format, $set_date='', $flat_div_id='', $flat_callbackfunction='', $arguments='', $disabled=false)
  {
	echo  $this->get_date_picker($name, $date_format, $set_date, $flat_div_id, $flat_callbackfunction, $arguments, $disabled);
  }

  function date_picker($name, $date_format, $set_date='', $flat_div_id='', $flat_callbackfunction='', $arguments='', $disabled=false)
  {
	$str_return='';
    //dirty hack for mysql default date
    if ($set_date == '00-00-0000')
    {
      $set_date = '';
    }
    if ($disabled)
    {
      $arguments .= ' disabled';
    }
    $date_format = $this->convert_date_format($date_format);

    if ($flat_div_id == '')
    {
      $str_return.='<input onfocus="this.select();" class="textbox" type="text" id="'.$name.'_input" name="'.$name.'" value="'.$set_date.'" '.$arguments.' />'.
	'<input type="button" name="'.$name.'_button" id="'.$name.'_button" value="..."';
      if ($disabled)
      {
	$str_return.=' disabled';
      }
      $str_return.='/>';
    }

    $first_weekday = isset($_SESSION['GO_SESSION']['first_weekday']) ? $_SESSION['GO_SESSION']['first_weekday'] : 0;

    $str_return.='<script type="text/javascript">
      var calendar = Calendar.setup(
	  {
	  electric: false,
firstDay  : '.$first_weekday.',
inputField  : "'.$name.'_input",
ifFormat    : "'.$date_format.'",';

if ($set_date != '')
{
$str_return.='date :  "'.str_replace('-','/',$set_date).'",';
}

if ($flat_div_id != '')
{
$str_return.='flat : "'.$flat_div_id.'", flatCallback : '.$flat_callbackfunction;
}else
{
$str_return.='button      : "'.$name.'_button"';
}
$str_return.='});</script>';
$this->outerHTML = $str_return;
      }

function get_html()
{
	return $this->outerHTML;
}

function convert_date_format($php_date_format)
{
  /*
     %a 	abbreviated weekday name
     %A 	full weekday name
     %b 	abbreviated month name
     %B 	full month name
     %C 	century number
     %d 	the day of the month ( 00 .. 31 )
     %e 	the day of the month ( 0 .. 31 )
     %H 	hour ( 00 .. 23 )
     %I 	hour ( 01 .. 12 )
     %j 	day of the year ( 000 .. 366 )
     %k 	hour ( 0 .. 23 )
     %l 	hour ( 1 .. 12 )
     %m 	month ( 01 .. 12 )
     %M 	minute ( 00 .. 59 )
     %n 	a newline character
     %p 	\x{201C}PM\x{201D} or \x{201C}AM\x{201D}
     %P 	\x{201C}pm\x{201D} or \x{201C}am\x{201D}
     %S 	second ( 00 .. 59 )
     %s 	number of seconds since Epoch (since Jan 01 1970 00:00:00 UTC)
     %t 	a tab character
     %U, %W, %V 	the week number
     %u 	the day of the week ( 1 .. 7, 1 = MON )
     %w 	the day of the week ( 0 .. 6, 0 = SUN )
     %y 	year without the century ( 00 .. 99 )
     %Y 	year including the century ( ex. 1979 )
     %% 	a literal % character
   */


  $cal_format[] = '%e';
  $php_format[] = 'j';

  $cal_format[] = '%d';
  $php_format[] = 'd';

  $cal_format[] = '%H';
  $php_format[] = 'H';

  $cal_format[] = '%I';
  $php_format[] = 'G';

  $cal_format[] = '%k';
  $php_format[] = 'h';

  $cal_format[] = '%l';
  $php_format[] = 'g';

  $cal_format[] = '%P';
  $php_format[] = 'a';

  $cal_format[] = '%Y';
  $php_format[] = 'Y';

  $cal_format[] = '%y';
  $php_format[] = 'y';

  $cal_format[] = '%m';
  $php_format[] = 'm';

  for ($i=0;$i<count($php_format);$i++)
  {
    $php_date_format = str_replace($php_format[$i], $cal_format[$i], $php_date_format);
  }
  return $php_date_format;
}


}
?>

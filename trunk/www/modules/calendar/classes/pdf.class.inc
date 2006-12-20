<?php

/**
*  Copyright Lorenz Softwareentwicklung & Systemintegration 2004
*  Author: Georg Lorenz <georg@lonux.de>
*  Version: 0.99 Release date: 11 Juni 2004
*
*  This program is free software; you can redistribute it and/or modify it
*  under the terms of the GNU General Public License as published by the
*  Free Software Foundation; either version 2 of the License, or (at your
*  option) any later version.
* 
**/

/**
* This file holds the pdf class containing methods and properties
* for generating pdf files from calendars and views
*
* @package  calendar pdf class
* @author   Georg Lorenz <georg@lonux.de>
* @since    Group-Office 2.06
*/

define('FPDF_FONTPATH', $GO_CONFIG->class_path.'fpdf/font/');
require_once($GO_CONFIG->class_path.'fpdf/xfpdf.class.inc');

class pdf extends XFPDF
{
  var $cell;
  
  function pdf($orientation='L',$unit='mm',$format='A4')
  {
	$this->FPDF($orientation,$unit,$format);
	$this->cell = array();
  }

  /**
  * Prints the table header row.
  *
  * @param  array $captions		Contains an array with table header titles.
  * @param  array $bg_colors	Background colors for the table header line.
  * @access public
  * @return void
  */
  function print_table_header($captions, $bg_colors)
  {
	//set the default background color
	$this->_set_bg_color("#FFF7EF");
		
	$border['weekday'] = 'LTR';
	$border['date'] = 'LBR';
	$border['holiday'] = 'LBR';
	
	$is_holiday = false;
	foreach($captions['holiday'] as $value)
	{
	  if($value)
	  {
	    $border['date'] = 'LR';
		$is_holiday = true;
	  }
	}
	
	foreach($captions as $type => $caption)
	{
	  if($type == 'holiday' && !$is_holiday)
	  {
	    break;
	  }
	  
	  $this->_set_table_header_cell(11, $border[$type]);
	  $this->_print_cell("");
	  $this->_set_table_header_cell(38, $border[$type]);
	  
	  for($day=0;$day<count($caption);$day++)
	  {
		$this->_set_bg_color($bg_colors[$day]);
		$this->_print_cell($caption[$day]);
	  }
	  $this->Ln();
	}
  }

  /**
  * Prints the table body row.
  *
  * @param  array $events		Contains an array with calendar events' elements relevant for printing.
  * @param  int $events_count	Count of events for a day containing the most of them for 
  * 							determination of line count to be printed in a cell.
  * @param  string $time_row	Contains either the text of time value (e.g. 10:00) to be
  * 							printed into the first table column or it is simply empty (table header row).
  * @access public
  * @return void
  */
  function print_body_row($events, $events_count, $time_row="")
  {
	$event_rows = ($events_count > 0) ? $events_count : 1;
	
	for($event_row=0;$event_row<$event_rows;$event_row++)
	{
	  $border = 'LR';
	  if($event_row == 0)
	  {
	    if($event_row == $event_rows-1)
		{
	      $border = $border.'TB';
		}else
		{
		  $border = $border.'T';
		}
	  }else
	  {
	    if($event_row == $event_rows-1)
		{
		  $border = $border.'B';
		}
	  }
	  //set the properties for the first column of the table (time column)
  	  $this->_set_table_header_cell(11, $border);
	  $this->_set_bg_color("#F7F3F7");
      $time_row = ($time_row != "" && $event_row == 0) ? $time_row.":00" : "";
	  $this->_print_cell($time_row);
	  
      $this->_set_table_body_cell(38, $border);

	  foreach($events as $event)
	  {
		$color = isset($event['color'][$event_row]) ? $event['color'][$event_row] : $event['color'][0];
	    $this->_set_bg_color($color);
		$text = !empty($event['name'][$event_row]) ? $event['name'][$event_row] : "";
        $this->_print_cell($text);
	  }
	  $this->Ln();
	}
  }
  
  /**
  * Sends the opened pdf file directly to the default printer.
  *
  * @param  bool $dialog	If set to false the pdf file will be printed immediately, else popups the printer dialog.
  * @access public
  * @return void
  */
  function auto_print($dialog=false)
  {
	$parm = $dialog ? 'true' : 'false';
	$script = "print(".$parm.");";
	$this->IncludeJS($script);
  }
  
  /**
  * Prints the page header row. This function mustn't be called manually.
  * It is called automatically on page brakes.
  *
  * @param  void
  * @access private
  * @return void
  */
  function Header()
  {
    global $week_title, $title;

    $this->_set_header_cell();
	//Background color
	$this->SetFillColor(255,255,255);
    //Title
    $this->_print_cell($title);
	//Background color
	$this->SetFillColor(102,102,102);
	//Foreground color
	$this->SetTextColor(255,255,255);
	//Week_Title
    $this->_print_cell($week_title);
  }
  
  /**
  * Sets the cell to be printed
  *
  * @param  string $text	Text to be printed
  * @access private
  * @return void
  */
  function _print_cell($text)
  {
    $this->Cell($this->cell['width'],$this->cell['height'],utf8_decode($text),$this->cell['border'],$this->cell['ln'],$this->cell['align'],$this->cell['fill']);
  }

  /**
  * Sets the page header cell properties.
  *
  * @param  float $width  	Cell width. If 0, the cell extends up to the right margin.
  * @param  mixed $border	Indicates if borders must be drawn around the cell.
  * 						The value can be either a number or a string 
  * 						containing some or all of the following characters:
  * 						0: no border, 1: frame, L:left, T:top, B:bottom, R:right
  * @access private
  * @return void
  */
  function _set_header_cell()
  {
    $this->SetFont('Arial','B',10);
	
    $this->cell['width'] = 0;
	$this->cell['height'] = 5;
	$this->cell['border'] = 0;
	$this->cell['ln'] = 1;
	$this->cell['align'] = 'L';
	$this->cell['fill'] = 1;
  }
  
  /**
  * Sets the table header cell properties.
  *
  * @param  float $width  	Cell width. If 0, the cell extends up to the right margin.
  * @param  mixed $border	Indicates if borders must be drawn around the cell.
  * 						The value can be either a number or a string 
  * 						containing some or all of the following characters:
  * 						0: no border, 1: frame, L:left, T:top, B:bottom, R:right
  * @access private
  * @return void
  */
  function _set_table_header_cell($width, $border)
  {
    $this->SetFont('Arial','',7);
	$this->SetTextColor(0,0,0);
	
	$this->cell['width'] = $width;
	$this->cell['height'] = 4;
	$this->cell['border'] = $border;
	$this->cell['ln'] = 0;
	$this->cell['align'] = 'C';
	$this->cell['fill'] = 1;
  }

  /**
  * Sets the table body cell properties.
  *
  * @param  float $width  	Cell width. If 0, the cell extends up to the right margin.
  * @param  mixed $border	Indicates if borders must be drawn around the cell.
  * 						The value can be either a number or a string 
  * 						containing some or all of the following characters:
  * 						0: no border, 1: frame, L:left, T:top, B:bottom, R:right
  * @access private
  * @return void
  */
  function _set_table_body_cell($width, $border)
  {
    $this->SetFont('Arial','',7);
	$this->SetTextColor(0,0,0);
	
	$this->cell['width'] = $width;
	$this->cell['height'] = 4;
	$this->cell['border'] = $border;
	$this->cell['ln'] = 0;
	$this->cell['align'] = 'L';
	$this->cell['fill'] = 1;
  }

  /**
  * Sets the background color
  *
  * @param  string $bg_color HTML color code: e.g. #FFFFFF
  * @access private
  * @return void
  */
  function _set_bg_color($bg_color="#FFFFFF")
  {
	$bg_color = hex2dec($bg_color);
	$this->SetFillColor($bg_color['R'],$bg_color['G'],$bg_color['B']);
  }
}

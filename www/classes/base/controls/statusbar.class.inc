<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.4 $ $Date: 2005/11/04 14:50:46 $
 *
   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.

 * @package Framework
 * @subpackage Controls
 */

/**
 * Creates a status bar
 * 
 * @package Framework
 * @subpackage Controls
 * 
 * @access public
 */

class statusbar
{
  var $width = "250px";
  var $height = "10px";
  var $info_text;
  var $turn_red_point = 0;
  
  var $usage;
  var $limit;
	
	function statusbar($usage, $limit, $width='150', $height='10', $turn_red_point=95)
	{
		$this->turn_red_point = $turn_red_point;
		$this->usage = $usage;
		$this->limit = $limit;
  	$this->width = $width;
  	$this->height = $height;
	}

  function get_html()
  {
    if ($this->limit != 0)
    {
      $percentage = ($this->usage/$this->limit)*100;
      $usage_width = number_format(($this->usage/$this->limit)*$this->width);
      $remaining_width = $this->width - $usage_width;
    }else
    {
      $usage_width= 0;
      $remaining_width = $this->width;
    }
  
    
    $div = new html_element('div');
    $div->set_attribute('class', 'statusBar');
    $div->set_attribute('style','height:'.$this->height.';width:'.$this->width);        
    
    $statusdiv = new html_element('div');
    $statusdiv->set_attribute('style','height:'.$this->height.';width:'.$usage_width);
    if ($this->turn_red_point > 0 && $percentage >= $this->turn_red_point)
    {
      $statusdiv->set_attribute('class', 'statusBarUsageCritical');
    }else
    {
    	$statusdiv->set_attribute('class', 'statusBarUsage');
    }
    $div->add_html_element($statusdiv);    
    
    $div->set_tooltip(new tooltip($this->info_text));
    
      
    
    return $div->get_html();
  }
  
  function print_bar($usage, $limit)
  {
  	$this->usage = $usage;
  	$this->limit = $limit;
  	return $this->get_html();
  }
}
?>

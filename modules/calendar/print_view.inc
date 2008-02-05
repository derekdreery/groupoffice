<?php

$cal_index = isset($cal_index) ? $cal_index : 0;

$events = $cal->get_events_in_array($calendar_id, $view_id, 0, 
  $interval_start_time, $interval_end_time, $cal_start_hour);

for($row=0;$row<$rows;$row++)
{
  $start_day = $row*$max_columns;
  $end_day = $start_day+$max_columns;
  $current_row = $row + $cal_index;
  
  if ($end_day > $cal_settings['show_days'])
  {
    $end_day = $cal_settings['show_days'];
  }
  
  //initiate a page break if not enough space on page for printing of week view
  if($pdf->GetY() > 110)
  {
    $tmp_weeknumber = date("W", mktime(0,0,0,$month,$day+$start_day,$year));
    $week_title = $sc_week.' '.$tmp_weeknumber;
    $pdf->AddPage();
  }

  $print_header = array();
  $colors = array();
  
  for ($day_offset=$start_day;$day_offset<$end_day;$day_offset++)
  {
    $day_to_display = $day+$day_offset;
    $new_time = mktime(0,0, 0, $month, $day_to_display, $year);
    $new_date = date(DB_DATE_FORMAT, $new_time);
    $new_day = date('j', $new_time);
    $new_month = date('m', $new_time);
    $new_year = date('Y', $new_time);

    //get holiday name if ever
    $holiday_name = "";
    if($holidays->get_holiday($GO_SECURITY->user_id, $calendar_id, $new_time))
    {
      $holiday_name = $holidays->f("name");
    }

    $weekday = date('w', $new_time);
	
	$colors[] = ($new_date == $current_date) ? "#CCCCCC" : "#FFF7EF";
	$print_header['weekday'][] = $full_days[$weekday];
	$print_header['date'][] = date($_SESSION['GO_SESSION']['date_format'],$new_time);
	$print_header['holiday'][] = $holiday_name;
  }
  $pdf->print_table_header($print_header, $colors);

  //process the all day events
  if (count($events['all_day']))
  {
    $print_events = array();
	$events_count = 0;
	
    for ($day_offset=$start_day;$day_offset<$end_day;$day_offset++)
    {
      $day_to_display = $day+$day_offset;
      $new_time = mktime(0,0, 0, $month, $day_to_display, $year);
      $new_date = date('Ymd', $new_time);
	  $count = 0;

      if (isset($events['all_day'][$new_date]))
      {
		while ($event = array_shift($events['all_day'][$new_date]))
	    {
		  $count +=1;
		  $print_events[$new_date]['name'][] = $event['name'];
		  $print_events[$new_date]['color'][] = "#".$event['background'];
	    }
      }else
	  {
		$print_events[$new_date]['name'][] = "";
		$print_events[$new_date]['color'][] = "#FFFFFF";
	  }
	  $events_count = ($events_count < $count) ? $count : $events_count;
    }
	$pdf->print_body_row($print_events, $events_count);
  }

  for ($hour_of_day=$cal_start_hour;$hour_of_day<=$cal_end_hour;$hour_of_day++)
  {
    $print_events = array();
	$events_count = 0;
	$time_row = "";

    //display the days row by row
    for ($day_offset=$start_day;$day_offset<$end_day;$day_offset++)
    {
      $day_to_display = $day+$day_offset;

      //make a valid date
      $new_time = mktime(0,0, 0, $month, $day_to_display, $year);
      $new_date = date('Ymd', $new_time);
      $new_day = date("j", $new_time);
      $new_month = date('m', $new_time);
      $new_year = date('Y', $new_time);		
	  $count = 0;
	  
      if(isset($events['timed'][$new_date][$hour_of_day]))
      {
        while($event = array_shift($events['timed'][$new_date][$hour_of_day]))
	    {
		  $count +=1;
		  
		  $date = getdate($event['start_time']);
		  $date['hours'] = ($date['hours'] < 10) ? "0".$date['hours'] : $date['hours'];
		  $date['minutes'] = ($date['minutes'] < 10) ? "0".$date['minutes'] : $date['minutes'];
		  $start_time = $date['hours'].":".$date['minutes'];
		  
		  $date = getdate($event['end_time']);
		  $date['hours'] = ($date['hours'] < 10) ? "0".$date['hours'] : $date['hours'];
		  $date['minutes'] = ($date['minutes'] < 10) ? "0".$date['minutes'] : $date['minutes'];
		  $end_time = $date['hours'].":".$date['minutes'];

		  $print_events[$new_date]['name'][] = $start_time."-".$end_time." ".$event['name'];
		  $print_events[$new_date]['color'][] = "#".$event['background'];
	    }
      }else
	  {
		$print_events[$new_date]['name'][] = "";
		if (isset($events['backgrounds'][$new_date][$hour_of_day]))
	    {
	      $print_events[$new_date]['color'][] = "#".$events['backgrounds'][$new_date][$hour_of_day];
	    }else
		{
		  $print_events[$new_date]['color'][] = "#FFFFFF";
		}
	  }
	  $events_count = ($events_count < $count) ? $count : $events_count;
    }
	$pdf->print_body_row($print_events, $events_count, $hour_of_day);
  }
}

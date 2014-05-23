<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Michael de Hart <mdhart@intermesh.nl>
 */
class GO_Calendar_Controller_Report extends GO_Base_Controller_AbstractJsonController {
	
	public function actionWeek($date) {
		
		$date = GO_Base_Util_Date::clear_time($date);
		
		$weekday =date('w',$date);
		if($weekday===0)
			$weekday=7;
		$weekday--;
		
		$start = $date-3600*24*($weekday);
		$end = $date+3600*24*(7-$weekday);
		
		$calendar = new GO_Calendar_Model_Calendar();
		$calendar->id = 1;//$cal_id;
		$events = $calendar->getEventsForPeriod($start, $end); //GO_Calendar_Model_Event::model()->findForPeriod(null, $start, $end);
		
		$report = new GO_Calendar_Reports_Week();
		$report->day = $start;
		$report->setEvents($events);
		$report->render($start);
		$report->Output('week.pdf');
	}
	
	public function actionMonth($date) {
		
		$date = GO_Base_Util_Date::clear_time($date);
		$start = strtotime(date('Y-m-01', $date));
		$end = strtotime(date('Y-m-t', $date));
		
		$calendar = new GO_Calendar_Model_Calendar();
		$calendar->id = 1;//$cal_id;
		$events = $calendar->getEventsForPeriod($start, $end);
		//$events = GO_Calendar_Model_Event::model()->findForPeriod(null, $start, $end);
		$report = new GO_Calendar_Reports_Month();
		$report->day = $start;
		$report->dayEnd = $end;
		$report->render($events);
		header('Content-Type','UTF-8');
		$report->Output('month.pdf');
	}
	
	public function actionDay($date) {
		
		$date = GO_Base_Util_Date::clear_time($date);
		
		//$calendar = GO_Calendar_Model_Calendar::model()->findDefault(GO::user()->id);
		//$pf = GO_Base_Db_FindParams::newInstance()->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('calendar_id',$calendar->id));
		
		$calendar = new GO_Calendar_Model_Calendar();
		$calendar->id = 1;//$cal_id;
		$events = $calendar->getEventsForPeriod($date-1, $date+24*3600);
		//$events = GO_Calendar_Model_Event::model()->findForPeriod($pf, $date-1, $date+24*3600); //findCalculatedForPeriod dont work
		$report = new GO_Calendar_Reports_Day();
		if(!empty($calendar->tasklist)) {
			$tasklist_id = $calendar->tasklist->id;
			$report->tasks = GO_Tasks_Model_Task::model()->findByDate($date,$tasklist_id)->fetchAll();
		}
		$report->setEvents($events);
		$report->render($date);
		$report->Output('day.pdf');
	}
	
}
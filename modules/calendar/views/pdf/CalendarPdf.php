<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

class GO_Calendar_Views_Pdf_CalendarPdf extends GO_Base_Util_Pdf {
	
	private $_start_time = '';
	private $_end_time = '';
	private $_title = '';
	private $_days = '';
	private $_date_range_text = '';	
	private $_calendar;
	private $_results;
	
	public $cell_height = 12;

	public function setParams($response) {
		$this->_start_time = $response['start_time'];
		$this->_end_time = $response['end_time'];
		$this->_title = GO_Base_Fs_File::stripInvalidChars($response['title']);
		$this->_days = ceil(($this->_end_time - $this->_start_time) / 86400);
		$this->_results = $response['results'];
		$this->_date_range_text = $this->_days > 1 ? date(GO::user()->completeDateFormat,$this->_start_time) . ' - ' . date(GO::user()->completeDateFormat,$this->_end_time) : date(GO::user()->completeDateFormat,$this->_start_time);
		
		$this->_loadCurrentCalendar($response['calendar_id']);
		$this->_processEvents();
	}
	
	public function Header() {
		$this->SetY(30);

		$this->SetTextColor(50, 135, 172);
		$this->SetFont($this->font, 'B', 16);
		$this->Write(16, $this->_calendar->name . ' ');
		$this->SetTextColor(125, 162, 180);
		$this->SetFont($this->font, '', 12);
		$this->setY($this->getY() + 3.5, false);
		$this->Write(12, $this->_title);

		$this->setY($this->getY() + 2.5, false);
		$this->SetFont($this->font, 'B', $this->font_size);
		$this->setDefaultTextColor();

		$this->Cell($this->getPageWidth() - $this->getX() - $this->rMargin, 12, $this->_date_range_text, 0, 0, 'R');
	}
	
	private function _processEvents($list=true, $headers=true, $calendar_name=''){
		
		$fullDays = GO::t('full_days');
		$calendarPrinted=false;
		
		for ($i = 0; $i < $this->_days; $i++) {
			$cellEvents[$i] = array();
		}
		
		foreach($this->_results as $event)
			$this->_insertEvent($event,$cellEvents);
				
		if (($this->_days > 1 && $this->_days<60) || !$list) {

			if($headers)
				$this->AddPage();

			$calendarPrinted=true;
			//green border
			$this->SetDrawColor(125, 165, 65);

			$maxCells = $this->_days > 7 ? 7 : $this->_days;
			$minHeight = $this->_days > $maxCells ? 70 : $this->cell_height;

			$nameColWidth = 100;
			$cellWidth = !empty($calendar_name) ? ($this->pageWidth - $nameColWidth) / $maxCells : $this->pageWidth / $maxCells;
			$timeColWidth = $this->GetStringWidth(date(GO::user()->time_format, mktime(23, 59, 0)), $this->font, '', $this->font_size) + 5;

			$time_format = str_replace('G', 'H', GO::user()->time_format);
			$time_format = str_replace('g', 'h', $time_format);

			$this->SetFillColor(248, 248, 248);
			$time = $this->_start_time;

			if ($headers) {
				if (!empty($calendar_name)) {
					$this->Cell($nameColWidth, 20, '', 1, 0, 'L', 1);
				}
				for ($i = 0; $i < $maxCells; $i++) {
 					$label = $this->_days > $maxCells ? $fullDays[date('w', $time)] : $fullDays[date('w', $time)] . ', ' . date(GO::user()->date_format, $time);
					$this->Cell($cellWidth, 20, $label, 1, 0, 'L', 1);
					$time = GO_Base_Util_Date::date_add($time, 1);
				}
				$this->Ln();
			}

			$this->SetFont($this->font, '', $this->font_size);

			$cellStartY = $maxY = $this->getY();
			$pageStart = $this->PageNo();

			$this->_daysDone = 0;
			$weekCounter = 0;

			$tableLeftMargin = $this->lMargin;
			if (!empty($calendar_name)) {
				//$this->SetTextColor(125,165, 65);
				$this->SetTextColor(0, 0, 0);
				$this->MultiCell($nameColWidth, $this->cell_height, $calendar_name, 0, 'L');
				$tableLeftMargin+=$nameColWidth;
				$this->setDefaultTextColor();

				$maxY = $this->getY();
			}


			for ($i = 0; $i < $this->_days; $i++) {
				$pos = $i - $this->_daysDone;
				$this->setPage($pageStart);
				$this->setXY($tableLeftMargin + ($pos * $cellWidth), $cellStartY);

				if ($this->_days > 7) {
					$time = GO_Base_Util_Date::date_add($this->_start_time, $i);
					$this->Cell($cellWidth, $this->cell_height, date('d', $time), 0, 1, 'R');
					$this->setX($tableLeftMargin + ($pos * $cellWidth));
				}

				//while($event = array_shift($cellEvents[$i]))
				foreach ($cellEvents[$i] as $event) {
					//$time = $event['all_day_event'] == '1' ? '-' : date($time_format, $event['start_time']);

					if(empty($event['all_day_event']))
						$event['name']=date($time_format, strtotime($event['start_time'])).': '.$event['name'];

					//$this->Cell($timeColWidth, $this->cell_height, $time, 0, 0, 'L');
					//$this->MultiCell($cellWidth-$timeColWidth,$this->cell_height, $event['name'], 0, 1, 0, 1, '', '', true, 0, false, false, 0);
					//$this->SetFillColor( hexdec(substr($event['background'], 0, 2)),hexdec(substr($event['background'], 2, 2)), hexdec(substr($event['background'], 4, 2)));
					$this->SetFillColor(hexdec(substr($event['background'], 0, 2)), hexdec(substr($event['background'], 2, 2)), hexdec(substr($event['background'], 4, 2)));
					
					//$this->Cell($timeColWidth, $this->cell_height, $time, 0, 0, 'L', 1);
					
					if(!empty($event['status_color']))
						$event_background_color = array(hexdec(substr($event['background'], 0, 2)), hexdec(substr($event['background'], 2, 2)), hexdec(substr($event['background'], 4, 2)));
					else
						$event_background_color = array(125, 165, 65);
					
					$event_name	= $event['name'];
					
					if(!empty($event['status_color'])){
						$event_status_color = array(hexdec(substr($event['status_color'], 0, 2)), hexdec(substr($event['status_color'], 2, 2)), hexdec(substr($event['status_color'], 4, 2)));

						$circleLine = array('width'=>0.5,'color'=>$event_status_color);
						$circleFill = $event_status_color;
						$circleX = $this->getX()+5;
						$circleY = $this->getY()+6;
						$circleRadius = 2.5;

						$this->Circle($circleX,$circleY,$circleRadius,0,360,'FD',$circleLine,$circleFill);
						
						$event_name = '   '.$event['name'];
					}

					$this->SetFillColorArray($event_background_color);
					
					$this->MultiCell($cellWidth /*- $timeColWidth*/, $this->cell_height,$event_name, array('B'=>array('width' => 2,'color' => array(255, 255, 255))), 1, 1, 1, '', '', true, 0, false, false, 0);
					
					// $this->MultiCell($cellWidth /*- $timeColWidth*/, $this->cell_height, $event['name'], 'B', 1, 1, 1, '', '', true, 0, false, false, 0);	
					$this->SetDrawColor(125,165, 65);
					$this->SetLineWidth(1); //similiar to cellspacing
					
					$this->setX($tableLeftMargin + ($pos * $cellWidth));
				}


				$y = $this->getY();
				if ($y < $cellStartY) {
					//went to next page so we must add the page height.
					$y+=$this->h;
				}
				if ($y > $maxY)
					$maxY = $y;


				$weekCounter++;
				if ($weekCounter == $maxCells) {
					$this->setPage($pageStart);

					$weekCounter = 0;
					$this->_daysDone+=$maxCells;

					//minimum cell height
					$cellHeight = $maxY - $cellStartY;
					if ($cellHeight < $minHeight)
						$cellHeight = $minHeight;

					if ($cellHeight + $this->getY() > $this->h - $this->bMargin) {
						$cellHeight1 = $this->h - $this->getY() - $this->bMargin;
						$cellHeight2 = $cellHeight - $cellHeight1 - $this->tMargin - $this->bMargin;

						$this->setXY($this->lMargin, $cellStartY);
						if (!empty($calendar_name)) {
							$this->Cell($nameColWidth, $cellHeight1, '', 'LTR', 0);
						}
						for ($n = 0; $n < $maxCells; $n++) {
							$this->Cell($cellWidth, $cellHeight1, '', 'LTR', 0);
						}
						$this->ln();

						if (!empty($calendar_name)) {
							$this->Cell($nameColWidth, $cellHeight2, '', 'LBR', 0);
						}
						for ($n = 0; $n < $maxCells; $n++) {
							$this->Cell($cellWidth, $cellHeight2, '', 'LBR', 0);
						}
						$this->ln();
					} else {
						$this->setXY($this->lMargin, $cellStartY);
						if (!empty($calendar_name)) {
							$this->Cell($nameColWidth, $cellHeight, '', 1, 0);
						}
						for ($n = 0; $n < $maxCells; $n++) {
							$this->Cell($cellWidth, $cellHeight, '', 1, 0);
						}
						$this->ln();
					}

					$cellStartY = $maxY = $this->getY();
					$pageStart = $this->PageNo();
				}
			}
		}
		
		if ($list) {

			$this->CurOrientation = 'P';

			/*if ($this->_days > 7) {
				$this->AddPage();
			} else {
				$this->w = 595.28;
			}*/
			//if($calendarPrinted)
				$this->AddPage();

			$this->H1(GO::t('printList','calendar'));

			$time = $this->_start_time;
			for ($i = 0; $i < $this->_days; $i++) {

				if (count($cellEvents[$i])) {
					
					$this->ln(10);
					
					$this->setCellPaddings(0,0,0,0);
					
					$this->H3($fullDays[date('w', $time)] . ', ' . date(GO::user()->completeDateFormat, $time));
					
					$this->setCellPaddings(13,0,0,0);
					
					$this->SetFont($this->font, '', $this->font_size);
					
					while ($event = array_shift($cellEvents[$i])) {
						
						if(!empty($event['background'])){
							$event_background_color = array(hexdec(substr($event['background'], 0, 2)), hexdec(substr($event['background'], 2, 2)), hexdec(substr($event['background'], 4, 2)));
							
							$rectLine = array('width'=>0.5,'color'=>$event_background_color);
							$rectFill = $event_background_color;
							$rectX = $this->getX()+1;
							$rectY = $this->getY()+1;

							$this->Rect($rectX, $rectY, 8, 24, 'F',$rectLine,$rectFill);
						}
						else
							$event_background_color = array(0,0,0);
						
						if(!empty($event['status_color'])){
							
							$event_status_color = array(hexdec(substr($event['status_color'], 0, 2)), hexdec(substr($event['status_color'], 2, 2)), hexdec(substr($event['status_color'], 4, 2)));

							$circleLine = array('width'=>0.5,'color'=>$event_status_color);
							$circleFill = $event_status_color;
							$circleX = $this->getX()+5;
							$circleY = $this->getY()+6;
							$circleRadius = 2.5;

							$this->Circle($circleX,$circleY,$circleRadius,0,360,'F',$circleLine,$circleFill);
						}
						
						$this->H4($event['name']);

						if (empty($event['all_day_event'])) {
							$text = sprintf(GO::t('printTimeFormat','calendar'), $event['start_time'], $event['end_time']);
						} else {
							$start_date = date(GO::user()->date_format, strtotime($event['start_time']));
							$end_date = date(GO::user()->date_format, strtotime($event['end_time']));

							if ($start_date == $end_date) {
								$text = sprintf(GO::t('printAllDaySingle','calendar'));
							} else {
								$text = sprintf(GO::t('printAllDayMultiple','calendar'), $start_date, $end_date);
							}
						}

						if (!empty($event['location']))
							$text .= sprintf(GO::t('printLocationFormat','calendar'), $event['location']);

						$pW = $this->getPageWidth() - $this->lMargin - $this->rMargin;
						
						
						$this->Cell($pW, 10, $text, 0, 1);
						if (!empty($event['description'])) {
							$this->ln(4);
							$this->MultiCell($pW, 10, $event['description'], 0, 'L', 0, 1);
						}

						$this->ln(10);
						$lineStyle = array(
							'color' => array(40, 40, 40),
							'width' => .5
						);
						$this->Line($this->lMargin, $this->getY(), $this->getPageWidth() - $this->rMargin, $this->getY(), $lineStyle);
						$this->ln(10);
					}
				}
				$time = GO_Base_Util_Date::date_add($time, 1);
			}
		}
	}

	private function _loadCurrentCalendar($calendarId) {
		
		if(empty($calendarId))
			throw new FileNotFoundException();
		
		$this->_calendar = GO_Calendar_Model_Calendar::model()->findByPk($calendarId);
	}

	private function _insertEvent($event,&$cellEvents) {
		$startTime = strtotime($event['start_time']);
		$endTime = strtotime($event['end_time']);
		
		$startDate = getdate($startTime);
		
		$index_time = mktime(0, 0, 0, $startDate['mon'], $startDate['mday'], $startDate['year']);
		while ($index_time <= $endTime && $index_time < $this->_end_time) {
			if ($this->_calendar->user_id != GO::user()->id && !empty($event['private'])) {
				$event['name'] = GO::t('private','calendar');
				$event['description'] = '';
				$event['location'] = '';
			}

			//$cellIndex = floor(($index_time-$this->_start_time)/86400);
			$cellIndex = GO_Base_Util_Date::date_diff_days($this->_start_time, $index_time);
			$index_time = GO_Base_Util_Date::date_add($index_time, 1);
			$cellEvents[$cellIndex][] = $event;
		}
	}
}

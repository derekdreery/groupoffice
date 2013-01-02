<?php

require_once($GLOBALS['GO_CONFIG']->class_path . 'tcpdf/tcpdf.php');

//ini_set('display_errors', 'off');

class PDF extends TCPDF {

	var $font = 'helvetica';
	var $pageWidth;
	var $font_size = 9;
	var $cell_height = 12;
	var $calendar;

	function __construct() {
		global $GO_CONFIG;

		if (!empty($GLOBALS['GO_CONFIG']->tcpdf_font)) {
			$this->font = $GLOBALS['GO_CONFIG']->tcpdf_font;
		}

		parent::__construct('L', 'pt', 'A4', true, 'UTF-8');

		$this->AliasNbPages();

		$this->setJPEGQuality(100);
		$this->SetMargins(30, 60, 30);

		$this->SetFont($this->font, '', $this->font_size);

		$this->pageWidth = $this->getPageWidth() - $this->lMargin - $this->rMargin;

		$this->SetAutoPageBreak(true, 30);

		//$this->SetTitle($order['order_id']);
		$this->SetSubject('Calendar print');
		$this->SetAuthor($_SESSION['GO_SESSION']['name']);
		//$this->SetCreator($GLOBALS['GO_CONFIG']->product_name . ' ' . $GLOBALS['GO_CONFIG']->version);
		$this->SetKeywords('Calendar print');
	}

	function Footer() {
		global $GO_CONFIG, $lang;

		$this->setDefaultTextColor();
		$this->SetFont($this->font, '', $this->font_size);
		$this->SetY(-20);
		$pW = $this->getPageWidth();
		//$this->Cell($pW / 2, 10, $GLOBALS['GO_CONFIG']->product_name . ' ' . $GLOBALS['GO_CONFIG']->version, 0, 0, 'L');
		$this->Cell($pW / 2, 10, '', 0, 0, 'L');
		$this->Cell(($pW / 2) - $this->rMargin, 10, sprintf($lang['calendar']['printPage'], $this->getAliasNumPage(), $this->getAliasNbPages()), 0, 0, 'R');
	}

	function Header() {

		global $lang;

		$this->SetY(30);

		$this->SetTextColor(50, 135, 172);
		$this->SetFont($this->font, 'B', 16);
		$this->Write(16, $lang['calendar']['name'] . ' ');
		$this->SetTextColor(125, 162, 180);
		$this->SetFont($this->font, '', 12);
		$this->setY($this->getY() + 3.5, false);
		$this->Write(12, $this->title);


		$this->setY($this->getY() + 2.5, false);

		$this->SetFont($this->font, 'B', $this->font_size);
		$this->setDefaultTextColor();

		$this->Cell($this->getPageWidth() - $this->getX() - $this->rMargin, 12, $this->date_range_text, 0, 0, 'R');
	}

	function calcMultiCellHeight($w, $h, $text) {
		$text = str_replace("\r", '', $text);
		$lines = explode("\n", $text);
		$height = count($lines) * $h;

		foreach ($lines as $line) {
			$width = $this->GetStringWidth($line);

			$extra_lines = ceil($width / $w) - 1;
			$height += $extra_lines * $h;
		}
		return $height;
	}

	function H1($title) {
		$this->SetFont($this->font, 'B', 16);
		$this->SetTextColor(50, 135, 172);
		$this->Cell($this->getPageWidth() - $this->lMargin - $this->rMargin, 20, $title, 0, 1);
		$this->setDefaultTextColor();
		$this->SetFont($this->font, '', $this->font_size);
	}

	function H2($title) {
		$this->SetFont($this->font, '', 14);
		$this->Cell($this->getPageWidth() - $this->lMargin - $this->rMargin, 24, $title, 0, 1);
		$this->SetFont($this->font, '', $this->font_size);
	}

	function H3($title) {
		$this->SetTextColor(125, 165, 65);
		$this->SetFont($this->font, 'B', 11);
		$this->Cell($this->getPageWidth() - $this->lMargin - $this->rMargin, 14, $title, '', 1);
		$this->SetFont($this->font, '', $this->font_size);
		$this->setDefaultTextColor();
		$this->ln(4);
	}

	function H4($title) {
		$this->SetFont($this->font, 'B', $this->font_size);
		//	$this->SetDrawColor(90, 90, 90);
		//$this->SetDrawColor(128, 128, 128);

		//go_debug($title.' '.$this->getPageWidth().'-'.($this->lMargin - $this->rMargin));
		$this->Cell($this->getPageWidth() - $this->lMargin - $this->rMargin, 14, $title, '', 1);
		//$this->SetDrawColor(0,0,0);
		$this->SetFont($this->font, '', $this->font_size);
	}

	function setDefaultTextColor() {
		$this->SetTextColor(40, 40, 40);
	}

	function setParams($title, $start_time, $end_time) {
		$this->start_time = $start_time;
		$this->end_time = $end_time;
		$this->title = $title;
		$this->days = ceil(($end_time - $start_time) / 86400);
		$this->date_range_text = $this->days > 1 ? date($_SESSION['GO_SESSION']['date_format'], $start_time) . ' - ' . date($_SESSION['GO_SESSION']['date_format'], $end_time) : date($_SESSION['GO_SESSION']['date_format'], $start_time);
	}

	function setCurrentCalendar($calendar) {
		$this->calendar = $calendar;
	}

	function addCalendar($events, $list=true, $headers=true, $calendar_name='') {
		global $lang, $GO_SECURITY;

		for ($i = 0; $i < $this->days; $i++) {
			$cellEvents[$i] = array();
		}

		while ($event = array_shift($events)) {
			$date = getdate($event['start_time']);
			$index_time = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
			while ($index_time <= $event['end_time'] && $index_time < $this->end_time) {
				if ($this->calendar['user_id'] != $GLOBALS['GO_SECURITY']->user_id && !empty($event['private'])) {
					$event['name'] = $lang['calendar']['private'];
					$event['description'] = '';
					$event['location'] = '';
				}

				//$cellIndex = floor(($index_time-$this->start_time)/86400);
				$cellIndex = Date::date_diff_days($this->start_time, $index_time);
				$index_time = Date::date_add($index_time, 1);
				$cellEvents[$cellIndex][] = $event;
			}
		}

	

		$calendarPrinted=false;

		if (($this->days > 1 && $this->days<60) || !$list) {

			if($headers)
				$this->AddPage();

			$calendarPrinted=true;
			//green border
			$this->SetDrawColor(125, 165, 65);

			$maxCells = $this->days > 7 ? 7 : $this->days;
			$minHeight = $this->days > $maxCells ? 70 : $this->cell_height;

			$nameColWidth = 100;
			$cellWidth = !empty($calendar_name) ? ($this->pageWidth - $nameColWidth) / $maxCells : $this->pageWidth / $maxCells;
			$timeColWidth = $this->GetStringWidth(date($_SESSION['GO_SESSION']['time_format'], mktime(23, 59, 0)), $this->font, '', $this->font_size) + 5;

			$time_format = str_replace('G', 'H', $_SESSION['GO_SESSION']['time_format']);
			$time_format = str_replace('g', 'h', $time_format);

			$this->SetFillColor(248, 248, 248);
			$time = $this->start_time;


			if ($headers) {
				if (!empty($calendar_name)) {
					$this->Cell($nameColWidth, 20, '', 1, 0, 'L', 1);
				}
				for ($i = 0; $i < $maxCells; $i++) {
					$label = $this->days > $maxCells ? $lang['common']['full_days'][date('w', $time)] : $lang['common']['full_days'][date('w', $time)] . ', ' . date($_SESSION['GO_SESSION']['date_format'], $time);
					$this->Cell($cellWidth, 20, $label, 1, 0, 'L', 1);
					$time = Date::date_add($time, 1);
				}
				$this->Ln();
			}

			$this->SetFont($this->font, '', $this->font_size);

			$cellStartY = $maxY = $this->getY();
			$pageStart = $this->PageNo();

			$this->daysDone = 0;
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


			for ($i = 0; $i < $this->days; $i++) {
				$pos = $i - $this->daysDone;
				$this->setPage($pageStart);
				$this->setXY($tableLeftMargin + ($pos * $cellWidth), $cellStartY);

				if ($this->days > 7) {
					$time = Date::date_add($this->start_time, $i);
					$this->Cell($cellWidth, $this->cell_height, date('d', $time), 0, 1, 'R');
					$this->setX($tableLeftMargin + ($pos * $cellWidth));
				}

				//while($event = array_shift($cellEvents[$i]))
				foreach ($cellEvents[$i] as $event) {
					//$time = $event['all_day_event'] == '1' ? '-' : date($time_format, $event['start_time']);

					if(empty($event['all_day_event']))
						$event['name']=date($time_format, $event['start_time']).': '.$event['name'];

					//$this->Cell($timeColWidth, $this->cell_height, $time, 0, 0, 'L');
					//$this->MultiCell($cellWidth-$timeColWidth,$this->cell_height, $event['name'], 0, 1, 0, 1, '', '', true, 0, false, false, 0);
					//$this->SetFillColor( hexdec(substr($event['background'], 0, 2)),hexdec(substr($event['background'], 2, 2)), hexdec(substr($event['background'], 4, 2)));
					$this->SetFillColor(hexdec(substr($event['background'], 0, 2)), hexdec(substr($event['background'], 2, 2)), hexdec(substr($event['background'], 4, 2)));
					//$this->Cell($timeColWidth, $this->cell_height, $time, 0, 0, 'L', 1);
					$this->MultiCell($cellWidth /*- $timeColWidth*/, $this->cell_height, $event['name'], 'B', 1, 1, 1, '', '', true, 0, false, false, 0);
					
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
					$this->daysDone+=$maxCells;

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

			/*if ($this->days > 7) {
				$this->AddPage();
			} else {
				$this->w = 595.28;
			}*/
			//if($calendarPrinted)
				$this->AddPage();

			$this->H1($lang['calendar']['printList']);

			$time = $this->start_time;
			for ($i = 0; $i < $this->days; $i++) {



				if (count($cellEvents[$i])) {

					
					$this->ln(10);
					$this->H3($lang['common']['full_days'][date('w', $time)] . ', ' . date($_SESSION['GO_SESSION']['date_format'], $time));


					$this->SetFont($this->font, '', $this->font_size);
					while ($event = array_shift($cellEvents[$i])) {
						

						$this->H4($event['name']);

						if (empty($event['all_day_event'])) {
							$date_format = date('Ymd', $event['start_time']) == date('Ymd', $event['end_time']) ? $_SESSION['GO_SESSION']['time_format'] : $_SESSION['GO_SESSION']['date_format'] . ' ' . $_SESSION['GO_SESSION']['time_format'];
							$text = sprintf($lang['calendar']['printTimeFormat'], date($_SESSION['GO_SESSION']['time_format'], $event['start_time']), date($date_format, $event['end_time']));
						} else {
							$start_date = date($_SESSION['GO_SESSION']['date_format'], $event['start_time']);
							$end_date = date($_SESSION['GO_SESSION']['date_format'], $event['end_time']);

							if ($start_date == $end_date) {
								$text = sprintf($lang['calendar']['printAllDaySingle']);
							} else {
								$text = sprintf($lang['calendar']['printAllDayMultiple'], $start_date, $end_date);
							}
						}

						if (!empty($event['location']))
							$text .= sprintf($lang['calendar']['printLocationFormat'], $event['location']);

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
				$time = Date::date_add($time, 1);
			}
		}
	}

}
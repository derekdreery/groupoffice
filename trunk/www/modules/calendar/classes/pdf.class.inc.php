<?php
require_once($GO_CONFIG->class_path.'tcpdf/tcpdf.php');

//ini_set('display_errors', 'off');

class PDF extends TCPDF
{
	var $font = 'helvetica';
	var $pageWidth;

	var $font_size=9;

	function __construct()
	{
		parent::__construct('L', 'pt', 'A4', true, 'UTF-8');

		$this->AliasNbPages();

		$this->setJPEGQuality(100);
		$this->SetMargins(30,30,30);

		$this->SetFont($this->font,'',$this->font_size);

		$this->pageWidth =$this->getPageWidth()-$this->lMargin-$this->rMargin;

		$this->SetAutoPageBreak(true, 30);
	}

	function Footer(){
		global $GO_CONFIG, $lang;

		$this->SetY(-20);
		$pW=$this->getPageWidth()-$this->lMargin-$this->rMargin;
		$this->Cell($pW/2, 10, 'Group-Office '.$GO_CONFIG->version, 0, 0, 'L');
		$this->Cell($pW/2, 10, sprintf($lang['calendar']['printPage'], $this->getAliasNumPage(), $this->getAliasNbPages()), 0, 0, 'R');
	}

	function Header(){

	}

	function calcMultiCellHeight($w, $h, $text)
	{
		$text = str_replace("\r",'', $text);
		$lines = explode("\n",$text);
		$height = count($lines)*$h;

		foreach($lines as $line)
		{
			$width = $this->GetStringWidth($line);

			$extra_lines = ceil($width/$w)-1;
			$height += $extra_lines*$h;
		}
		return $height;
	}

	function H1($title)
	{
		$this->SetFont($this->font,'B',18);
		$this->SetTextColor(50,135,172);
		$this->Cell($this->getPageWidth()-$this->lMargin-$this->rMargin,30, $title,0,1);
		$this->setDefaultTextColor();
		$this->SetFont($this->font,'',$this->font_size);
	}

	function H2($title)
	{
		$this->SetFont($this->font,'',14);
		$this->Cell($this->getPageWidth()-$this->lMargin-$this->rMargin,24, $title,0,1);
		$this->SetFont($this->font,'',$this->font_size);
	}

	function H3($title)
	{
		$this->SetTextColor(125,165, 65);
		$this->SetFont($this->font,'B',11);
		$this->Cell($this->getPageWidth()-$this->lMargin-$this->rMargin,14, $title,'',1);
		$this->SetFont($this->font,'',$this->font_size);
		$this->setDefaultTextColor();
		$this->ln(4);
	}

	function H4($title)
	{
		$this->SetFont($this->font,'B',$this->font_size);
		//	$this->SetDrawColor(90, 90, 90);
		//$this->SetDrawColor(128, 128, 128);
		$this->Cell($this->getPageWidth()-$this->lMargin-$this->rMargin,14, $title,'',1);
		//$this->SetDrawColor(0,0,0);
		$this->SetFont($this->font,'',$this->font_size);


	}

	function setDefaultTextColor()
	{
		$this->SetTextColor(40,40,40);
	}

	function addDays($title, $start_time, $end_time, $events)
	{
		global $lang;
		$days = ceil(($end_time-$start_time)/86400);

		$date_range_text = $days > 1 ? date($_SESSION['GO_SESSION']['date_format'], $start_time).' - '.date($_SESSION['GO_SESSION']['date_format'], $end_time) : date($_SESSION['GO_SESSION']['date_format'], $start_time);

		for($i=0;$i<$days;$i++)
		{
			$cellEvents[$i]=array();
		}

		while($event = array_shift($events))
		{
			$cellIndex = floor(($event['start_time']-$start_time)/86400);
			$cellEvents[$cellIndex][]=$event;
		}
			

		if($days>1)
		{

			$this->AddPage();
				
			$this->SetTextColor(50,135,172);
			$this->SetFont($this->font,'B',18);
			$this->Write(18, $lang['calendar']['name'].' ');
			$this->SetTextColor(125,162,180);
			$this->SetFont($this->font,'',12);
			$this->setY($this->getY()+5.5, false);
			$this->Write(12, $title);
				
				
			$this->setY($this->getY()+2.5, false);
				
			$this->SetFont($this->font,'B',$this->font_size);
			$this->setDefaultTextColor();
				
			$this->Cell($this->getPageWidth()-$this->getX()-$this->rMargin,12,$date_range_text,0,0,'R');
				
			$this->ln(30);				
				
			//green border
			$this->SetDrawColor(125,165, 65);
				
			$maxCells = $days>7 ? 7 : $days;
			$cellWidth = $this->pageWidth/$maxCells;
			$timeColWidth=30;

			$this->SetFillColor(248, 248, 248);
			$time = $start_time;
				
				
			for($i=0;$i<$maxCells;$i++)
			{
				$label = $days>$maxCells ? $lang['common']['full_days'][date('w', $time)] : $lang['common']['full_days'][date('w', $time)].', '.date($_SESSION['GO_SESSION']['date_format'], $time);
				$this->Cell($cellWidth, 20, $label, 1,0,'L', 1);
				$time = Date::date_add($time, 1);
			}
			$this->Ln();
				
			$this->SetFont($this->font,'',$this->font_size);
				
			$cellStartY = $maxY= $this->getY();				
				

			$daysDone=0;
			$weekCounter = 0;
			for($i=0;$i<$days;$i++)
			{
				$time = Date::date_add($start_time, $i);

				$pos = $i-$daysDone;
				$this->setXY($this->lMargin+($pos*$cellWidth), $cellStartY);

				$this->Cell($cellWidth, $this->font_size, date('d',$time),0,1,'R');
				$this->setX($this->lMargin+($pos*$cellWidth));

				//while($event = array_shift($cellEvents[$i]))
				foreach($cellEvents[$i] as $event)
				{
					$this->MultiCell($timeColWidth, $this->font_size, date($_SESSION['GO_SESSION']['time_format'],$event['start_time']), 0, 'L',0,0, '', '', true, 0, false, false, 0);
					$this->MultiCell($cellWidth-$timeColWidth, $this->font_size, $event['name'], 0, 1, 0, 1, '', '', true, 0, false, false, 0);
					$this->setX($this->lMargin+($pos*$cellWidth));
				}

				if($this->getY()>$maxY)
				$maxY=$this->getY();
					
				$weekCounter++;
				if($weekCounter==$maxCells)
				{
					$weekCounter=0;
					$daysDone+=$maxCells;
						
					//miniumum cell height
					$cellHeight = $maxY-$cellStartY;
					if($cellHeight<70)
					$cellHeight=70;

					for($n=0;$n<$maxCells;$n++)
					{
						$this->setXY($this->lMargin+($n*$cellWidth), $cellStartY);
						$this->Cell($cellWidth, $cellHeight,'',1,1);
					}
					$this->Ln(0);
					$cellStartY = $maxY= $this->getY();
				}
			}

			$this->ln(20);
		}

		$this->CurOrientation='P';
		$this->AddPage();
		//list

		$this->SetTextColor(50,135,172);
		$this->SetFont($this->font,'B',18);
		$this->Write(18, $lang['calendar']['printList'].' ');
		$this->SetTextColor(125,162,180);
		$this->SetFont($this->font,'',12);
		$this->setY($this->getY()+5.5, false);
		$this->Write(12, $title);


		$this->setY($this->getY()+2.5, false);

		$this->SetFont($this->font,'B',$this->font_size);
		$this->setDefaultTextColor();

		$this->Cell($this->getPageWidth()-$this->getX()-$this->rMargin,12,$date_range_text,0,0,'R');

		$this->ln(30);




		$time = $start_time;
		for($i=0;$i<$days;$i++)
		{
			if(count($cellEvents[$i]))
			{
				$this->ln(10);
				$this->H3($lang['common']['full_days'][date('w', $time)].', '.date($_SESSION['GO_SESSION']['date_format'], $time));
				$time = Date::date_add($time, 1);

				$this->SetFont($this->font,'',$this->font_size);
				while($event = array_shift($cellEvents[$i]))
				{

					$this->H4($event['name']);
					$date_format = date('Ymd', $event['start_time'])==date('Ymd', $event['end_time']) ? $_SESSION['GO_SESSION']['time_format'] : $_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'];
					$text = sprintf($lang['calendar']['printTimeFormat'], date($_SESSION['GO_SESSION']['time_format'],$event['start_time']), date($date_format,$event['end_time']));
						
					if(!empty($event['location']))
					$text .= sprintf($lang['calendar']['printLocationFormat'], $event['location']);

					$pW=$this->getPageWidth()-$this->lMargin-$this->rMargin;
						
					$this->Cell($pW,10, $text, 0, 1);
					if(!empty($event['description']))
					{
						$this->ln(4);
						$this->MultiCell($pW,10, $event['description'],0,'L',0,1);
					}
						
					$this->ln(10);
					$lineStyle = array(
						'color'=>array(40,40,40),
						'width'=>.5				
					);
					$this->Line($this->lMargin+$this->cMargin,$this->getY(), $this->getPageWidth()-$this->rMargin-$this->cMargin,$this->getY(), $lineStyle);
					$this->ln(10);

				}
			}
		}
	}
}
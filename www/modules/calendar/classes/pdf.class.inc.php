<?php
require_once($GO_CONFIG->class_path.'tcpdf/tcpdf.php');

//ini_set('display_errors', 'off');

class PDF extends TCPDF
{
	var $font = 'vera';
	var $pageWidth;
	
	function __construct()
	{
		parent::__construct('L', 'pt', 'A4', true, 'UTF-8');
		
		$this->AliasNbPages(); 

		$this->setJPEGQuality(100);
		$this->SetMargins(30,30,30);
				
		$this->SetFont($this->font,'',8);
		
		$this->pageWidth =$this->getPageWidth()-$this->lMargin-$this->rMargin;

		$this->SetAutoPageBreak(true, 30);
	}

	function Footer(){

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
		$this->SetFont($this->font,'',18);
		$this->Cell($this->pageWidth,30, $title,0,1);
		$this->SetFont($this->font,'',8);
	}

	function H2($title)
	{
		$this->SetFont($this->font,'B',10);
		$this->Cell($this->pageWidth,20, $title,0,1);
		$this->SetFont($this->font,'',8);
	}

	function addDays($start_time, $end_time, $events)
	{
		$days = ceil(($end_time-$start_time)/86400);		
		$cellWidth = $this->pageWidth/$days;
		$timeColWidth=30;
		
		
		$this->SetFillColor(224, 235, 255); 
		$time = $start_time;
		for($i=0;$i<$days;$i++)
		{			
			$cellEvents[$i]=array();
			$this->Cell($cellWidth, 12, date($_SESSION['GO_SESSION']['date_format'], $time), 1,0,'L', 1);
			$time = Date::date_add($time, 1);
		}
		
		$this->Ln();
		$cellStartY = $maxY= $this->getY();
		
		while($event = array_shift($events))
		{
			$cellIndex = floor(($event['start_time']-$start_time)/86400);
			$cellEvents[$cellIndex][]=$event;
		}
		
		
		
		for($i=0;$i<$days;$i++)
		{	
			$this->setXY($this->lMargin+($i*$cellWidth), $cellStartY);
			
			while($event = array_shift($cellEvents[$i]))
			{
				$this->Cell($timeColWidth, 12, date($_SESSION['GO_SESSION']['time_format'],$event['start_time']), 0, 0, 'R');
				$this->Cell($cellWidth-$timeColWidth, 12, $event['name'], 0, 1);
				$this->setX($this->lMargin+($i*$cellWidth));
			}
			
			if($this->getY()>$maxY)
				$maxY=$this->getY();			
		}
		
		for($i=0;$i<$days;$i++)
		{
			$this->setXY($this->lMargin+($i*$cellWidth), $cellStartY);
			$this->Cell($cellWidth, $maxY-$cellStartY,'',1);
		}
		
	}
}
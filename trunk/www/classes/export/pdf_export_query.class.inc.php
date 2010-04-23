<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require_once($GO_CONFIG->class_path.'tcpdf/tcpdf.php');


class pdf_export_query extends base_export_query{
	var $extension='pdf';

	var $row_body_column=false;

	function init_columns(){
		parent::init_columns();

		if(!empty($_SESSION['GO_SESSION']['export_queries'][$_REQUEST['query']]['pdf_row_body_column'])){
			$this->row_body_column = $_SESSION['GO_SESSION']['export_queries'][$_REQUEST['query']]['pdf_row_body_column'];

			$key = array_search($this->row_body_column, $this->columns);
			unset($this->columns[$key]);
			unset($this->headers[$key]);
		}
	}

	function download_headers()
	{
		$browser = detect_browser();
		header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
		if ($browser['name'] == 'MSIE')
		{
			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="'.rawurlencode($this->title).'.pdf";');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		}else
		{
			header('Content-Type: application/pdf');
			header('Pragma: no-cache');
			header('Content-Disposition: attachment; filename="'.$this->title.'.pdf"');
		}
		header('Content-Transfer-Encoding: binary');
	}


	function print_column_headers(){

		//$this->pdf->SetFillColor(241,241,241);
		//$this->pdf->SetTextColor(255,255,255);
		$this->pdf->cellWidth = $this->pdf->pageWidth/count($this->columns);
		if(count($this->headers)){
			for($i=0;$i<count($this->headers);$i++)
			{				
				$this->pdf->Cell($this->pdf->cellWidth, 20, $this->headers[$i], 1,0,'L', 1);
			}
			$this->pdf->Ln();
		}
	}

	function format_record(&$record){
		if(is_array($this->q) && isset($this->q['method']))
		{
			call_user_func_array(array($this->q['class'], $this->q['method']),array(&$record, $this->cf));
		}
	}

	function init_pdf(){
		$this->pdf = new export_pdf();
		$this->pdf->AddPage();

		//green border
		$this->pdf->SetDrawColor(125,165, 65);
		$this->pdf->SetFillColor(248, 248, 248);
		$this->pdf->SetTextColor(0,0,0);

		$this->pdf->SetTitle($_REQUEST['title']);
		$this->pdf->SetSubject($_REQUEST['title']);
		$this->pdf->SetAuthor($_SESSION['GO_SESSION']['name']);
		$this->pdf->SetCreator('Group-Office '.$GLOBALS['GO_CONFIG']->version);
		$this->pdf->SetKeywords($_REQUEST['title']);
	}

	function increase_totals($record){
		if(is_array($this->totals)){
			foreach($this->totals as $field=>$value)
			{
				$this->totals[$field]+=$record[$field];
			}
		}
	}

	function export($fp){
		parent::export($fp);
		global $GO_USERS, $lang, $GO_MODULES;

		$this->init_pdf();
		
		$this->print_column_headers();
		
		//$this->totals=array();


		$fill=false;
		while($record = $this->db->next_record())
		{
			$this->increase_totals($record);

			if(!count($this->columns))
			{
				foreach($record as $key=>$value)
				{
					$this->columns[]=$key;
					$this->headers[]=$key;
				}
				$this->print_column_headers();
			}

			$this->format_record($record);

			if(isset($record['user_id']) && isset($this->columns['user_id']))
			{
				$user = $GO_USERS->get_user($record['user_id']);
				$record['user_id']=$user['username'];
			}

			$lines=1;
			foreach($this->columns as $index)
			{
				$new_lines = $this->pdf->getNumLines($record[$index],$this->pdf->cellWidth);
				if($new_lines>$lines)
				{
					$lines = $new_lines;
				}
			}

			if($lines*($this->pdf->font_size+2)+8+$this->pdf->getY()>$this->pdf->getPageHeight()-$this->pdf->getBreakMargin())
			{
				$this->pdf->AddPage();
				$this->print_column_headers();
			}

			foreach($this->columns as $index)
			{
				$this->pdf->MultiCell($this->pdf->cellWidth,$lines*($this->pdf->font_size+2)+8, $record[$index],1,'L',$fill,0);
			}
			$this->pdf->Ln();

			if($this->row_body_column){
				$this->pdf->MultiCell($this->pdf->cellWidth*count($this->columns),$lines*($this->pdf->font_size+2)+8, $record[$this->row_body_column],1,'L',$fill,1);
				$this->pdf->Ln(5);
			}
			//$fill=!$fill;
		}


		if(count($this->totals))
		{
			$this->pdf->Ln();
			$this->pdf->Cell($this->pdf->getPageWidth(),20,$lang['common']['totals'].':');
			$this->pdf->Ln();
			foreach($this->columns as $index)
			{
				$value = isset($this->totals[$index]) ? Number::format($this->totals[$index]) : '';
				$this->pdf->Cell($this->pdf->cellWidth, 20, $value, 'T',0,'L');
			}
		}

		fwrite($fp, $this->pdf->Output('export.pdf', 'S'));
	}
}


class export_pdf extends TCPDF
{
	var $font = 'helvetica';
	var $pageWidth;
	var $font_size=9;
	var $cell_height=12;


	function __construct()
	{
		global $GO_CONFIG;

		if(!empty($GO_CONFIG->tcpdf_font))
		{
			$this->font = $GO_CONFIG->tcpdf_font;
		}

		parent::__construct('L', 'pt', 'A4', true, 'UTF-8');

		$this->AliasNbPages();

		$this->setJPEGQuality(100);
		$this->SetMargins(30,60,30);

		$this->SetFont($this->font,'',$this->font_size);

		$this->pageWidth =$this->getPageWidth()-$this->lMargin-$this->rMargin;

		$this->SetAutoPageBreak(true, 30);

	}

	function Footer(){
		global $GO_CONFIG, $lang;

		$this->setDefaultTextColor();
		$this->SetFont($this->font,'',$this->font_size);
		$this->SetY(-20);
		$pW=$this->getPageWidth();
		$this->Cell($pW/2, 10, 'Group-Office '.$GO_CONFIG->version, 0, 0, 'L');
		$this->Cell(($pW/2)-$this->rMargin, 10, sprintf($lang['common']['printPage'], $this->getAliasNumPage(), $this->getAliasNbPages()), 0, 0, 'R');
	}

	function Header(){

		global $lang;

		$this->SetY(30);

		$this->SetTextColor(50,135,172);
		$this->SetFont($this->font,'B',16);
		$this->Write(16, $_REQUEST['title']);

		if(!empty($_REQUEST['subtitle']))
		{
			$this->SetTextColor(125,162,180);
			$this->SetFont($this->font,'',12);
			$this->setXY($this->getX()+5, $this->getY()+3.5);
			$this->Write(12, $_REQUEST['subtitle']);
		}


		$this->setY($this->getY()+2.5, false);

		$this->SetFont($this->font,'B',$this->font_size);
		$this->setDefaultTextColor();

		$this->Cell($this->getPageWidth()-$this->getX()-$this->rMargin,12,Date::get_timestamp(time()),0,0,'R');

		if(!empty($_REQUEST['text']))
		{
			$this->SetFont($this->font,'',$this->font_size);
			$this->Ln(20);
			$this->MultiCell($this->getPageWidth(), 12, $_REQUEST['text']);
		}else
		{
			$this->Ln();
		}

		$this->SetTopMargin($this->getY()+10);

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
		$this->SetFont($this->font,'B',16);
		$this->SetTextColor(50,135,172);
		$this->Cell($this->getPageWidth()-$this->lMargin-$this->rMargin,20, $title,0,1);
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
}
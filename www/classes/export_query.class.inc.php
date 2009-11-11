<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: Date.class.inc.php 3589 2009-11-05 13:02:37Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require_once($GO_CONFIG->class_path.'tcpdf/tcpdf.php');

//ini_set('display_errors', 'off');

class export_query extends TCPDF
{
	var $db;

	var $q;

	var $totals;

	var $font = 'helvetica';
	var $pageWidth;

	var $title='';

	var $font_size=9;
	var $cell_height=12;

	var $type = 'CSV';

	var $list_separator=';';
	var $text_separator='"';

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

		$this->db = new db();

		if($_REQUEST['type']=='PDF')
			$this->type='PDF';

		$this->list_separator=$_SESSION['GO_SESSION']['list_separator'];
		$this->text_separator=$_SESSION['GO_SESSION']['text_separator'];
	}

	function find_custom_exports(){
		global $GO_CONFIG;

		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem();

		$ce=array();

		$files = $fs->get_files($GO_CONFIG->file_storage_path.'customexports');
		while($file = array_shift($files)){
			require_once($file['path']);

			$names = explode('.', $file['name']);
			
			$cls = new $names[0];

			if(!isset($ce[$cls->query]))
				$ce[$cls->query]=array();

			$ce[$cls->query][]=array('name'=>$cls->name, 'cls'=>$names[0]);
		}

		return 'GO.customexports='.json_encode($ce).';';

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

	function query(){
		$this->q = $_SESSION['GO_SESSION']['export_queries'][$_REQUEST['query']];

		$params = array();
		$types='';


		if(is_array($this->q))
		{
			if(!empty($this->q['require']))
			{
				require_once($this->q['require']);
			}

			$this->totals=array();
			if(isset($this->q['totalize_columns']))
			{
				foreach($this->q['totalize_columns'] as $column){
					$this->totals[$column]=0;
				}
			}else
			{
				unset($this->totals);
			}

			$extra_sql=array();
			$sql = $this->q['query'];
			if(isset($this->q['extra_params']))
			{
				foreach($this->q['extra_params'] as $param=>$sqlpart)
				{
					if(!empty($_REQUEST[$param]))
					{
						$params[] = $_REQUEST[$param];
						$extra_sql[]=$sqlpart;
					}
				}
			}
			if(count($params))
			{
				$insert = ' ';
				if(!strpos($sql, 'WHERE'))
				{
					$insert .= 'WHERE ';
				}else
				{
					$insert .= ' AND ';
				}
				$insert .= implode(' AND ', $extra_sql);

				$pos = strpos($sql, 'ORDER');

				if(!$pos)
				{
					$sql .= $insert;
				}else
				{
					$sql = substr($sql, 0, $pos).$insert.' '.substr($sql, $pos);
				}

				$types=str_repeat('s',count($params));
			}
		}else
		{
			$sql = $this->q;

			$params=array();
		}

		$this->db->query($sql,$types,$params);
	}

	function download_headers()
	{
		$browser = detect_browser();
		if($this->type=='CSV')
		{
			header("Content-type: text/x-csv;charset=UTF-8");
			if ($browser['name'] == 'MSIE')
			{
				header('Content-Disposition: inline; filename="'.$_REQUEST['title'].'.csv"');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
			} else {
				header('Pragma: no-cache');
				header('Content-Disposition: attachment; filename="'.$_REQUEST['title'].'.csv"');
			}
		}else
		{
			header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
			if ($browser['name'] == 'MSIE')
			{
				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename="'.rawurlencode($_REQUEST['title']).'.pdf";');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
			}else
			{
				header('Content-Type: application/pdf');
				header('Pragma: no-cache');
				header('Content-Disposition: attachment; filename="'.$_REQUEST['title'].'.pdf"');
			}
			header('Content-Transfer-Encoding: binary');
		}
	}

	function export($fp)
	{
		global $GO_MODULES;
		
		if($GO_MODULES->has_module('customfields')) {
			require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
			$this->cf = new customfields();
		}else
		{
			$this->cf=false;
		}

		$this->query();
		$this->init_columns();
		
		if($this->type=='CSV')
		return $this->export_to_csv($fp);
		else
		return $this->export_to_pdf($fp);
	}

	function print_column_headers(){
		$this->cellWidth = $this->pageWidth/count($this->columns);
		if(count($this->headers)){
			for($i=0;$i<count($this->headers);$i++)
			{
				$this->Cell($this->cellWidth, 20, $this->headers[$i], 1,0,'L', 1);
			}
			$this->Ln();
		}
	}

	function format_record(&$record){
		if(is_array($this->q) && isset($this->q['method']))
		{
			call_user_func_array(array($this->q['class'], $this->q['method']),array(&$record, $this->cf));
		}
	}

	function init_columns(){
		$this->columns=array();
		$this->headers=array();
		if(isset($_REQUEST['columns']))
		{
			$indexesAndHeaders = explode(',', $_REQUEST['columns']);

			foreach($indexesAndHeaders as $i)
			{
				$indexAndHeader = explode(':', $i);

				$this->headers[]=$indexAndHeader[1];
				$this->columns[]=$indexAndHeader[0];
			}
		}
	}

	function export_to_pdf($fp){
		global $GO_USERS, $lang, $GO_MODULES;

		$this->AddPage();

		//green border
		$this->SetDrawColor(125,165, 65);
		$this->SetFillColor(248, 248, 248);

		$this->print_column_headers();


		while($record = $this->db->next_record())
		{
			if(is_array($this->totals)){
				foreach($this->totals as $field=>$value)
				{
					$this->totals[$field]+=$record[$field];
				}
			}

			if(!count($this->columns))
			{
				foreach($record as $key=>$value)
				{
					$this->columns[]=$key;
					$this->headers[]=$key;
				}
				$this->print_column_headers();
			}

			/*if(is_array($this->q) && isset($this->q['method']))
			{
				call_user_func_array(array($this->q['class'], $this->q['method']),array(&$record, $cf));
			}*/

			$this->format_record($record);

			if(isset($record['user_id']) && isset($this->columns['user_id']))
			{
				$user = $GO_USERS->get_user($record['user_id']);
				$record['user_id']=$user['username'];
			}

			$lines=1;
			foreach($this->columns as $index)
			{
				$new_lines = $this->getNumLines($record[$index],$this->cellWidth);
				if($new_lines>$lines)
				{
					$lines = $new_lines;
				}
			}
			
			if($lines*($this->font_size+2)+8+$this->getY()>$this->h-$this->bMargin)
			{
				$this->AddPage();
				$this->print_column_headers();
			}
			
			foreach($this->columns as $index)
			{
				$this->MultiCell($this->cellWidth,$lines*($this->font_size+2)+8, $record[$index],1,'L',0,0);				
			}
			$this->Ln();
		}


		if(count($this->totals))
		{
			$this->Ln();
			$this->Cell($this->getPageWidth(),20,$lang['common']['totals'].':');
			$this->Ln();
			foreach($this->columns as $index)
			{
				$value = isset($this->totals[$index]) ? Number::format($this->totals[$index]) : '';
				$this->Cell($this->cellWidth, 20, $value, 'T',0,'L');
			}
		}

		fwrite($fp, $this->Output('export.pdf', 'S'));
	}

	function fputcsv($fp, $record, $ls, $ts){

		if(empty($ts)){
			$data = implode($ls, $record)."\r\n";
			return fputs($fp, $data);
		}else
		{
			return fputcsv($fp, $record, $ls, $ts);
		}
	}

	function export_to_csv($fp){

		global $GO_USERS, $lang, $GO_MODULES;

		

		if(count($this->headers))
			$this->fputcsv($fp, $this->headers, $this->list_separator, $this->text_separator);


		while($record = $this->db->next_record())
		{
			if(!count($this->columns))
			{
				foreach($record as $key=>$value)
				{
					$this->columns[]=$key;
					$this->headers[]=$key;
				}
				$this->fputcsv($fp, $this->headers, $this->list_separator, $this->text_separator);
			}

			/*if(is_array($this->q) && isset($this->q['method']))
			{
				call_user_func_array(array($this->q['class'], $this->q['method']),array(&$record, $cf));
			}*/
			$this->format_record($record);

			if(isset($record['user_id']) && isset($this->columns['user_id']))
			{
				$user = $GO_USERS->get_user($record['user_id']);
				$record['user_id']=$user['username'];
			}
			$values=array();
			foreach($this->columns as $index)
			{
				$values[] = $record[$index];
			}
			$this->fputcsv($fp, $values,$this->list_separator, $this->text_separator);
		}
	}
}
<?php
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
		if($_REQUEST['type']=='CSV')
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
		if($_REQUEST['type']=='CSV')
		return $this->export_to_csv($fp);
		else
		return $this->export_to_pdf($fp);
	}

	function print_column_headers($headers){

		$this->cellWidth = $this->pageWidth/count($headers);

		for($i=0;$i<count($headers);$i++)
		{
			$this->Cell($this->cellWidth, 20, $headers[$i], 1,0,'L', 1);
		}
		$this->Ln();
	}

	function export_to_pdf($fp){
		global $GO_USERS, $lang;

		$this->query();

		$this->AddPage();

		//green border
		$this->SetDrawColor(125,165, 65);
		$this->SetFillColor(248, 248, 248);


		$columns=array();
		$headers=array();
		if(isset($_REQUEST['columns']))
		{
			$indexesAndHeaders = explode(',', $_REQUEST['columns']);

			foreach($indexesAndHeaders as $i)
			{
				$indexAndHeader = explode(':', $i);

				$headers[]=$indexAndHeader[1];
				$columns[]=$indexAndHeader[0];
			}

			$this->print_column_headers($headers);
		}


		while($record = $this->db->next_record())
		{
			if(is_array($this->totals)){
				foreach($this->totals as $field=>$value)
				{
					$this->totals[$field]+=$record[$field];
				}
			}

			if(!count($columns))
			{
				foreach($record as $key=>$value)
				{
					$columns[]=$key;
					$headers[]=$key;
				}
				$this->print_column_headers($headers);
			}

			if(is_array($this->q) && isset($this->q['method']))
			{
				call_user_func_array(array($this->q['class'], $this->q['method']),array(&$record));
			}

			if(isset($record['user_id']) && isset($columns['user_id']))
			{
				$user = $GO_USERS->get_user($record['user_id']);
				$record['user_id']=$user['username'];
			}

			$lines=1;
			foreach($columns as $index)
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
				$this->print_column_headers($headers);
			}
			
			foreach($columns as $index)
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
			foreach($columns as $index)
			{
				$value = isset($this->totals[$index]) ? Number::format($this->totals[$index]) : '';
				$this->Cell($this->cellWidth, 20, $value, 'T',0,'L');
			}
		}

		fwrite($fp, $this->Output('export.pdf', 'S'));
	}

	function export_to_csv($fp){

		global $GO_USERS, $lang;

		$this->query();

		$columns=array();
		$headers=array();
		if(isset($_REQUEST['columns']))
		{
			$indexesAndHeaders = explode(',', $_REQUEST['columns']);

			foreach($indexesAndHeaders as $i)
			{
				$indexAndHeader = explode(':', $i);

				$headers[]=$indexAndHeader[1];
				$columns[]=$indexAndHeader[0];
			}

			fputcsv($fp, $headers, $_SESSION['GO_SESSION']['list_separator'], $_SESSION['GO_SESSION']['text_separator']);
		}




		while($record = $this->db->next_record())
		{
			if(!count($columns))
			{

				foreach($record as $key=>$value)
				{
					$columns[]=$key;
					$headers[]=$key;
				}

				fputcsv($fp, $headers, $_SESSION['GO_SESSION']['list_separator'], $_SESSION['GO_SESSION']['text_separator']);
			}

			if(is_array($this->q) && isset($this->q['method']))
			{
				call_user_func_array(array($this->q['class'], $this->q['method']),array(&$record));
			}

			if(isset($record['user_id']) && isset($columns['user_id']))
			{
				$user = $GO_USERS->get_user($record['user_id']);
				$record['user_id']=$user['username'];
			}
			$values=array();
			foreach($columns as $index)
			{
				$values[] = $record[$index];
			}
			fputcsv($fp, $values, $_SESSION['GO_SESSION']['list_separator'], $_SESSION['GO_SESSION']['text_separator']);
		}
	}
}
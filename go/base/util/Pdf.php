<?php
require_once GO::config()->root_path."go/vendor/tcpdf/tcpdf.php";

class GO_Base_Util_Pdf extends TCPDF {

	public function __construct($orientation = 'P') {
		
		parent::__construct($orientation,'pt');
		
		$this->init();
	}
	
	protected $font = 'freesans';
	protected $font_size=9;
	
	public $title="";
	public $subtitle="";
	
	
	protected function init() {
		
		//set image scale factor
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$this->SetDrawColor(125,165, 65);
		$this->SetFillColor(248, 248, 248);
		$this->SetTextColor(0,0,0);
		
		$this->getAliasNbPages();

		$this->setJPEGQuality(100);
		$this->SetMargins(30,60,30);
		
		if(!empty(GO::config()->tcpdf_ttf_font)){
			$this->addTTFfont(GO::config()->tcpdf_ttf_font);
		}		
		
		if (!empty(GO::config()->tcpdf_font)) {
			$this->font = GO::config()->tcpdf_font;
		}
		
		if (!empty(GO::config()->tcpdf_font_size)) {
			$this->font_size = GO::config()->tcpdf_font_size;
		}

		$this->SetFont($this->font,'',$this->font_size);

		$this->pageWidth =$this->getPageWidth()-$this->lMargin-$this->rMargin;

		$this->SetAutoPageBreak(true, 30);
		
				// set font
		$this->SetFont($this->font, '', $this->font_size);
				
	}
	
	public function Footer() {
		
		$this->setDefaultTextColor();
		$this->SetFont($this->font,'',$this->font_size);
		$this->SetY(-20);
		$pW=$this->getPageWidth();
		$this->Cell($pW/2, 10, GO::config()->product_name.' '.GO::config()->version, 0, 0, 'L');
		$this->Cell(($pW/2), 10, sprintf(GO::t('printPage'), $this->getAliasNumPage(), $this->getAliasNbPages()), 0, 0, 'R');
	}

	public function Header() {
		
		$this->SetY(10); // DEZE WAS T

		$this->SetTextColor(50,135,172);
		$this->SetFont($this->font,'B',16);
		
		if(!empty($this->title))
		{
			$this->Write(16,$this->title);
		}

		if(!empty($this->subtitle))
		{
			$this->SetTextColor(125,162,180);
			$this->SetFont($this->font,'',12);
			$this->setXY($this->getX()+5, $this->getY()+3.5);
			$this->Write(12, $this->subtitle);
		}


		$this->setY($this->getY()+2.5, false);

		$this->SetFont($this->font,'',$this->font_size);
		$this->setDefaultTextColor();

		$this->Cell($this->getPageWidth()-$this->getX()-$this->rMargin,12,  GO_Base_Util_Date::get_timestamp(time()),0,0,'R');

		if(!empty($_REQUEST['text']))
		{
			$this->SetFont($this->font,'',$this->font_size);
			$this->Ln(20);
			$this->MultiCell($this->getPageWidth(), 12, $_REQUEST['text']);
		}
		
		if(!empty($_REQUEST['html']))
		{
			$this->SetFont($this->font,'',$this->font_size);
			$this->Ln(20);
			
			$this->writeHTML($_REQUEST['html']);
		}
		
		if(empty($_REQUEST['text']) && empty($_REQUEST['html']))
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
		//$this->Cell($this->getPageWidth()-$this->lMargin-$this->rMargin,20, $title,0,1);
		$this->MultiCell($this->getPageWidth()-$this->lMargin-$this->rMargin,20, $title, 0, 'L', false, '1');
		$this->setDefaultTextColor();
		$this->SetFont($this->font,'',$this->font_size);
	}

	function H2($title)
	{

		$this->SetFont($this->font,'',14);
		$this->SetTextColor(125,165, 65);
		//$this->Cell($this->getPageWidth()-$this->lMargin-$this->rMargin,24, $title,0,1);
		$this->MultiCell($this->getPageWidth()-$this->lMargin-$this->rMargin,24, $title, 0, 'L', false, '1');
		$this->setDefaultTextColor();
		$this->SetFont($this->font,'',$this->font_size);
	}

	function H3($title)
	{
//		$this->SetTextColor(102,102, 102);
		$this->SetFont($this->font,'B',11);
//		$this->Cell($this->getPageWidth()-$this->lMargin-$this->rMargin,14, $title,'',1);
		$this->MultiCell($this->getPageWidth()-$this->lMargin-$this->rMargin,14, $title, '', 'L', false, '1');
		$this->SetFont($this->font,'',$this->font_size);
//		$this->setDefaultTextColor();
		$this->ln(4);
	}

	function H4($title)
	{
		$this->SetFont($this->font,'B',$this->font_size);
		//	$this->SetDrawColor(90, 90, 90);
		//$this->SetDrawColor(128, 128, 128);
		
//		$this->Cell($this->getPageWidth()-$this->lMargin-$this->rMargin,14, $title,'',1);
		$this->MultiCell($this->getPageWidth()-$this->lMargin-$this->rMargin,14, $title, '', 'L', false, '1');
		
		//$this->SetDrawColor(0,0,0);
		$this->SetFont($this->font,'',$this->font_size);


	}
	
	private $_headers;
	
	public function tableHeaders($columns){
		$this->_headers=$columns;
		
		return $this->tableRow($columns);
	}
	
	
	public function tableRow($columns){
		$html = '<tr>';
		
		$headerIndex=0;
		for($i=0;$i<count($columns);$i++){
			
			if(isset($this->_headers[$headerIndex])){
				$columns[$i]->width=$this->_headers[$headerIndex]->width;
				$columns[$i]->align=$this->_headers[$headerIndex]->align;
			
				$headerIndex++;
				if(isset($columns[$i]->colspan)){
					for($n=1;$n<$columns[$i]->colspan;$n++){
						if(isset($this->_headers[$headerIndex]))
							$columns[$i]->width+=$this->_headers[$headerIndex]->width;
						
						$headerIndex++;
					}
				}
			}
			
			$html .= $columns[$i]->render();
		}
		
		$html .= '</tr>';
		
		return $html;
	}
	

	function setDefaultTextColor()
	{
		$this->SetTextColor(40,40,40);
	}
	
	
}


class GO_Base_Util_PdfTableColumn{
	
	public $width;
	public $text="";
	public $align;
	public $bgcolor;
	public $colspan;
	public $extraStyle="";
	
	public $isHeader=false;
	
	public function __construct($config) {
		
		foreach($config as $prop=>$value)
			$this->$prop = $value;
		
		if($this->isHeader && !isset($this->bgcolor)){
			$this->bgcolor='rgb(248, 248, 248)';
		}
	}
	
	public function render(){
		
		$tag = $this->isHeader ? 'th' : 'td';
		
		$html = '<'.$tag.' style="';
		
		if(isset($this->width))
			$html .='width:'.$this->width.'px;';
		
		if(isset($this->bgcolor))
			$html .='background-color:'.$this->bgcolor.';';
		
		if(isset($this->align))
			$html .='text-align:'.$this->align.';';
		
		$html .= $this->extraStyle.'"';
		
		if(isset($this->colspan))
			$html .= ' colspan="'.$this->colspan.'"';
		
		$html .='>'.$this->text.'</'.$tag.'>';
		
		return $html;
	}
	
}
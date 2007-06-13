<?php

/**
*  Copyright Lorenz Softwareentwicklung & Systemintegration 2004
*  Author: Georg Lorenz <georg@lonux.de>
*  Version: 1.0 Release date: 11 Juni 2004
*
*  This program is free software; you can redistribute it and/or modify it
*  under the terms of the GNU General Public License as published by the
*  Free Software Foundation; either version 2 of the License, or (at your
*  option) any later version.
* 
**/

/**
* This file holds the xfpdf class extending the main fpdf class
*
* @package  xfpdf class
* @author   Georg Lorenz <georg@lonux.de>
* @since    Group-Office 2.06
*/

require_once($GO_CONFIG->class_path.'fpdf/fpdf.class.inc');

class XFPDF extends FPDF
{
  var $javascript;
  var $n_js;
  
  /**
  * Javascript support for pdf 
  *
  * @author Johannes Güntert (johannes.guentert@reservix.de)
  * @param  string $script Script to be executed after page open.
  * @access public
  * @return void
  */
  function IncludeJS($script)
  {
	$this->javascript=$script;
  }

  /**
  * Extended original Output method by workaround for IE
  * not opening the posted pdf file.
  *
  * @param  string $name Name of the file.
  * @param  string $dest Output destination the pdf file should go to
  * @access public
  * @return pdf file
  */
  function Output($name='',$dest='')
  {
	//Finish document if necessary
	if($this->state<3)
		$this->Close();
	//Normalize parameters
	if(is_bool($dest))
		$dest=$dest ? 'D' : 'F';
	$dest=strtoupper($dest);
	if($dest=='')
	{
		if($name=='')
		{
			$name='doc.pdf';
			$dest='I';
		}
		else
			$dest='F';
	}

	$browser = detect_browser();
	
	switch($dest)
	{
		case 'I':
			//Send to standard output
			if(isset($_SERVER['SERVER_NAME']))
			{
			  //We send to a browser
			  Header('Content-Type: application/pdf');
			  if(headers_sent())
				$this->Error('Some data has already been output to browser, can\'t send PDF file');
			  Header('Content-Length: '.strlen($this->buffer));
				Header('Content-disposition: inline; filename="'.$name.'"');
			  //Workaround for the IE to open the pdf file without any errors
			  //but the displaying of the pdf file still doesn't work (an issue from IE, buggy browser??)
			  if ($browser['name'] == 'MSIE')
              {
		        Header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				Header('Pragma: public');
			  }
			}
			echo $this->buffer;
			break;
		case 'D':
			//Download file
			if(isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
				Header('Content-Type: application/force-download');
			else
				Header('Content-Type: application/octet-stream');
			if(headers_sent())
				$this->Error('Some data has already been output to browser, can\'t send PDF file');
			Header('Content-Length: '.strlen($this->buffer));
			Header('Content-disposition: attachment; filename="'.$name.'"');
			echo $this->buffer;
			break;
		case 'F':
			//Save to local file
			$f=fopen($name,'wb');
			if(!$f)
				$this->Error('Unable to create output file: '.$name);
			fwrite($f,$this->buffer,strlen($this->buffer));
			fclose($f);
			break;
		case 'S':
			//Return as a string
			return $this->buffer;
		default:
			$this->Error('Incorrect output destination: '.$dest);
	}
	return '';
  }

  /**
  * Javascript support for pdf 
  *
  * @author Johannes Güntert (johannes.guentert@reservix.de)
  * @param  void
  * @access private
  * @return void
  */
  function _putjavascript()
  {
	$this->_newobj();
	$this->n_js=$this->n;
	$this->_out('<<');
	$this->_out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R ]');
	$this->_out('>>');
	$this->_out('endobj');
	$this->_newobj();
	$this->_out('<<');
	$this->_out('/S /JavaScript');
	$this->_out('/JS '.$this->_textstring($this->javascript));
	$this->_out('>>');
	$this->_out('endobj');
  }

  /**
  * Javascript support for pdf 
  *
  * @author Johannes Güntert (johannes.guentert@reservix.de)
  * @param  void
  * @access private
  * @return void
  */
  function _putresources()
  {
	parent::_putresources();
	if (!empty($this->javascript))
	{
	  $this->_putjavascript();
	}
  }

  /**
  * Javascript support for pdf 
  *
  * @author Johannes Güntert (johannes.guentert@reservix.de)
  * @param  void
  * @access private
  * @return void
  */
  function _putcatalog()
  {
	parent::_putcatalog();
	if (isset($this->javascript))
	{
	  $this->_out('/Names <</JavaScript '.($this->n_js).' 0 R>>');
	}
  }

  /**
  * Extends the native fpdf method with: 
  * - multiline support with delimiter "\n"
  * - compresses text to fit the cell width
  *
  * @author	Pivkin Vladimir (boba@khspu.ru)
  * @param	float $w Cell width. If 0, the cell extends up to the right margin.
  * @param	float $h Cell height.
  * @param  string $txt String to print.
  * @param  mixed $border Indicates if borders must be drawn around the cell. Possible values: 0,1 or any combination of L,T,B,R
  * @param  int $ln Indicates where the current position should go after the call. Possible values: 0,1,2
  * @param  string $align Allows to center or align the text. Possible values: L,C,R
  * @param  int $fill Indicates if the cell background must be painted (1) or transparent (0).
  * @param  mixed $link URL or identifier returned by AddLink().
  * @access public
  * @return void
  */
  function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
  {
	//Output a cell
	$k=$this->k;
	if($this->y+$h>$this->PageBreakTrigger and !$this->InFooter and $this->AcceptPageBreak())
	{
	  $x=$this->x;
	  $ws=$this->ws;
	  if($ws>0)
	  {
		$this->ws=0;
		$this->_out('0 Tw');
	  }
	  $this->AddPage($this->CurOrientation);
	  $this->x=$x;
	  if($ws>0)
	  {
		$this->ws=$ws;
		$this->_out(sprintf('%.3f Tw',$ws*$k));
	  }
	}
	if($w==0)
	  $w=$this->w-$this->rMargin-$this->x;
	$s='';
    // begin change Cell function 12.08.2003 
	if($fill==1 or $border>0)
	{
	  if($fill==1)
		$op=($border>0) ? 'B' : 'f';
	  else
		$op='S';
	  if ($border>1) {
	    $s=sprintf(' q %.2f w %.2f %.2f %.2f %.2f re %s Q ',$border,
		$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
	  }
	  else
		$s=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
	}
	if(is_string($border))
	{
	  $x=$this->x;
	  $y=$this->y;
	  if(is_int(strpos($border,'L')))
		$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
	  elseif(is_int(strpos($border,'l')))
		$s.=sprintf('q 2 w %.2f %.2f m %.2f %.2f l S Q ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
			
	  if(is_int(strpos($border,'T')))
		$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
	  elseif(is_int(strpos($border,'t')))
		$s.=sprintf('q 2 w %.2f %.2f m %.2f %.2f l S Q ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
		
	  if(is_int(strpos($border,'R')))
		$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
	  elseif(is_int(strpos($border,'r')))
		$s.=sprintf('q 2 w %.2f %.2f m %.2f %.2f l S Q ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		
	  if(is_int(strpos($border,'B')))
		$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
	  elseif(is_int(strpos($border,'b')))
		$s.=sprintf('q 2 w %.2f %.2f m %.2f %.2f l S Q ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
	}
	if (trim($txt)!='') {
	  $cr=substr_count($txt,"\n");
	  if ($cr>0) { // Multi line
		$txts = explode("\n", $txt);
	    $lines = count($txts);
	    //$dy=($h-2*$this->cMargin)/$lines;
	    for($l=0;$l<$lines;$l++) {
		  $txt=$txts[$l];
		  $w_txt=$this->GetStringWidth($txt);
		  if($align=='R')
	  	    $dx=$w-$w_txt-$this->cMargin;
		  elseif($align=='C')
	  	    $dx=($w-$w_txt)/2;
		  else
		    $dx=$this->cMargin;

		  $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
		  if($this->ColorFlag)
		    $s.='q '.$this->TextColor.' ';
		  $s.=sprintf('BT %.2f %.2f Td (%s) Tj ET ',
		    ($this->x+$dx)*$k,
			($this->h-($this->y+.5*$h+(.7+$l-$lines/2)*$this->FontSize))*$k,
			$txt);
		  if($this->underline)
		    $s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
		  if($this->ColorFlag)
		    $s.='Q ';
		  if($link)
		    $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$w_txt,$this->FontSize,$link);
	    }
	  }
	  else { // Single line
		$w_txt=$this->GetStringWidth($txt);
		$Tz=100;
		if ($w_txt>$w-2*$this->cMargin) { // Need compression
		  $Tz=($w-2*$this->cMargin)/$w_txt*100;
		  $w_txt=$w-2*$this->cMargin;
		}
		if($align=='R')
		  $dx=$w-$w_txt-$this->cMargin;
		elseif($align=='C')
		  $dx=($w-$w_txt)/2;
		else
		  $dx=$this->cMargin;
		$txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
		if($this->ColorFlag)
		  $s.='q '.$this->TextColor.' ';
		$s.=sprintf('q BT %.2f %.2f Td %.2f Tz (%s) Tj ET Q ',
		  ($this->x+$dx)*$k,
		  ($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,
		  $Tz,$txt);
		if($this->underline)
		  $s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
		if($this->ColorFlag)
		  $s.='Q ';
		if($link)
		  $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$w_txt,$this->FontSize,$link);
	  }
	}
    // end change Cell function 12.08.2003
	if($s)
	  $this->_out($s);
	$this->lasth=$h;
	if($ln>0)
	{
	  //Go to next line
	  $this->y+=$h;
	  if($ln==1)
		$this->x=$this->lMargin;
	}
	else
	  $this->x+=$w;
  }

  /**
  * Memory optimization 
  *
  * @author	Philip Clarke (nod@sqlopus.org)
  * @param	void
  * @access private
  * @return void
  */
  function _putpages()
  {
	$nb=$this->page;
	if(!empty($this->AliasNbPages))
	{
	  //Replace number of pages
	  for($n=1;$n<=$nb;$n++)
		$this->pages[$n]=($this->compress) ? gzcompress(str_replace($this->AliasNbPages,$nb,gzuncompress($this->pages[$n]))) : str_replace($this->AliasNbPages,$nb,$this->pages[$n]) ;
	}
	if($this->DefOrientation=='P')
	{
	  $wPt=$this->fwPt;
	  $hPt=$this->fhPt;
	}
	else
	{
	  $wPt=$this->fhPt;
	  $hPt=$this->fwPt;
	}
	$filter=($this->compress) ? '/Filter /FlateDecode ' : '';
	for($n=1;$n<=$nb;$n++)
	{
	  //Page
	  $this->_newobj();
	  $this->_out('<</Type /Page');
	  $this->_out('/Parent 1 0 R');
	  if(isset($this->OrientationChanges[$n]))
		$this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]',$hPt,$wPt));
	  $this->_out('/Resources 2 0 R');
	  if(isset($this->PageLinks[$n]))
	  {
		//Links
		$annots='/Annots [';
		foreach($this->PageLinks[$n] as $pl)
		{
		  $rect=sprintf('%.2f %.2f %.2f %.2f',$pl[0],$pl[1],$pl[0]+$pl[2],$pl[1]-$pl[3]);
		  $annots.='<</Type /Annot /Subtype /Link /Rect ['.$rect.'] /Border [0 0 0] ';
		  if(is_string($pl[4]))
			$annots.='/A <</S /URI /URI '.$this->_textstring($pl[4]).'>>>>';
		  else
		  {
			$l=$this->links[$pl[4]];
			$h=isset($this->OrientationChanges[$l[0]]) ? $wPt : $hPt;
			$annots.=sprintf('/Dest [%d 0 R /XYZ 0 %.2f null]>>',1+2*$l[0],$h-$l[1]*$this->k);
		  }
		}
		$this->_out($annots.']');
	  }
	  $this->_out('/Contents '.($this->n+1).' 0 R>>');
	  $this->_out('endobj');
	  //Page content
	  $this->_newobj();
	  $this->_out('<<'.$filter.'/Length '.strlen($this->pages[$n]).'>>');
	  $this->_putstream($this->pages[$n]);
	  $this->_out('endobj');
	}
	//Pages root
	$this->offsets[1]=strlen($this->buffer);
	$this->_out('1 0 obj');
	$this->_out('<</Type /Pages');
	$kids='/Kids [';
	for($i=0;$i<$nb;$i++)
	  $kids.=(3+2*$i).' 0 R ';
	$this->_out($kids.']');
	$this->_out('/Count '.$nb);
	$this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]',$wPt,$hPt));
	$this->_out('>>');
	$this->_out('endobj');
  }

  /**
  * Memory optimization 
  *
  * @author	Philip Clarke (nod@sqlopus.org)
  * @param	void
  * @access private
  * @return void
  */
  function _endpage()
  {
	//End of page contents
	$this->pages[$this->page] = ($this->compress) ? gzcompress($this->pages[$this->page]) : $this->pages[$this->page];
	$this->state=1;
  }
}

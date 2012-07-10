<?php

/*
 * Copyright Intermesh BV
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * Generated PDF document for reservatrion
 *
 * @package GO.reservation
 * @copyright Copyright Intermesh
 * @version $Id print.php 2012-07-06 11:26:55 mdhart $ 
 * @author Michael de Hart <mdehart@intermesh.nl> 
 */

class ReservationPDF extends GO_Base_Util_Pdf
{
	public function Header() {}
	public function Footer() {}
	
	public function drawTable($header,$data) {
        // Colors, line width and bold font
        $this->SetFillColor(50, 50, 50);
        $this->SetTextColor(255);
        $this->SetDrawColor(0);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');
        // Header
        $w = array(170, 140, 100, 110);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        foreach($data as $row) {
            $this->Cell($w[0], 6, $row[0], 'LBR', 0, 'L');
            $this->Cell($w[1], 6, $row[1], 'LBR', 0, 'C');
            $this->Cell($w[2], 6, $row[2], 'LBR', 0, 'C');
            $this->Cell($w[3], 6, $row[3], 'LBR', 0, 'C');
            $this->Ln();
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

$pdf= new ReservationPDF('P','mm','A4',true,'UTF-8');

$pdf->AddPage();
$pdf->SetAuthor('Superfun');
$pdf->SetTitle('Uw reservering bij Superfun');

$pdf->SetFontSize(40);
$pdf->MultiCell(500, 5, 'Uw reservering bij Superfun', 0, 'L', false, 1);
$pdf->SetFontSize(15);

$pdf->Ln();
$pdf->MultiCell(200, 5, 'Reserverings nummer', 0, 'L', false, 0);
$pdf->MultiCell(200, 5, $model->getNumber(), 0, 'L', false, 1);
$pdf->MultiCell(200, 5, 'Datum', 0, 'L', false, 0);
$pdf->MultiCell(200, 5, $model->dateText, 0, 'L', false, 1);
$pdf->MultiCell(200, 5, 'Aantal personen', 0, 'L', false, 0);
$pdf->MultiCell(200, 5, $model->person_count, 0, 'L', false, 1);
$pdf->MultiCell(200, 5, 'Totaal prijs', 0, 'L', false, 0);
$pdf->MultiCell(200, 5, $model->getPriceText(), 0, 'L', false, 1);

$pdf->Ln();

$header = array('Activiteit', 'Tijden', 'Personen', 'Resource');
$data = array();
foreach($model->plannings as $planning)
{
	$data[] = array(
			$planning->activity->planboard->name, 
			$planning->timeFromText ." - ". $planning->timeTillText,
			$planning->getPersonCount(),
			$planning->resource->name,
	);
}
$pdf->drawTable($header, $data);

$pdf->Output('Reservering.pdf', 'I');
?>

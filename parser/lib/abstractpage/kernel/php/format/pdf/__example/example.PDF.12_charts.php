<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


$pdf = new PDF();

$pdf->open();
$pdf->addPage();

$data = array(
	'Men'      => 1510, 
	'Women'    => 1610, 
	'Children' => 1400);

// Pie chart
$pdf->setFont( 'Arial', 'BIU', 12 );
$pdf->cell( 0, 5, '1 - Pie chart', 0, 1 );
$pdf->ln( 8 );

$pdf->setFont( 'Arial', '', 10 );
$valX = $pdf->getX();
$valY = $pdf->getY();
$pdf->cell( 30, 5, 'Number of men:' );
$pdf->cell( 15, 5, $data['Men'], 0, 0, 'R' );
$pdf->ln();
$pdf->cell( 30, 5, 'Number of women:' );
$pdf->cell( 15, 5, $data['Women'], 0, 0, 'R' );
$pdf->ln();
$pdf->cell( 30, 5, 'Number of children:' );
$pdf->cell( 15, 5, $data['Children'], 0, 0, 'R' );
$pdf->ln();
$pdf->ln( 8 );

$pdf->setXY( 90, $valY );
$col1=array( 100, 100, 255 );
$col2=array( 255, 100, 100 );
$col3=array( 255, 255, 100 );
$pdf->pieChart( 100, 35, $data, '%l (%p)', array( $col1, $col2, $col3 ) );
$pdf->setXY( $valX, $valY + 40 );

// Bar diagram
$pdf->setFont( 'Arial', 'BIU', 12 );
$pdf->cell( 0, 5, '2 - Bar diagram', 0, 1 );
$pdf->ln( 8 );
$valX = $pdf->getX();
$valY = $pdf->getY();
$pdf->barDiagram( 190, 70, $data, '%l : %v (%p)', array( 255, 175, 100 ) );
$pdf->setXY( $valX, $valY + 80 );

$pdf->output();

?> 

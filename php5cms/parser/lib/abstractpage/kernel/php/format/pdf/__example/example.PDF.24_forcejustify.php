<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


$pdf = new PDF();
$pdf->open();
$pdf->addPage();
$pdf->setFont( 'Arial', 'U', 10 );
$pdf->setFillColor( 250, 180, 200 );

// Set the interior cell margin to 1cm
$pdf->cMargin = 10;

// Print 2 Cells
$pdf->cell( 190, 8, 'a short text which is left aligned', 1, 1, 'L', 1 );
$pdf->ln();
$pdf->cell( 190, 8, 'a short text which is forced justified', 1, 1, 'FJ', 1 );
$pdf->ln();

// Print 2 MultiCells
$y = $pdf->getY();
$pdf->multicell( 90, 8, "It is a long text\nwhich is left aligned", 1, 'L', 1 );
$pdf->setXY( 110, $y );
$pdf->multicell( 90, 8, "It is a long text\nwhich is forced justified", 1, 'FJ', 1 );

$pdf->output();

?>

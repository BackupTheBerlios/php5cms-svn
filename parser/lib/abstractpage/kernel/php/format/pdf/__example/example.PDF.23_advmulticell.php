<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


$pdf = new PDF();
$pdf->open();
$pdf->addPage();

for ( $i = 1; $i < 19; $i++ )
	$txt .= 'all work and no play makes jack a dull boy ';

$pdf->rect( 20, 20, 100, 100 );
$pdf->rect( 80, 20,  40,  40 );
$pdf->rect( 20, 80,  40,  40 );
$pdf->setXY( 20, 20 );
$pdf->setFont( 'Helvetica', '', 10 );
$txt = $pdf->multicell(  60, 5, $txt, 0, 'J', 0, 8 );
$txt = $pdf->multicell( 100, 5, $txt, 0, 'J', 0, 4 );
$pdf->setX( 60 );
$txt = $pdf->multicell( 60, 5, $txt, 0, 'J', 0, 8 );

$pdf->output();

?>

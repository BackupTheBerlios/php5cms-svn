<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


$pdf = new PDF();
$pdf->open();
$pdf->addPage();
$pdf->setLineWidth( 0.1 );
$pdf->setDash( 5, 5 ); //5mm on, 5mm off
$pdf->line( 20, 20, 190, 20 );
$pdf->setLineWidth( 0.5 );
$pdf->line( 20, 25, 190, 25 );
$pdf->setLineWidth( 0.8 );
$pdf->setDash( 4, 2 ); //4mm on, 2mm off
$pdf->rect( 20, 30, 170, 20 );
$pdf->setDash(); //restore no dash
$pdf->line( 20, 55, 190, 55 );
$pdf->output();

?>

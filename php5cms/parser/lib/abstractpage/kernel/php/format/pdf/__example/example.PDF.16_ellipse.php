<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


$pdf = new PDF();
$pdf->open();
$pdf->addPage();
$pdf->ellipse( 100, 50, 30, 20 );
$pdf->setFillColor( 255, 255, 0 );
$pdf->circle( 110, 47, 7, 'F' );
$pdf->output();

?> 

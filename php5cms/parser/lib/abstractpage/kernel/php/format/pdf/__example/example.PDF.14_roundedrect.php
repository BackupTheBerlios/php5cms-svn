<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


$pdf = new PDF();
$pdf->open();
$pdf->addPage();
$pdf->setLineWidth( 0.5 );
$pdf->setFillColor( 192 );
$pdf->roundedRect( 70, 30, 68, 46, 3.5, 'DF' );
$pdf->output();

?> 

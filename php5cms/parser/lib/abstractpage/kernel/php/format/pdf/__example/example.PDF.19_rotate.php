<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


$pdf = new PDF();
$pdf->open();
$pdf->addPage();
$pdf->setFont( 'Arial', '', 20 );
$pdf->rotatedImage( 'circle.png', 85, 60, 40, 16, 45 );
$pdf->rotatedText( 100, 60, 'Hello!', 45 );
$pdf->output();

?> 

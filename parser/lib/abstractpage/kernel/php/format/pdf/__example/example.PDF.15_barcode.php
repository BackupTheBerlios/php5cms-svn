<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


$pdf = new PDF();
$pdf->open();
$pdf->addPage();
$pdf->ean13( 80, 40, '123456789012' );
$pdf->output();

?> 

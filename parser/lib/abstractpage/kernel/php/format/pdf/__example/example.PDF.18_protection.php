<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


$pdf = new PDF();
$pdf->setProtection( array( 'print' ) );
$pdf->open();
$pdf->addPage();
$pdf->setFont( 'Arial' );
$pdf->write( 10, 'You can print me but not copy my text.' );
$pdf->output();

?> 

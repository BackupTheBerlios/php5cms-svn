<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


$pdf = new PDF();
$pdf->open();
$pdf->addPage();
$pdf->setFont( 'Arial', '', 20 );
$pdf->text( 90, 50, 'Print me!' );

$script = "print(true);"; // true = show dialog

$pdf->includeJavaScript( $script );
$pdf->output();

?> 

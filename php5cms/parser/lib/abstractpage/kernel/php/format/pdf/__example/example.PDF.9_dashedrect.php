<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


$pdf = new PDF();
$pdf->open();
$pdf->addPage();
$pdf->setDrawColor( 200 );
$pdf->dashedRect( 40, 10, 165, 40 );
$pdf->setFont( 'Arial', 'B', 30 );
$pdf->setXY( 40, 10 );
$pdf->cell( 125, 30, 'Enjoy dashes!', 0, 0, 'C', 0 );
$pdf->output();

?>

<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


$pdf = new PDF();
$pdf->open();
$pdf->setFont( 'Arial', '', 15 );

// Page 1
$pdf->addPage();
$pdf->bookmark( 'Page 1' );
$pdf->bookmark( 'Paragraph 1', 1, -1 );
$pdf->cell( 0, 6, 'Paragraph 1' );
$pdf->ln( 50 );
$pdf->bookmark( 'Paragraph 2', 1, -1 );
$pdf->cell( 0, 6, 'Paragraph 2' );

// Page 2
$pdf->addPage();
$pdf->bookmark( 'Page 2' );
$pdf->bookmark( 'Paragraph 3', 1, -1 );
$pdf->cell( 0, 6, 'Paragraph 3' );

$pdf->output();

?> 

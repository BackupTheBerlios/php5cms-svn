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
$pdf->bookmark( 'Section 1' );
$pdf->cell( 0, 6, 'Section 1', 0, 1 );
$pdf->bookmark( 'Subsection 1', 1, -1 );
$pdf->cell( 0, 6, 'Subsection 1' );
$pdf->ln( 50 );
$pdf->bookmark( 'Subsection 2', 1, -1 );
$pdf->cell( 0, 6, 'Subsection 2' );

// Page 2
$pdf->addPage();
$pdf->bookmark( 'Section 2' );
$pdf->cell( 0, 6, 'Section 2', 0, 1 );
$pdf->bookmark( 'Subsection 1', 1, -1 );
$pdf->cell( 0, 6, 'Subsection 1' );

// Index
$pdf->addPage();
$pdf->bookmark( 'Index' );
$pdf->createIndex();
$pdf->output();

?> 

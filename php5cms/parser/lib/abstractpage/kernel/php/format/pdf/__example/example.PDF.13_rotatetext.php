<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


$pdf = new PDF();
$pdf->open();
$pdf->addPage();
$pdf->setFont( 'Arial', '', 40 );
$pdf->textWithRotation( 50, 65, 'Hello', 45, -45 );
$pdf->setFontSize( 30 );
$pdf->textWithDirection( 110, 50, 'world!', 'L' );
$pdf->textWithDirection( 110, 50, 'world!', 'U' );
$pdf->textWithDirection( 110, 50, 'world!', 'R' );
$pdf->textWithDirection( 110, 50, 'world!', 'D' );
$pdf->output();

?> 

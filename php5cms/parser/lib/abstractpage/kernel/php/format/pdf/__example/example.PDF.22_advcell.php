<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


$pdf = new PDF();
$pdf->open();
$pdf->addPage();
$pdf->setFont( 'Arial', '', 12 );

$pdf->vCell( 15, 50, "Text at\nbottom", 1, 0, 'D' );
$pdf->vCell( 10, 50, 'Centered text', 2, 0, 'C' );
$pdf->vCell( 15, 50, "Text\non top", 1, 0, 'U' );

$pdf->hCell( 50, 50, "Text on\nthe left", 'lbtR', 0, 'L' );
$pdf->hCell( 50, 50, 'This line is very long and gets compressed', 'LtRb', 0, 'C' );
$pdf->hCell( 50, 50, "Text on\nthe right", 'Ltrb', 0, 'R' );

$pdf->output();

?>

<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


$pdf = new PDF();
$pdf->open();
$pdf->addPage();
$pdf->setFont( 'Arial', '', 9 );
$pdf->cell( 85, 4, "EXAMPLE OF FUNCTION USAGE", 1, 1, 'C' );

$f = fopen( 'ex.txt', 'r' );
$text = fread( $f, filesize( 'ex.txt' ) );
fclose( $f );

$pdf->justify( $text, 85, 4 );
$pdf->output();

?>

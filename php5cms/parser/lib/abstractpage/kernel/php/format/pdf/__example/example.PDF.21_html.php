<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );
using( 'format.pdf.PDF_HTMLTable' );


$pdf = new PDF();
$pdf->open();
$pdf->addPage();
$pdf->setFont( 'Times', '', 12 );

$f = fopen( 'ex.htm', 'r' );
$html = fread( $f, filesize( 'ex.htm' ) );
fclose( $f );

$pdf->parseHTML( $html );
$pdf->output();

?>

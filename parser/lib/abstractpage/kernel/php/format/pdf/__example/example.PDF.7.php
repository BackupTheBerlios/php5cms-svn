<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  'calligra/' );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


$pdf = new PDF();
$pdf->open();
$pdf->addPage();
$pdf->addFont( 'Calligrapher', '', 'calligra.php' );
$pdf->setFont( 'Calligrapher', '', 35 );
$pdf->cell( 0, 10, 'Enjoy new fonts with PDF!' );
$pdf->output();

?>

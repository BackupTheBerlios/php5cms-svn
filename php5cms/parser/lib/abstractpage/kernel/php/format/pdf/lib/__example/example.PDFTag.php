<?php

require( '../../../../../../prepend.php' );

using( 'format.pdf.lib.PDFTag' );


$srcXML = 'example1.xml';
$pdf = new PDFTag();
$pdf->readFromFile( $srcXML );

/* decide if only generate or generate and show */
if ( !isset( $_GET["show"] ) ) 
{
	$pdf->pdfProfile = true;
	$pdf->generatePDF();
?>
<center>
<a href="example.PDFTag.php?show">click here to display/download</a>
</center>
<?php
} 
else 
{
	$pdf->generatePDF();
	$pdf->dumpPDF();
}

?>

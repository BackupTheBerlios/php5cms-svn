<?php

require( '../../../../../../prepend.php' );

using( 'format.pdf.lib.PDFTag' );


$srcXML = 'example2.xml';
$pdf = new PDFTag();
$pdf->readFromFile( $srcXML );
$pdf->setParser( 'SAX' );

/* decide if only generate or generate and show */
if ( !isset( $_GET["show"] ) )
{
	$pdf->pdfProfile = true;
	$pdf->generatePDF();
?>
<center>
<a href="example.PDFTag.2.php?show">click here to display/download</a>
</center>
<?php
}
else
{
	$pdf->generatePDF();
	$pdf->dumpPDF();
}

?>

<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


class MyPDF extends PDF
{
	// Page header
	function header()
	{
		// Arial bold 15
		$this->setFont( 'Arial', 'B', 15 );
	
		// Move to the right
		$this->cell( 80 );
	
		// Title
		$this->cell( 30, 10, 'Title', 1, 0, 'C' );
	
		// Line break
		$this->ln( 20 );
	}

	//Page footer
	function footer()
	{
		// Position at 1.5 cm from bottom
		$this->setY( -15 );
	
		// Arial italic 8
		$this->setFont( 'Arial', 'I', 8 );
		
		// Page number
		$this->cell( 0, 10, 'Page ' . $this->pageNum() . '/{nb}', 0, 0, 'C' );
	}
}


$pdf = new MyPDF();
$pdf->open();
$pdf->aliasNbPages();
$pdf->addPage();
$pdf->setFont( 'Times', '', 12 );

for ( $i = 1; $i <= 40; $i++ )
	$pdf->cell( 0, 10, 'Printing line number ' . $i, 0, 1 );
	
$pdf->output();

?>

<?php

require( '../../../../../prepend.php' );

define( 'PDF_FONTPATH',  AP_ROOT_PATH . ap_ini_get( "path_metrics_php", "path" ) );
define( 'PDF_IMAGEPATH', '' );

using( 'format.pdf.PDF' );


class MyPDF extends PDF
{
	// Current column
	var $col = 0;
	
	// Ordinate of column start
	var $y0;


	function header()
	{
		// Page header
		global $title;
	
		$this->setFont( 'Arial', 'B', 15 );
		$w = $this->getStringWidth( $title ) + 6;
		$this->setX( ( 210 - $w ) / 2 );
		$this->setDrawColor( 0, 80, 180 );
		$this->setFillColor( 230, 230, 0 );
		$this->setTextColor( 220, 50, 50 );
		$this->setLineWidth( 1 );
		$this->cell( $w, 9, $title, 1, 1, 'C', 1 );
		$this->ln( 10 );
		
		// Save ordinate
		$this->y0 = $this->getY();
	}

	function footer()
	{
		// Page footer
		$this->setY( -15 );
		$this->setFont( 'Arial', 'I', 8 );
		$this->setTextColor( 128 );
		$this->cell( 0, 10, 'Page ' . $this->pageNum(), 0, 0, 'C' );
	}

	function SetCol( $col )
	{
		// Set position at a given column
		$this->col = $col;
		$x = 10 + $col * 65;
		$this->setLeftMargin( $x );
		$this->setX( $x );
	}

	function acceptPageBreak()
	{
		// Method accepting or not automatic page break
		if ( $this->col < 2 )
		{
			// Go to next column
			$this->SetCol( $this->col + 1 );
			
			// Set ordinate to top
			$this->setY( $this->y0 );
			
			// Keep on page
			return false;
		}
		else
		{
			// Go back to first column
			$this->SetCol( 0 );
			
			// Page break
			return true;
		}
	}

	function ChapterTitle( $num, $label )
	{
		// Title
		$this->setFont( 'Arial', '', 12 );
		$this->setFillColor( 200, 220, 255 );
		$this->cell( 0, 6, "Chapter  $num : $label", 0, 1, 'L', 1 );
		$this->ln( 4 );
		
		// Save ordinate
		$this->y0 = $this->getY();
	}

	function ChapterBody( $fichier )
	{
		// Read text file
		$f = fopen( $fichier, 'r' );
		$txt = fread( $f, filesize( $fichier ) );
		fclose( $f );
		
		// Font
		$this->setFont( 'Times', '',12 );
		
		// Output text in a 6 cm width column
		$this->multicell( 60, 5, $txt );
		$this->ln();
		
		// Mention
		$this->setFont( '', 'I' );
		$this->cell( 0, 5, '(end of excerpt)' );
		
		// Go back to first column
		$this->SetCol( 0 );
	}

	function PrintChapter( $num, $title, $file )
	{
		// Add chapter
		$this->addPage();
		$this->ChapterTitle( $num, $title );
		$this->ChapterBody( $file );
	}
}


$pdf = new MyPDF();
$pdf->open();
$title = '20000 Leagues Under the Seas';
$pdf->setTitle( $title );
$pdf->setAuthor( 'Jules Verne' );
$pdf->PrintChapter( 1, 'A RUNAWAY REEF', '20k_c1.txt' );
$pdf->PrintChapter( 2, 'THE PROS AND CONS', '20k_c2.txt' );
$pdf->output();

?>

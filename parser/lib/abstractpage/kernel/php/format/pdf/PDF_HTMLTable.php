<?php

/*
+----------------------------------------------------------------------+
|This program is free software; you can redistribute it and/or modify  |
|it under the terms of the GNU General Public License as published by  |
|the Free Software Foundation; either version 2 of the License, or     |
|(at your option) any later version.                                   |
|                                                                      |
|This program is distributed in the hope that it will be useful,       |
|but WITHOUT ANY WARRANTY; without even the implied warranty of        |
|MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
|GNU General Public License for more details.                          |
|                                                                      |
|You should have received a copy of the GNU General Public License     |
|along with this program; if not, write to the Free Software           |
|Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.             |
+----------------------------------------------------------------------+
|Authors: Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * @package format_pdf
 */
 
class PDF_HTMLTable
{
	/**
	 * @access  public
	 */
	var $html;
	
	/**
	 * @access  public
	 */
	var $width = 1;
	
	/**
	 * @access  public
	 */
	var $title = '';
	
	/**
	 * @access  public
	 */
	var $borderColor = "000000";
	
	/**
	 * @access  public
	 */
	var $border = 0;
	
	/**
	 * @access  public
	 */
	var $cellPadding = 2;
	
	/**
	 * @access  public
	 */
	var $Cellspacing = 0;
	
	/**
	 * @access  public
	 */
	var $infRows = array();
	
	/**
	 * @access  public
	 */
	var $infCols = array();
	
	/**
	 * @access  public
	 */
	var $nbCols = 0;
	
	/**
	 * @access  public
	 */
	var $drawData = array();
	
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	function PDF_HTMLTable( $pdf, $html, $w = '' )
	{
		global $pdf;
						
		if ( empty( $w ) || !isset( $w ) )
			$this->width = $pdf->wPt - $pdf->lMargin - $pdf->rMargin;
		else
			$this->width = ( $pdf->wPt - $pdf->lMargin - $pdf->rMargin ) * $w;
		
		$this->Param = eregi_replace( '(<table[^>]*>).*', '\\1', $html );
		eregi( "<caption[^>]*>(.*)</caption>", $html, $t );
		
		if ( strlen( $t[1] ) )
			$this->title = $t[1];
		
		$this->html = ereg_replace( "(</?table[^>]*>)|(<caption[^>]*>(.*)</caption>)", "", $html );
		
		$this->getNfoTable( "BorderColor", "#([[:alnum:]]{6})" );
		$this->getNfoTable( "CellPadding", "([[:alnum:]]+)"    );
		$this->getNfoTable( "CellSpacing", "([[:alnum:]]+)"    );
		$this->getNfoTable( "Width",       "([[:alnum:]]+)%"   );
		$this->getNfoTable( "Border",      "([[:alnum:]]+)"    );
		
		$this->width      = ( $this->width > 1 )? $this->width / 100 : $this->width;
		$this->width      = ( $pdf->wPt - $pdf->lMargin - $pdf->rMargin ) * $this->width;
		$this->OldLMargin = $pdf->lMargin;
		$this->OldRMargin = $pdf->rMargin;
		$this->Margin     = ( $pdf->wPt - $this->width ) / 2;
		
		$this->splitTabHTML();
	}
	
	
	/**
	 * @access  public
	 */
	function getNfoTable( $prop, $reg )
	{
		eregi( '<table[^>]*' . strtolower( $prop ) . '="' . $reg . '"', $this->Param, $a );
		
		if ( strlen( $a[1] ) )
			$this->$prop = $a[1];
	}
	
	/**
	 * @access  public
	 */
	function splitTabHTML()
	{
		$this->Rows = preg_split( '/<\/?tr>/U', $this->html, -1, PREG_SPLIT_NO_EMPTY );
		$DecalRowSpan = array();
		
		for ( $nRows = 0; $nRows < count( $this->Rows ); $nRows++ )
		{
			$this->Cells = preg_split( '/<(\/?td.*)>/U', $this->Rows[$nRows], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
			
			$curCell = 1;
			$curRow  = chr( 65 + $nRows );
			
			for ( $p = 0; $p < count( $this->Cells ); $p += 3 )
			{
				$col = array();
				$row = array();
				
				$nameCell = $curRow . $curCell;
				
				if ( in_array( $nameCell, $DecalRowSpan ) )
				{
					$curCell++;
					$nameCell = $curRow . $curCell;
				}
				
				$this->nbCols = max( $this->nbCols, $curCell );
				
				ereg( '.*colspan="([[:digit:]]+)".*', $this->Cells[$p], $col );
				ereg( '.*rowspan="([[:digit:]]+)".*', $this->Cells[$p], $row );

				$txtCell = $this->Cells[$p + 1];
				$this->infCols[$curCell]["width"] = max( $this->infCols[$curCell]["width"], strlen( $txtCell ) );
				$minWord = $this->minWidthCell( $txtCell );
				
				if ( strlen( $minWord ) > strlen( $this->infCols[$curCell]["minWidth"] ) )
					$this->infCols[$curCell]["minWidth"] = $minWord;

				if ( $col[1] > 1 )
				{
					for ( $i = 2; $i <= $col[1]; $i++ )
					{
						$curCell++;
						$nomCell .= "¤" . $curRow . $curCell;
					}
				}
				else 
				{
					$col[1] = 1;
				}
				
				if ( $row[1] > 1 )
				{
					for ( $i = 2; $i <= $row[1]; $i++ )
					{
						$nameCell .= "¤" . chr( 65 + $nRows + $i - 1 ) . $curCell;
						$DecalRowSpan[]  = chr( 65 + $nRows + $i - 1 ) . $curCell;
					}
				}
				else 
				{	
					$row[1] = 1;
				}
				
				$this->infRows[$curRow][] = array(
					$nameCell, 
					$col[1], 
					$row[1], 
					$txtCell
				);
				
				$nbCellule = $curCell;
				$curCell++;
			}
		}
	}
	
	/**
	 * @access  public
	 */
	function testWriteTable( $pdfTest )
	{
		$CurrPage = $pdfTest->page;
		$this->writeTable( &$pdfTest );
		
		if ( $pdfTest->page != $CurrPage )
			return true;
		else 
			return false;
	}
	
	/**
	 * @access  public
	 */
	function writeTable( $pdf, $addPage = false )
	{
		if ( $addPage )
			$pdf->addPage();
		
		$noRow = 0;
		
		$pdf->setLeftMargin( $this->Margin );
		$pdf->setRightMargin( $this->Margin );
		$pdf->parseHTML( $pdf->_explodeHTML( $this->title . "<br><br>" ) );
		
		while ( list( $ligne, $cellule ) = each( $this->infRows ) )
		{
			$noRow++;
			$this->drawData[$noRow] = array();
			$htCell    = 0;
			$yBase     = $pdf->getY();
			$wCurrCell = 0;
			
			for ( $i = 0; $i < count( $cellule ); $i++ )
			{
				$nom     = $cellule[$i][0];
				$colSpan = $cellule[$i][1];
				$rowSpan = $cellule[$i][2];
				$contenu = $cellule[$i][3];
				$curCell = substr( $nom, 1, 1 );
				$dimCell = $pdf->_getDimCell( "¤", $nom );
				
				$pdf->setY( $yBase );
				$lMarge = $this->Margin;
				
				for ( $col = $curCell - 1; $col >= 1; $col-- )
					$lMarge += $this->infCols[$col]["width"] + $this->Cellspacing;
				
				$pdf->setLeftMargin( $lMarge + $this->Cellspacing );
				$wCurrCell = $this->infCols[$curCell]["width"];
				
				if ( $colSpan > 1 )
				{
					for( $z = 1; $z < $colSpan; $z++ )
						$wCurrCell += $this->infCols[$curCell + $z]["width"] + $this->Cellspacing;
				}
				
				$pdf->setRightMargin( $pdf->wPt - $pdf->lMargin - $wCurrCell );
				$pdf->setX( $pdf->lMargin );
				
				$this->drawData[$noRow]["cellule"][] = array(
					$pdf->getX(), 
					$pdf->getY(), 
					$wCurrCell, 
					$rowSpan
				);

				$pdf->setLeftMargin( $pdf->lMargin + $this->cellPadding );
				$pdf->setX( $pdf->lMargin );
				
				$pdf->setRightMargin( $pdf->rMargin + $this->cellPadding );
				$pdf->setY( $yBase + $this->cellPadding );
				
				$pdf->parseHTML( $pdf->_explodeHTML( $contenu ) );
				
				if ( $pdf->getY() - $yBase > $htCell )
					$htCell = $pdf->getY() - $yBase;
					
				if ( $rowSpan * $htCelluleParDefaut > $htCell )
					$htCell = $rowSpan * $htCelluleParDefaut;
					
				$pdf->setLeftMargin( $pdf->lMargin - $this->cellPadding );
			}
			
			$this->drawData[$noRow]["hauteur"] = $htCell + $pdf->fontSize + $this->cellPadding * 2;
			$pdf->setY( $yBase + $htCell + $pdf->fontSize + $this->cellPadding * 2 + $this->Cellspacing );
		}
		
		$pdf->setLeftMargin( $this->OldLMargin );
		$pdf->setRightMargin( $this->OldRMargin );
	}
	
	/**
	 * @access  public
	 */
	function drawCell( $pdf )
	{
		if ( $this->border < 1 ) 
			return;
		
		$pdf->setLineWidth( $this->border );
		$pdf->setDrawColorRGB( $this->borderColor );

		for ( $k = 1; $k <= count( $this->drawData ); $k++ )
		{
			for ( $i = 0; $i < count( $this->drawData[$k]["cellule"] ); $i++ )
			{
				$Cell   = $this->drawData[$k]["cellule"][$i];
				$htCell = $this->drawData[$k]["hauteur"];
				
				if ( $Cell[3] > 1 )
				{
					for ( $j = 1; $j < $Cell[3]; $j++ )
						$htCell +=  $this->drawData[$k + 1]["hauteur"] + $this->Cellspacing;
				}
				
				$pdf->_traceCell( $Cell[0], $Cell[1], $Cell[2], $htCell );
			}
		}
					
		if ( $this->Cellspacing > 0 )
		{
			$t = count( $this->drawData );
			$c = count( $this->drawData[$t]["cellule"] ) - 1;
			
			$pdf->_traceCell(
				$this->Margin, 
				$this->drawData[1]["cellule"][0][1] - $this->Cellspacing, 
				$this->width + ( 2 + count( $this->infCols ) - 1 ) * $this->Cellspacing, 
				$this->drawData[$t]["cellule"][$c][1] + $this->drawData[$t]["hauteur"] - $this->drawData[1]["cellule"][0][1] + 2 * $this->Cellspacing 
			);
		}
	}
	
	/**
	 * @access  public
	 */
	function minWidthCell( $t )
	{
		$t = strip_tags( $t );
		$l = preg_split( "/ /U", $t, -1, PREG_SPLIT_NO_EMPTY );
	
		for ( $i = 0; $i < count( $l ); $i++ )
		{
			if ( strlen( $mot ) < strlen( $l[$i] ) )
				$mot = $l[$i];
		}
						
		return $mot;
	}
	
	/**
	 * @access  public
	 */
	function widthCol( $pdf )
	{
		for ( $i = 1; $i <= count( $this->infCols ); $i++ )
		{
			$SumCol    += $this->infCols[$i]["width"];
			$SumMinCol += $pdf->getStringWidth( $this->infCols[$i]["minWidth"] );
		}
		
		$reste = $this->width - $SumMinCol;
		
		for ( $i = 1; $i <= count( $this->infCols ); $i++ )
		{
			$part = $reste / $SumCol * $this->infCols[$i]["width"];
			$this->infCols[$i]["width"] = $pdf->getStringWidth( $this->infCols[$i]["minWidth"] ) + $part;
		}
	}
} // END OF PDF_HTMLTable

?>

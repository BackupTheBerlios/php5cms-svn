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
 * This class generate an image with random text
 * to be used in form verification. It has visual
 * elements design to confuse OCR software preventing
 * the use of BOTS.
 *
 * @package image_verification
 */

class VerificationImage extends PEAR
{
	/**
	 * @access public
	 */
	var $numChars = 3;
	
	/**
	 * @access public
	 */
	var $w;
	
	/**
	 * @access public
	 */
	var $h = 20;
	
	/**
	 * @access public
	 */
	var $colBG = "188 220 231";
	
	/**
	 * @access public
	 */
	var $colTxt = "0 0 0";
	
	/**
	 * @access public
	 */
	var $colBorder = "0 128 192";
	
	/**
	 * @access public
	 */
	var $charx = 20;
	
	/**
	 * @access public
	 */
	var $numCirculos = 10;
	

	/**
	 * @access public
	 */
	function generateText( $num )
	{
		if ( ( $num != '' ) && ( $num > $this->numChars ) ) 
			$this->numChars = $num;		
		
		$this->texto = $this->gerString();
		$_SESSION['vImageCodS'] = $this->texto;
	}
	
	/**
	 * @access public
	 */
	function loadCodes()
	{
		$this->postCode    = $_POST['vImageCodP'];
		$this->sessionCode = $_SESSION['vImageCodS'];
	}
	
	/**
	 * @access public
	 */
	function checkCode()
	{
		if ( isset( $this->postCode ) ) 
			$this->loadCodes();
		
		if ( $this->postCode == $this->sessionCode )
			return true;
		else
			return false;
	}
	
	/**
	 * @access public
	 */
	function showCodeBox( $mode = 0, $extra = '' )
	{
		$str = "<input type=\"text\" name=\"vImageCodP\" " . $extra . " > ";
		
		if ( $mode )
			echo $str;
		else
			return $str;
	}
	
	/**
	 * @access public
	 */
	function showImage()
	{	
		$this->generateImage();
		
		header( "Content-type: image/png" );
		imagepng( $this->im );
	}

	/**
	 * @access public
	 */	
	function generateImage()
	{
		$this->w  = ( $this->numChars * $this->charx ) + 40;
		$this->im = imagecreatetruecolor( $this->w, $this->h ); 
		
		imagefill( $this->im, 0, 0, $this->getColor( $this->colBG ) );

		for ( $i = 1;$i <= $this->numCirculos;$i++ ) 
		{
			$randomcolor = imagecolorallocate( $this->im, rand( 100, 255 ), rand( 100, 255 ), rand( 100, 255 ) );
			imageellipse( $this->im, rand( 0, $this->w - 10 ), rand( 0, $this->h - 3 ), rand( 20, 60 ), rand( 20, 60 ), $randomcolor );
		}

		$ident = 20;
		for ( $i = 0; $i < $this->numChars; $i++ )
		{
			$char = substr( $this->texto, $i, 1 );
			$font = rand( 4, 5 );
			$y    = round( ( $this->h - 15 ) / 2 );
			$col  = $this->getColor( $this->colTxt );
			
			if ( ( $i % 2 ) == 0 )
				imagechar( $this->im, $font, $ident, $y, $char, $col );
			else
				imagecharup( $this->im, $font, $ident, $y + 10, $char, $col );
			
			$ident = $ident + $this->charx;
		}

		imagerectangle ( $this->im, 0, 0, $this->w - 1, $this->h - 1, $this->getColor( $this->colBorder ) );
	}

	/**
	 * @access public
	 */	
	function getColor( $var )
	{
		$rgb = explode( " ", $var );
		$col = imagecolorallocate( $this->im, $rgb[0], $rgb[1], $rgb[2] );
		
		return $col;
	}
	
	/**
	 * @access public
	 */
	function gerString()
	{
		rand( 0, time() );
		$possible="AGHacefhjkrStVxY124579";
		
		while ( strlen( $str ) < $this->numChars )
			$str .= substr( $possible, ( rand() % ( strlen( $possible ) ) ), 1 );

		$txt = $str;
		return $txt;
	}
} // END OF VerificationImage

?>

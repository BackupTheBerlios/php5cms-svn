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
 * Barcode Image Generator (Code 3 of 9).
 *
 * NOTE:
 * You must have GD-1.8 or higher compiled into PHP
 * in order to use PNG and JPEG. GIF images only work with
 * GD-1.5 and lower. (http://www.boutell.com)
 *
 * @package image_barcode
 */
		
class BarcodeImage39 extends PEAR
{
	/**
	 * Generate a Code 3 of 9 barcode.
	 *
	 * @access public
	 */
	function generate( $barcode, $width = 160, $height = 80, $quality = 100, $format = "PNG", $text = 1 )
	{
		$format = strtoupper( $format );
		
        switch ( $format )
        {
			default:
				$format = "JPEG";
			
			case "JPEG": 
				header( "Content-type: image/jpeg" );
				break;
				
			case "PNG":
				header( "Content-type: image/png" );
				break;
			
			case "GIF":
				header( "Content-type: image/gif" );
				break;
        }

		$im = imagecreate( $width, $height );
		
        $White = imagecolorallocate( $im, 255, 255, 255 );
        $Black = imagecolorallocate( $im,   0,   0,   0 );

        imageinterLace( $im, 1 );

        $NarrowRatio = 20;
        $WideRatio   = 55;
        $QuietRatio  = 35;

        $nChars    = ( strlen( $barcode ) + 2 ) * ( ( 6 * $NarrowRatio ) + ( 3 * $WideRatio ) + ( $QuietRatio ) );
        $Pixels    = $width / $nChars;
        $NarrowBar = (int)( 20 * $Pixels );
        $WideBar   = (int)( 55 * $Pixels );
        $QuietBar  = (int)( 35 * $Pixels );

        $ActualWidth = ( ( $NarrowBar * 6 ) + ( $WideBar * 3 ) + $QuietBar ) * ( strlen( $barcode ) + 2 );
        
        if ( ( $NarrowBar == 0) || ( $NarrowBar == $WideBar ) || ( $NarrowBar == $QuietBar ) || ( $WideBar == 0 ) || ( $WideBar == $QuietBar ) || ( $QuietBar == 0 ) )
        {
			imagestring( $im, 1, 0, 0, "image is too small", $Black );
			$this->_outputImage( $im, $format, $quality );
			
			exit;
        }
        
        $CurrentBarX = (int)( ( $width - $ActualWidth ) / 2 );
        $Color       = $White;
        $BarcodeFull = "*" . strtoupper( $barcode ) . "*";
        
		settype( $BarcodeFull, "string" );
        
        $FontNum    = 3;
        $FontHeight = imagefontheight( $FontNum );
        $FontWidth  = imagefontwidth( $FontNum );
		
        if ( $text != 0 )
        {
			$CenterLoc = (int)( ( $width - 1 ) / 2 ) - (int)( ( $FontWidth * strlen( $BarcodeFull ) ) / 2 );
			imagestring( $im, $FontNum, $CenterLoc, $height - $FontHeight, "$BarcodeFull", $Black );
        }

        for ( $i = 0; $i < strlen( $BarcodeFull ); $i++ )
        {
			$StripeCode = $this->_code39( $BarcodeFull[$i] );

			for ( $n = 0; $n < 9; $n++ )
			{
				if ( $Color == $White )
					$Color = $Black;
				else
					$Color = $White;
				
				switch ( $StripeCode[$n] )
				{
					case '0':
						imagefilledrectangle( $im, $CurrentBarX, 0, $CurrentBarX + $NarrowBar, $height - 1 - $FontHeight - 2, $Color );
						$CurrentBarX += $NarrowBar;
						break;

					case '1':
						imagefilledrectangle( $im, $CurrentBarX, 0, $CurrentBarX + $WideBar, $height - 1 - $FontHeight - 2, $Color );
						$CurrentBarX += $WideBar;
						break;
				}
			}

			$Color = $White;
			imagefilledrectangle( $im, $CurrentBarX, 0, $CurrentBarX + $QuietBar, $height - 1 - $FontHeight - 2, $Color );
			$CurrentBarX += $QuietBar;
        }

        $this->_outputImage( $im, $format, $quality );
	}

	
	// private methods
	
	/**
	 * Output an image to the browser.
	 *
	 * @access private
	 */
	function _outputImage( $im, $format, $quality )
	{
		switch ( $format )
        {
			case "JPEG": 
				imagejpeg( $im, "", $quality );
				break;
				
			case "PNG":
				imagepng( $im );
				break;
				
			case "GIF":
				imagegif( $im );
				break;
        }
	}

	/**
	 * Returns the Code 3 of 9 value for a given ASCII character.
	 *
	 * @access private
	 */
	function _code39( $Asc )
	{
        switch ( $Asc )
        {
			case ' ':
				return "011000100";
				
			case '$':
				return "010101000";
				
			case '%':
				return "000101010";
				
			case '*':
				return "010010100"; // * Start/Stop
				
			case '+':
				return "010001010";
				
			case '|':
				return "010000101";
				
			case '.':
				return "110000100";
				
			case '/':
				return "010100010";
				
			case '0':
				return "000110100";
				
			case '1':
				return "100100001";
				
			case '2':
				return "001100001";
				
			case '3':
				return "101100000";
				
			case '4':
				return "000110001";
				
			case '5':
				return "100110000";
				
			case '6':
				return "001110000";
				
			case '7':
				return "000100101";
				
			case '8':
				return "100100100";
				
			case '9':
				return "001100100";
				
			case 'A':
				return "100001001";
				
			case 'B':
				return "001001001";
				
			case 'C':
				return "101001000";
				
			case 'D':
				return "000011001";
				
			case 'E':
				return "100011000";
				
			case 'F':
				return "001011000";
				
			case 'G':
				return "000001101";
				
			case 'H':
				return "100001100";
				
			case 'I':
				return "001001100";
				
			case 'J':
				return "000011100";
				
			case 'K':
				return "100000011";
				
			case 'L':
				return "001000011";
				
			case 'M':
				return "101000010";
				
			case 'N':
				return "000010011";
				
			case 'O':
				return "100010010";
				
			case 'P':
				return "001010010";
				
			case 'Q':
				return "000000111";
				
			case 'R':
				return "100000110";
				
			case 'S':
				return "001000110";
				
			case 'T':
				return "000010110";
				
			case 'U':
				return "110000001";
				
			case 'V':
				return "011000001";
				
			case 'W':
				return "111000000";
				
			case 'X':
				return "010010001";
				
			case 'Y':
				return "110010000";
				
			case 'Z':
				return "011010000";
				
			default:
				return "011000100";
		}
	}
} // END OF BarcodeImage39

?>

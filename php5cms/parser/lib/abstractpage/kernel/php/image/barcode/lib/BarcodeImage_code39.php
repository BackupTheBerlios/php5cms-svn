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
|Authors: Ryan Briones <ryanbriones@webxdesign.org>                    |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'image.barcode.lib.BarcodeImage' );


/**
 * BarcodeImage_code39 creates Code 3 of 9 ( Code39 ) barcode images. It's
 * implementation borrows heavily for the perl module GD::Barcode::Code39
 *
 * @package image_barcode_lib
 */
 
class BarcodeImage_code39 extends BarcodeImage
{
	/**
	 * Barcode type
	 *
	 * @var    string
	 * @access private
	 */
	var $_type = 'code39';
	
	/**
	 * Barcode height
	 *
	 * @var    integer
	 * @access private
	 */
	var $_barcodeheight = 50;
	
	/**
	 * Bar thin width
	 *
	 * @var    integer
	 * @access private
	 */
	var $_barthinwidth = 1;
	
	/**
	 * Bar thick width
	 *
	 * @var    integer
	 * @access private
	 */
	var $_barthickwidth = 3;
	
	/**
	 * Whether to show text under barcode
	 *
	 * @var    array
	 * @access private
	 */
	var $_notext = false;
	
	/**
	 * Coding map
	 *
	 * @var    array
	 * @access private
	 */
	var $_coding_map = array(
		'0' => '000110100',
		'1' => '100100001',
		'2' => '001100001',
		'3' => '101100000',
		'4' => '000110001',
		'5' => '100110000',
		'6' => '001110000',
		'7' => '000100101',
		'8' => '100100100',
		'9' => '001100100',
		'A' => '100001001',
		'B' => '001001001',
		'C' => '101001000',
		'D' => '000011001',
		'E' => '100011000',
		'F' => '001011000',
		'G' => '000001101',
		'H' => '100001100',
		'I' => '001001100',
		'J' => '000011100',
		'K' => '100000011',
		'L' => '001000011',
		'M' => '101000010',
		'N' => '000010011',
		'O' => '100010010',
		'P' => '001010010',
		'Q' => '000000111',
		'R' => '100000110',
		'S' => '001000110',
		'T' => '000010110',
		'U' => '110000001',
		'V' => '011000001',
		'W' => '111000000',
		'X' => '010010001',
		'Y' => '110010000',
		'Z' => '011010000',
		'-' => '010000101',
		'*' => '010010100',
		'+' => '010001010',
		'$' => '010101000',
		'%' => '000101010',
		'/' => '010100010',
		'.' => '110000100',
		' ' => '011000100'
	);
		
		
	/**
	 * Constructor
	 *
	 * @access public
	 */
    function BarcodeImage_code39( $params = array() )
	{
		$this->BarcodeImage( $params );
		
		if ( !empty( $params['wthin'] ) )
            $this->_barthinwidth = $params['wthin'];

		if ( !empty( $params['wthick'] ) )
            $this->_barthickwidth = $params['wthick'];
			
		if ( !empty( $params['height'] ) )
            $this->_barcodeheight = $params['height'];
			
		if ( !empty( $params['notext'] ) )
            $this->_notext = $params['notext'];
    }
		

	/**
	 * Draw barcode.
	 *
	 * @param  string $text
	 * @return bool
	 * @access public
	 */
    function draw( $text = "" )
	{
		// Check $text for invalid characters
		if ( $this->_checkInvalid( $text ) )
            return false;
				
		// add start and stop * characters
		$final_text = "*" . $text . "*";
		$barcode    = '';
		
		foreach ( str_split( $final_text ) as $character )
           $barcode .= $this->_dumpCode( $this->_coding_map[$character] . '0' );
        
		$barcode_len = strlen( $barcode );

		// Create GD image object
		$img = imagecreate( $barcode_len, $this->_barcodeheight );
       
		// Allocate black and white colors to the image
		$black       = imagecolorallocate( $img, 0, 0, 0 );
		$white       = imagecolorallocate( $img, 255, 255, 255 );
		$font_height = ( $this->_notext? 0 : imagefontheight( "gdFontSmall" ) );
		$font_width  = imagefontwidth( "gdFontSmall" );

		// fill background with white color
		imagefill( $img, 0, 0, $white );

		// Initialize X position
		$xpos = 0;
       
		// draw barcode bars to image
		if ( $this->_notext ) 
		{
			foreach ( str_split( $barcode ) as $character_code ) 
			{
				if ( $character_code == 0 )
					imageline( $img, $xpos, 0, $xpos, $this->_barcodeheight, $white );
				else
					imageline( $img, $xpos, 0, $xpos, $this->_barcodeheight, $black );
						 
				$xpos++;
			}
		} 
		else 
		{
			foreach ( str_split( $barcode ) as $character_code )
			{
				if ( $character_code == 0 )
					imageline( $img, $xpos, 0, $xpos, $this->_barcodeheight - $font_height - 1, $white );
				else
					imageline( $img, $xpos, 0, $xpos, $this->_barcodeheight - $font_height - 1, $black );
							 
				$xpos++;
			}
						
			// draw text under barcode
			imagestring( $img, "gdFontSmall", ( $barcode_len - $font_width * strlen( $text ) )/2, $this->_barcodeheight - $font_height, $text, $black );
		}
			
		$this->_send( $img );		
		return true;
    }

	
	// private methods
	
	/**
	 * _dumpCode is a PHP implementation of dumpCode from the Perl module
	 * GD::Barcode::Code39. I royally screwed up when trying to do the thing
	 * my own way the first time. This way works.
	 *
	 * @param  string $code Code39 barcode code
	 * @return string $result barcode line code
     * @access private
     */
	function _dumpCode( $code )
	{
		$result = '';
		$color  = 1; // 1: Black, 0: White
		
		// if $bit is 1, line is wide; if $bit is 0 line is thin
		foreach ( str_split( $code ) as $bit ) 
		{
			$result .= ( ( $bit   == 1 )? str_repeat( "$color", $this->_barthickwidth ) : str_repeat( "$color", $this->_barthinwidth ) );
			$color   = ( ( $color == 0 )? 1 : 0 );
		}
		
		return $result;
	}
	
	/**
	 * Check for invalid characters
	 *
	 * @param  string $text
	 * @return bool returns true when invalid characters have been found
	 * @access private
	 */
	function _checkInvalid( $text )
	{
		return preg_match( "/[^0-9A-Z\-*+\$%\/. ]/", $text );
	}
} // END OF BarcodeImage_code39


/**
 * The function str_split() has been including, not part of the class for
 * it's ability to emulate Perl's split( //, $text ). This function was
 * stolen from the PHP function documentation comments on PHP's str_split()
 * which is to be included in PHP5.
 *
 * @param string $str
 * @param int number of characters you wish to split on
 * @return array|false Returns an array or false when $num is less than 1
 */
if ( !function_exists( 'str_split' ) )
{
	function str_split( $str, $num = '1' ) 
	{
   		if ( $num < 1 ) 
			return false;
   	
		$arr = array();
   		
		for ( $j = 0; $j < strlen( $str ); $j = $j + $num )
       		$arr[] = substr( $str, $j, $num );
   
   		return $arr;
	}
}

?>

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
|Authors: Marcelo Subtil Marcal <jason@unleashed.com.br>               |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'image.barcode.lib.BarcodeImage' );


/**
 * Class to create a Interleaved 2 of 5 barcode.
 *
 * @package image_barcode_lib
 */

class BarcodeImage_int25 extends BarcodeImage
{
    /**
     * Barcode type
	 *
     * @var    string
	 * @access private
     */
    var $_type = 'int25';

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
     * Coding map
	 *
     * @var    array
	 * @access private
     */
    var $_coding_map = array(
		'0' => '00110',
		'1' => '10001',
		'2' => '01001',
		'3' => '11000',
		'4' => '00101',
		'5' => '10100',
		'6' => '01100',
		'7' => '00011',
		'8' => '10010',
		'9' => '01010'
	);

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function BarcodeImage_int25( $params = array() )
	{
		$this->BarcodeImage( $params );
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
        $text = trim( $text );

        if ( !preg_match( "/[0-9]/", $text ) ) 
			return;

        // if odd $text lenght adds a '0' at string beginning
        $text = strlen( $text ) % 2 ? '0' . $text : $text;

        // Calculate the barcode width
        $barcodewidth = ( strlen( $text ) ) * ( 3 * $this->_barthinwidth + 2 * $this->_barthickwidth ) +
            ( strlen( $text ) ) * 2.5 +
            ( 7 * $this->_barthinwidth + $this->_barthickwidth ) + 3;

        // Create the image
        $img = imagecreate( $barcodewidth, $this->_barcodeheight );

        // Allocate the black and white colors
        $black = imagecolorallocate( $img, 0, 0, 0 );
        $white = imagecolorallocate( $img, 255, 255, 255 );

        // Fill image with white color
        imagefill( $img, 0, 0, $white );

        // Initiate x position
        $xpos = 0;

        // Draws the leader
        for ( $i = 0; $i < 2; $i++ ) 
		{
            $elementwidth = $this->_barthinwidth;
            imagefilledrectangle( $img, $xpos, 0, $xpos + $elementwidth - 1, $this->_barcodeheight, $black );
            $xpos += $elementwidth;
            $xpos += $this->_barthinwidth;
            $xpos++;
        }

        // Draw $text contents (2 chars at a time)
        for ( $idx = 0; $idx < strlen( $text ); $idx += 2 ) 
		{
			$oddchar  = substr( $text, $idx, 1 );     // get odd char
			$evenchar = substr( $text, $idx + 1, 1 ); // get even char

            // interleave
            for ( $baridx = 0; $baridx < 5; $baridx++ ) 
			{
                // Draws odd char corresponding bar (black)
                $elementwidth = ( substr( $this->_coding_map[$oddchar], $baridx, 1 ) )?  $this->_barthickwidth : $this->_barthinwidth;
                imagefilledrectangle( $img, $xpos, 0, $xpos + $elementwidth - 1, $this->_barcodeheight, $black );
                $xpos += $elementwidth;

                // Left enought space to draw even char (white)
                $elementwidth = ( substr($this->_coding_map[$evenchar], $baridx, 1 ) )?  $this->_barthickwidth : $this->_barthinwidth;
                $xpos += $elementwidth; 
                $xpos++;
            }
        }

        // Draws the trailer
        $elementwidth = $this->_barthickwidth;
        imagefilledrectangle( $img, $xpos, 0, $xpos + $elementwidth - 1, $this->_barcodeheight, $black );
        $xpos += $elementwidth;
        $xpos += $this->_barthinwidth;
        $xpos++;
        
		$elementwidth = $this->_barthinwidth;
        imagefilledrectangle( $img, $xpos, 0, $xpos + $elementwidth - 1, $this->_barcodeheight, $black );
		$this->_send( $img );
		
		return true;
    }
} // END OF BarcodeImage_int25

?>

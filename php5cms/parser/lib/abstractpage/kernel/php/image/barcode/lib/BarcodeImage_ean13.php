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
|Authors: Didier Fournout <didier.fournout@nyc.fr>                     |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'image.barcode.lib.BarcodeImage' );


/**
 * Class to create a EAN 13 barcode.
 *
 * @package image_barcode_lib
 */

class BarcodeImage_ean13 extends BarcodeImage
{
    /**
     * Barcode type
	 *
     * @var    string
	 * @access private
     */
    var $_type = 'ean13';

    /**
     * Barcode height
     *
     * @var    integer
	 * @access private
     */
    var $_barcodeheight = 50;

    /**
     * Font use to display text
     *
     * @var    integer
	 * @access private
     */
    var $_font = 2; // gd internal small font

    /**
     * Bar width
     *
     * @var    integer
	 * @access private
     */
    var $_barwidth = 1;

    /**
     * Number set
	 *
     * @var    array
	 * @access private
     */
    var $_number_set = array(
		'0' => array(
			'A' => array( 0, 0, 0, 1, 1, 0, 1 ),
			'B' => array( 0, 1, 0, 0, 1, 1, 1 ),
			'C' => array( 1, 1, 1, 0, 0, 1, 0 )
		),
		'1' => array(
			'A' => array( 0, 0, 1, 1, 0, 0, 1 ),
			'B' => array( 0, 1, 1, 0, 0, 1, 1 ),
			'C' => array( 1, 1, 0, 0, 1, 1, 0 )
		),
		'2' => array(
			'A' => array( 0, 0, 1, 0, 0, 1, 1 ),
			'B' => array( 0, 0, 1, 1, 0, 1, 1 ),
			'C' => array( 1, 1, 0, 1, 1, 0, 0 )
		),
		'3' => array(
			'A' => array( 0, 1, 1, 1, 1, 0, 1 ),
			'B' => array( 0, 1, 0, 0, 0, 0, 1 ),
			'C' => array( 1, 0, 0, 0, 0, 1, 0 )
		),
		'4' => array(
			'A' => array( 0, 1, 0, 0, 0, 1, 1 ),
			'B' => array( 0, 0, 1, 1, 1, 0, 1 ),
			'C' => array( 1, 0, 1, 1, 1, 0, 0 )
		),
		'5' => array(
			'A' => array( 0, 1, 1, 0, 0, 0, 1 ),
			'B' => array( 0, 1, 1, 1, 0, 0, 1 ),
			'C' => array( 1, 0, 0, 1, 1, 1, 0 )
		),
		'6' => array(
			'A' => array( 0, 1, 0, 1, 1, 1, 1 ),
			'B' => array( 0, 0, 0, 0, 1, 0, 1 ),
			'C' => array( 1, 0, 1, 0, 0, 0, 0 )
		),
		'7' => array(
			'A' => array( 0, 1, 1, 1, 0, 1, 1 ),
			'B' => array( 0, 0, 1, 0, 0, 0, 1 ),
			'C' => array( 1, 0, 0, 0, 1, 0, 0 )
		),
		'8' => array(
			'A' => array( 0, 1, 1, 0, 1, 1, 1 ),
			'B' => array( 0, 0, 0, 1, 0, 0, 1 ),
			'C' => array( 1, 0, 0, 1, 0, 0, 0 )
		),
		'9' => array(
			'A' => array( 0, 0, 0, 1, 0, 1, 1 ),
			'B' => array( 0, 0, 1, 0, 1, 1, 1 ),
			'C' => array( 1, 1, 1, 0, 1, 0, 0 )
		)
	);

	/**
	 * @access private
	 */
    var $_number_set_left_coding = array(
		'0' => array( 'A', 'A', 'A', 'A', 'A', 'A' ),
		'1' => array( 'A', 'A', 'B', 'A', 'B', 'B' ),
		'2' => array( 'A', 'A', 'B', 'B', 'A', 'B' ),
		'3' => array( 'A', 'A', 'B', 'B', 'B', 'A' ),
		'4' => array( 'A', 'B', 'A', 'A', 'B', 'B' ),
		'5' => array( 'A', 'B', 'B', 'A', 'A', 'B' ),
		'6' => array( 'A', 'B', 'B', 'B', 'A', 'A' ),
		'7' => array( 'A', 'B', 'A', 'B', 'A', 'B' ),
		'8' => array( 'A', 'B', 'A', 'B', 'B', 'A' ),
		'9' => array( 'A', 'B', 'B', 'A', 'B', 'A' )
	);
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function BarcodeImage_ean13( $params = array() )
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
        // TODO: Check if $text is number and len=13

        // Calculate the barcode width
        $barcodewidth = ( strlen( $text ) ) * ( 7 * $this->_barwidth )
            + 3 // left
            + 5 // center
            + 3 // right
            + imagefontwidth( $this->_font ) + 1;

        $barcodelongheight = (int)( imagefontheight( $this->_font ) / 2 ) + $this->_barcodeheight;

        // Create the image
        $img = imagecreate( $barcodewidth, $barcodelongheight + imagefontheight( $this->_font ) + 1 );

        // Allocate the black and white colors
        $black = imagecolorallocate( $img, 0, 0, 0 );
        $white = imagecolorallocate( $img, 255, 255, 255 );

        // Fill image with white color
        imagefill( $img, 0, 0, $white );

        // get the first digit which is the key for creating the first 6 bars
        $key = substr( $text, 0, 1 );

        // Initiate x position
        $xpos = 0;

        // print first digit
        imagestring( $img, $this->_font, $xpos, $this->_barcodeheight, $key, $black );
        $xpos = imagefontwidth( $this->_font ) + 1;

        // Draws the left guard pattern (bar-space-bar)

        // bar
        imagefilledrectangle( $img, $xpos, 0, $xpos + $this->_barwidth - 1, $barcodelongheight, $black );
        $xpos += $this->_barwidth;
		
        // space
        $xpos += $this->_barwidth;
        
		// bar
        imagefilledrectangle( $img, $xpos, 0, $xpos + $this->_barwidth - 1, $barcodelongheight, $black );
        $xpos += $this->_barwidth;

        // Draw left $text contents
        $set_array = $this->_number_set_left_coding[$key];
        
		for ( $idx = 1; $idx < 7; $idx ++ ) 
		{
            $value = substr( $text, $idx, 1 );
            imagestring( $img, $this->_font, $xpos + 1, $this->_barcodeheight, $value, $black );
            
			foreach ( $this->_number_set[$value][$set_array[$idx - 1]] as $bar ) 
			{
                if ( $bar )
                    imagefilledrectangle( $img, $xpos, 0, $xpos + $this->_barwidth - 1, $this->_barcodeheight, $black );
                
                $xpos += $this->_barwidth;
            }
        }

        // Draws the center pattern (space-bar-space-bar-space)
        
		// space
        $xpos += $this->_barwidth;
        
		// bar
        imagefilledrectangle( $img, $xpos, 0, $xpos + $this->_barwidth - 1, $barcodelongheight, $black );
        $xpos += $this->_barwidth;

        // space
        $xpos += $this->_barwidth;

        // bar
        imagefilledrectangle( $img, $xpos, 0, $xpos + $this->_barwidth - 1, $barcodelongheight, $black );
        $xpos += $this->_barwidth;

        // space
        $xpos += $this->_barwidth;

        // Draw right $text contents
        for ( $idx = 7; $idx < 13; $idx ++ ) 
		{
            $value = substr( $text, $idx, 1 );
            imagestring( $img, $this->_font, $xpos + 1, $this->_barcodeheight, $value, $black );
			
            foreach ( $this->_number_set[$value]['C'] as $bar ) 
			{
                if ( $bar )
                    imagefilledrectangle( $img, $xpos, 0, $xpos + $this->_barwidth - 1, $this->_barcodeheight, $black );
                
                $xpos += $this->_barwidth;
            }
        }

        // Draws the right guard pattern (bar-space-bar)

        // bar
        imagefilledrectangle( $img, $xpos, 0, $xpos + $this->_barwidth - 1, $barcodelongheight, $black );
        $xpos += $this->_barwidth;

        // space
        $xpos += $this->_barwidth;

        // bar
        imagefilledrectangle( $img, $xpos, 0, $xpos + $this->_barwidth - 1, $barcodelongheight, $black );
        $xpos += $this->_barwidth;
		$this->_send( $img );
		
		return true;
    }
} // END OF BarcodeImage_ean13

?>

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


using( 'format.swf.FlashWriter' );

 
define( "FLASHIMAGE_MAXJPEGSIZE", 100000 );


/**
 * PHP utility function for generating Shockwave Flash files
 * with the FlashWriter Toolkit
 *
 * @package format_swf
 */

class FlashImage extends PEAR
{
	/**
	 * @access public
	 */
	var $image;
	
	/**
	 * @access public
	 */
	var $width;
	
	/**
	 * @access public
	 */
	var $height;
	
	/**
	 * @access public
	 */
	var $filesize;
	
	/**
	 * @access public
	 */
	var $imageData;
	
	/**
	 * @access public
	 */
	var $footer1;
	
	/**
	 * @access public
	 */
	var $footer2;
	
	/**
	 * @access public
	 */
	var $footer3;
	
	
	/**
	 * @access public
	 */
	function setImage( $filename )
	{
		$this->image     = "";
		$this->imageData = "";
		$this->width     = 0;
		$this->height    = 0;
		$this->filesize  = 0;

		$fp = null;
		$fp = @fopen( $filename, "rb" );
		
		if ( !$fp )
			return false;

		$this->imageData = fread( $fp, FLASHIMAGE_MAXJPEGSIZE );
		
		if ( !$this->imageData )
			return false;
		
		@fclose( $fp );
		
		if ( strlen( $this->imageData ) == FLASHIMAGE_MAXJPEGSIZE )
		{
			$this->imageData = "";
			return false;
		}
		
		$imageInfo = GetImageSize( $filename );
		
		if ( $imageInfo[2] != 2 )
			return false;
		
		$this->filesize = strlen( $this->imageData );
		
		if ( !$this->filesize )
			return false;

		$this->image  = $filename;
		$this->width  = $imageInfo[0];
		$this->height = $imageInfo[1];
		
		unset( $imageInfo );
		return true;
	}
	
	/**
	 * @access public
	 */
	function createFooters()
	{
		$this->footer1 = "";
		$this->footer2 = "";
		$this->footer3 = "";
		
		$this->footer2 .= FlashWriter::writeWord( 0x02 );

		// rect
		$this->footer2 .= FlashWriter::writeRect( FlashWriter::getMinBits() + 1, 0, ( $this->width ) * FLASHWRITER_SCOORD1, 0, ( $this->height ) * FLASHWRITER_SCOORD1 );
		
		$this->footer2 .= FlashWriter::writeByte( 0x01 ); // FillStyleCount (1 style)
		$this->footer2 .= FlashWriter::writeByte( 0x41 ); // clipped bitmap fill
		$this->footer2 .= FlashWriter::writeWord( 1 );    // bitmap-id
		
		$this->footer2 .= FlashWriter::writeMatrix( true, 20 * FLASHWRITER_FIXED1, 20 * FLASHWRITER_FIXED1, false, 0, 0, 0, 0 );
		
		$this->footer2 .= FlashWriter::writeByte( 0x00 ); // LineStyleCount (no line)
		$this->footer2 .= FlashWriter::writeByte( 0x10 ); // number of fill/line index bits
		$this->footer2 .= FlashWriter::writeBits( 0, 1 ); // Non-edge record flag
		$this->footer2 .= FlashWriter::writeBits( 0, 1 ); // New styles flag
		$this->footer2 .= FlashWriter::writeBits( 0, 1 ); // Line style change flag
		$this->footer2 .= FlashWriter::writeBits( 1, 1 ); // Fill style 1 change flag
		$this->footer2 .= FlashWriter::writeBits( 0, 1 ); // Fill style 0 change flag
		$this->footer2 .= FlashWriter::writeBits( 1, 1 ); // Move to flag
		
		$this->footer2 .= FlashWriter::writeBits( FlashWriter::getMinBits() + 1, 5 );
		$this->footer2 .= FlashWriter::writeBits( ( $this->width  ) * FLASHWRITER_SCOORD1, FlashWriter::getMinBits() + 1 );
		$this->footer2 .= FlashWriter::writeBits( ( $this->height ) * FLASHWRITER_SCOORD1, FlashWriter::getMinBits() + 1 );
		
		$this->footer2 .= FlashWriter::writeBits( 1, 1 ); // Fill 1 Style = 1 (this is our bitmap-style)		
		
		$this->footer2 .= FlashWriter::writeLine( 0, -( $this->width )  * FLASHWRITER_SCOORD1 );
		$this->footer2 .= FlashWriter::writeLine( 1, -( $this->height ) * FLASHWRITER_SCOORD1 );
		$this->footer2 .= FlashWriter::writeLine( 0,  ( $this->width )  * FLASHWRITER_SCOORD1 );
		$this->footer2 .= FlashWriter::writeLine( 1,  ( $this->height ) * FLASHWRITER_SCOORD1 );
		
		$this->footer2 .= FlashWriter::writeBits( 0, 1 ); // Non-edge record flag
		$this->footer2 .= FlashWriter::writeBits( 0, 5 ); // End of shape flag
		
		$this->footer2 .= FlashWriter::flushBits(); // flush bits to keep byte aligned
		
		// 1st footer
		$this->footer1 .= FlashWriter::writeWord( 0xbf );
		$this->footer1 .= FlashWriter::writeDWord( strlen( $this->footer2 ) );
		
		// 3rd footer		
		$this->footer3 .= FlashWriter::writeWord( 0x0686 ); // placeObject2
		$this->footer3 .= FlashWriter::writeByte( 0x06 );   // body has a transform matrix and object has a character ID
		$this->footer3 .= FlashWriter::writeWord( 0x01 );   // depth = 1
		$this->footer3 .= FlashWriter::writeWord( 0x02 );   // character-id
		$this->footer3 .= FlashWriter::writeByte( 0x00 );   // no transformation
		
		$this->footer3 .= FlashWriter::writeWord( 0x40 );
		$this->footer3 .= FlashWriter::writeWord( 0x00 );
	}
	
	/**
	 * @access public
	 */
	function checkImage()
	{
		if ( strstr( $this->imageData, pack( "n", 0xFFC2 ) ) )
			return false;
		
		if ( strstr( $this->imageData, pack( "n", 0xFFC6 ) ) )
			return false;
		
		if ( strstr( $this->imageData, pack( "n", 0xFFCA ) ) )
			return false;
		
		return true;
	}

	/**
	 * @access public
	 */	
	function toString()
	{
		if ( !$this->image )
			return;
		
		FlashWriter::initMinBits( $this->width, $this->height );
		$this->createFooters();
		
		$swfsize  = 29;
		$swfsize += $this->filesize;
		$swfsize += strlen( $this->footer1 );
		$swfsize += strlen( $this->footer2 );
		$swfsize += strlen( $this->footer3 );
		$swfsize += strlen( FlashWriter::writeRect( FlashWriter::getMinBits() + 1, 0, FLASHWRITER_SCOORD1 * ( $this->width ), 0, FLASHWRITER_SCOORD1 * ( $this->height ) ) );

		// reversed SWF signature (little endian/big endian issue)
		$swf = "FWS";
		
		$swf.= FlashWriter::writeByte( 5 );          // file version, flash 5 is less complicated
		$swf.= FlashWriter::writeDWord( $swfsize );  // length of entire file in bytes
		
		// frame size in TWIPS
		$swf.= FlashWriter::writeRect( FlashWriter::getMinBits() + 1, 0, FLASHWRITER_SCOORD1 * ( $this->width ), 0, FLASHWRITER_SCOORD1 * ( $this->height ) );
		
		$swf.= FlashWriter::writeByte( 0 );          // this one is ignored!
		$swf.= FlashWriter::writeByte( 12 );         // frame delay in 8.8 fixed number of frames per second
		$swf.= FlashWriter::writeWord( 1 );          // total number of frames in movie
		$swf.= FlashWriter::writeWord( 0x0243 );     // setBackgroundColor
		$swf.= FlashWriter::writeByte( 0xff );       // red
		$swf.= FlashWriter::writeByte( 0xff );       // green
		$swf.= FlashWriter::writeByte( 0xff );       // blue
		
		$swf.= FlashWriter::writeWord( 0x057f );     // DefineBitsJPEG2
		
		// +6, for 0xff 0xd9 0xff 0xd8 [imagedata] 0xff 0xd8
		$swf.= FlashWriter::writeDWord( ( $this->filesize ) + 6 );

		$swf.= FlashWriter::writeWord( 1 );          // character-id
		
		$swf.= FlashWriter::writeByte( 0xff );       // SOI
		$swf.= FlashWriter::writeByte( 0xd9 );
		
		$swf.= FlashWriter::writeByte( 0xff );       // EOI
		$swf.= FlashWriter::writeByte( 0xd8 );
		
		// raw image data including startImage and endImage
		$swf.= $this->imageData;
		
		$swf.= $this->footer1;
		$swf.= $this->footer2;
		$swf.= $this->footer3;
		
		return $swf;
	}
	
	/**
	 * @access public
	 */
	function outputSWF()
	{
		header( "Content-type: application/x-shockwave-flash" );
		echo $this->toString();
	}

	/**
	 * @access public
	 */	
	function outputSWFDownload( $filename )
	{
		$swf = $this->toString();
		
		header( "Content-Type: application/force-download" );
		header( "Content-disposition: attachment; filename=$filename" );
		header( "Content-Transfer-Encoding: binary" );
		header( "Content-Length: " . strlen( $swf ) );
		header( "Pragma: no-cache" );
		header( "Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0" );
		header( "Expires: 0" );
		
		echo $swf;
	}

	/**
	 * @access public
	 */	
	function saveToFile( $filename )
	{
		$fp = @fopen( $filename, "wb" );
		
		if ( !$fp )
			return false;
		
		fwrite( $fp, $this->toString() );
		@fclose( $fp );
		
		return true;
	}
} // END OF FlashImage

?>

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
 * SWFDump is a class which defines an object to hold the properties
 * of the Flash file you pass to the constructor. The list of available
 * properties is the list of var's declared at the begginning of the class
 * code. See the example use of the function at the end of this code 
 * to get an idea of what each property is used for.
 *
 * Usage:
 *
 * $flashInfo = new SWFProperties( "example.swf" );
 * 
 * // If $flashInfo->status == true, the file is not compressed, and we continue,
 * // otherwise, we will show an error message and halt the program.
 * if ( !$flashInfo->status ) 
 * {	
 * 		exit( "SWF file is compressed. This cannot read compressed SWF files." );
 * } 
 * else 
 * {	
 * 		echo( "<PRE>");
 * 		echo( "       SWF version: " . $flashInfo->mVersion    . "\n" );
 * 		echo( "       File length: " . $flashInfo->mFilelen    . " (" . round( $flashInfo->mFilelen / 1000 ) . " KB)\n\n" );
 * 		echo( "             Width: " . $flashInfo->mWidth      . " px\n"     );
 * 		echo( "            Height: " . $flashInfo->mHeight     . " px\n\n"   );
 * 		echo( "             X min: " . $flashInfo->mXmin / 20  . " px\n"     );
 * 		echo( "             X max: " . $flashInfo->mXmax / 20  . " px\n"     );
 * 		echo( "             Y min: " . $flashInfo->mYmin / 20  . " px\n"     );
 * 		echo( "             Y max: " . $flashInfo->mYmax / 20  . " px\n\n"   );
 * 		echo( "        Frame rate: " . $flashInfo->mFrameRate  . " fps\n"    );
 * 		echo( "       Frame count: " . $flashInfo->mFrameCount . " frames\n" );
 * 		echo( "          Duration: " . ceil( $flashInfo->mFrameCount / $flashInfo->mFrameRate ) . " seconds\n" );
 *
 * }
 *
 * @package format_swf
 */

class SWFProperties extends PEAR
{
	/**
	 * @access public
	 */
	var $flashHeader;
	
	/**
	 * @access public
	 */
	var $RECTdata;
	
	/**
	 * @access public
	 */
	var $nBits;
	
	/**
	 * @access public
	 */
	var $mVersion;
	
	/**
	 * @access public
	 */
	var $mFilelen;
	
	/**
	 * @access public
	 */
	var $mXmax;
	
	/**
	 * @access public
	 */
	var $mYmax;
	
	/**
	 * @access public
	 */
	var $mXmin;
	
	/**
	 * @access public
	 */
	var $mYmin;
	
	/**
	 * @access public
	 */
	var $mHeight;
	
	/**
	 * @access public
	 */
	var $mWidth;
	
	/**
	 * @access public
	 */
	var $mFrameRate;
	
	/**
	 * @access public
	 */
	var $mFrameCount;
	
	/**
	 * @access public
	 */
	var $mFrameRateByteLoc;
	
	/**
	 * @access public
	 */
	var $i;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function SWFProperties( $filename ) 
	{
		// Read in the flash file header.
		$this->flashHeader = $this->_getFlashHeader( $filename );
		
		/*
		if ( PEAR::isError( $this->flashHeader ) )
		{
			$this = $this->flashHeader;
			return;
		}
		*/
		
		// Check if the SWF is compressed (for v6), if so, we can't read it..
		if ( substr( $this->flashHeader, 0, 3 ) == "CWS" ) 
		{
			$this->status = false;
			return;
		} 
		else if ( substr( $this->flashHeader, 0, 3 ) == "FWS" ) 
		{
			$this->status = true;
		}
		
		// Get the flash file version.
		$this->mVersion = ord( substr( $this->flashHeader, 3, 1 ) );
    		
    	// Figure out the file length.
    	$this->mFilelen  = ord( substr( $this->flashHeader, 4, 1 ) );
    	$this->mFilelen += ord( substr( $this->flashHeader, 5, 1 ) ) * 256;
    	$this->mFilelen += ord( substr( $this->flashHeader, 6, 1 ) ) * 65536;
    	$this->mFilelen += ord( substr( $this->flashHeader, 7, 1 ) ) * 16777216;	
  	
  		// Get the RECT coords to find out movie dimensions and boundaries.
    	$this->RECTdata  = $this->_decbin_pad( ord( substr( $this->flashHeader,  8, 1 ) ), 8 );
    	$this->RECTdata .= $this->_decbin_pad( ord( substr( $this->flashHeader,  9, 1 ) ), 8 );
    	$this->RECTdata .= $this->_decbin_pad( ord( substr( $this->flashHeader, 10, 1 ) ), 8 );
    	$this->RECTdata .= $this->_decbin_pad( ord( substr( $this->flashHeader, 11, 1 ) ), 8 );
    	$this->RECTdata .= $this->_decbin_pad( ord( substr( $this->flashHeader, 12, 1 ) ), 8 );
    	$this->RECTdata .= $this->_decbin_pad( ord( substr( $this->flashHeader, 13, 1 ) ), 8 );
    	$this->RECTdata .= $this->_decbin_pad( ord( substr( $this->flashHeader, 14, 1 ) ), 8 );
    	$this->RECTdata .= $this->_decbin_pad( ord( substr( $this->flashHeader, 15, 1 ) ), 8 );
    	$this->RECTdata .= $this->_decbin_pad( ord( substr( $this->flashHeader, 16, 1 ) ), 8 );

		// Get the Xmin, Xmax, Ymin, Ymax boundaries (in pixels).
		$this->nBits = bindec( substr( $this->RECTdata, 0, 5 ) );				
		$this->mXmin = bindec( substr( $this->RECTdata, 5, $this->nBits ) );
		$this->mXmax = bindec( substr( $this->RECTdata, 5 + $this->nBits * 1, $this->nBits ) );
		$this->mYmin = bindec( substr( $this->RECTdata, 5 + $this->nBits * 2, $this->nBits ) );
		$this->mYmax = bindec( substr( $this->RECTdata, 5 + $this->nBits * 3, $this->nBits ) );
		
		// Get the width & height of the movie clip.
		$this->mHeight = ( $this->mYmax - $this->mYmin ) / 20;
		$this->mWidth  = ( $this->mXmax - $this->mXmin ) / 20;
		
		// Get the number of bits occupied by RECTANGLE.
		$this->nBitsRect = ( $this->nBits * 4 ) + 5;

		// Get the number of bytes occupied by RECTANGLE
		// number of bytes would be completely divisible by 8.
		while ( ( $this->nBitsRect + $this->i ) / 8 != ceil( $this->nBitsRect / 8 ) )
			$this->i++;
		
		// add 9 bytes ( 3 (FWS) + 1 (Version) + 4 (filesize) + 1 (first byte is for decimals, so skip it)
		$this->mFrameRateByteLoc = ( ( $this->nBitsRect + $this->i ) / 8 ) + 9 ; 

		// .. and the frame rate and total # of frames..
		$this->mFrameRate = ord( substr( $this->flashHeader, $this->mFrameRateByteLoc, 1 ) ) + "." + $this->_fraction_bintodec( ord( substr( $this->flashHeader, $this->mFrameRateByteLoc - 1 , 1 ) ) );
	
		$this->mFrameCount  = ord( substr( $this->flashHeader, $this->mFrameRateByteLoc + 1, 1 ) );
		$this->mFrameCount += ord( substr( $this->flashHeader, $this->mFrameRateByteLoc + 2, 1 ) ) * 256;
	}
	 
	 
	// private methods
	    
	/**
	 * Reads in the flash header from the SWF file on disk.
	 *
	 * @access private
	 */
	function _getFlashHeader( $filename ) 
	{
		$filename = basename( $filename );
		
		if ( file_exists( $filename ) ) 
		{
			$FLASHFILE = fopen( $filename, "r" );
			$tempData  = fread( $FLASHFILE, 21 );
			fclose( $FLASHFILE );
			
			return ( $tempData );
		}
	}
	
	/**
	 * Converts Decimal -> Binary, and left-pads it with $padvalue 0's.
	 *
	 * @access private
	 */
	function _decbin_pad( $inputdec, $padvalue ) 
	{
    	return str_pad( decbin( $inputdec ), $padvalue, "0", STR_PAD_LEFT );
    }

	/**
	 * @access private
	 */	
	function _fraction_bintodec( $inputfraction ) 
	{
		$binRep = $this->_decbin_pad( $inputfraction, 8 );
		$old    = 0;

		for ( $i = 8; $i--; $i > 0 ) 
			$old = ( $old + $binRep[$i] ) / 2;
						
		return $old;
	}
} // END OF SWFProperties

?>

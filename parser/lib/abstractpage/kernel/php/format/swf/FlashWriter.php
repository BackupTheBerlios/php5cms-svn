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


define( "FLASHWRITER_FIXED1",  0x00010000 );
define( "FLASHWRITER_SCOORD1", 20 );


/**
 * PHP function library for generating Shockwave Flash files.
 *
 * @package format_swf
 */
 
class FlashWriter extends PEAR
{
	/**
	 * Converts a given DWORD into its binary representation.
	 *
	 * @access public
	 */
	function writeDWord( $o )
	{
		return pack( "V", $o );
	}

	/**
	 * Converts a given WORD into its binary representation.
	 *
	 * @access public
	 */
	function writeWord( $o )
	{
		return pack( "v", $o );
	}

	/**
	 * Converts a given BYTE into its binary representation.
	 *
	 * @access public
	 */
	function writeByte( $o )
	{
		return pack( "C", $o );
	}

	/**
	 * Adds 'size' bits from 'data' to the stream until all 
	 * 'size' bits have been output.
	 *
	 * Currently a maximum of 31 bits is supported only. 
	 * For adding 32 bits split it up into two 16-bit shunks of data.
	 *
	 * @access public
	 */
	function writeBits( $data, $size )
	{
		global $bytePos;
		global $currentByte;
	
		$ret = "";

		if ( $data < 0 )
		{
			$data *= -1;
			$data = ( 0x7FFFFFFF >> ( 31 - $size ) ) - $data;
			$data += 1;
		}

		while ( $size != 0 )
		{
			if ( $size > $bytePos )
			{
				$currentByte |= $data << ( 30 - $size ) >> ( 30 - $bytePos );
				$ret .= FlashWriter::writeByte( $currentByte );
				$size -= $bytePos;
				$currentByte = 0;
				$bytePos = 8;
			}
			else if ( $size <= $bytePos )
			{
				$currentByte |= $data << ( 30 - $size ) >> ( 30 - $bytePos );
				$bytePos -= $size;
				$size = 0;
			
				if ( !$bytePos )
				{
					$ret .= FlashWriter::writeByte( $currentByte );
					$currentByte = 0;
					$bytePos = 8;
				}
			}
		}
	
		return $ret;
	}

	/**
	 * Kick out the current partially filled byte to the stream.
	 * If there is a byte currently being built for addition to
	 * the stream, then the end of that byte is filled with zeroes
	 * and the byte is added to the stream.
	 *
	 * @access public
	 */
	function flushBits()
	{
		global $bytePos;
		global $currentByte;
	
		$ret = "";

		if ( $bytePos != 8 )
		{
			$ret = FlashWriter::writeByte( $currentByte );
			$currentByte = 0;
			$bytePos = 8;
		}
	
		return $ret;
	}

	/**
	 * Writes a rectangle to the stream.
	 *
	 * @access public
	 */
	function writeRect( $nBits, $xmin, $xmax, $ymin, $ymax )
	{
		$ret  = "";
		$ret .= FlashWriter::writeBits( $nBits, 5 );
		$ret .= FlashWriter::writeBits( $xmin, $nBits );
		$ret .= FlashWriter::writeBits( $xmax, $nBits );
		$ret .= FlashWriter::writeBits( $ymin, $nBits );
		$ret .= FlashWriter::writeBits( $ymax, $nBits );
		$ret .= FlashWriter::flushBits();
	
		return $ret;
	}

	/**
	 * Writes a matrix to the stream.
	 *
	 * @access public
	 */
	function writeMatrix( $hasScale, $scaleX, $scaleY, $hasRotate, $rotateSkew0, $rotateSkew1, $translateX, $translateY )
	{
		$nScaleBits     = 22;
		$nRotateBits    = 0;
		$nTranslateBits = 0;

		$ret = "";
		$ret .= FlashWriter::writeBits( $hasScale, 1 );
		
		if ( $hasScale )
		{
			$ret .= FlashWriter::writeBits( $nScaleBits, 5);
			$ret .= FlashWriter::writeBits( $scaleX, $nScaleBits );
			$ret .= FlashWriter::writeBits( $scaleY, $nScaleBits );
		}
		
		$ret .= FlashWriter::writeBits( $hasRotate, 1 );
		
		if ( $hasRotate )
		{
			$ret .= FlashWriter::writeBits( $nRotateBits, 5 );
			$ret .= FlashWriter::writeBits( $rotateSkew0, $nRotateBits );
			$ret .= FlashWriter::writeBits( $rotateSkew1, $nRotateBits );
		}
		
		$ret .= FlashWriter::writeBits( $nTranslateBits, 5 );
		$ret .= FlashWriter::writeBits( $translateX, $nTranslateBits);
		$ret .= FlashWriter::writeBits( $translateY, $nTranslateBits);
		$ret .= FlashWriter::flushBits();
		
		return $ret;
	}

	/**
	 * Writes a line to the stream.
	 *
	 * @access public
	 */
	function writeLine( $hv, $delta )
	{
		$ret  = "";
		$ret .= FlashWriter::writeBits( 1, 1 );										// edge-record
		$ret .= FlashWriter::writeBits( 1, 1 );										// straight
		$ret .= FlashWriter::writeBits( FlashWriter::getMinBits() - 1, 4 );			// nBits
		$ret .= FlashWriter::writeBits( 0, 1 );										// Vert/Horz
		$ret .= FlashWriter::writeBits( $hv, 1 );									// vertical
		$ret .= FlashWriter::writeBits( $delta, FlashWriter::getMinBits() + 1 );	// DeltaX
	
		return $ret;
	}

	/**
	 * Calculates the mininum of bits required to store the
	 * variables width and height and stores result in globa
	 * variable minBits.
	 *
	 * @access public
	 */
	function initMinBits( $width, $height )
	{
		global $minBits;
		
		$max = max( $width * FLASHWRITER_SCOORD1, $height * FLASHWRITER_SCOORD1 );
	
		if ( !$max )
			return false;
	
		$x = 1;
	
		for ( $i = 1; $i < 32; $i++ )
		{
			$x <<= 1;

			if ( $x > $max )
				break;
		}
	
		$minBits = $i;
	}

	/**
	 * Returns the global variable minBits.
	 *
	 * @access public
	 */
	function getMinBits()
	{
		global $minBits;
		return $minBits;
	}
} // END OF FlashWriter

?>

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
 * Image2SWF class - no extension required.
 *
 * Note: maximum size 700x700px
 * No progressive Jpegs!
 *
 * @package format_swf
 */

class Image2SWF extends PEAR
{
	/**
	 * @access public
	 */
	var $swfCode = "";
	
	/**
	 * @access public
	 */
	var $error = false;
	
	
	/**
	 * @access public
	 */
	function int2code( $zahl, $byteAnzahl )
	{
		$tmp = "";
		
		for ( $i = 0; $i < $byteAnzahl; $i++ ) 
			$tmp .= chr( ( integer )( $zahl / pow( 256, $i ) ) % 256 );
		
		return $tmp;
	}
	
	/**
	 * @access public
	 */
	function bin2code( $binString )
	{
		$tmp = "";
		
		while ( $byte = substr( $binString, 0, 8 ) )
		{
			$binString = substr( $binString, 8 );
			$tmp .= chr( bindec( $byte ) );
		}
		
		return $tmp;
	}
	
	/**
	 * @access public
	 */
	function byteArray2code( $byteArray )
	{
		$tmp = "";
		
		foreach ( $byteArray as $byte )
			$tmp .= chr( $byte );
			
		return $tmp;
	}
	
	/**
	 * @access public
	 */
	function getBinRECT( $minX, $maxX, $minY, $maxY )
	{
		return "01111" . sprintf("%015b%015b%015b%015b", $minX * 20, $maxX * 20, $minY * 20, $maxY * 20 );
	}
	
	/**
	 * @access public
	 */
	function getTagSetBgColor( $r = 0, $g = 0, $b = 0 )
	{
		return sprintf( "%c%c%c%c%c", 67, 2, $r, $g, $b );
	}
	
	/**
	 * @access public
	 */
	function getTagDefineShape( $minX, $maxX, $minY, $maxY, $id )
	{
		$tmp  = $this->bin2code( $this->getBinRECT( $minX, $maxX, $minY, $maxY ) . "0000000" );
		$tmp .= $this->byteArray2code( array( 0x01, 0x41, 0x01, 0x00, 0xd9, 0x40, 0x00, 0x05, 0x00, 0x00, 0x00, 0x00, 0x10 ) );
		$binString = "000100111110100" . sprintf( "%015b", ( $minX + $maxX ) * 20 ) . "11110101" . sprintf( "%015b", ( $minY + $maxY ) * 20 ) . "11110100" . substr( decbin( ( $minX - $maxX ) * 20 ), -15 ) . "11110101" . substr( decbin( ( $minY - $maxY ) * 20 ), -15 ) . "0000000000000";
		$tmp .= $this->bin2code( $binString );
		$tmp .= $this->byteArray2code( array( 0x13, 0xe0, 0x96, 0x07, 0x8a, 0x58, 0x1e, 0x16, 0xa0, 0x78, 0xda, 0x80, 0x00) );
		
		return $this->byteArray2code( array( 0xbf, 0x00 ) ) . $this->int2code( strlen( $tmp ) + 2, 4 ) . $this->int2code( $id, 2 ) . $tmp;
	}
	
	/**
	 * @access public
	 */
	function getTagPlaceObject2( $depth, $id )
	{
		return $this->byteArray2code( array( 0x86, 0x06, 0x06 ) ) . $this->int2code( $depth, 2 ) . $this->int2code( $id, 2 ) . chr( 0 );
	}
	
	/**
	 * @access public
	 */
	function getTagShowFrame()
	{
		return chr( 0x40 ) . chr( 0x00 );
	}
	
	/**
	 * @access public
	 */
	function getTagDefineBitsJPG2( $filename, $id )
	{
		if ( !file_exists( $filename ) )
			return false;
		
		if ( !( $picInfo = getimagesize( $filename ) ) )
			return false;
		
		if ( $picInfo[2] != 2 )
			return false;
		
		if ( !( $fp = @fopen( $filename, "rb" ) ) )
			return false;
		
		if ( !( $img = @fread( $fp, @filesize( $filename ) ) ) )
		{
			@fclose( $fp );
			return false;
		}
		
		if ( !@fclose( $fp ) )
			return false;
		
		return chr( 0x7f ) . chr( 0x05 ) . $this->int2code( strlen( $img ) + 6, 4 ) . $this->int2code( $id, 2 ) . $this->byteArray2code( array( 0xff, 0xd9, 0xff, 0xd8 ) ) . $img;
	}
	
	/**
	 * @access public
	 */
	function getTagEnd()
	{
		return chr( 0 ).chr( 0 );
	}
	
	/**
	 * @access public
	 */
	function buildFromJPG( $filename )
	{
		if ( !file_exists( $filename ) )
			return false;
		
		if ( !( $picInfo = getimagesize( $filename ) ) )
			return false;
		
		if ( $picInfo[2] != 2 )
			return false;
		
		$framesize = $this->bin2code( $this->getBinRECT( 0, $picInfo[0], 0, $picInfo[1] ) . "0000000" );
		$framerateUndAnzahl = $this->byteArray2code( array( 0x00, 0x0c, 0x01, 0x00 ) );
		$this->swfCode  = $framesize . $framerateUndAnzahl . $this->getTagSetBgColor( 0, 0, 0 );
		
		$tmp = $this->getTagDefineBitsJPG2( $filename, 1 );
		
		if ( $tmp == false )
		{
			$this->error = true;
			return false;
		}
		$this->swfCode .= $tmp;
		
		$this->swfCode .= $this->getTagDefineShape( 0, $picInfo[0], 0, $picInfo[1], 2 ) . $this->getTagPlaceObject2( 1, 2 ) . $this->getTagShowFrame() . $this->getTagEnd();
		$this->swfCode  = $this->byteArray2code( array( 0x46, 0x57, 0x53, 0x03 ) ) . $this->int2code( strlen( $this->swfCode ) + 8, 4 ) . $this->swfCode;
		
		return true;
	}
	
	/**
	 * @access public
	 */
	function get()
	{
		return $this->swfCode;
	}
	
	/**
	 * @access public
	 */
	function show()
	{
		header( 'Content-type: application/x-shockwave-flash' );
		
		if ( print( $this->swfCode ) )
			return true;
		
		return false;
	}
	
	/**
	 * @access public
	 */
	function save( $filename )
	{
		if ( !( $fp = @fopen( $filename, "wb" ) ) )
			return false;
		
		if ( !@fwrite( $fp, $this->swfCode ) )
		{
			@fclose( $fp );
			return false;
		}
		
		if ( !@fclose( $fp ) )
			return false;
		
		return true;
	}
} // END OF Image2SWF

?>

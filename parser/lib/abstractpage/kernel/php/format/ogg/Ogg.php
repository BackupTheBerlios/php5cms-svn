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
 * A Class for reading Ogg comment tags.
 * 
 * To use this code first create a new instance on a file. Then loop
 * though the $ogg->fields array. Inside that loop, loop again. The
 * ogg comment format allows mome then one field with the same name
 * so it is possible for the ARTIST fields to appear twice if a work
 * has two performers.
 *
 * @package format_ogg
 */
	 
class Ogg extends PEAR
{
	/**
	 * ogg file name (you should never modify this)
	 * @access public
	 */
	var $file = false;

	/**
	 * comments fields, this is a two dimentional array
	 * @access public
	 */
	var $fields = array();
	
	/**
	 * The comments fields read and split but not orgainzed
	 * @access public
	 */
	var $_rawfields = array();


	/**
     * Constructor
     *
     * @param  file   the path to the ogg file. When in doubt use a full path.
	 * @access public
     */
	function Ogg( $file )
	{
		$this->file = $file;
    }
	

	/**
	 * Finds the comment in a ogg stream.
	 *
	 * @access public
	 */
	function read()
	{
		if ( !( $f = fopen( $this->file, 'rb' ) ) )
			return PEAR::raiseError( 'Unable to open ' . $file );

		$this->_find_page( $f );
		$this->_find_page( $f );

		fseek( $f, 26 - 4, SEEK_CUR );
		$segs = fread( $f, 1 );
		$segs = unpack( 'C1size', $segs );
		$segs = $segs['size'];

		fseek( $f, $segs, SEEK_CUR );

		// Skip preamble
		// $r = fread( $f, 1 );
		// print_r( unpack( 'H*raw', $r ) );
		fseek( $f, 7, SEEK_CUR );

		// Skip Vendor
		$size = fread( $f, 4 );
		$size = unpack( 'V1size', $size );
		$size = $size['size'];

		fseek( $f, $size, SEEK_CUR );

		// Comments
		$comments = fread( $f, 4 );
		$comments = unpack( 'V1comments', $comments );
		$comments = $comments['comments'];

		for ( $i = 0; $i < $comments; $i++ )
		{
			$size = fread( $f, 4 );
			$size = unpack( 'V1size', $size );
			$size = $size['size'];
	
			$comment = fread( $f, $size );

			$comment = explode( '=', $comment, 2 );
			$this->fields[strtoupper( $comment[0] )][] = $comment[1];
			$this->_rawfields[] = $comment;
		}
    }


	// private methods

	/**
	 * Seeks to the next ogg page start.
	 *
	 * @access private
	 */
	function _find_page( &$f )
	{
		$header = 'OggS'; // 0xf4 . 0x67 . 0x 67 . 0x53
		$bytes  = fread( $f, 4 );

		while ( $header != $bytes )
		{
			$bytes  = substr( $bytes, 1 );
			$bytes .= fread( $f, 1 );
		}
	}
} // END OFF Ogg

?>

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
|         David H. <exaton@free.dot.fr>                                |
+----------------------------------------------------------------------+
*/


using( 'util.text.huffmann.Huffmann' );
using( 'util.text.huffmann.Huffmann_Node' );


/**
 * Usage:
 *
 * $expander = new Huffmann_Expand();
 * $expander->setFiles( "path/to/input/file", "path/to/output/file" );
 * $expander->expand();
 *
 * @package util_text_huffmann
 */
 
class Huffmann_Expand extends Huffmann
{
	/**
	 * Size of the output file, in bytes 
	 *
	 * @access private
	 */	
	var $_ofsize;
	
	/**
	 * For use in Huffman Tree reconstruction 
	 *
	 * @access private
	 */
	var $_ttlnodes;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Huffmann_Expand()
	{
		$this->Huffmann();
	
		// Initializing expansion-specific variables
		$this->_icarrier = "";
		$this->_icarlen  = 0;
	}


	/**
	 * @access public
	 */
	function expand()
	{
		if ( !$this->_havefiles )
			return PEAR::raiseError( "Files not provided." );

		// From header: reading Huffman tree (with no weights, mind you)
		$this->_reconstructTree();
	
		// From header: number of characters to read (ie. size of output file)
		$this->_ofsize = bindec( $this->_bitRead( 24 ) );
	
		// Reading bit-by-bit and generating output
		$this->_readToMakeOutput();
	
		// Writing the output and closing resource handles
		fwrite( $this->_ofhand, $this->_odata );

		fclose( $this->_ofhand );
		fclose( $this->_ifhand );
	}

	
	// private methods
	
	/**
	 * Reconstruct the Huffman tree transmitted in header.
	 *
	 * @access private
	 */
	function _readTPForChild( $par, $child, $childid, $charin )
	{
		// Creating child, setting right parent and right child for parent
		$this->_nodes[$par]->$child = $childid;
	
		$char = ( $charin == $this->_nodeCharC )? "" : $charin;
	
		$this->_nodes[$childid] = new Huffmann_Node( $char, 0, $par );
	
		// Special business if we have a Branch Node
		// Doing all of this for the child!
		if ( $char === "" )
			$this->_readTreePart( $childid );
	}	

	/**
	 * @access private
	 */
	function _readTreePart( $nodenum )
	{
		// Reading from the header, creating a child
		$charin = fgetc( $this->_ifhand );
		$this->_readTPForChild( $nodenum, "_child0", ++$this->_ttlnodes, $charin );
	
		$charin = fgetc( $this->_ifhand );
		$this->_readTPForChild( $nodenum, "_child1", ++$this->_ttlnodes, $charin );
	}

	/**
	 * @access private
	 */
	function _reconstructTree()
	{
		// Creating Root Node. Here root is indexed 0.
		// It's parent is -1, it's children are as yet unknown.
		// NOTE : weights no longer have the slightest importance here
	
		$this->_nodes[0] = new Huffmann_Node( "", 0 );
	
		// Launching the business
		$this->_ttlnodes = 0; // Init value	
		$this->_readTreePart( 0 );
	}

	/**
	 * Reading the compressed data bit-by-bit and generating the output.
	 *
	 * Huffman Compression has unique-prefix property, so as soon as 
	 * we recognise a code, we can assume the corresponding char. 
	 * All adding up, by reading $ofsize chars from the file, we should get
	 * to the end of it !
	 *
	 * @access private
	 */
	function _readUntilLeaf( $curnode )
	{
		if ( $curnode->_char !== "" )
			return $curnode->_char;
	
		if ( $this->_bitRead1() )
			return $this->_readUntilLeaf( $this->_nodes[$curnode->_child1] );
		
		return $this->_readUntilLeaf( $this->_nodes[$curnode->_child0] );
	}

	/**
	 * @access private
	 */
	function _readToMakeOutput()
	{	
		for ( $i = 0; $i < $this->_ofsize; $i++ )
		{
			// We follow the Tree down from Root with the successive bits read
			// We know we have found the character as soon as we hit a leaf Node
			$this->_odata .= $this->_readUntilLeaf( $this->_nodes[0] );
		}
	}
} // END OF Huffmann_Expand

?>

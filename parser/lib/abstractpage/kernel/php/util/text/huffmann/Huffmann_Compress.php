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
 * $compressor = new Huffmann_Compress();
 * $compressor->setFiles( "path/to/input/file", "path/to/output/file" );
 * $compressor->compress();
 * 
 * @package util_text_huffmann
 */

class Huffmann_Compress extends Huffmann
{	
	/**
	 * Size of the input file, in bytes 
	 *
	 * @access private
	 */	
	var $_ifsize;
	
	/**
	 * Array of letter occurrences 
	 *
	 * @access private
	 */
	var $_occ;

	/**
	 * Index of the root of the Huffman tree 
	 *
	 * @access private
	 */
	var $_hroot;
	
	/**
	 * Array of character codes 
	 *
	 * @access private
	 */
	var $_codes;
	
	/**
	 * Array of character code lengths 
	 *
	 * @access private
	 */
	var $_codelens;


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Huffmann_Compress()
	{
		$this->Huffmann();
	
		// Initializing compression-specific variables
		$this->_ocarrier = "";
		$this->_ocarlen  = 0;
	}


	/**
	 * @access public
	 */
	function compress()
	{
		if ( !$this->_havefiles )
			return PEAR::raiseError( "Files not provided." );

		// Counting letter occurrences in input file
		$this->_countOccurrences();
	
		// Converting occurrences into basic nodes
		// The nodes array has been initialized, as it will be filled with dynamic incrementation
		$this->_occurrencesToNodes();
	
		// Construction of the Huffman tree
		$this->_makeHuffmanTree();

		// Constructing character codes
		$this->_makeCharCodes();
	
		// !! No need for 8 bits of nb of chars in alphabet ?? still use $this->nbchars ? NO
		// !! No need for 8+5+codelen bits of chars & codes ?? still use $this->_codelens array ? YES
	
		// Header : passing the Huffman tree with an automatically stopping algorithm
		$this->_transmitTree();

		// End of header : number of chars actually encoded, over 3 bytes
		$this->_bitWrite( $this->_decBinDig( $this->_ifsize, 24 ), 24 );

		// Contents: compressed data
		rewind( $this->_ifhand );
	
		while ( ( $char = fgetc( $this->_ifhand ) ) !== false )
			$this->_bitWrite( $this->_codes[$char], $this->_codelens[$char] );

		// Finalising output, closing file handles
		$this->_bitWriteEnd();
	
		fclose( $this->_ofhand );
		fclose( $this->_ifhand );
	}

	/**
	 * setFiles() is called to specify the paths to the input and output files.
	 * It calls a parent function for its role, then sets some compression-
	 * specific variables concerning files.
	 *
	 * @access public
	 */
	function setFiles( $ifile = "", $ofile = "" )
	{
		// Calling the parent function for this role
		parent::setFiles( $ifile, $ofile );
	
		// Setting compression-specific variables concerning files
		$this->_ifsize = filesize( $this->_ifile );
	}
	
	/**
	 * Show info on characters codes created from the Huffman tree.
	 *
	 * @access public
	 */
	function getSCodes()
	{	
		// Sorting codes
		arsort( $this->_occ );
	
		// Preparing informative $scodes array
		foreach ( $this->_occ as $char => $nbocc )
		{
			$tmp = "";
	
			if ( ord( $char ) >= 32 )
			{
				$schar = $char;
			}
			else
			{
				$schar = "�";
				$tmp   = " (ASCII : " . ord( $char ) . ")";
			}
		
			$nboccprefix = "";
			
			for ( $i = 0; $i < 6 - strlen( $nbocc ); $i++ )
				$nboccprefix .= "0";
			
			$occpercent = round( $nbocc / $this->_ifsize * 100, 2 );
			$scodes[$schar] = "(" . $nboccprefix . $nbocc." occurences, or " . $occpercent . "%) " . $this->_codes[$char] . " (code on " . $this->_codelens[$char] . " bits)" . $tmp;
		}

		return $scodes;
	}

	/**
	 * @access public
	 */
	function getCompressionRatio()
	{
		// Simulating output file size
		$csize = 0;
	
		foreach ( $this->_occ as $char => $nbocc )
			$csize += $nbocc * $this->_codelens[$char];
	
		$nbchars = count( $this->_occ );

		$csize += 16 * ( $nbchars - 1 ); // For Huffman tree in header
		$csize += 24; // For nb. chars to read
	
		$csize  = ceil( $csize / 8 );
		$cratio = round( $csize / $this->_ifsize * 100, 2 );
	
		return $cratio;
	}
	
	
	// private methods
	
	/**
	 * Count character occurrences in the file, to identify information
	 * quantities and later construct the Huffman tree.
	 *
	 * @access private
	 */
	function _countOccurrences()
	{
		while ( ( $char = fgetc( $this->_ifhand ) ) !== false )
		{	
			if ( !isset( $this->_occ[$char] ) )
				$this->_occ[$char] = 1;
			else
				$this->_occ[$char]++;
		}
	}

	/**
	 * Convert the character occurrences to basic Nodes of according weight.
	 *
	 * @access private
	 */
	function _occurrencesToNodes()
	{
		foreach ( $this->_occ as $char => $nboccs )
			$this->_nodes[] = new Huffmann_Node( $char, $nboccs );
	}

	/**
	 * Get the index of the first node of lightest weight in the nodes array.
	 *
	 * @access private
	 */
	function _findLightestNode()
	{
		$minw_nodenum = -1;
		$minw = -1;
	
		foreach ( $this->_nodes as $nodenum => $node )
		{
			if ( !$node->_lndone && ( $minw == -1 || $node->_w < $minw ) )
			{
				$minw = $node->_w;
				$minw_nodenum = $nodenum;
			}
		}
	
		return $minw_nodenum;
	}

	/**
	 * Create the Huffman tree, after the following algorithm :
	 * - Find the two nodes of least weight (least info value)
	 * - Set each one's parent to the index a new node which has a weight equal to the sum of weights of the two
	 * - At the same time, specify the new nodes children as being the two lightest nodes
	 * - Eliminate the two lightest nodes from further searches for lightest nodes
	 *
	 * This carries on until there is only one node difference between nodes
	 * constructed and nodes done : the root of the tree.
	 *
	 * By following the tree from root down to leaf, by successive children 0 or
	 * 1, we can thereafter establish the code for the character.
	 *
	 * @access private
	 */
	function _makeHuffmanTree()
	{
		$nbnodes     = count( $this->_nodes );
		$nbnodesdone = 0;
	
		while ( $nbnodesdone < $nbnodes - 1 )
		{
			// Find two lightest nodes and consider them done
			for ( $i = 0; $i < 2; $i++ )
			{
				$ln[$i] = $this->_findLightestNode();
				$this->_nodes[$ln[$i]]->_lndone = true;
			}
		
			$nbnodesdone += 2;
		
			// Link them with a parent node of sum weight
			// (whose parent is as yet unknown ; in the case of root, it will stay with -1)
			$this->_nodes[] = new Huffmann_Node( "", $this->_nodes[$ln[0]]->_w + $this->_nodes[$ln[1]]->_w, -1, $ln[0], $ln[1] );
			
			$this->_nodes[$ln[0]]->_par = $nbnodes; // The number of nodes before incrementation is the index
			$this->_nodes[$ln[1]]->_par = $nbnodes; // of the node which has just been created
		
			$nbnodes++;
		}
	
		// Note that the last node is the root of the tree
		$this->_hroot = $nbnodes - 1;
	}

	/**
	 * Read the Huffman tree to determine character codes.
	 *
	 * @access private
	 */
	function _makeCharCodes()
	{
		// Note : original alphabet is the keys of $occ
		$i = 0;
	
		foreach ( $this->_occ as $char => $nbocc )
		{
			$code = "";
			$codelen = 0;
		
			// Following tree back up to root
			// (therefore _pre_positionning each new bit in the code)
			// $this->nodes[$i] is the original Node of $char
			$curnode = $i;
		
			do
			{
				$parnode = $this->_nodes[$curnode]->_par;
				$code    = ( ( $this->_nodes[$parnode]->_child0 == $curnode )? "0" : "1" ) . $code;
				$codelen++;
				$curnode = $parnode;
			} while ( $curnode != $this->_hroot );
		
			$this->_codes[$char]    = $code;
			$this->_codelens[$char] = $codelen;
		
			$i++;
		}
	}

	/**
	 * Transmit the Huffman tree in header.
	 *
	 * @access private
	 */
	function _transmitTreePart( $nodenum, $isroot )
	{
		// Transmitting current node representation, if we are not working with root (that's only the first time).
		// Then looking at children if appropriate (gee that sounds bad).
		$curnode = $this->_nodes[$nodenum];
		$char    = $curnode->_char;
			
		if ( $char === "" )
		{
			// Branch Node
			// Being root can only be in this case
		
			if ( !$isroot )
				$this->_bitWrite( $this->_nodeChar, 8 );
		
			// Looking at children
			$this->_transmitTreePart( $curnode->_child0, false );
			$this->_transmitTreePart( $curnode->_child1, false );
		}
		else
		{
			// Leaf Node
			// Just transmitting the char
			$this->_bitWrite( $this->_decBinDig( ord( $char ), 8), 8 );
		}
	}

	/**
	 * @access private
	 */
	function _transmitTree()
	{
		// Launching the business, specifying that we are starting at root	
		$this->_transmitTreePart( $this->_hroot, true );
	}
} // END OF Huffmann_Compress

?>

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


using( 'xml.dom.lib.Node' );

 
/**
 * The CharacterData class extends the Node class with a set of methods to access the character data of the node.
 * The new method in the DOM/XML library "set_content" is the basis for this class tree!
 *
 * @package xml_dom_lib
 */
 
class CharacterData extends Node
{
	/** 
	 * Contains the data for the text section
	 * @var string $data
	 * @access public
	 */
	var $data = "";
	
	
	/** 
	 * Constructor
	 *
	 * @access public
	 */
	function CharacterData()
	{
		$this->Node( "", false );
	}
	
	/** 
	 * setData sets the text portion of the text element.
	 *
	 * @param		string		$data
	 * @access		public
	 */	
	function setData( $data )
	{
		if ( $this->node )
			$this->node->set_content( $data );
		
		$this->nodevalue = $data;
	}
	
	/** 
	 * getText returns the text.
	 *
	 * @return		string		$data
	 * @access		public
	 */
	function getData()
	{
		if ( $this->node )
			return $this->node->content;
		
		return $this->nodevalue;
	}
	
	/**
	 * Append the string to the end of the character data of the node.
	 *
	 * The given string will be appended. 
	 *
	 * @param		string		The string to append
	 * @access		public
	 */
	function appendData( $strappend )
	{
		if ( $this->node )
			$this->node->set_content( $this->node->content . $strappend );
		else
			$this->nodevalue .= $strappend;
	}
	
	/**
	 * remove a range of characters from the node.
	 *
	 * @param		int			The offset from which to start removing
	 * @param		int			The number of characters to remove
	 * @access		public
	 */
	function deleteData( $offset, $count )
	{
		if ( $this->node )
		{
			$newval = substr_replace( $this->node->content, "", $offset, $count );
			$this->node->set_content( $newval );
		}
		else
		{
			$this->nodevalue = substr_replace( $this->nodevalue, "", $offset, $count );
		}
	}
	
	/**
	 * Replace a range of characters from the node with a given string.
	 *
	 * @param		int			The offset from which to start removing
	 * @param		int			The number of characters to remove
	 * @param		string		The replacement string
	 * @access		public
	 */
	function replaceData( $offset, $count, $replacement )
	{
		if ( $this->node )
		{
			$newval = substr_replace( $this->node->content, $replacement, $offset, $count );
			$this->node->set_content( $newval );
		}
		else
		{
			$this->nodevalue = substr_replace( $this->nodevalue, $replacement, $offset, $count );
		}
	}
	
	/**
	 * Insert a string portion at a given offset.
	 *
	 * @param		int			The offset from which to start removing
	 * @param		string		The string to insert
	 * @access		public
	 */
	function insertData( $offset, $arg )
	{
		if ( $this->node )
		{
			$newval = substr_replace( $this->node->content, $arg, $offset, 0 );
			$this->node->set_content( $newval );
		}
		else
		{
			$this->nodevalue = substr_replace( $this->nodevalue, $arg, $offset, 0 );
		}
	}
	
	/**
	 * Retrieves a text portion of the current string, starting at a given offset.
	 *
	 * @param		int			The offset from which to start removing
	 * @param		int			The number of characters to return
	 * @return		string		The substring
	 * @access		public
	 */
	function substringData( $offset, $count )
	{
		if ( $this->node )
			return substr( $this->node->content, $offset, $count );
		else
			return substr( $this->nodevalue, $offset, $count );
	}
	
	/** 
	 * Retrieves the length of the current node value.
	 *
	 * @return		int			The length of the string
	 * @access		public
	 * @access		public
	 */
	function getLength()
	{
		if ( $this->node )
			$this->nodevalue = $this->node->content;
		
		return strlen( $this->nodevalue );
	}
} // END OF CharacterData

?>

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


using( 'xml.minixml.MiniXMLElement' );


/**
 * The MiniXMLElementEntity class is a specific extension of the MiniXMLElement class.
 *
 * It is used to create the special <!ENTITY name "val">  tags and an instance in created when calling
 * $elementObject->comment('');
 *
 * It's methods are the same as for MiniXMLElement - see those for documentation.
 *
 * @package xml_minixml
 */

class MiniXMLElementEntity extends MiniXMLElement 
{
	/**
	 * Constructor
	 *
	 * @access public
	 */	
	function MiniXMLElementEntity( $name, $value = null )
	{
		$this->MiniXMLElement( $name );
		
		if ( !is_null( $value ) )
			$this->createNode( $value, 0 );
	}
	

	/**
	 * @access public
	 */	
	function toString( $depth = 0 )
	{
		$spaces = '';
		
		if ( $depth != MINIXML_NOWHITESPACES )
			$spaces = $this->_spaceStr( $depth );
		
		$retString = "$spaces<!ENTITY " . $this->name();
		
		if ( !$this->xnumChildren )
		{
			$retString .= ">\n";
			return $retString;
		}
		
		$nextDepth  = ( $depth == MINIXML_NOWHITESPACES )? MINIXML_NOWHITESPACES : $depth + 1;
		$retString .= '"';
		
		for ( $i=0; $i < $this->xnumChildren; $i++ )
			$retString .= $this->xchildren[$i]->toString( MINIXML_NOWHITESPACES );
		
		$retString .= '"';
		$retString .= " >\n";
		
		return $retString;
	}

	/**
	 * @access public
	 */	
	function toStringNoWhiteSpaces()
	{
		return $this->toString( MINIXML_NOWHITESPACES );
	}

	/**
	 * @access public
	 */	
	function toStringWithWhiteSpaces( $depth = 0 )
	{
		return $this->toString( $depth );
	}
} // END OF MiniXMLElementEntity

?>

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
 * The MiniXMLElementCData class is a specific extension of the MiniXMLElement class.
 *
 * It is used to create the special <![CDATA [ data ]]> tags and an instance in created when calling
 * $elementObject->cdata('data');
 *
 * It's methods are the same as for MiniXMLElement - see those for documentation.
 *
 * @package xml_minixml
 */

class MiniXMLElementCData extends MiniXMLElement 
{	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function MiniXMLElementCData( $contents )
	{
		$this->MiniXMLElement( 'CDATA' );
		
		if ( !is_null( $contents ) )
			$this->createNode( $contents, 0 );
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

	/**
	 * @access public
	 */	
	function toString( $depth = 0 )
	{
		$spaces = '';
		
		if ( $depth != MINIXML_NOWHITESPACES )
			$spaces = $this->_spaceStr( $depth );
		
		$retString = "$spaces<![CDATA[ ";
		
		if ( !$this->xnumChildren )
		{
			$retString .= "]]>\n";
			return $retString;
		}
		
		for ( $i = 0; $i < $this->xnumChildren; $i++ )
			$retString .= $this->xchildren[$i]->getValue();
		
		$retString .= " ]]>\n";
		return $retString;
	}
} // END OF MiniXMLElementCData

?>

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
 * The MiniXMLElementComment class is a specific extension of the MiniXMLElement class.
 *
 * It is used to create the special <!-- comment --> tags and an instance in created when calling
 * $elementObject->comment('this is a comment');
 *
 * It's methods are the same as for MiniXMLElement - see those for documentation.
 *
 * @package xml_minixml
 */

class MiniXMLElementComment extends MiniXMLElement 
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function MiniXMLElementComment( $name = null )
	{
		$this->MiniXMLElement( '!--' );
	}
	

	/**
	 * @access public
	 */
	function toString( $depth = 0 )
	{
		if ( $depth == MINIXML_NOWHITESPACES )
			return $this->toStringNoWhiteSpaces();
		else 
			return $this->toStringWithWhiteSpaces( $depth );
	}

	/**
	 * @access public
	 */
	function toStringWithWhiteSpaces( $depth = 0 )
	{
		$spaces    = $this->_spaceStr( $depth );
		$retString = "$spaces<!-- \n";
		
		if ( !$this->xnumChildren )
		{
			// No kids, no text - consider a <unary/> element.
			$retString .= " -->\n";
			
			return $retString;
		}
		
		// If we get here, the element does have children... get their contents.
		$nextDepth = $depth + 1;
		
		for ( $i = 0; $i < $this->xnumChildren ; $i++ )
			$retString .= $this->xchildren[$i]->toStringWithWhiteSpaces( $nextDepth );
		
		$retString .= "\n$spaces -->\n";
		return $retString;
	}
	
	/**
	 * @access public
	 */
	function toStringNoWhiteSpaces()
	{
		$retString = '';
		$retString = "<!-- ";
		
		if ( !$this->xnumChildren )
		{
			// No kids, no text - consider a <unary/> element.
			$retString .= " -->";
			return $retString;
		}
		
		// If we get here, the element does have children... get their contents.
		for ( $i = 0; $i < $this->xnumChildren ; $i++ )
			$retString .= $this->xchildren[$i]->toStringNoWhiteSpaces();
		
		$retString .= " -->";
		return $retString;
	}
} // END OF MiniXMLElementComment

?>

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
 * The MiniXMLElementDocType class is a specific extension of the MiniXMLElement class.
 *
 * It is used to create the special <!DOCTYPE def [...]> tags and an instance in created when calling
 * $elementObject->comment('');
 *
 * It's methods are the same as for MiniXMLElement - see those for documentation.
 *
 * @package xml_minixml
 */

class MiniXMLElementDocType extends MiniXMLElement 
{
	/**
	 * @access public
	 */
	var $dtattr;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function MiniXMLElementDocType( $attr )
	{
		$this->MiniXMLElement('DOCTYPE');

		$this->dtattr = $attr;
	}
	

	/**
	 * @access public
	 */	
	function toString( $depth )
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
		$retString = "$spaces<!DOCTYPE " . $this->dtattr . " [\n";
		
		if ( !$this->xnumChildren )
		{
			$retString .= "]>\n";
			return $retString;
		}
		
		$nextDepth = $depth + 1;
		
		for ( $i=0; $i < $this->xnumChildren; $i++ )
			$retString .= $this->xchildren[$i]->toStringWithWhiteSpaces( $nextDepth );
		
		$retString .= "\n$spaces]>\n";
		return $retString;
	}

	/**
	 * @access public
	 */
	function toStringNoWhiteSpaces()
	{
		$retString = "<!DOCTYPE " . $this->dtattr . " [ ";
		
		if ( !$this->xnumChildren )
		{
			$retString .= "]>\n";
			return $retString;
		}
		
		for ( $i=0; $i < $this->xnumChildren; $i++ )
			$retString .= $this->xchildren[$i]->toStringNoWhiteSpaces();
		
		$retString .= " ]>\n";
		return $retString;
	}
} // END OF MiniXMLElementDocType

?>

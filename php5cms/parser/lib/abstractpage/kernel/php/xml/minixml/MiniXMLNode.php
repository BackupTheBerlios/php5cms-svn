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


using( 'xml.minixml.MiniXMLTreeComponent' );


/**
 * MiniXMLNodes are used as atomic containers for numerical and text data
 * and act as leaves in the XML tree.
 *
 * They have no name or children.
 *
 * They always exist as children of MiniXMLElements.
 * For example, 
 * <B>this text is bold</B>
 * Would be represented as a MiniXMLElement named 'B' with a single
 * child, a MiniXMLNode object which contains the string 'this text 
 * is bold'.
 *
 * a MiniXMLNode has
 * - a parent
 * - data (text OR numeric)
 *
 * @package xml_minixml
 */

class MiniXMLNode extends MiniXMLTreeComponent 
{
	/**
	 * @access public
	 */
	var $xtext;
	
	/**
	 * @access public
	 */
	var $xnumeric;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function MiniXMLNode( $value = null, $escapeEntities = null )
	{
		$this->MiniXMLTreeComponent();
		
		$this->xtext    = null;
		$this->xnumeric = null;
		
		// If we were passed a value, save it as the appropriate type.
		if ( !is_null( $value ) )
		{
			if ( is_numeric( $value ) )
			{
				$this->xnumeric = $value;
			} 
			else 
			{
				if ( MINIXML_IGNOREWHITESPACES > 0 )
				{
					$value = trim( $value );
					$value = rtrim( $value );
				}
				
				if ( !is_null( $escapeEntities ) )
				{
					if ( $escapeEntities )
						$value = htmlentities( $value );
				} 
				else if ( MINIXML_AUTOESCAPE_ENTITIES > 0 ) 
				{
					$value = htmlentities( $value );
				} 

				$this->xtext = $value;	
			}
		}			
	}
		
	/**
	 * Returns the text or numeric value of this Node.
	 *
	 * @access public
	 */
	function getValue()
	{
		$retStr = null;
		
		if  ( !is_null( $this->xtext ) )
			$retStr = $this->xtext;
		else if ( !is_null( $this->xnumeric ) )
			$retStr = "$this->xnumeric";

		return $retStr;
	}
	
	
	/**
	 * The text() method is used to get or set text data for this node.
	 *
	 * If SETTO is passed, the node's content is set to the SETTO string.
	 *
	 * If the optional SETTOALT is passed and SETTO is false, the 
	 * node's value is set to SETTOALT.  
	 *
	 * Returns this node's text, if set or null 
	 *
	 * @access public
	 */
	function text( $setToPrimary = null, $setToAlternate=null )
	{
		$setTo = ( $setToPrimary? $setToPrimary : $setToAlternate );
		
		if ( !is_null( $setTo ) )
		{
			if ( !is_null( $this->xnumeric ) )
				return null;
			else if ( !is_string( $setTo ) && !is_numeric( $setTo ) )	
				return null;
			
			if ( MINIXML_IGNOREWHITESPACES > 0 )
			{
				$setTo = trim( $setTo );
				$setTo = rtrim( $setTo );
			}
			
			if ( MINIXML_AUTOESCAPE_ENTITIES > 0 )
				$setTo = htmlentities( $setTo );

			$this->xtext = $setTo;
		}
		
		return $this->xtext;
	}
	
	/**
	 * The numeric() method is used to get or set numerical data for this node.
	 *
	 * If SETTO is passed, the node's content is set to the SETTO string.
	 *
	 * If the optional SETTOALT is passed and SETTO is null, the 
	 * node's value is set to SETTOALT.  
	 *
	 * Returns this node's text, if set or null
	 *
	 * @access public
	 */
	function numeric( $setToPrim = null, $setToAlt = null )
	{
		$setTo = is_null( $setToPrim )? $setToAlt : $setToPrim;
		
		if ( !is_null( $setTo ) )
		{
			if ( !is_null( $this->xtext ) ) 
				return null;
			else if ( !is_numeric( $setTo ) )
				return null;

			$this->xnumeric = $setTo;
		}
		
		return $this->xnumeric;
	}

	/**
	 * Returns this node's contents as a string.
	 *
	 * Note: Nodes have only a single value, no children.  It is 
	 * therefore pointless to use the same toString() method split as 
	 * in the MiniXMLElement class.
	 *
	 * @access public
	 */	
	function toString( $depth = 0 )
	{
		if ( $depth == MINIXML_NOWHITESPACES )
			return $this->toStringNoWhiteSpaces();

		$spaces = $this->_spaceStr( $depth );
		$retStr = $spaces;
		
		if ( !is_null( $this->xtext ) )
		{
			// a text element
			$retStr .= $this->xtext;
		} 
		else if ( !is_null( $this->xnumeric ) ) 
		{
			// a numeric element
			$retStr .=  $this->xnumeric;
		} 
		
		// indent all parts of the string correctly
		$retStr = preg_replace( "/\n\s*/sm", "\n$spaces", $retStr );
		
		return $retStr;
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
	function toStringNoWhiteSpaces()
	{
		if ( !is_null( $this->xtext ) )
		{
			// a text element
			$retStr = $this->xtext;
		} 
		else if ( !is_null( $this->xnumeric ) ) 
		{
			// a numeric element
			$retStr =  $this->xnumeric;
		}
		
		return $retStr;
	}	
} // END OF MiniXMLNode

?>

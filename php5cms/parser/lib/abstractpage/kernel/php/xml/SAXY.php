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
|Authors: John Heinstein <jheinstein@engageinteractive.com>            |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


define( "SAXY_STATE_NONE",       0 );
define( "SAXY_STATE_PARSING",    1 );
define( "SAXY_STATE_ATTR_NONE",  0 );
define( "SAXY_STATE_ATTR_KEY",   1 );
define( "SAXY_STATE_ATTR_VALUE", 2 );
	
define( "SAXY_SEARCH_CDATA",     "![CDATA["  );
define( "SAXY_SEARCH_NOTATION",  "!NOTATION" );
define( "SAXY_SEARCH_DOCTYPE",   "!DOCTYPE"  );


/**
 * SAXY is a non-validating, but lightweight 
 * and fast SAX parser for PHP.
 *
 * The primary goal of SAXY is to provide 
 * PHP developers with an alternative to Expat 
 * that is written purely in PHP.
 *
 * Since SAXY is not an extension, it should 
 * run on any Web hosting platform with PHP 4 
 * and above installed.
 *
 * @package xml
 */

class SAXY extends PEAR
{
	/**
	 * @access public
	 */
	var $state;
	
	/**
	 * @access public
	 */
	var $charContainer;
	
	/**
	 * @access public
	 */
	var $startElementHandler;
	
	/**
	 * @access public
	 */
	var $endElementHandler;
	
	/**
	 * @access public
	 */
	var $characterDataHandler;
		
		
	/**
	 * Constructor
	 *
	 * @access public
	 */	
	function SAXY()
	{
		$this->charContainer = "";
		$this->state = SAXY_STATE_NONE;
	}
	
	
	/**
	 * @access public
	 */
	function xml_set_element_handler( $startHandler, $endHandler ) 
	{
		$this->startElementHandler = $startHandler;
		$this->endElementHandler   = $endHandler;
	}
		
	/**
	 * @access public
	 */
	function xml_set_character_data_handler( $handler )
	{
		$this->characterDataHandler =& $handler;
	}
		
	/**
	 * @access public
	 */
	function preprocessXML( $xmlText ) 
	{
		// strip prolog
		$xmlText   = trim( $xmlText );
		$startChar = -1;
		$total     = strlen( $xmlText );
			
		for ( $i = 0; $i < $total; $i++ ) 
		{
			$currentChar = substr( $xmlText, $i, 1 );
			$nextChar    = substr( $xmlText, ( $i + 1 ), 1 );
				
			if ( ( $currentChar == "<" ) && ( $nextChar != "?" )  && ( $nextChar != "!" ) ) 
			{
				$startChar  = $i; 
				break;
			}
		}
			
		return ( substr( $xmlText, $startChar ) );
	}
		
	/**
	 * @access public
	 */
	function parse( $xmlText ) 
	{
		$xmlText = $this->preprocessXML( $xmlText );
		$total   = strlen( $xmlText );
	
		for ( $i = 0; $i < $total; $i++ ) 
		{
			$currentChar = substr( $xmlText, $i, 1 );
				
			switch ( $this->state ) 
			{
				case SAXY_STATE_NONE:
					switch ( $currentChar ) 
					{
						case "<":
							$this->state = SAXY_STATE_PARSING;
							break;
					}	
						
					break;
						
				case SAXY_STATE_PARSING:
					switch ( $currentChar ) 
					{
						case "<":
							if ( substr( $this->charContainer, 0, strlen( SAXY_SEARCH_CDATA ) ) == SAXY_SEARCH_CDATA ) 
							{
								$this->charContainer .= $currentChar;
							}
							else 
							{
								$this->parseBetweenTags( $this->charContainer );
								$this->charContainer = "";
							}
						
							break;
								
						case ">":
							if ( ( substr( $this->charContainer, 0, strlen( SAXY_SEARCH_CDATA ) ) == SAXY_SEARCH_CDATA ) &&
								 ( $this->getCharFromEnd( $this->charContainer, 0 ) != "]" ) && 
								 ( $this->getCharFromEnd( $this->charContainer, 1 ) != "]" ) ) 
							{
								$this->charContainer .= $currentChar;
							}
							else 
							{
								$this->parseTag( $this->charContainer );
								$this->charContainer = "";
							}
							
							break;
								
						default:
							$this->charContainer .= $currentChar;
					}
						
					break;
			}
		}	

		return true;
	}

	/**
	 * @access public
	 */
	function getCharFromEnd( $text, $index ) 
	{
		$len  = strlen( $text );
		$char = substr( $text, ( $len - 1 - $index ), 1 );
			
		return $char;
	}
		
	/**
	 * @access public
	 */
	function parseTag( $tagText ) 
	{
		$tagText   = trim( $tagText );
		$firstChar = substr( $tagText, 0, 1 );
		$myAttributes = "";
		
		if ( $firstChar == "/" ) 
		{
			$tagName = substr( $tagText, 1 );
			$this->fireEndElementEvent( $tagName );
		}
		else if ( $firstChar  == "!" ) 
		{
			$upperCaseTagText = strtoupper( $tagText );
				
		 	// CDATA Section
			if ( strpos( $upperCaseTagText, SAXY_SEARCH_CDATA ) !== false ) 
			{
				$total = strlen( $tagText );
				$openBraceCount = 0;
				$textNodeText   = "";
					
				for ( $i = 0; $i < $total; $i++ ) 
				{
					$currentChar = substr( $tagText, $i, 1 );
						
					if ( $currentChar == "]" )
						break;
					else if ( $openBraceCount > 1 )
						$textNodeText .= $currentChar;
					else if ( $currentChar == "[" )
						$openBraceCount ++;
				}
					
				$this->fireCharacterDataEvent( $textNodeText );
			}
			// NOTATION node, discard
			else if ( strpos( $upperCaseTagText, SAXY_SEARCH_NOTATION ) !== false ) 
			{
				return;
			}
			// COMMENT node, discard
			else if ( substr( $tagText, 0, 2 ) == "--" ) 
			{
				return;
			}				
		}
		// Processing Instruction node, discard
		else if ( $firstChar == "?" ) 
		{
			return;
		}
		else 
		{
			if ( ( strpos( $tagText, "\"" ) !== false ) || ( strpos( $tagText, "\'" ) !== false ) ) 
			{
				$total   = strlen( $tagText );
				$tagName = "";

				for ( $i = 0; $i < $total; $i++ ) 
				{
					$currentChar = substr( $tagText, $i, 1 );
						
					if ( $currentChar == " " ) 
					{
						$myAttributes = $this->parseAttributes( substr( $tagText, $i ) );
						break;
					}
					else 
					{
						$tagName.= $currentChar;
					}
				}

				// check $tagText, but send $tagName
				if ( strrpos( $tagText, "/" ) == ( strlen( $tagText ) - 1 ) ) 
				{
					$this->fireStartElementEvent( $tagName, $myAttributes );
					$this->fireEndElementEvent( $tagName );
				}
				else 
				{
					$this->fireStartElementEvent( $tagName, $myAttributes );
				}
			}
			else 
			{
				if ( strpos( $tagText, "/" ) !== false ) 
				{
					$tagText = trim( substr( $tagText, 0, ( strrchr( $tagText, "/" ) - 1 ) ) );
					$this->fireStartElementEvent( $tagText, $myAttributes );
					$this->fireEndElementEvent( $tagText );
				}
				else 
				{
					$this->fireStartElementEvent( $tagText, $myAttributes );
				}
			}
		}
	}
		
	/**
	 * @access public
	 */
	function parseAttributes( $attrText ) 
	{
		$attrText     = trim( $attrText );	
		$attrArray    = array();
		$total        = strlen( $attrText );
		$keyDump      = "";
		$valueDump    = "";
		$currentState = SAXY_STATE_ATTR_NONE;
		$quoteType    = "";
			
		for ( $i = 0; $i < $total; $i++ ) 
		{
			$currentChar = substr( $attrText, $i, 1 );
				
			if ( $currentState == SAXY_STATE_ATTR_NONE ) 
			{
				if ( trim( $currentChar != "" ) )
					$currentState = SAXY_STATE_ATTR_KEY;
			}
				
			switch ( $currentChar ) 
			{
				case "\t":
					$currentChar = "";
				
				case "\n":
					$currentChar = "";
						
				case "=";
					$currentState = SAXY_STATE_ATTR_VALUE;
					$quoteType = "";
					
					break;
						
				case "\"":
					if ( $currentState == SAXY_STATE_ATTR_VALUE ) 
					{
						if ( $quoteType == "" ) 
						{
							$quoteType = "\"";
						}
						else
						{
							if ( $quoteType == $currentChar ) 
							{
								$attrArray[trim($keyDump)] = trim( $valueDump );
								$keyDump      = "";
								$valueDump    = "";
								$quoteType    = "";
								$currentState = SAXY_STATE_ATTR_NONE;
							}
							else 
							{
								$valueDump .= $currentChar;
							}
						}
					}
					
					break;
						
				case "\'":
					if ( $currentState == SAXY_STATE_ATTR_VALUE ) 
					{
						if ( $quoteType == "" ) 
						{
							$quoteType = "\'";
						}
						else 
						{
							if ( $quoteType == $currentChar ) 
							{
								$attrArray[$keyDump] = $valueDump;
								$keyDump      = "";
								$valueDump    = "";
								$quoteType    = "";
								$currentState = SAXY_STATE_ATTR_NONE;
							}
							else 
							{
								$valueDump .= $currentChar;
							}
						}
					}
					
					break;
						
				default:
					if ( $currentState == SAXY_STATE_ATTR_KEY )
						$keyDump   .= $currentChar;
					else
						$valueDump .= $currentChar;
			}
		}

		return $attrArray;
	}
		
	/**
	 * @access public
	 */
	function parseBetweenTags( $betweenTagText ) 
	{
		if ( trim( $betweenTagText ) != "" )
			$this->fireCharacterDataEvent( $betweenTagText );
	}

	/**
	 * @access public
	 */
	function fireStartElementEvent( $tagName, $attributes ) 
	{
		call_user_func( $this->startElementHandler, $this, $tagName, $attributes );
	}
		
	/**
	 * @access public
	 */
	function fireEndElementEvent( $tagName ) 
	{
		call_user_func( $this->endElementHandler, $this, $tagName );
	}
		
	/**
	 * @access public
	 */
	function fireCharacterDataEvent( $data ) 
	{
		call_user_func( $this->characterDataHandler, $this, $data );
	}
} // END OF SAXY

?>

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


using( 'xml.dom.simple.lib.SimpleDomComment' );


/**
 * @package xml_dom_simple_lib
 */
 
class SimpleDomElement extends PEAR
{
	/**
	 * @access public
	 */
	var $attributes;
	
	/**
	 * @access public
	 */
	var $comment;
	
	/**
	 * @access public
	 */
	var $element = array();   

	/**
	 * @access public
	 */
	var $parent = '';
	
	/**
	 * @access public
	 */
	var $name = '';
	
	/**
	 * @access public
	 */
	var $value = '';

	/**
	 * @access public
	 */
	var $hasChild = false;
	
	/**
	 * @access public
	 */
	var $child = array();


	/**
	 * Constructor
	 *
	 * @access public
	 */
  	function SimpleDomElement() 
	{
         $GLOBALS["AP_DOM_ERRORS"] = 0;

         $arga = func_num_args();
         $args = func_get_args();

         for( $i = 0; $i < $arga; $i++ ) 
		 {
            if ( $i < 2 ) 
			{
				if ( is_string( $args[$i] ) ) 
				{
					if ( $i == 0 )
						$this->name   = $args[$i];
					else
						$this->parent = $args[$i];
  				} 
				else 
				{
					$GLOBALS["AP_DOM_ERRORS"] = 1; // wrong data type
					break;
				} 
			} 
			else 
			{
				if ( is_object( $args[$i] ) ) 
				{
					if ( get_class( $args[$i] ) == "domattribute" ) 
					{
						$this->attributes = $args[$i];
						break;
					} 
					else 
					{
						$GLOBALS["AP_DOM_ERRORS"] = 1; // wrong data type
						break;
					}
				} 
				else 
				{
					$GLOBALS["AP_DOM_ERRORS"] = 1; // wrong data type
					break;
				} 
			} 
		} 
	}  
	
	
	/**
	 * @access public
	 */
	function addElement( $element ) 
	{
		$GLOBALS["AP_DOM_ERRORS"] = 0;
		$result = false;

		if ( is_object( $element ) ) 
		{
			if ( get_class( $element ) == "domelement" ) 
			{
				array_push( $this->element, $element );
				array_push( $this->child,   $element->name );

				$this->hasChild = true;
				$result = true;
			} 
			else 
			{
				$GLOBALS["AP_DOM_ERRORS"] = 1; // invalid datatype
			} 
		} 
		else 
		{
			$GLOBALS["AP_DOM_ERRORS"] = 1; // invalid datatype
		} 

		return $result;
	} 

	/**
	 * @access public
	 */  
  	function setParent( $parent ) 
	{
		$GLOBALS["AP_DOM_ERRORS"] = 0;
		$result = false;

		if ( !empty( $parent ) ) 
		{
			if ( is_string( $parent ) ) 
			{
				$this->parent = $parent;
				$result = true;
			} 
			else 
			{
				$GLOBALS["AP_DOM_ERRORS"] = 1; // wrong data type
			}
		} 
		else 
		{
			$GLOBALS["AP_DOM_ERRORS"] = 5; // empty param
		} 
		
		return $result;
	} 
      
	/**
	 * @access public
	 */
	function setName( $name ) 
	{
		$GLOBALS["AP_DOM_ERRORS"] = 0;
		$result = false;

		if ( !empty( $name ) ) 
		{
			if ( is_string( $name ) ) 
			{
				$this->name = $name;
				$result     = true;
			} 
			else 
			{
				$GLOBALS["AP_DOM_ERRORS"] = 1; // wrong data type
            }  
		} 
		else 
		{
			$GLOBALS["AP_DOM_ERRORS"] = 5; // empty param
		}
		
		return $result;
	} 

	/**
	 * @access public
	 */	
	function setValue( $value ) 
	{
		$this->value = $value;
	} 

	/**
	 * @access public
	 */	
	function setAttributes( $attributes ) 
	{
		$GLOBALS["AP_DOM_ERRORS"] = 0;
		$result = false;

		if ( is_object( $attributes ) ) 
		{
			if ( get_class( $attributes ) == "domattribute" ) 
			{
				$this->attributes = $attributes;
				$result = true;
			} 
			else 
			{
				$GLOBALS["AP_DOM_ERRORS"] = 1; // wrong data type
			} 
		} 
		else 
		{
			$GLOBALS["AP_DOM_ERRORS"] = 1; // wrong data type
		}
		
		return result;
	} 
	
	/**
	 * @access public
	 */
	function addComment( $comment ) 
	{
		if ( empty( $this->comment ) )
			$this->comment = new SimpleDomComment();

		$this->comment->addComment( $comment );
	}

	/**
	 * @access public
	 */	
	function getElement( $element ) 
	{
		$GLOBALS["AP_DOM_ERRORS"] = 0;

		if ( is_long( $element ) ) 
		{
			if ( $element < count( $this->element ) ) 
			{
				if ( $element > -1 ) 
				{
					$result = $this->element[$element];
				} 
				else 
				{
					$GLOBALS["AP_DOM_ERRORS"] = 4; // index out of bounds
					$result = false;
				} 
			} 
			else 
			{
				$GLOBALS["AP_DOM_ERRORS"] = 4; // index out of bounds
				$result = false;
			}  
		}  
		else if ( is_string( $element ) ) 
		{
			$memory = array_flip( $this->child );

			if ( isset( $memory[$element] ) ) 
			{
				$result = $this->element[$memory[$element]];
			} 
			else 
			{
				$GLOBALS["AP_DOM_ERRORS"] = 4; // index out of bounds
				$result = false;
            } 
		} 
		else 
		{
			$GLOBALS["AP_DOM_ERRORS"] = 1; // wrong data type
			$result = false;
		}
		
		return $result;
	} 
	
	/**
	 * @access public
	 */
	function getElements() 
	{
		return $this->element;
	} 

	/**
	 * @access public
	 */	
	function getParent() 
	{
		return $this->parent;
	} 

	/**
	 * @access public
	 */	
	function getName() 
	{
		return $this->name;
	}  

	/**
	 * @access public
	 */	
	function getValue() 
	{
		return $this->value;
	} 

	/**
	 * @access public
	 */	
	function getAttributes() 
	{
		return $this->attributes;
	} 

	/**
	 * @access public
	 */	
	function getComment() 
	{
		return $this->comment;
	} 

	/**
	 * @access public
	 */	
	function hasElement() 
	{
		$result = true;

		if ( $this->hasChild ) 
		{
			if ( isset( $this->index ) ) 
			{
				if ( !( $this->index < count( $this->element ) ) )
					$result = false;
			} 
			else 
			{
				$this->index = 0;
			}  
		} 
		else 
		{
			$result = false;
		} 
		
		return $result;
	} 

	/**
	 * @access public
	 */	
	function getNext() 
	{
		if ( $this->hasElement() ) 
		{
			$result = $this->element[$this->index++];
		} 
		else 
		{
			$GLOBALS["AP_DOM_ERRORS"] = 4; // index out of bounds
			$result = false;
		} 
		
		return $result;
	}  

	/**
	 * @access public
	 */	
	function getChildByName() 
	{
		return $this->child;
	}  

	/**
	 * @access public
	 */	
	function toString() 
	{
		$result  = '';
		$result .= 'Element: ' . $this->name . "<br>\n";
		$result .= '&nbsp;&nbsp;&nbsp;Parent: ' . $this->parent . "<br>\n";
		$result .= '&nbsp;&nbsp;&nbsp;Value: '  . $this->value  . "<br>\n<br>\n";

		if ( ! empty( $this->attributes ) ) 
			$result .= $this->attributes->toString() . "<br>\n";

		if ( ! empty( $this->comment ) ) 
			$result .= $this->comment->toString() . "<br>\n<br>\n";

		$result .= 'Children : ' . "<br>\n";

		for ( $i = 0; $i< count( $this->child ); $i++ ) 
			$result .= '&nbsp;&nbsp;&nbsp;' . $this->child[$i] . "<br>\n<br>\n";

		return $result;
	}
} // END OF SimpleDomElement

?>

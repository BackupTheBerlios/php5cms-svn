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


/**
 * Dynamic CSS builder class. With this class you can generic CSS in one file, then
 * in other file (first is included in the second) you can add/change/remove 
 * identificators/values and finally output the whole CSS to the browser.
 *
 * @package html_css
 */

class CSSBuilder extends PEAR
{
	/**
	 * Array, which holds the whole CSS structure.
	 * @var	array
	 */
	var $cssData;

	
	/**
	 * Constructor. Creates new CSS object.
	 *
	 * @access public
	 * @return CSS
	 */ 	
	function CSSBuilder()
	{
		$this->cssData = array();
	}

	
	/**
	 * Adds new CSS identificator (tag name, class name or id).
	 *
	 * @param string $identificator Identificator name.
	 * @access public
	 * @return void
	 */ 		
	function addIdentificator( $identificator )
	{
		if ( !isset( $this->cssData[$identificator] ) )
		    $this->cssData[$identificator] = array();
	}

	/**
	 * Removes selected CSS identificator.
	 *
	 * @param string $identificator Identificator name.
	 * @access public
	 * @return void
	 */ 			
	function removeIdentificator( $identificator )
	{
		if ( isset( $this->cssData[$identificator] ) )
		    unset($this->cssData[$identificator]);
	}

	/**
  	 * Sets specified property of identificator to the value $value.
	 *
 	 * @param string $identificator Identificator name.
 	 * @param string $property	Property name.
	 * @param mixed $value	Value of the property
	 * @access public
	 * @return void
	 */ 				
	function setProperty( $identificator, $property, $value )
	{
		if ( isset( $this->cssData[$identificator] ) )
			$this->cssData[$identificator][$property] = $value;
	}

	/**
	 * Gets the value of the specified property of the identificator.
	 *
	 * @param string $identificator Identificator name.
	 * @param string $property	Property name.
	 * @access public
	 * @return mixed
	 */ 					
	function getProperty( $identificator, $property )
	{
		if ( isset( $this->cssData[$identificator] ) )
		{
			if ( isset( $this->cssData[$identificator][$property] ) )
				return $this->cssData[$identificator][$property];
		}
		
		return false;
	}

	/**
	 * Generates the plain CSS output.
	 *
	 * @access private
	 * @return string
	 */ 						
	function generateCSS()
	{
		$output = "";
		
		foreach ( $this->cssData as $identificator => $properties )
		{
			if ( count( $properties ) > 0 )
			{
			    $output.="\n$identificator\n{\n";
				
				foreach ( $properties as $name => $value )
					$output .= "\t$name:\t$value;\n";
				
				$output.="}\n";
			}
		}
		
		return $output;
	}

	/**
	 * Returns and if required,outputs the CSS for this object.
	 *
	 * @param boolean $browser	Ouput to the browser (send header and plain CSS text) ?
	 * @access private
	 * @return string
	 */ 							
	function output( $browser = true )
	{
		$cssText = $this->generateCSS();
		
		if ( $browser )
		{
			header("Content-type: text/css");
			echo $cssText;
		}
		
		return $cssText;
	}
} // END OF CSSBuilder

?>

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
 * @package xml_dom_simple_lib
 */
 
class SimpleDomAttribute extends PEAR
{
	/**
	 * @access public
	 */
	var $attributes = array();


	/**
	 * Constructor
	 *
	 * @access public
	 */
	function SimpleDomAttribute() 
	{
		$GLOBALS["AP_DOM_ERRORS"] = 0;

		$arga = func_num_args();
		$args = func_get_args();

		if ( $arga > 0 ) 
		{
			if ( $arga == 1 ) 
			{
				if ( is_array( $args[0] ) ) 
				{
					$keys = array_keys( $args[0] );

					if ( ! empty( $keys ) ) 
					{
						if ( is_string( $keys[0] ) ) 
							$this->attributes = $args[0];
						else 
							$GLOBALS["AP_DOM_ERRORS"] = 2; // not an associative array
					} 
				} 
				else 
				{
               		$GLOBALS["AP_DOM_ERRORS"] = 1; // wrong data type
				} 
			} 
            else if ( $arga == 2 ) 
			{
				if ( is_string( $args[0] ) ) 
					$this->attributes[$args[0]] = $args[1];
				else 
					$GLOBALS["AP_DOM_ERRORS"] = 1;
			}
		}
	}


	/**
	 * @access public
	 */
	function setAttributes( $attributes ) 
	{
		$GLOBALS["AP_DOM_ERRORS"] = 0;
		$result = false;

		if ( is_array( $attributes ) ) 
		{
			$keys = array_keys( $attributes );

			if ( ! empty( $keys ) ) 
			{
				if ( is_string( $keys[0] ) ) 
				{
					$this->attributes = $attributes;
					$result = true;
				} 
				else 
				{
					$GLOBALS["AP_DOM_ERRORS"] = 2; // not an associative array
				}
			} 
		} 
		else 
		{
			$GLOBALS["AP_DOM_ERRORS"] = 1; // wrong data type
		}

		return $result;
	} 

	/**
	 * @access public
	 */
	function setAttribute( $attribut, $value ) 
	{
		$GLOBALS["AP_DOM_ERRORS"] = 0;
		$result = false;

		if ( is_string( $attribut ) ) 
		{
			$this->attributes[$attribut]  = $value;
			$result = true;
		} 
		else 
		{
			$GLOBALS["AP_DOM_ERRORS"] = 1; // wrong data type
		}

		return $result;
	}

	/**
	 * @access public
	 */
	function getAttribut( $attribut ) 
	{
		$GLOBALS["AP_DOM_ERRORS"]    = 0;

		if ( is_string( $attribut ) ) 
		{
			if ( isset( $this->attributes[$attribut] ) ) 
			{
               $result = $this->attributes[$attribut];
			} 
			else 
			{
               $GLOBALS["AP_DOM_ERRORS"] = 3; // atrribute not in collection
               $result = false;
            }  // if ( isset( $this->attributes[$attribut] ) )
		} 
		else 
		{
			$keys = array_keys( $this->attributes );

            if ( $attribut < count( $keys ) ) 
			{
				if ( $attribut > -1 ) 
				{
					$result = $this->attributes[$keys[$attribut]];
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

		return $result;
	}

	/**
	 * @access public
	 */
	function getKeys() 
	{
		return array_keys( $this->attributes );
	}

	/**
	 * @access public
	 */
	function toString() 
	{
         $result = "Attribute:<br><br>\n\n";
         $keys   = array_keys( $this->attributes );

   		for ( $i = 0; $i < count( $keys ); $i++ )
			$result .= "&nbsp;&nbsp;&nbsp;" . $keys[$i] . " = " . $this->attributes[$keys[$i]] . "<br>\n";

		return $result;
	}
} // END OF SimpleDomAttribute

?>

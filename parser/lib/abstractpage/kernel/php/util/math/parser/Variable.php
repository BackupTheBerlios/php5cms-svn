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


using( 'util.math.parser.MathExpression' );


/**
 * @package util_math_parser
 */
 
class Variable extends MathExpression 
{
	/**
	 * @access public
	 */
	var $name;
 

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Variable( $the_name ) 
	{
		$this->name = $the_name;
		$this->prop["var"] = true;
	}
	

	/**
	 * @access public
	 */	
	function evalf() 
	{		
		if ( !isset( $GLOBALS["AP_MATHPARSER_VARIABLES"][$this->name] ) ) 
		{
			echo "Error: Undefined variable ", $this->name, "\n";
			return false;
		}
		
		return $GLOBALS["AP_MATHPARSER_VARIABLES"][$this->name]->evalf();
	}

	/**
	 * @access public
	 */	
	function evals() 
	{
		if ( !isset( $GLOBALS["AP_MATHPARSER_VARIABLES"][$this->name] ) )
			return $this;
       
		return $GLOBALS["AP_MATHPARSER_VARIABLES"][$this->name]->evals();
	}
} // END OF Variable

?>

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


using( 'util.math.parser.BinaryOperator' );
using( 'util.math.parser.Constant' );


/**
 * @package util_math_parser
 */
 
class OperatorLessThan extends BinaryOperator 
{
	/**
  	 * Constructor
	 *
	 * @access public
	 */
	function OperatorLessThan( $op1, $op2 ) 
	{
		$this->BinaryOperator( $op1, $op2 );
		
		$this->prop["name"] = "<";
		$this->prop["prec"] = 5;
	}
	

	/**
	 * @access public
	 */	
	function evalf() 
	{
		return ( $this->operand1->evalf() < $this->operand2->evalf() )? 1 : 0;
	}

	/**
	 * @access public
	 */	
	function evals() 
	{
		$arg1   = $this->operand1->evals();
		$arg2   = $this->operand2->evals();
		$const1 = isset( $arg1->prop["const"] );
		$const2 = isset( $arg2->prop["const"] );
		
		if ( $const1 && $const2 )
            return new Constant( ( $arg1->evalf() < $arg2->evalf() )? 1 : 0 );

        return new OperatorLessThan( $arg1, $arg2 );
	}
} // END OF OperatorLessThan

?>

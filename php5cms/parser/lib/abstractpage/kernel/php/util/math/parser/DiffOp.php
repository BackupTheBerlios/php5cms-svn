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


using( 'util.math.parser.NFunc' );
using( 'util.math.parser.Constant' );
using( 'util.math.parser.OperatorPlus' );
using( 'util.math.parser.OperatorMinus' );
using( 'util.math.parser.OperatorTimes' );
using( 'util.math.parser.OperatorOver' );
using( 'util.math.parser.UnaryMinus' );
using( 'util.math.parser.OperatorPow' );
using( 'util.math.parser.LnFunc' );
using( 'util.math.parser.CosFunc' );
using( 'util.math.parser.SinFunc' );
using( 'util.math.parser.ExpFunc' );
using( 'util.math.parser.SqrtFunc' );


/**
 * @package util_math_parser
 */
 
class DiffOp extends NFunc 
{
	/**
	 * @access public
	 */
	var $varname;
	
	/**
	 * @access public
	 */
	var $depname;
	
	
	/**
  	 * Constructor
	 *
	 * @access public
	 */
	function DiffOp( $the_args, $nr_args ) 
	{
		$this->NFunc( $the_args, $nr_args );
		$this->prop["name"] = "diff";
		
		if ( !isset( $this->args[1]->prop["var"] ) ) 
		{
			echo "Error: Argument 2 of diff must be of type 'variable'<br>\n";
			return false;
		} 
		else 
		{
			$this->varname = $this->args[1]->name;
			$this->depname = "nodep" . $this->varname;
		}
	}
	

	/**
	 * @access public
	 */
	function check_nodep( &$expr ) 
	{
		// walk through the tree
		// and note on every element, if it depends on the variable
		if ( isset( $expr->prop["const"] ) ) 
		{
			$expr->prop[$this->depname] = true;
			return true;
		}
		else if ( isset( $expr->prop["var"] ) ) 
		{
			if ( $expr->name == $this->varname ) 
			{
				unset( $expr->prop[$this->depname] );
				return false;
			} 
			else 
			{
				$expr->prop[$this->depname] = true;
				return true;
			}
		}
		else if ( isset( $expr->prop["binop"] ) ) 
		{
			$nodep1 = $this->check_nodep( $expr->operand1 );
			$nodep2 = $this->check_nodep( $expr->operand2 );
			
			if ( $nodep1 && $nodep2 ) 
			{
				// no operand depends on the variable
				$expr->prop[$this->depname] = true;
				return true;
			} 
			else 
			{
				unset( $expr->prop[$this->depname] );
				return false;
			}
		}
		else if ( isset( $expr->prop["func"] ) ) 
		{
			if ( $this->check_nodep( $expr->operand ) ) 
			{
				// the operand doesn't depend on the variable
				$expr->prop[$this->depname] = true;
				return true;
			} 
			else 
			{
				unset( $expr->prop[$this->depname] );
				return false;
			}
		}
		else if ( isset( $expr->prop["nfunc"] ) ) 
		{
			$erg = true;
			
			for ( $i = 0; $i < $expr->nargs; $i++ ) 
			{
				if ( !$this->check_nodep( $expr->args[$i] ) ) 
				{
					// one of the arguments depends on the variable --> also the whole function
					$erg = false;
					break;
				}
			}
			
			if ( $erg ) 
			{
				// no operand depends on the variable
				$expr->prop[$this->depname] = true;
				return true;
			} 
			else 
			{
				unset( $expr->prop[$this->depname] );
				return false;
			}
		}
	}

	/**
	 * @access public
	 */
	function sub_diff( &$expr ) 
	{
		// check for nodetype and return appropriate
		// value
		// simplest case is no dependencie on the variable
		if ( isset( $expr->prop[$this->depname] ) ) 
			return new Constant( 0 );
			
        // this is the primitive way
        // now, look closer to the expression
        if ( isset( $expr->prop["binop"] ) ) 
		{
			// we have +,-,*,/ or , #
			$dep1 = isset( $expr->operand1->prop[$this->depname] );
			$dep2 = isset( $expr->operand2->prop[$this->depname] );

			switch ( $expr->prop["name"] ) 
			{
				case '+':
					if ( $dep1 ) 
						return $this->sub_diff( $expr->operand2 );
						
					if ( $dep2 ) 
						return $this->sub_diff( $expr->operand1 );
						
					return new OperatorPlus( 
						$this->sub_diff( $expr->operand1 ), 
						$this->sub_diff( $expr->operand2 ) 
					);
				
				case '-':
					if ( $dep1 ) 
						return $this->sub_diff( $expr->operand2 );
					
					if ( $dep2 ) 
						return $this->sub_diff( $expr->operand1 );
					
					return new OperatorMinus( 
						$this->sub_diff( $expr->operand1 ), 
						$this->sub_diff( $expr->operand2 ) 
					);
				
				case '*':
					if ( $dep1 ) 
						return new OperatorTimes( $expr->operand1, $this->sub_diff( $expr->operand2 ) );
						
					if ( $dep2 ) 
						return new OperatorTimes( $this->sub_diff( $expr->operand1 ), $expr->operand2 );
					
					return new OperatorPlus(
                        new OperatorTimes( $expr->operand1, $this->sub_diff( $expr->operand2 ) ),
                        new OperatorTimes( $this->sub_diff( $expr->operand1 ), $expr->operand2 )
					);
				
				case '/':
					if ( $dep2 ) 
						return new OperatorOver( $this->sub_diff( $expr->operand1 ), $expr->operand2 );
						
						// d/dx f(x)/const = f'(x)/const
						if ( $dep1 ) 
							return new OperatorTimes( new UnaryMinus( new OperatorOver( $expr->operand1, new OperatorPow( $expr->operand2, new Constant( 2 ) ) ) ), $this->sub_diff( $expr->operand2 ) );
							
                       // d/dx const/f(x) = -(const/f(x)^2)*f'(x)
						// most awful situation: Both operands depend on the variable
						return new OperatorOver(  new OperatorMinus(
							new OperatorTimes( $expr->operand1, $this->sub_diff( $expr->operand2 ) ),
							new OperatorTimes( $this->sub_diff( $expr->operand1 ), $expr->operand2 ) ),
							new OperatorPow( $expr->operand2, new Constant( 2 ) )
						);
						
				case '^':
					if ( $dep2 ) 
						return new OperatorTimes( new OperatorTimes( $expr->operand2, new OperatorPow( $expr->operand1, new OperatorMinus( $expr->operand2, new Constant( 1 ) ) ) ), $this->sub_diff( $expr->operand1 ) );

					if ( $dep1 ) 
						return new OperatorTimes( new OperatorTimes( new LnFunc( $expr->operand1 ), new OperatorPow( $expr->operand1, $expr->operand2 ) ), $this->sub_diff( $expr->operand2 ) );

					// worst case: logarithmic differentiation
					return new OperatorTimes( 
						new OperatorPow( $expr->operand1, $expr->operand2 ),
						new OperatorPlus( new OperatorTimes(
						$this->sub_diff( $expr->operand2 ), new LnFunc( $expr->operand1 ) ),
						new OperatorTimes( $expr->operand2,
						new OperatorOver( $this->sub_diff( $expr->operand1 ), $expr->operand1 ) ) )
					);
					
				case '#':
					if ( $dep1 ) 
						return new OperatorTimes( new OperatorOver( new OperatorPow($expr->operand2, new OperatorOver( new OperatorMinus( new Constant( 1 ), $expr->operand1 ), $expr->operand1 ) ), $expr->operand1 ), $this->sub_diff( $expr->operand2 ) );
               			
					// all other cases are too complex to handle them simply
               		// just pass to v^(1/u)
               		$help = new OperatorPow( $expr->operand2, new OperatorOver( new Constant( 1 ), $expr->operand1 ) );
					$this->check_nodep( $help );
					return $this->sub_diff( $help );
			}
		}
		else if ( isset( $expr->prop["var"] ) ) 
		{
         		return new Constant( 1 );
        }
        else if ( isset( $expr->prop["func"] ) ) 
		{
         	switch ( $expr->prop["name"] ) 
			{
				case '-':
					return new UnaryMinus( $this->sub_diff( $expr->operand ) );
       
				case 'sin':
					return new OperatorTimes( new CosFunc( $expr->operand ), $this->sub_diff( $expr->operand ) );
       
				case 'cos':
					return new UnaryMinus( new OperatorTimes( new SinFunc( $expr->operand ), $this->sub_diff( $expr->operand ) ) );
       
				case 'exp':
					return new OperatorTimes( new ExpFunc( $expr->operand ), $this->sub_diff( $expr->operand ) );
       
				case 'ln':
					return new OperatorOver( $this->sub_diff( $expr->operand ), $expr->operand );
       
				case 'sqrt':
					return new OperatorOver( $this->sub_diff( $expr->operand ), new OperatorTimes( new Constant( 2 ), new SqrtFunc( $expr->operand ) ) );
          	}
        }
        
		return "Cannot diff";
	}

	/**
	 * @access public
	 */
	function evals() 
	{
		if ( !isset( $this->args[1]->prop["var"] ) ) 
		{
			echo "Error: Argument 2 of diff must be of type 'variable'<br>\n";
			return false;
		}
		
		$this->check_nodep( $this->args[0] );
		// now, we must walk through the tree
		// recursively and apply the differentiation rules for all nodes
		return $this->sub_diff( $this->args[0] );
	}
} // END OF DiffOp

?>

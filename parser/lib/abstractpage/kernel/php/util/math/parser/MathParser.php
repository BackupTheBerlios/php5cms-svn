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


using( 'util.math.parser.OperatorPlus' );
using( 'util.math.parser.OperatorMinus' );
using( 'util.math.parser.OperatorTimes' );
using( 'util.math.parser.OperatorOver' );
using( 'util.math.parser.OperatorModulo' );
using( 'util.math.parser.OperatorPow' );
using( 'util.math.parser.OperatorRoot' );
using( 'util.math.parser.OperatorLessThan' );
using( 'util.math.parser.OperatorGreaterThan' );
using( 'util.math.parser.UnaryMinus' );
using( 'util.math.parser.Variable' );
using( 'util.math.parser.SinFunc' );
using( 'util.math.parser.CosFunc' );
using( 'util.math.parser.ExpFunc' );
using( 'util.math.parser.SqrtFunc' );
using( 'util.math.parser.LnFunc' );
using( 'util.math.parser.DiffOp' );


define( "MATHPARSER_ERR_PARENTH_CLOSE",  -2 );
define( "MATHPARSER_ERR_PARENTH_OPEN",   -3 );
define( "MATHPARSER_NOPOS",            -500 );
define( "MATHPARSER_ERR_NOOP",           -5 );

$GLOBALS["AP_MATHPARSER_VARIABLES"] = array();


/**
 * MathParser class acts as a simple math expression parser for an input string.
 *
 *
 * Functions:
 *
 * exp,   (done) 
 * ln,    (done)
 * log,   ?
 * sin,   (done)
 * cos,   (done)
 * tan, 
 * asin, 
 * acos, 
 * atan, 
 * sinh, 
 * cosh, 
 * tanh, 
 * asinh, 
 * acosh, 
 * atanh, 
 * abs, 
 * floor, 
 * ceil, 
 * round, 
 * sqrt,  (done)
 * int
 *
 *
 * Operators (working on integers, floats and strings):
 *
 * -,     (done) 
 * +,     (done) 
 * /,     (done) 
 * *,     (done) 
 * ^,     (done) 
 * #      (done) 
 * %,     (done)
 * +=,
 * -=,
 * *=,
 * /=,
 * ==, 
 * !=, 
 * <=, 
 * >=, 
 * <,     (done)
 * >,     (done)
 * &&, 
 * ||
 * ?:
 *
 * @package util_math_parser
 */
 
class MathParser extends PEAR
{
	/**
	 * @access public
	 */
	var $expr_string;
	
	/**
	 * @access public
	 */
	var $expr_length;

	
	/**
  	 * Constructor
	 *
	 * @access public
	 */
	function MathParser( $expr_to_parse ) 
	{
		$this->expr_string = $expr_to_parse;
		$this->expr_length = strlen( $expr_to_parse );
	}


	/**
	 * @access public
	 */	
	function last_operation( $start, $end ) 
	{
		$par_depth = 0;
		$last_mult = MATHPARSER_NOPOS;
		$last_pow  = MATHPARSER_NOPOS;
		$last_add  = MATHPARSER_NOPOS;
		$last_comp = MATHPARSER_NOPOS;
		
		for ( $pos = $end; $pos >= $start; $pos-- ) 
		{
			switch ( substr( $this->expr_string, $pos, 1 ) ) 
			{
				case ')':
					$par_depth++;
					break;
				
				case '(':
					$par_depth--;
					break;
				
				case '+':
				
				case '-':
					if ( $par_depth == 0 ) 
					{
						if ( $last_add == MATHPARSER_NOPOS )  
							$last_add = $pos;
					}
					
					break;
				
				case 'e':
				
				case 'E':
					// exponential notation means
					// that the - or + sign detected here
					// as a binary operator is wrong, if
					// the preceding char is a digit
					// this looks rather awful
					if ( $par_depth == 0 && $last_add == $pos + 1 ) 
					{
						// following + or - sign
						// echo "plus sign found";
						if ( $pos > $start ) 
						{
							// we havn't reached the beginning of the string
							// echo "inside string";
							if ( MathParser::is_digit( substr( $this->expr_string, $pos - 1, 1 ) ) ) 
							{
								// echo "It's a digit";
								$last_add = MATHPARSER_NOPOS;
								// it is not really an operation
							}
						}
					}
					
					break;

				case '%':
				
				case '*':
				
				case '/':
                	if ( $par_depth == 0 ) 
					{
						if ( $last_mult == MATHPARSER_NOPOS )  
							$last_mult = $pos;
					}
					
					break;
				
				case '<':
				
				case '>':
                	if ( $par_depth == 0 ) 
					{
						if ( $last_comp == MATHPARSER_NOPOS )  
							$last_comp = $pos;
					}
					
					break;
					
				case '^':
				
				case '#':
					if ( $par_depth == 0 ) 
					{
						if ( $last_pow == MATHPARSER_NOPOS )  
							$last_pow = $pos;
					}
					
					break;
				
				default:
           }
			
			if ( $par_depth < 0 ) 
				return MATHPARSER_ERR_PARENTH_CLOSE;
		}
		
		if ( $par_depth != 0 ) 
			return MATHPARSER_ERR_PARENTH_OPEN;
		
		if ( $last_add  != MATHPARSER_NOPOS ) 
			return $last_add;
		
		if ( $last_mult != MATHPARSER_NOPOS ) 
			return $last_mult;
		
		if ( $last_comp != MATHPARSER_NOPOS ) 
			return $last_comp;
			
		if ( $last_pow  != MATHPARSER_NOPOS ) 
			return $last_pow;
         
		// if we reach this point, there has been no
		// binary operator at top level, but there was
		// also parenthesis error
		// so we can assume that the expression is either
		// a function call like sin(a+b^2), a single variable
		// or a constant expression. All that have starting position=first character
		return $start;
	}

	/**
	 * @access public
	 */
	function sub_parser( $start, $end ) 
	{
		// this function does the entire work
		// first, we delete whitespace from right and left
		// note that we cannot really delete them, we just have to ignore
		while ( MathParser::is_whitespace( substr( $this->expr_string, $start, 1 ) ) && $start <= $end ) 
			$start++;
		
		while ( MathParser::is_whitespace( substr( $this->expr_string, $end, 1 ) ) && $start <= $end ) 
			$end--;

		if ( $start > $end ) 
			return false;
       
		// this means, there is nothing left - e.g. missing operand like in
		// 3+-4

		// first, look for enclosing parenthesis

		for ( $num = 0; substr( $this->expr_string, $start + $num, 1 ) == '(' && substr( $this->expr_string, $end - $num, 1 ) == ')'; $num++ );
		
		// we must go back, if there are parentheses *not* enclosing,
		// left and right, e.g. (a+b)+(c+d)
		$par_depth     = 0;
		$min_par_depth = 0;

		for ( $i = $start + $num; $i <= $end - $num; $i++ ) 
		{
			switch ( substr( $this->expr_string, $i, 1 ) ) 
			{
				case '(':
					$par_depth++;
					break;
					
				case ')':
					$par_depth--;
					break;
			}
			
			$min_par_depth = min( $min_par_depth, $par_depth );
		}

		if ( -$min_par_depth > $num ) 
		{
			echo "Parse error, missing (\n";
			return false;
		} 
		else 
		{
			$num += $min_par_depth;
		}

		$start += $num;
		$end   -= $num;

		// echo "Expression is ", substr( $this->expr_string, $start, $end - $start + 1 ), "<br>";

		if ( ( $last_op_pos = $this->last_operation( $start, $end ) ) < 0 ) 
		{
			echo "Parse error, Parentheses don't match\n";
			return false;
		}

		if ( $last_op_pos > $start ) 
		{
			// this must be a binary operator
			// calculate both operands by recursive calls
			// and insert them into the return path
			$op1 = $this->sub_parser( $start, $last_op_pos - 1 );
			$op2 = $this->sub_parser( $last_op_pos + 1, $end );
			
			if ( ( $op1 === false) || ( $op2 === false ) ) 
				return false;
         
		 	$opchar = substr( $this->expr_string, $last_op_pos, 1 );
			
			switch ( $opchar ) 
			{
				case '+':
					return new OperatorPlus( $op1, $op2 );
					break;
				
				case '-':
					return new OperatorMinus( $op1, $op2 );
					break;
				
				case '*':
					return new OperatorTimes( $op1, $op2 );
					break;
				
				case '/':
					return new OperatorOver( $op1, $op2 );
					break;
				
				case '%':
					return new OperatorModulo( $op1, $op2 );
					break;
					
				case '^':
					return new OperatorPow( $op1, $op2 );
					break;
				
				case '#':
					return new OperatorRoot( $op1, $op2 );
					break;
					
				case '<':
					return new OperatorLessThan( $op1, $op2 );
					break;
								
				case '>':
					return new OperatorGreaterThan( $op1, $op2 );
					break;
				
				/*			
				case '&':
					return new OperatorBitwiseAnd( $op1, $op2 );
					break;
								
				case '|':
					return new OperatorBitwiseOr( $op1, $op2 );
					break;
				*/
			}
			
			/*
			// execute only if two chars...
			$opchar = substr( $this->expr_string, $last_op_pos, 2 );
						
			switch ( $opchar ) 
			{
				case '==':
					return new OperatorEquals( $op1, $op2 );
					break;
							
				case '<=':
					return new OperatorLessOrEqualThan( $op1, $op2 );
					break;
							
				case '>=':
					return new OperatorGreaterOrEqualThan( $op1, $op2 );
					break;
							
				case '&&':
					return new OperatorAnd( $op1, $op2 );
					break;
						
				case '||':
					return new OperatorOr( $op1, $op2 );
					break;
			}
			*/
		} 
		else 
		{
			// we got the first position
			// it can be a unary sign, number, function call
			// or variable ( or error )
			// first we assume it's a number
			$part_expr = substr( $this->expr_string, $start, $end - $start + 1 );
			
			if ( MathParser::is_number( $part_expr ) ) 
			{
				return new Constant( $part_expr );
			} 
			else 
			{
				switch ( substr( $part_expr, 0, 1 ) ) 
				{
					case '+':
						// we found unary +
						// just ignore
						return $this->sub_parser( $start + 1, $end );
						break;
					
					case '-':
						// we found unary -
						// we must instantiate the unary operator
						$operand = $this->sub_parser( $start + 1, $end );
						
						if ( $operand === false ) 
							return false;
                 
				 		return new UnaryMinus( $operand );
                 		break;
             	}

             	if ( MathParser::is_variable( $part_expr ) ) 
				{
               		return new Variable( $part_expr );
             	} 
				else 
				{
               		if ( MathParser::is_functioncall( $part_expr ) ) 
					{
                 		// get function name and argument
                 		$open_par_pos  = strpos( $part_expr, "(" );
                 		$close_par_pos = strrpos( $part_expr, ")" );
                 
				 		// this should be the last character but don't count on it
						$function_name = trim( substr( $part_expr, 0, $open_par_pos ) );
						
						// now find the ',' delimiters on top level
						$arg_nr    = 1;
						$argpos[0] = $start + $open_par_pos;
						$par_depth = 0;
						
						for ( $pos = $start + $open_par_pos + 1; $pos <= $start + $close_par_pos - 1; $pos++ ) 
						{
							switch ( substr( $this->expr_string, $pos, 1 ) ) 
							{
								case '(':
									$par_depth++;
									break;
								
								case ')':
									$par_depth--;
									break;
								
								case ',':
									if ( $par_depth == 0 )
										$argpos[$arg_nr++] = $pos;
                       
                       				break;
                   			}
                 		}
                 
				 		$argpos[$arg_nr] = $start + $close_par_pos;

                 		for ( $i = 0; $i < $arg_nr; $i++ ) 
						{
                   			$arguments[$i] = $this->sub_parser( $argpos[$i] + 1, $argpos[$i + 1] - 1 );
                   
				   			if ( $arguments[$i] === false ) 
								return false;
                   
				   			// one of the arguments failed to parse
                 		}
                 
				 		return $this->construct_func( $function_name, $arguments, $arg_nr );
					} 
					else 
					{
						echo "Parse error: $part_expr is neither a number, variable nor function call <br>";
						return false;
					}
				}
			}
		}
	}
	
	/**
	 * @access public
	 */
	function construct_func( $name, $arguments, $nr_arguments = 1 ) 
	{
		if ( $nr_arguments == 1 )
		{
			switch ( $name ) 
			{
				case 'sin':
					return new SinFunc( $arguments[0] );
					break;
    
			    case 'cos':
					return new CosFunc( $arguments[0] );
					break;
			
				case 'exp':
					return new ExpFunc( $arguments[0] );
					break;
			
				case 'sqrt':
					return new SqrtFunc( $arguments[0] );
					break;
			
				case 'ln':
					return new LnFunc( $arguments[0] );
					break;
			
				case 'eval':
					return $arguments[0]->evals();
					break;
			}
		}
	
		if ( $nr_arguments == 2 )
		{
			switch ( $name ) 
			{
				case 'diff':
					return new DiffOp( $arguments, $nr_arguments );
					break;
			}
		}

		echo "Unknown function: $name with $nr_arguments arguments <br>\n";
		return false;
	}
	
	
	// static methods
	
	/**
	 * @access public
	 * @static
	 */
	function is_digit( $dig ) 
	{
		return strchr( "012345567889", $dig );
	}
	
	/**
	 * @access public
	 * @static
	 */
	function is_whitespace( $dig ) 
	{
		return strchr( " \r\n\t", $dig );
	}
	
	/**
	 * @access public
	 * @static
	 */
	function is_variable( $str ) 
	{
		return ereg( "^[a-zA-Z][0-9a-zA-Z']*$", $str );
	}
	
	/**
	 * @access public
	 * @static
	 */
	function is_functioncall( $str ) 
	{
		return ereg( "^[a-zA-Z][0-9a-zA-Z]*[[:space:]]*\\(.*\\)\$", $str );
	}
	
	/**
	 * @access public
	 * @static
	 */
	function is_number( $str ) 
	{
		return ereg( "^[-+]?[[:digit:]]+(\\.[[:digit:]]+)?([eE][-+]?[[:digit:]]+)?\$", $str );
	}
	
	/**
	 * @access public
	 * @static
	 */
	function math_print( $expr ) 
	{
		if ( isset( $expr->prop["const"] ) ) 
		{
			return $expr->evalf();
		}
		else if ( isset( $expr->prop["binop"] ) ) 
		{
			$exp1 = MathParser::math_print( $expr->operand1 );
			$exp2 = MathParser::math_print( $expr->operand2 );
       
			// determine, if the parentheses are needed really
			if ( isset( $expr->operand1->prop["binop"] ) && $expr->operand1->prop["prec"] < $expr->prop["prec"] )
				$exp1 = "(" . $exp1 . ")";

			if ( isset( $expr->operand2->prop["binop"] ) && $expr->operand2->prop["prec"] <= $expr->prop["prec"] )
				$exp2 = "(" . $exp2 . ")";

			return $exp1 . $expr->prop["name"] . $exp2;
		}
		else if ( isset( $expr->prop["func"] ) ) 
		{
			if ( $expr->prop["name"] == "-" ) 
			{
				// its a unary minus -  special care must be taken
				$exp = MathParser::math_print( $expr->operand );
				
				if ( isset( $expr->operand->prop["binop"] ) && $expr->operand->prop["prec"] < 2 )
					$exp = "(" . $exp . ")";
         
				return "(-" . $exp . ")";
			}
		
			return $expr->prop["name"] . "(" . MathParser::math_print( $expr->operand ) . ")";
		}
		else if ( isset( $expr->prop["var"] ) )
    	{
			return $expr->name;
		}
	
		if ( isset( $expr->prop["nfunc"] ) ) 
		{
			$exp = $expr->prop["name"] . "(";
		
			for ( $i = 0; $i < $expr->nargs-1; $i++ )
				$exp .= MathParser::math_print( $expr->args[$i] ) . ",";

			$exp .= MathParser::math_print( $expr->args[$expr->nargs-1] ) . ")";
			return $exp;
		}
		else
		{ 
			return "Unknown object $expr";
		}
	}
} // END OF MathParser

?>

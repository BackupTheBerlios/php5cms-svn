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
|Authors: Marko Riedel <mriedel@neuearbeit.de>                         |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * @package util
 */
 
class Scheme extends PEAR
{
	/**
	 * @access public
	 */
	var $errmsg;
	
	/**
	 * @access public
	 */
	var $tokens;
	
	/**
	 * @access public
	 */
	var $base;
	
	/**
	 * @access public
	 */
	var $argstack;
	
	/**
	 * @access public
	 */
	var $argp;
	
	/**
	 * @access public
	 */
	var $initialenv;
	
	/**
	 * @access public
	 */
	var $outputstr;
	
	/**
	 * @access public
	 */
	var $codestack;
	
	/**
	 * @access public
	 */
	var $codep;
	
	/**
	 * @access public
	 */
	var $envstack;
	
	/**
	 * @access public
	 */
	var $envp;
	
	/**
	 * @access public
	 */
	var $bcode;
	
	/**
	 * @access public
	 */
	var $bc;
	
	/**
	 * @access public
	 */
	var $envindex = 0;
	
	/**
	 * @access public
	 */
	var $environments = array();
	
	/**
	 * @access public
	 */
	var $thunkindex = 0;
	
	/**
	 * @access public
	 */
	var $thunks = array();
	
	/**
	 * @access public
	 */
	var $closureindex = 0;
	
	/**
	 * @access public
	 */
	var $closures = array();
	
	/**
	 * @access public
	 */
	var $pairindex = 0;
	
	/**
	 * @access public
	 */
	var $pairs = array();
	
	/**
	 * @access public
	 */
	var $nullunique = array( 'empty' );

	/**
	 * @access public
	 */
	var $chartable = array(
		"newline" => "\n",
		"tab"     => "\t",
		"space"   => " "
	);

	/**
	 * @access public
	 */
	var $specialforms = array(
		"define"  => 1, 
		"set!"    => 1, 
		"lambda"  => 1, 
		"if"      => 1, 
		"and"     => 1, 
		"or"      => 1,
		"begin"   => 1, 
		"apply"   => 1,
		"quote"   => 1, 
		"case"    => 1, 
		"cond"    => 1,
		"let"     => 1, 
		"let*"    => 1, 
		"letrec"  => 1,
		"call-with-current-continuation" => 1
	);

	/**
	 * @access public
	 */
	var $primtable = array(
		"+", 
		"*", 
		"-", 
		"/", 
		"=", 
		">", 
		"<",
		"draw-move", 
		"draw-line", 
		"draw-color",
		"sin", 
		"cos", 
		"sqrt",
		"quotient", 
		"remainder", 
		"not",
		"zero?", 
		"pair?", 
		"number?", 
		"eqv?", 
		"eq?",
		"cons", 
		"car", 
		"cdr", 
		"list", 
		"null?",
		"set-car!", 
		"set-cdr!",
		"display", 
		"newline"
	);
	

	/**
	 * @access public
	 */	
	function init()
	{
    	$prim = array();
    
		for ( $p = 0; $p < count( $this->primtable ); $p++ )
			$prim[$this->primtable[$p]] = $this->newval( 'primitive', $this->primtable[$p] );
    
    	$this->initialenv = $this->cons( $this->newenv( array() ), $this->cons( $this->newenv( $prim ), $this->null() ) );
	}

	/**
	 * @access public
	 */
	function tokenize( $text )
	{
    	$this->tokens = array();
		$this->base   = 0;
	
    	$lines = explode( "\n", $text );

    	for ( $lind = 0; $lind < count( $lines ); $lind++ )
		{
			if ( preg_match( "/^([^;]*);/", $lines[$lind], $parts ) )
	    		$lines[$lind] = $parts[1];
	
			for ( $current = $lines[$lind], $ltokens = array(); preg_match( "/([^\"]*)\"([^\"]*)\"(.*)$/", $current, $parts ); $current = $parts[3] )
			{
	    		if ( strlen( $parts[1] ) > 0 )
					$ltokens[] = $parts[1];
	    
	    		$ltokens[] = array( 'string', $parts[2] );
			}
	
			if ( strlen( $current ) )
	    		$ltokens[] = $current;

			for ( $tok = 0; $tok < count( $ltokens ); $tok++ )
			{
	    		if ( is_array( $ltokens[$tok] ) )
				{
					$this->tokens[] = $ltokens[$tok];
	    		}
	    		else
				{
					for ( $tok1 = 0, $nosp = preg_split( "/\s+/", $ltokens[$tok] ); $tok1 < count( $nosp ); $tok1++ )
					{
		    			for ( $current = $nosp[$tok1];  preg_match( "/([^\(\)\']*)(\(|\)|\')(.*)$/", $current, $parts ); $current = $parts[3] )
						{
							if ( strlen( $parts[1] ) > 0 )
			    				$this->tokens[] = array( 'text', $parts[1] );
			
							if ( $parts[2] == '(' )
			    				$this->tokens[] = array( 'left' );
							else if ( $parts[2] == ')' )
			    				$this->tokens[] = array( 'right' );
							else
			    				$this->tokens[] = array( 'quote' );
		    			}
		    
						if ( strlen( $current ) )
							$this->tokens[] = array( 'text', $current );
					}
	    		}
			}
    	}
	}

	/**
	 * @access public
	 */
	function newenv( $table )
	{
  		$this->environments[$this->envindex++] = $table;
  		return ( $this->envindex - 1 );
	}

	/**
	 * @access public
	 */
	function writetoenv( $tag, $sym, $val )
	{
  		$this->environments[$tag][$sym] = $val;
	}

	/**
	 * @access public
	 */
	function readfromenv( $tag, $sym )
	{
  		$val = $this->environments[$tag][$sym];
  
  		if ( isset( $val ) )
    		return $val;
  
  		return -1;
	}

	/**
	 * @access public
	 */
	function newval( $type, $val )
	{
  		return array( $type, $val );
	}

	/**
	 * @access public
	 */
	function valtype( $v )
	{
  		return $v[0];
	}

	/**
	 * @access public
	 */
	function valdata( $v )
	{
  		return $v[1];
	}

	/**
	 * @access public
	 */
	function newthunk()
	{
  		$this->thunks[$this->thunkindex++] = array( -1, -1 );
  		return array( 'thunk', $this->thunkindex - 1 );
	}

	/**
	 * @access public
	 */
	function writeargptothunk( $t, $argp )
	{
  		$this->thunks[$t[1]][0] = $argp;
	}

	/**
	 * @access public
	 */
	function readargpfromthunk( $t )
	{
  		return $this->thunks[$t[1]][0];
	}

	/**
	 * @access public
	 */
	function writeenvptothunk( $t, $envp )
	{
  		$this->thunks[$t[1]][1] = $envp;
	}

	/**
	 * @access public
	 */
	function readenvpfromthunk( $t )
	{
  		return $this->thunks[$t[1]][1];
	}

	/**
	 * @access public
	 */
	function newclosure( $args, $code, $argtype, $env )
	{
  		$this->closures[$this->closureindex++] = array( $args, $code, $argtype, $env );
  		return array( 'closure', $this->closureindex - 1 );
	}

	/**
	 * @access public
	 */
	function closuretag( $c )
	{
  		return $c[1];
	}

	/**
	 * @access public
	 */
	function closureargs( $c )
	{
  		return $this->closures[$c[1]][0];
	}

	/**
	 * @access public
	 */
	function closurebody( $c )
	{
  		return $this->closures[$c[1]][1];
	}

	/**
	 * @access public
	 */
	function closureargtype( $c )
	{
  		return $this->closures[$c[1]][2];
	}

	/**
	 * @access public
	 */
	function closureenv( $c )
	{
  		return $this->closures[$c[1]][3];
	}

	/**
	 * @access public
	 */
	function cons( $a, $b )
	{
  		$this->pairs[$this->pairindex++] = array( $a, $b );
  		return array( 'pair', $this->pairindex - 1 );
	}

	/**
	 * @access public
	 */
	function car( $p )
	{
  		return $this->pairs[$p[1]][0];
	}

	/**
	 * @access public
	 */
	function cdr( $p )
	{
  		return $this->pairs[$p[1]][1];
	}

	/**
	 * @access public
	 */
	function setcar( $p, $v )
	{
  		$this->pairs[$p[1]][0] = $v;
	}

	/**
	 * @access public
	 */
	function setcdr( $p, $v )
	{
  		$this->pairs[$p[1]][1] = $v;
	}

	/**
	 * @access public
	 */
	function null()
	{
    	return $this->nullunique;
	}

	/**
	 * @access public
	 */
	function array2list($items)
	{
    	$res = $this->null();

    	for ( $p = count( $items ) - 1; $p >= 0; $p-- )
			$res = $this->cons( $items[$p], $res );

    	return $res;
	}

	/**
	 * @access public
	 */
	function btrue()
	{
  		return $this->newval( 'boolean', '#t' );
	}

	/**
	 * @access public
	 */
	function bfalse()
	{
  		return $this->newval( 'boolean', '#f' );
	}

	/**
	 * @access public
	 */
	function isfalse( $val )
	{
  		if ( $val[0] == 'empty' || ( $val[0] == 'boolean' && $val[1] == '#f' ) )
    		return true;

  		return false;
	}

	/**
	 * @access public
	 */
	function readexp()
	{
    	while ( isset( $this->tokens[$this->base] ) )
		{
			$tok = $this->tokens[$this->base++];
	
			if ( $tok[0] == 'string' )
	    		return $tok;

			if ( $tok[0] == 'text' )
			{
	   	 		if ( preg_match( "/^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/", $tok[1] ) || preg_match( "/^[+-]?\d+$/", $tok[1] ) )
					return $this->newval( 'number', $tok[1] );
	    
	    		if ( preg_match( "/^[\+\-\*\/\=\>\<]|<=|>=$/", $tok[1] ) || preg_match( "/^[a-zA-Z\?][a-zA-Z0-9\-\?\!\*]*$/", $tok[1] ) )
					return $this->newval( 'symbol', $tok[1] );
	    
	    		if ( preg_match( "/^\#[tf]$/", $tok[1] ) )
					return $this->newval( 'boolean', $tok[1] );
	    
	    		if ( preg_match( "/^\#\\\\(\w+|\.|\,|\+|\-|\*|\/)$/", $tok[1], $parts ) )
	        		return $this->newval( 'character', $parts[1] );
	    
	    		if ( $tok[1] == '.' )
					return $this->newval( 'improper', $tok[1] );
			}
			else if ( $tok[0] == 'quote' )
			{
	    		$quoted = $this->readexp();
	    
				if ( is_array( $quoted ) )
				{
					return $this->cons( $this->newval( 'symbol', 'quote' ), $this->cons( $quoted, $this->null() ) );
				}
	    		else
				{
					$this->errmsg = 'quote missing an item';
					return -1;
	    		}
			}
			else if ( $tok[0] == 'left' )
			{
	    		$items = array(); 
				$isimproper = 0;
	    
				while ( isset( $this->tokens[$this->base] ) && $this->tokens[$this->base][0] != 'right' )
				{
					$item = $this->readexp();
		
					if ( is_array( $item ) )
					{
		    			if ( $item[0] == 'improper' )
						{
							$isimproper = 1;
							break;
		    			}
		    			else
						{
							$items[] = $item;
		    			}
					}
					else
					{
		    			return -1;
					}
	    		}
	    
				if ( !isset( $this->tokens[$this->base] ) )
				{
					$this->errmsg = 'ran out of list items';
					return -1;
	    		}
	    
				if ( $isimproper )
				{
					$item = $this->readexp();
		
					if ( is_array( $item ) )
					{
		    			if ( !isset( $this->tokens[$this->base] ) )
						{
							$this->errmsg = 'improper list missing closing parenthesis';
							return -1;
		    			}
		    			
						if ( $this->tokens[$this->base][0] != 'right' )
						{
							$this->errmsg = 'improper list not closed by parenthesis';
							return -1;
		    			}
		    
						$this->base++;
		    			$result = $item;
					}
					else
					{
		    			$this->errmsg = 'improper list missing last item';
		    			return -1;
					}
	    		}
	    		else
				{
					$this->base++;
					$result = $this->null();
	    		}

	    		for ( $ind = count( $items ) - 1; $ind >= 0; $ind-- )
					$result = $this->cons( $items[$ind], $result );

	    		return $result;
			}
			else if ( $tok[0] == 'right' )
			{
	    		$this->errmsg = 'missing open parenthesis';
	    		return -1;
			}
    	}

    	$this->errmsg = 'parse error';
    	return -1;
	}

	/**
	 * @access public
	 */
	function tostring( $exp, $expchars )
	{
    	if ( $this->valtype( $exp ) == 'pair' )
		{
			$result  = '(';
			$result .= $this->tostring( $this->car( $exp ), $expchars );
	
			for ( $rest = $this->cdr( $exp ); $this->valtype( $rest ) == 'pair'; $rest = $this->cdr( $rest ) )
	  			$result .= ' ' . $this->tostring( $this->car( $rest ), $expchars );
	
			if ( $this->valtype( $rest ) != 'empty' )
	    		$result .= ' . ' . $this->tostring( $rest, $expchars );

			$result .= ')';
			return $result;
    	}
    	else if ( $this->valtype( $exp ) == 'empty' )
		{
			return '()';
    	}
    	else if ( $this->valtype( $exp ) == 'closure' )
		{
			return '<closure: ' . $this->tostring( $this->closureargs( $exp ), 'noexpchars' ) . '>';
    	}
    	else if ( $this->valtype( $exp ) == 'bcode' )
		{
        	return '<byte codes: ' . count( $this->valdata( $exp ) ) . '>';
    	}
    	else if ( $this->valtype( $exp ) == 'thunk' )
		{
        	return '<thunk: #' . $this->valdata( $exp ) . '>';
    	}
    	else if ( $this->valtype( $exp ) == 'primitive' )
		{
			return '<primitive: ' . $this->valdata( $exp ) . '>';
    	}
    	else if ( $this->valtype( $exp ) == 'string' )
		{
			if ( $expchars == 'expchars' )
	    		return $this->valdata( $exp );
	
			return '"' . $this->valdata( $exp ) . '"';
    	}
    	else if ( $this->valtype( $exp ) == 'character' )
		{
			if ( $expchars == 'expchars' )
			{
	    		$expanded = $this->chartable[$this->valdata( $exp )];
	    
				if ( !empty( $expanded ) )
				{
					return $expanded;
	    		}
	    		else
				{
	      			$str = $this->valdata( $exp );
	      			return $str[0];
	    		}
			}
			else
			{
	    		return "#\\" . $this->valdata( $exp );
			}
    	}
    	else
		{
			return $this->valdata( $exp );
    	}
	}

	/**
	 * @access public
	 */
	function tohtmlstring( $exp, $expchars )
	{
  		return htmlspecialchars( $this->tostring( $exp, $expchars ) );
	}

	/**
	 * @access public
	 */
	function tostring2( $exp, $depth )
	{
    	if ( $this->valtype( $exp ) == 'pair' )
		{
        	if ( !$depth )
	  			return '...';

			$result  = '(';
			$result .= $this->tostring2( $this->car( $exp ), $depth - 1 );
	
			for ( $rest = $this->cdr( $exp ); $rest[0] == 'pair'; $rest = $this->cdr( $rest ) )
	  			$result .= ' ' . $this->tostring2( $this->car( $rest ), $depth - 1 );
	
			if ( $rest[0] != 'empty' )
	    		$result .= ' . ' . $this->tostring( $rest, 'noexpchars' );

			$result .= ')';
			return $result;
    	}

    	return $this->tostring( $exp, 'noexpchars' );
	}

	/**
	 * @access public
	 */
	function tohtmlstring2( $exp )
	{
  		$stringdepth = 3;
  		return htmlspecialchars( $this->tostring2( $exp, $stringdepth ) );
	}

	/**
	 * @access public
	 */
	function lookup( $symbol, $layers )
	{
    	if ( $layers[0] == 'empty' )
			return -1;
    
    	$layer = $this->car( $layers );
    	$val   = $this->readfromenv( $layer, $symbol );
		
    	if ( is_array( $val ) )
      		return array( $layer, $val );
    
    	return $this->lookup( $symbol, $this->cdr( $layers ) );
	}

	/**
	 * @access public
	 */
	function sequence( $seq )
	{
    	$count = 0;
    	while ( $this->valtype( $seq ) == 'pair' )
		{
      		$this->compile( $this->car( $seq ) );
      		$seq = $this->cdr( $seq );
      
	  		if ( $this->valtype( $seq ) == 'pair' )
	  			$this->bcode[$this->bc++] = array( 'popargs', 1 );
      
      		$count++;
    	}

    	if ( $seq[0] != 'empty' )
		{
      		$this->bcode[$this->bc++] = array( 'error', 'parse error in sequence term ' . $count );
     	 	return;
    	}
	}

	/**
	 * @access public
	 */
	function codesegment( $current )
	{
  		$codeseg = array();
  
  		for ( $c = $current; $c < $this->bc; $c++ )
    		$codeseg[] = $this->bcode[$c];
  
  		$this->bc = $current;
  		return $codeseg;
	}

	/**
	 * @access public
	 */
	function handlespecial( $name, $args )
	{
    	switch ( $name )
		{
        	case 'apply':
				if ( $this->valtype( $args ) != 'pair' )
				{
	  				$this->bcode[$this->bc++] = array( 'error', "bad first arg to $name" );
	  				return;
				}
	
				if ( $this->valtype( $this->cdr( $args ) ) != 'pair' )
				{
	  				$this->bcode[$this->bc++] = array( 'error', "bad second arg to $name" );
	  				return;
				}
	
				if ( $this->valtype( $this->cdr( $this->cdr( $args ) ) ) != 'empty' )
				{
	  				$this->bcode[$this->bc++] = array( 'error', "too many args to $name" );
	  				return;
				}
	
				$this->compile( $this->car( $args ) );
				$this->bcode[$this->bc++] = array( 'checkptc' );
				$this->compile( $this->car( $this->cdr( $args ) ) );
				$this->bcode[$this->bc++] = array( 'listapplication' );
        
				break;

        	case  'call-with-current-continuation':
				if ( $this->valtype( $args ) != 'pair' )
				{
	  				$this->bcode[$this->bc++] = array( 'error', "bad first arg to $name" );
	  				return;
				}

				$t = $this->newthunk();

				$this->bcode[$this->bc++] = array( 'argptothunk', $t );
				$this->bcode[$this->bc++] = array( 'envptothunk', $t );
				$this->compile( $this->car( $args ) );
				$this->bcode[$this->bc++] = array( 'checkptc' );
				$this->bcode[$this->bc++] = array( 'toargs', $t );
				$this->bcode[$this->bc++] = array( 'application', 1 );
				$this->bcode[$this->bc++] = $t;
        
				break;

			case 'define': 
        
			case 'set!':
				if ( $this->valtype( $args ) != 'pair' )
				{
	  				$this->bcode[$this->bc++] = array( 'error', "bad first arg to $name" );
	  				return;
				}
	
				if ( $this->valtype( $this->car( $args ) ) != 'symbol' )
				{
	  				$this->bcode[$this->bc++] = array( 'error', "first arg to $name not a symbol" );
	  				return;
				}
	
				$this->bcode[$this->bc++] = array( 'toargs', $this->car( $args ) );
				
				if ( $this->valtype( $this->cdr( $args ) ) != 'pair' )
				{
	  				$this->bcode[$this->bc++] = array( 'error', "bad second arg to $name" );
	 	 			return;
				}
				
				if ( $name == 'define' )
				{
	  				$this->bcode[$this->bc++] = array( 'globalenv' );
	  				$this->compile( $this->car( $this->cdr( $args ) ) );
	  				$this->bcode[$this->bc++] = array( 'popenv', 1 );
				}
				else
				{
	  				$this->compile( $this->car( $this->cdr( $args ) ) );
				}
	
				$this->bcode[$this->bc++] = array( $name );
				break;

			case 'lambda':
				if ( $this->valtype( $args ) != 'pair' )
				{
	  				$this->bcode[$this->bc++] = array( 'error', 'bad first arg to lambda' );
	  				return;
				}
	
				$argstr  = $this->car( $args );
				$argtype = -1;
				
				if ( $this->valtype( $argstr ) == 'symbol' )
				{
	    			$argtype = 0;
				}
				else if ( $this->valtype( $argstr ) == 'pair' || $this->valtype( $argstr ) == 'empty' )
				{
	    			for ( $tocheck = $argstr, $count = 1; $this->valtype( $tocheck ) == 'pair'; $tocheck = $this->cdr( $tocheck ), $count++ )
					{
						if ( $this->valtype( $this->car( $tocheck ) ) != 'symbol' )
						{
		    				$msg = 'lambda arg ' . $count . ' not a symbol';
		    				break;
						}
	    			}
	    
					if ( $this->valtype( $tocheck ) == 'symbol' )
						$argtype = 1;
	    			else if ( $this->valtype( $tocheck ) == 'empty' )
						$argtype = 2;
	    			else
						$msg = 'lambda arg not symbol or null terminator:';
				}
				else
				{
	    			$msg = 'lambda single arg not a symbol';
				}
	
				if ( $argtype == -1 )
				{
	    			$this->bcode[$this->bc++] = array( 'error', $msg );
	    			return;
				}

				if ( $this->valtype( $this->cdr( $args ) ) == 'empty' )
				{
	  				$this->bcode[$this->bc++] = array( 'error', 'lambda body is empty' );
	  				return;
				}

				$current = $this->bc;
				$this->sequence( $this->cdr( $args ) );
				$lcode = $this->codesegment( $current );

				$this->bcode[$this->bc++] = array( 'toargs', $this->car( $args ) );  
				$this->bcode[$this->bc++] = array( 'toargs', $this->newval( 'bcode',  $lcode   ) );
				$this->bcode[$this->bc++] = array( 'toargs', $this->newval( 'number', $argtype ) );
				$this->bcode[$this->bc++] = array( 'closure' );
	
				break;

        	case 'begin':
				if ( $this->valtype( $args ) == 'empty' )
				{
	  				$this->bcode[$this->bc++] = array( 'toargs', $this->null() );
	  				return;
				}

				$this->sequence( $args );
        		break;

			case 'cond':
				for ( $clauses = array(), $elseclause = 0; $this->valtype( $args ) == 'pair'; $args = $this->cdr( $args ) )
				{
	    			$clause = $this->car( $args );

	    			if ( $this->valtype( $clause ) != 'pair' )
					{
						$this->bcode[$this->bc++] = array( 'error', 'bad cond clause' );
						return;
	    			}
	    
					$test  = $this->car( $clause );
	    			$ccode = $this->cdr( $clause );
					
	    			if ( $this->valtype( $ccode ) != 'pair' )
					{
						$this->bcode[$this->bc++] = array( 'error', 'empty cond clause' );
						return;
	    			}

	    			if ( $this->valtype( $test ) == 'symbol' && $this->valdata( $test ) == 'else' )
					{
						if ( is_array( $elseclause ) )
						{
		    				$this->bcode[$this->bc++] = array( 'error', 'cond: more than one else clause' );
		    				return;
						}
		
						$elseclause = $clause;
	    			}
	    			else
					{
						if ( is_array( $elseclause ) )
						{
		    				$this->bcode[$this->bc++] = array( 'error', 'cond: else clause must be last' );
		    				return;
						}
		
						$type  = 'seq';
						$first = $this->car( $ccode );
						
						if ( $this->valtype( $first ) == 'symbol' && $this->valdata( $first ) == '=>' )
						{
		    				$expr = $this->cdr( $ccode );
		    
							if ( $this->valtype( $expr ) == 'empty' )
							{
								$this->bcode[$this->bc++] = array( 'error', 'cond: empty => clause' );
								return;
		    				}
		    
							if ( $this->valtype( $this->cdr( $expr ) ) != 'empty' )
							{
								$this->bcode[$this->bc++] = array( 'error', 'cond: more than one expr in => clause' );
								return;
		    				}
		    
							$type = 'proc';
						}

						$clauses[] = array( $clause, $type );
	    			}
				}
	
				$count   = 0;
				$current = $this->bc;
	
				if ( is_array( $elseclause ) )
				{
	    			$this->sequence( $this->cdr( $elseclause ) );
	    			$elsecode = $this->codesegment( $current );
	    
	    			$this->bcode[$this->bc++] = array( 'toargs', $this->newval( 'string', 'else'    ) );
	    			$this->bcode[$this->bc++] = array( 'toargs', $this->newval( 'bcode',  $elsecode ) );
	    
					$count += 2;
				}

				for ( $c = count( $clauses ) - 1; $c >= 0; $c-- )
				{
	    			$clause = $clauses[$c][0];
	    			$type   = $clauses[$c][1];

	    			$current = $this->bc;
	    			$this->compile( $this->car( $clause ) );
	    			$tcode = $this->codesegment( $current );

	    			if ( $type == 'proc' )
					{
						$this->compile( $this->car( $this->cdr( $this->cdr( $clause ) ) ) );
						$code = $this->codesegment( $current );
	    			}
	    			else
					{
						$this->sequence( $this->cdr( $clause ) );
						$code = $this->codesegment( $current );
	    			}
	    
	    			$this->bcode[$this->bc++] = array( 'toargs', $this->newval( 'bcode',  $tcode ) );
	    			$this->bcode[$this->bc++] = array( 'toargs', $this->newval( 'string', $type  ) );
	    			$this->bcode[$this->bc++] = array( 'toargs', $this->newval( 'bcode',  $code  ) );
	    
					$count+=3;
				}

				$this->bcode[$this->bc++] = array( 'cond', $count );
				break;
	
			case 'case':
				if ( $this->valtype( $args ) != 'pair' )
				{
	  				$this->bcode[$this->bc++] = array( 'error', 'case value missing' );
	  				return;
				}
	
				$caseval = $this->car( $args );
				$this->compile( $caseval );

				for ( $clauses = array(), $elseclause = 0, $count = 0, $cl = $this->cdr( $args ); $this->valtype( $cl ) == 'pair'; $cl = $this->cdr( $cl ) )
				{
	    			$clause = $this->car( $cl );

	    			if ( $this->valtype( $clause ) != 'pair' )
					{
						$this->bcode[$this->bc++] = array( 'error', 'bad case clause' );
						return;
	    			}
	    
					$data = $this->car( $clause );
					
	    			if ( $this->valtype( $data ) != 'pair' && !( $this->valtype( $data ) == 'symbol' && $this->valdata( $data ) == 'else' ) )
					{
						$this->bcode[$this->bc++] = array( 'error', 'bad case data: ' . $this->tostring( $data, 0 ) );
						return;
	    			}
	    
					$ccode = $this->cdr( $clause );
	    
					if ( $this->valtype( $ccode ) != 'pair' )
					{
						$this->bcode[$this->bc++] = array( 'error', 'empty case clause' );
						return;
	    			}

	    			if ( $this->valtype( $data ) == 'symbol' && $this->valdata( $data ) == 'else' )
					{
						if ( is_array( $elseclause ) )
						{
		    				$this->bcode[$this->bc++] = array( 'error', 'case: more than one else clause' );
		    				return;
						}
		
						$elseclause = $clause;
	    			}
	    			else
					{
						if ( is_array( $elseclause ) )
						{
		    				$this->bcode[$this->bc++] = array( 'error', 'case: else clause must be last' );
		    				return;
						}
		
						$clauses[] = $clause;
	    			}

	    			$count++;
				}
	
				$current = $this->bc;
	
				if ( is_array( $elseclause ) )
				{
	    			$this->sequence( $this->cdr( $elseclause ) );
	    			$elsecode = $this->codesegment( $current );
	    
	    			$this->bcode[$this->bc++] = array( 'toargs', $this->car( $elseclause ) );
	    			$this->bcode[$this->bc++] = array( 'toargs', $this->newval( 'bcode', $elsecode ) );
				}

				for ( $c = count( $clauses ) - 1; $c >= 0; $c-- )
				{
	    			$clause  = $clauses[$c];
	    			$current = $this->bc;
	    
					$this->sequence( $this->cdr( $clause ) );
	    			$ccode = $this->codesegment( $current );
	    
	    			$this->bcode[$this->bc++] = array( 'toargs', $this->car( $clause ) );
	    			$this->bcode[$this->bc++] = array( 'toargs', $this->newval( 'bcode', $ccode ) );
				}

				$this->bcode[$this->bc++] = array( 'case', $count );
				break;

        	case 'if':
				if ( $this->valtype( $args ) != 'pair' )
				{
	  				$this->bcode[$this->bc++] = array( 'error', 'bad if condition' );
	  				return;
				}
	
				$ifcond = $this->car( $args );
				
				if ( $this->valtype( $this->cdr( $args ) ) != 'pair' )
				{
	  				$this->bcode[$this->bc++] = array( 'error', 'true clause missing from if' );
	  				return;
				}
	
				$iftrue = $this->car( $this->cdr( $args ) );
				
				if ( $this->valtype( $this->cdr( $this->cdr( $args ) ) ) != 'pair' )
	  				$iffalse = $this->cons( $this->newval( 'symbol', 'quote' ), $this->cons( $this->null(), $this->null() ) );
				else
	  				$iffalse = $this->car( $this->cdr( $this->cdr( $args ) ) );

				$this->compile( $ifcond );
				$current = $this->bc;
				$this->compile( $iftrue );
				$tcode = $this->codesegment( $current );
				$this->compile( $iffalse );
				$fcode = $this->codesegment( $current );

				$this->bcode[$this->bc++] = array( 'toargs', $this->newval( 'bcode', $tcode ) );
				$this->bcode[$this->bc++] = array( 'toargs', $this->newval( 'bcode', $fcode ) );
				$this->bcode[$this->bc++] = array( 'if' );

        		break;

        	case 'and':
        
			case 'or':
				$count   = 0;
				$current = $this->bc;
				$terms   = array();

				while ( $this->valtype( $args ) == 'pair' )
				{
	  				$this->compile( $this->car( $args ) );
	  				$terms[] = $this->codesegment( $current );
	  				$count++;
					$args = $this->cdr( $args );
				}
	
				for ( $tind = $count - 1; $tind >= 0; $tind-- )
				{
	  				$tcode = $terms[$tind];
	  				$this->bcode[$this->bc++] = array( 'toargs', $this->newval( 'bcode', $tcode ) );
				}

				$this->bcode[$this->bc++] = array( 'toargs', ( ( $name == 'and' )? $this->btrue() : $this->bfalse() ) );
				$this->bcode[$this->bc++] = array( $name, $count );
        
				break;

        	case 'quote':
				if ( $this->valtype( $args ) != 'pair' )
				{
	  				$this->bcode[$this->bc++] = array( 'error', 'quote missing an item' );
	  				return;
				}
				
				if ( $this->valtype( $this->cdr( $args ) ) != 'empty' )
				{
	  				$this->bcode[$this->bc++] = array( 'error', 'quote takes a single argument' );
	  				return;
				}
	
				$this->bcode[$this->bc++] = array( 'toargs', $this->car( $args ) );
        		break;
	
			case 'let':
        	
			case 'let*':
        
			case 'letrec':
				if ($this->valtype( $args ) != 'pair' )
				{
	  				$this->bcode[$this->bc++] = array( 'error', "bad first arg to $name" );
	  				return;
				}
				
				if ( $name == 'letrec' )
	  				$this->bcode[$this->bc++] = array( 'layer', 0 );
	
				for ( $bindings = $this->car( $args ), $count = 0; $this->valtype( $bindings ) == 'pair'; $bindings = $this->cdr( $bindings ) )
				{
	    			$binding = $this->car( $bindings );
					
	    			if ( $this->valtype( $binding ) != 'pair' )
					{
	      				$this->bcode[$this->bc++] = array( 'error', '$name binding ' . ( $count + 1 ) . ' bad' );
	      				return;
	    			}

	    			if ( $this->valtype( $this->car( $binding ) ) != 'symbol' )
					{
						$this->bcode[$this->bc++] = array( 'error', "first arg to $name binding " . ( $count + 1 ) . ' not a symbol' );
						return;
	    			}
	    			
					$this->bcode[$this->bc++] = array( 'toargs', $this->car( $binding ) );
	    			$count++;
	    			$this->compile( $this->car( $this->cdr( $binding ) ) );
	    
					if ( $name == 'let*' )
					{
	      				$this->bcode[$this->bc++] = array( 'layer', 1 );
	    			}
	    			else if ( $name == 'letrec' )
					{
	      				$this->bcode[$this->bc++] = array( 'define',  1 );
	      				$this->bcode[$this->bc++] = array( 'popargs', 1 );
	    			}
				}
				
				if ( $this->valtype( $bindings ) != 'empty' )
				{
	  				$this->bcode[$this->bc++] = array( 'error', "parse error at $name binding " . ( $count + 1 ) );
	  				return;
				}
	
				if ( $name == 'let' )
	  				$this->bcode[$this->bc++] = array( 'layer', $count );

				if ( $this->valtype( $this->cdr( $args ) ) == 'empty' )
				{
	  				$this->bcode[$this->bc++] = array( 'error', "$name body is empty" );
	  				return;
				}
				
				$this->sequence( $this->cdr( $args ) );

				if ( $name == 'let' || $name == 'letrec' )
	  				$this->bcode[$this->bc++] = array( 'popenv', 1 );
				else
	  				$this->bcode[$this->bc++] = array( 'popenv', $count );
	
				break;
		}
	}

	/**
	 * @access public
	 */
	function drawcmd( $name, $x, $y )
	{
  		global $imagedata;

  		if ( $x < $imagedata[0] )
      		$imagedata[0] = $x;
  
  		if ( $y < $imagedata[1] )
      		$imagedata[1] = $y;
  
  		if ( $x > $imagedata[2] )
      		$imagedata[2] = $x;
  
  		if ( $y > $imagedata[3] )
      		$imagedata[3] = $y;
  
  		if ( $name == 'draw-move' )
      		$imagedata[4][] = array( 0, $x, $y );
  		else
      		$imagedata[4][] = array( 1, $x, $y );
	}

	/**
	 * @access public
	 */
	function len( $l )
	{
    	$len = 0; 
    	while ( $this->valtype( $l ) == 'pair' )
		{
			$len++;
			$l = $this->cdr( $l );
    	}
    
		return $len;
	}

	/**
	 * @access public
	 */
	function applyprimitive( $name, $argc )
	{
  		global $imagedata;

  		switch ( $name )
		{
  			case 'sin':
  
  			case 'cos':
  
  			case 'sqrt':
      			if ( $argc != 1 )
				{
	  				$this->errmsg = "$name requires one argument";
	  				return -1;
      			}
      
	  			$a = $this->argstack[$this->argp - 1];
      
	  			if ( $this->valtype( $a ) != 'number' )
				{
	  				$this->errmsg = "first arg to $name not a number";
	  				return -1;
      			}

      			$av = $this->valdata( $a );

      			if ( $name == 'sin' )
	  				return $this->newval( 'number', sin( $av ) );
      			else if ( $name == 'cos' )
	  				return $this->newval( 'number', cos( $av ) );
      
      			if ( $av < 0 )
				{
	  				$this->errmsg = "arg to $name must not be negative";
	  				return -1;
      			}
      
	  			return $this->newval( 'number', sqrt( $av ) );
  				break;

  			case 'draw-move':
  
  			case 'draw-line':
      			if ( $argc != 2 )
				{
	  				$this->errmsg = "$name requires two arguments";
	  				return -1;
      			}
      
	  			$a = $this->argstack[$this->argp - 2];
      
	  			if ( $this->valtype( $a ) != 'number' )
				{
	  				$this->errmsg = "first arg to $name not a number";
	  				return -1;
      			}
      
	  			$b = $this->argstack[$this->argp - 1];
      
	  			if ( $this->valtype( $b ) != 'number' )
				{
	  				$this->errmsg = "second arg to $name not a number";
	  				return -1;
      			}

      			$av = $this->valdata( $a );
      			$bv = $this->valdata( $b );

      			if ( !count( $imagedata[4] ) )
				{
	  				if ( $name == 'draw-line' )
					{
	      				$imagedata[0] = 0;
	      				$imagedata[1] = 0;
	      				$imagedata[2] = 0;
	      				$imagedata[3] = 0;
	  				}
	  				else
					{
	      				$imagedata[0] = $av;
	      				$imagedata[1] = $bv;
	      				$imagedata[2] = $av;
	      				$imagedata[3] = $bv;
	  				}
      			}
      
	  			$this->drawcmd( $name, $av, $bv );
      			return $this->null();
  
  				break;
  
  			case 'draw-color':
      			if ( $argc != 1 )
				{
	  				$this->errmsg = "$name requires one argument";
	  				return -1;
      			}
      
	  			$c = $this->argstack[$this->argp - 1];
      
	  			if ( $this->len( $c ) != 3 )
				{
	  				$this->errmsg = "$name requires a list; form: (R, G, B)";
	  				return -1;
      			}
      
	  			$red = $this->car( $c );
      
	  			if ( $this->valtype( $red ) != 'number' )
				{
	  				$this->errmsg = "$name: red component not a number";
	  				return -1;
      			}
      
	  			$green = $this->car( $this->cdr( $c ) );
      
	  			if ( $this->valtype( $green ) != 'number' )
				{
	  				$this->errmsg = "$name: green component not a number";
	  				return -1;
      			}
      
	  			$blue = $this->car( $this->cdr( $this->cdr( $c ) ) );
      
	  			if ( $this->valtype( $blue ) != 'number' )
				{
	  				$this->errmsg = "$name: blue component not a number";
	  				return -1;
      			}
      
	  			$imagedata[4][] = array( 
					2, 
					$this->valdata( $red   ), 
					$this->valdata( $green ), 
					$this->valdata( $blue  )
				);
      
	  			return $this->null();
  				break;
  
  			case 'quotient':
      			if ( $argc != 2 )
				{
	  				$this->errmsg = 'quotient requires two arguments';
	  				return -1;
      			}
      
	  			$a = $this->argstack[$this->argp - 2];
      
	  			if ( $this->valtype( $a ) != 'number' )
				{
	  				$this->errmsg = 'first arg to quotient not a number';
	  				return -1;
      			}
      
	  			$p = (int)$this->valdata( $a );
      
	  			if ( $p != $this->valdata( $a ) )
				{
	  				$this->errmsg = 'first arg to quotient not an integer';
	  				return -1;
      			}

      			$b = $this->argstack[$this->argp - 1];
      
	  			if ( $this->valtype( $a ) != 'number' )
				{
	  				$this->errmsg = 'second arg to quotient not a number';
	  				return -1;
      			}
      
	  			$q = (int)$this->valdata( $b );
				
      			if ( $q != $this->valdata( $b ) )
				{
	  				$this->errmsg = 'second arg to quotient not an integer';
	  				return -1;
      			}
      
	  			if ( !$q )
				{
	  				$this->errmsg = 'second arg to quotient must not be zero';
	  				return -1;
      			}

      			return $this->newval( 'number', (int)( $p / $q ) );
  				break;
  
  			case 'remainder':
      			if ( $argc != 2 )
				{
	  				$this->errmsg = 'remainder requires two arguments';
	  				return -1;
      			}
      
	  			$a = $this->argstack[$this->argp - 2];
      
	  			if ( $this->valtype( $a ) != 'number' )
				{
	  				$this->errmsg = 'first arg to remainder not a number';
	  				return -1;
      			}
      
	  			$p = (int)$this->valdata( $a );
				
      			if ( $p != $this->valdata( $a ) )
				{
	  				$this->errmsg = 'first arg to remainder not an integer';
	  				return -1;
      			}

      			$b = $this->argstack[$this->argp - 1];
      
	  			if ( $this->valtype( $a ) != 'number' )
				{
	  				$this->errmsg = 'second arg to remainder not a number';
	  				return -1;
      			}
      
	  			$q = (int)$this->valdata( $b );
				
      			if ( $q != $this->valdata( $b ) )
				{
	  				$this->errmsg = 'second arg to remainder not an integer';
	  				return -1;
      			}
      
	  			if ( !$q )
				{
	  				$this->errmsg = 'second arg to remainder must not be zero';
	  				return -1;
      			}

      			return $this->newval( 'number', $p % $q );
  				break;
  
  			case 'eqv?':
  
  			case 'eq?':
    			if ( $argc != 2 )
				{
      				$this->errmsg = '$name requires two arguments';
      				return -1;
    			}
    
				$itema = $this->argstack[$this->argp - 2];
    			$itemb = $this->argstack[$this->argp - 1];
    
    			if ( $this->valtype( $itema ) != $this->valtype( $itemb ) )
      				return $this->bfalse();    

    			return ( ( $this->valdata( $itema ) == $this->valdata( $itemb ) )? $this->btrue() : $this->bfalse() );
  
  			case 'pair?':
    			if ( $argc != 1 )
				{
      				$this->errmsg = 'pair? requires one argument';
      				return -1;
    			}
    
				$item = $this->argstack[$this->argp - $argc];
    			return ( ( $this->valtype( $item ) == 'pair' )? $this->btrue() : $this->bfalse() );
  
  			case 'number?':
    			if ( $argc != 1 )
				{
      				$this->errmsg = 'number? requires one argument';
      				return -1;
    			}
    
				$item = $this->argstack[$this->argp - $argc];
    			return ( ( $this->valtype( $item ) == 'number' )? $this->btrue() : $this->bfalse() );

  			case 'zero?':
    			if ( $argc != 1 )
				{
      				$this->errmsg = 'zero? requires one argument';
      				return -1;
    			}
    
				$item = $this->argstack[$this->argp - $argc];
				
    			if ( $this->valtype( $item ) != 'number' )
				{
      				$this->errmsg = 'zero? requires a numeric argument';
      				return -1;
    			}
    
				return ( $this->valdata( $item )? $this->bfalse() : $this->btrue() );
				
  			case 'not':
    			if ( $argc != 1 )
				{
      				$this->errmsg = 'not requires one argument';
      				return -1;
    			}
    
				$item = $this->argstack[$this->argp - $argc];

    			if ( $this->valtype( $item ) == 'boolean' && $this->valdata( $item ) == '#f' )
      				return $this->btrue();
    
    			return $this->bfalse();
  
  			case 'list':
    			for ( $res = $this->null(), $c = 1; $c <= $argc; $c++ )
      				$res = $this->cons( $this->argstack[$this->argp - $c], $res );
    
    			return $res;
  
  			case 'cons':
    			if ( $argc != 2 )
				{
      				$this->errmsg = 'cons requires two arguments';
      				return -1;
    			}
				
    			$a = $this->argstack[$this->argp - 2];
    			$b = $this->argstack[$this->argp - 1];
    
				return $this->cons( $a, $b );
				
  			case 'set-car!':
    			if ( $argc != 2 )
				{
      				$this->errmsg = 'set-car! requires two arguments';
      				return -1;
    			}
    			
				$p = $this->argstack[$this->argp - 2];
    
				if ( $this->valtype( $p ) != 'pair' )
				{
      				$this->errmsg = 'first argument to set-car! must be a pair';
      				return -1;
    			}
    
				$v = $this->argstack[$this->argp - 1];
    			$this->setcar( $p, $v );
    			return $v;
  
  			case 'set-cdr!':
    			if ( $argc != 2 )
				{
      				$this->errmsg = 'set-cdr! requires two arguments';
      				return -1;
    			}
    
				$p = $this->argstack[$this->argp - 2];
    
				if ( $this->valtype( $p ) != 'pair' )
				{
      				$this->errmsg = 'first argument to set-cdr! must be a pair';
      				return -1;
    			}
    
				$v = $this->argstack[$this->argp - 1];
    			$this->setcdr( $p, $v );
    			return $v;
  
  			case 'car':
    			if ( $argc != 1 )
				{
      				$this->errmsg = 'car takes a single argument';
      				return -1;
    			}
    
				$p = $this->argstack[$this->argp - 1];
    
				if ( $this->valtype( $p ) != 'pair' )
				{
      				$this->errmsg = 'argument to car must be a pair';
      				return -1;
    			}
    
				return $this->car( $p );
  
  			case 'cdr':
    			if ( $argc != 1 )
				{
      				$this->errmsg = 'cdr takes a single argument';
      				return -1;
    			}
    
				$p = $this->argstack[$this->argp - 1];
				
    			if ( $this->valtype( $p ) != 'pair' )
				{
      				$this->errmsg = 'argument to cdr must be a pair';
      				return -1;
    			}
    
				return $this->cdr( $p );
				
  			case 'null?':
    			if ( $argc != 1 )
				{
      				$this->errmsg = 'null takes a single argument';
      				return -1;
    			}
    
				$p = $this->argstack[$this->argp - 1];
    
				if ( $this->valtype( $p ) == 'empty' )
      				return $this->newval( 'boolean', '#t' );
    
    			return $this->newval( 'boolean', '#f' );
  
  			case 'display':
    			if ( $argc != 1 )
				{
      				$this->errmsg = 'display requires one argument';
      				return -1;
    			}
    
				$item = $this->argstack[$this->argp - $argc];
    			$this->outputstr .= $this->tohtmlstring( $item, 'expchars' );
    			
				return $this->null();
  
  			case 'newline':
    			if ( $argc )
				{
      				$this->errmsg = 'newline takes no arguments';
      				return -1;
    			}
				
    			$this->outputstr .= "\n";
    			return $this->null();
  
  			case '+':
    			if ( !$argc )
      				return $this->newval( 'number', 0 );
    
    			$item = $this->argstack[$this->argp - $argc];
    
				if ( $this->valtype( $item ) != 'number' )
				{
      				$this->errmsg = 'first arg to + not a number';
      				return -1;
    			}
    
				for ( $res = $this->valdata( $item ), $c = 1; $c < $argc; $c++ )
				{
      				$item = $this->argstack[$this->argp - $argc + $c];
      
	  				if ( $this->valtype( $item ) != 'number' )
					{
						$this->errmsg = 'arg ' . ( $c + 1 )  . ' to + not a number';
						return -1;
      				}
      
	  				$res += $this->valdata( $item );
    			}
    
				return $this->newval( 'number', $res );
  
  			case '*':
    			if ( !$argc )
      				return $this->newval( 'number', 1 );
    
    			$item = $this->argstack[$this->argp - $argc];
    
				if ( $this->valtype( $item ) != 'number' )
				{
      				$this->errmsg = 'first arg to * not a number';
      				return -1;
    			}
    
				for ( $res = $this->valdata( $item ), $c = 1; $c < $argc; $c++ )
				{
      				$item = $this->argstack[$this->argp - $argc + $c];
      
	  				if ( $this->valtype( $item ) != 'number' )
					{
						$this->errmsg = 'arg ' . ( $c + 1 )  . ' to * not a number';
						return -1;
      				}
      
	  				$res *= $this->valdata( $item );
    			}
    
				return $this->newval( 'number', $res );
  
  			case '-':
    			if ( !$argc )
				{
      				$this->errmsg = '- requires at least one argument';
      				return -1;
    			}
				
    			$item = $this->argstack[$this->argp - $argc];
    
				if ( $this->valtype( $item ) != 'number' )
				{
      				$this->errmsg = 'first arg to - not a number';
     	 			return -1;
    			}
    
				for ( $res = $this->valdata( $item ), $c = 1; $c < $argc; $c++ )
				{
      				$item = $this->argstack[$this->argp - $argc + $c];
      
	  				if ( $this->valtype( $item ) != 'number' )
					{
						$this->errmsg = 'arg ' . ( $c + 1 )  . ' to - not a number';
						return -1;
      				}
      
	  				$res -= $this->valdata( $item );
   	 			}
    
				return $this->newval( 'number', ( ( $argc == 1 )? -$res : $res ) );
  
  			case '/':
    			if ( !$argc )
				{
      				$this->errmsg = '/ requires at least one argument';
      				return -1;
    			}
    
				$item = $this->argstack[$this->argp - $argc];
				
    			if ( $this->valtype( $item ) != 'number' )
				{
      				$this->errmsg = 'first arg to - not a number';
      				return -1;
    			}
    
				for ( $res = $this->valdata( $item ), $c = 1; $c < $argc; $c++ )
				{
      				$item = $this->argstack[$this->argp - $argc + $c];
      
	  				if ( $this->valtype( $item ) != 'number' )
					{
						$this->errmsg = 'arg ' . ( $c + 1 )  . ' to - not a number';
						return -1;
      				}
					
      				$res /= $this->valdata( $item );
    			}
    
				return $this->newval( 'number', ( ( $argc == 1 )? 1 / $res : $res ) );
  
  			case '=':
    			$item = $this->argstack[$this->argp - $argc];
				
    			if ( $this->valtype( $item ) != 'number' )
				{
      				$this->errmsg = 'first arg to = not a number';
      				return -1;
    			}
				
    			for ( $res = $this->valdata( $item ), $c = 1; $c < $argc; $c++ )
				{
      				$item = $this->argstack[$this->argp - $argc + $c];
      
	  				if ( $this->valtype( $item ) != 'number' )
					{
						$this->errmsg = 'arg ' . ( $c + 1 )  . ' to = not a number';
						return -1;
      				}
      
	  				if ( $res != $this->valdata( $item ) )
						return $this->newval( 'boolean', '#f' );
    			}
    
				return $this->newval( 'boolean', '#t' );
				
  			case '>':
    			if ( $argc < 2 )
				{
      				$this->errmsg = '> requires at least two arguments';
      				return -1;
    			}
    
				$current = $this->argstack[$this->argp - $argc];
    
				if ( $this->valtype( $current ) != 'number' )
				{
      				$this->errmsg = 'first arg to - not a number';
      				return -1;
    			}
    
				for ( $c = 1; $c < $argc; $c++ )
				{
      				$item = $this->argstack[$this->argp - $argc + $c];
      
	  				if ( $this->valtype( $item ) != 'number' )
					{
						$this->errmsg = 'arg ' . ( $c + 1 )  . ' to > not a number';
						return -1;
      				}
      
	  				if ( $this->valdata( $current ) <= $this->valdata( $item ) )
						return $this->bfalse();
      
      				$current = $item;
    			}
    
				return $this->btrue();
  
  			case '<':
    			if ( $argc < 2 )
				{
      				$this->errmsg = '< requires at least two arguments';
      				return -1;
    			}
    
				$current = $this->argstack[$this->argp - $argc];
    
				if ( $this->valtype( $current ) != 'number' )
				{
      				$this->errmsg = 'first arg to - not a number';
      				return -1;
    			}
    
				for ( $c = 1; $c < $argc; $c++ )
				{
      				$item = $this->argstack[$this->argp - $argc + $c];
      
	  				if ( $this->valtype( $item ) != 'number' )
					{
						$this->errmsg = 'arg ' . ( $c + 1 )  . ' to < not a number';
						return -1;
      				}
      
	  				if ( $this->valdata( $current ) >= $this->valdata( $item ) )
						return $this->bfalse();
      
      				$current = $item;
    			}
    
				return $this->btrue();
  		}
	}

	/**
	 * @access public
	 */
	function printargstack()
	{
  		for ( $p = 0; $p < $this->argp; $p++ )
    		echo $this->tohtmlstring( $this->argstack[$p], 'noexpchars' ) . " ";
  
  		echo "\n";
	}

	/**
	 * @access public
	 */
	function insertcode( $prev, $code, $tag )
	{
    	$this->codestack[$this->codep - 1][1] = $prev;
    	$this->codestack[$this->codep] = array( $code, -1, count( $code ), $tag );
    	$this->codep++;
	}

	/**
	 * @access public
	 */
	function findmarkforward( $obj )
	{
    	$type = $this->valtype( $obj );
		$tag  = $this->valdata( $obj );

    	while ( $this->codep > 0 )
		{
			$bcode     = $this->codestack[$this->codep - 1][0];
			$searchpos = $this->codestack[$this->codep - 1][1]; 
			$mx        = $this->codestack[$this->codep - 1][2];
	
			while ( $searchpos < $mx )
			{
	    		if ( $this->valtype( $bcode[$searchpos] ) != $type || $this->valdata( $bcode[$searchpos] ) != $tag )
					$searchpos++;
	    		else
					return $searchpos;
			}
		
			$this->codep--;
    	}

    	return -1;
	}

	/**
	 * @access public
	 */
	function run( $stacktrace = 0 )
	{
    	$this->codestack = array( array( $this->bcode, 0, $this->bc, -1, 0 ) ); 
    	$this->codep = 1;

    	$this->argstack = array();
    	$this->argp = 0;

    	$b = 0;
    	while ( 1 )
		{
			$instr = $this->codestack[$this->codep-1][0][$b];

			// echo $b . ' ' . $instr[0] . "<BR>\n";
			if ( $stacktrace && $instr[0] != 'start' )
			{
	  			echo "<B>&gt;</B> ";
	  			$this->printargstack();
			}

			switch ( $instr[0] )
			{
	    		case 'cond':
            		$count  = $instr[1];
            		$type   = $this->valdata( $this->argstack[$this->argp - 2] );
	    			$code   = $this->valdata( $this->argstack[$this->argp - 1] );

	    			if ( $type == 'else' )
					{
						$this->insertcode( $b, $code, -1 );
						$b = -1;

						unset( $this->argstack[$this->argp - 1] );
						$this->argp--;
						
						unset( $this->argstack[$this->argp - 1] );
						$this->argp--;
	    			}
	    			else
					{
						$tcode = $this->valdata( $this->argstack[$this->argp - 3] );
		
						$this->insertcode( $b, array( array( 'cond1', $count ) ), -1 );
						$this->insertcode( -1, $tcode, -1 );
						
						$b = -1;
	    			}
	    
					break;

	    		case 'cond1':
	    			$count = $instr[1];
            		$type  = $this->valdata( $this->argstack[$this->argp - 3] );
	    			$code  = $this->valdata( $this->argstack[$this->argp - 2] );
	    			$tres  = $this->argstack[$this->argp - 1];
	    
	    			if ( !$this->isfalse( $tres ) )
					{
						if ( $type == 'proc' )
						{
		    				$pcode   = array();
		    				$pcode[] = array( 'checkptc', $tres );
		    				$pcode[] = array( 'toargs',   $tres );
		    				$pcode[] = array( 'application', 1  );
		    				
							$this->insertcode( $b, $pcode, -1 );
		    				$this->insertcode( -1, $code,  -1 );
						}
						else
						{
		    				$this->insertcode( $b, $code, -1 );
						}
		
						$b = -1;
						$topop = $count + 1;
	    			}
	    			else
					{
						if ( $count >= 3 )
						{
		    				$this->insertcode( $b, array( array( 'cond', $count - 3 ) ), -1 );
		    				$b = -1;
						}

						$topop = 4;
	    			}

	    			for ( $c = 0; $c < $topop; $c++ )
					{
						unset( $this->argstack[$this->argp - 1] );
						$this->argp--;
	    			}

	    			break;

	    		case 'case':
            		$count   = $instr[1];
            		$caseval = $this->argstack[$this->argp - 1 - 2 * $count];
	    			$casevaltype = $this->valtype( $caseval );
	    			$casevaldata = $this->valdata( $caseval );


	    			for ( $c = 0; $c < $count; $c++ )
					{
						$cases = $this->argstack[$this->argp - 2];
						$code  = $this->argstack[$this->argp - 1];
						$match = 0;
						
						if ( $this->valtype( $cases ) == 'symbol' )
						{
		    				$match = 1;
						}
						else
						{
		    				while ( $this->valtype( $cases ) == 'pair' )
							{
								$item = $this->car( $cases );
			
								if ( $this->valtype( $item ) == $casevaltype && $this->valdata( $item ) == $casevaldata )
								{
			    					$match = 1;
			    					break;
								}
			
								$cases = $this->cdr( $cases );
		    				}
						}
		
						if ( $match )
						{
		    				$this->insertcode( $b, $this->valdata( $code ), -1 );
		    				$b = -1;
		    
							break;
						}

						unset( $this->argstack[$this->argp - 1] );
						$this->argp--;
						
						unset( $this->argstack[$this->argp - 1] );
						$this->argp--;
	    			}

	    			while ( $c < $count )
					{
						unset( $this->argstack[$this->argp - 1] );
						$this->argp--;
						
						unset( $this->argstack[$this->argp - 1] );
						$this->argp--;
		
						$c++;
	    			}

	    			unset( $this->argstack[$this->argp - 1] );
					$this->argp--;
	    
					break;

	    		case 'thunk':
	    			$this->writeargptothunk( $instr, -1 );
	   	 			break;

	    		case 'argptothunk':
	    			$this->writeargptothunk( $instr[1], $this->argp );
	    			break;

	    		case 'envptothunk':
	    			$this->writeenvptothunk( $instr[1], $this->envp );
	    			break;

	    		case 'error':
	    			$this->errmsg = $instr[1];
					return -1;

	    		case 'start':
	    			$this->envstack = array( $this->initialenv );
	    			$this->envp = 1;
	    
					break;
	    
	    		case 'if':
	    			if ( $this->isfalse( $this->argstack[$this->argp - 3] ) )
	      				$icode = $this->argstack[$this->argp - 1];
	    			else
	      				$icode = $this->argstack[$this->argp - 2];

	    			$this->insertcode( $b, $this->valdata( $icode ), -1 );
	    			$b = -1;

	    			unset( $this->argstack[$this->argp - 1] );
	    			unset( $this->argstack[$this->argp - 2] );
	    			unset( $this->argstack[$this->argp - 3] );
	    
					$this->argp -= 3;
					break;

	    		case 'and':
	    
				case 'or':
	    			$op    = $instr[0];
					$count = $instr[1];
	    
					if ( $this->valtype( $this->argstack[$this->argp - 1] ) != 'boolean' )
					{
	      				$this->errmsg = 'boolean required in ' . $op . '; got ' . $this->valtype( $this->argstack[$this->argp - 1] );
	      				return -1;
	    			}

	    			$bool = $this->argstack[$this->argp - 1];
	    
					if ( ( $op == 'and' && $this->valdata( $bool ) == '#t' ) || ( $op == 'or'  && $this->valdata( $bool ) == '#f' ) )
					{
	      				if ( $count )
						{
							$tcode = $this->valdata( $this->argstack[$this->argp - 2] );
							$this->insertcode( $b, array( array( $op, $count - 1 ) ), -1 );
							$this->insertcode( -1, $tcode, -1 );
							$b = -1;
		
							unset( $this->argstack[$this->argp - 1] );
							$this->argp--;
							
							unset( $this->argstack[$this->argp - 1] );
							$this->argp--;
	      				}
	    			}
	    			else
					{
	      				unset( $this->argstack[$this->argp - 1] );
						$this->argp--; // boolean
	      
		  				while ( $count > 0 )
						{
							unset( $this->argstack[$this->argp - 1] );
							$this->argp--;
							$count--;
	      				}
	      
		  				$this->argstack[$this->argp++] = $bool;
	    			}
	    
					break;

	    		case 'closure':
	    			$cl = $this->newclosure(
						$this->argstack[$this->argp - 3],
			     		$this->argstack[$this->argp - 2],
			     		$this->argstack[$this->argp - 1],
			     		$this->envstack[$this->envp - 1]
					);

	    			unset( $this->argstack[$this->argp - 1] );
	    			unset( $this->argstack[$this->argp - 2] );
	    			$this->argp -= 2;
	    			$this->argstack[$this->argp-1] = $cl;
	    
					break;

	    		case 'layer':
	    			$newlayer = array();

            		for ( $p = $this->argp - 2; $p >= $this->argp - 2 * $instr[1]; $p-=2 )
					{
	      				$newlayer[$this->argstack[$p][1]] = $this->argstack[$p + 1];
	      				
						unset( $this->argstack[$p] );
						unset( $this->argstack[$p + 1] );
	    			}
	    
					$this->argp -= 2 * $instr[1];
	    			$this->envstack[$this->envp] = $this->cons( $this->newenv( $newlayer ), $this->envstack[$this->envp - 1] );
	    			$this->envp++;
	    
					break;

	    		case 'listapplication':
            		$argl = $this->argstack[$this->argp - 1];
	    
					if ( $this->valtype( $argl ) != 'empty' && $this->valtype( $argl ) != 'pair' )
					{
	      				$this->errmsg = 'second arg to apply not a list';
	      				return -1;
	    			}
	    
					unset( $this->argstack[--$this->argp] );
	    			$argc = 0;
					
	    			while ( $this->valtype( $argl ) == 'pair' )
					{
	      				$this->argstack[$this->argp++] = $this->car( $argl );
	      				$argc++;
	      				$argl = $this->cdr( $argl );
	    			}
	    
				// pass through to application
	    		case 'application':
	    			if ( $instr[0] == 'application' )
						$argc = $instr[1];

	    			$op = $this->argstack[$this->argp - 1 - $argc];
	    
					if ( $this->valtype( $op ) == 'primitive' )
					{
	      				$res = $this->applyprimitive( $this->valdata( $op ), $argc );
	      
		  				if ( !is_array( $res ) )
							return -1;
	      
	      				$newargp = $this->argp - $argc - 1;
	    			}
	    			else if ( $this->valtype( $op ) == 'thunk' )
					{
	      				if ( $argc != 1 )
						{
							$this->errmsg = 'continuation requires a single argument';
							return -1;
	      				}
	      
		  				if ( $this->readargpfromthunk( $op ) == -1 )
						{
							$this->errmsg = 'thunk #' . $this->valdata( $op ) . ' has expired';
							return -1;
	      				}

	      				$this->codestack[$this->codep - 1][1] = $b;
	      				$b = $this->findmarkforward( $op );
						$newenvp = $this->readenvpfromthunk( $op );
						
	      				while ( $this->envp > $newenvp )
							unset( $this->envstack[--$this->envp] );

	      				$res = $this->argstack[$this->argp - 1];
	      				$newargp = $this->readargpfromthunk( $op );
	    			}
	    			else
					{
	      				$newlayer = array();
	      				$argl = $this->closureargs( $op );
						
	      				if ( $this->valdata( $this->closureargtype( $op ) ) > 0 )
						{
							for ( $p = $this->argp - $argc; $this->valtype( $argl ) == 'pair'; $p++, $argl = $this->cdr( $argl ) )
							{
		  						if ( $p >= $this->argp )
								{
		    						$this->errmsg = 'not enough arguments';
		    						return -1;
		  						}
		  
		  						$newlayer[$this->valdata( $this->car( $argl ) )] = $this->argstack[$p];
							}
		
							if ( $this->valdata( $this->closureargtype( $op ) ) == 1 )
							{
		  						$items = array();
		  
		  						while ( $p < $this->argp )
								{
		    						$items[] = $this->argstack[$p];
		    						$p++;
		  						}
		  
		  						$newlayer[$this->valdata( $argl )] = $this->array2list( $items );
							}
							else if ( $p < $this->argp )
							{
		  						$this->errmsg = 'too many arguments';
		  						return -1;
							}
	      				}
	      				else
						{
							for ( $p = $this->argp - $argc, $items = array(); $p < $this->argp; $p++ )
								$items[] = $this->argstack[$p];
		
							$newlayer[$this->valdata( $argl )] = $this->array2list( $items );
	      				}

	      				$tag = $this->closuretag( $op );
	      				$tailrec = 0;
						$this->codestack[$this->codep-1][1] = $b;
	      				$popcount = 0;
						
	      				for ( $cp = $this->codep - 1; $cp >= 0; $cp-- )
						{
		  					$pos = $this->codestack[$cp][1] + 1;
		  					$mx  = $this->codestack[$cp][2];
		  
		  					while ( $pos < $mx )
							{
		      					$instr = $this->codestack[$cp][0][$pos];
		      
			  					if ( $this->valtype( $instr ) == 'popenv' )
								{
			  						$popcount += $this->valdata( $instr );
			  						$pos++;
		      					}
		      					else
								{
			  						break;
		      					}
		  					}
		  
		  					if ( $pos < $mx )
		      					break;
		  
		  					if ( $this->codestack[$cp][3] == $tag )
							{
		      					$tailrec = 1;
		      					break;
		  					}
	      				}

	      				if ( $tailrec )
						{
		  					$this->envp -= $popcount;
		  					$this->envstack[$this->envp-1] = $this->cons( $this->newenv( $newlayer ), $this->closureenv( $op ) );
		  					$this->codep = $cp + 1;
	      				}
	      				else
						{
		  					$this->envstack[$this->envp] = $this->cons( $this->newenv( $newlayer ), $this->closureenv( $op ) );
		  					$this->envp++;

		  					$lcode = $this->valdata( $this->closurebody( $op ) );
		  					$this->insertcode( $b, array( array( 'popenv', 1 ) ), -1 );
		  					$this->insertcode( -1, $lcode, $tag );
	      				}
	      
		  				$b = -1;
	      				$newargp = $this->argp - $argc - 1;
	    			}

	    			while ( $this->argp > $newargp )
	      				unset( $this->argstack[--$this->argp] );

	    			if ( $this->valtype( $op ) == 'primitive' || $this->valtype( $op ) == 'thunk' )
					{
	      				$this->argstack[$this->argp] = $res;
	      				$this->argp++;
	    			}
	    
					break;

	    		case 'toargs':
	    			$this->argstack[$this->argp] = $instr[1];
	    			$this->argp++;
	    			break;

	    		case 'popargs':
	    			unset( $this->argstack[$this->argp - 1] );
	    			$this->argp--;
	    			break;

	    		case 'popenv':
            		$count = $instr[1];
	    
					while ( $count > 0 )
					{
	      				unset( $this->envstack[$this->envp - 1] );
	      				$this->envp--;
	      				$count--;
	    			}
	    
					break;

	    		case 'globalenv':
	    			$this->envstack[$this->envp] = $this->envstack[0];
	    			$this->envp++;
	    			break;

	    		case 'checkptc':
	    			$item = $this->argstack[$this->argp - 1];
	    
					if ( $this->valtype( $item ) != 'primitive' && 
						 $this->valtype( $item ) != 'closure'   &&
						 $this->valtype( $item ) != 'thunk' )
					{
						$this->errmsg = 'primitive, closure or thunk required';
						return -1;
	    			}
	    
					break;

	    		case 'lookup':
	    			$item = $this->argstack[$this->argp - 1];
	    			$res  = $this->lookup( $this->valdata( $item ), $this->envstack[$this->envp - 1] );
	    
					if ( !is_array( $res ) )
					{
						$this->errmsg = "symbol " . $this->valdata( $item ) . " not bound";
						return -1;
	    			}
	    
					$this->argstack[$this->argp - 1] = $res[1];
	    			break;

	    		case 'define':
	    
				case 'set!':
            		$val = $this->argstack[--$this->argp];
					unset( $this->argstack[$this->argp] );
	    			$sym = $this->argstack[$this->argp - 1];
	    			$env = $this->car( $this->envstack[$this->envp - 1] );
	    
					if ( $instr[0] == 'set!' )
					{
	      				$res = $this->lookup( $this->valdata( $sym ), $this->envstack[$this->envp - 1] );
	      
		  				if ( is_array( $res ) )
							$env = $res[0];
	    			}
	    
					$this->writetoenv( $env, $this->valdata( $sym ), $val );
	    			break;

	    		default:
	    			$this->errmsg = "instruction $instr[0] unknown (codestack: $this->codep, position: $b)<BR>\n";
	    			return -1;
			}

			$b++;
			while ( $b == $this->codestack[$this->codep - 1][2] )
			{
	    		$this->codep--;
	    
				if ( !$this->codep )
					break;
	    
	    		$b = $this->codestack[$this->codep-1][1] + 1;
			}

			if ( !$this->codep )
	    		break;
    	}

    	return false;
	}

	/**
	 * @access public
	 */
	function compile( $exp )
	{
    	if ( $exp[0] == 'pair' )
		{
			$toapply = $this->car( $exp );
			
			if ( $toapply[0] == 'symbol' )
			{
	    		if ( isset( $this->specialforms[$toapply[1]] ) )
				{
					$this->handlespecial( $toapply[1], $this->cdr( $exp ) );
					return;
	    		}
			}
	
			for ( $item = $exp, $count = 0; $this->valtype( $item ) == 'pair'; $item = $this->cdr( $item ) )
			{
	    		$count++;
	    		$this->compile( $this->car( $item ) );
	    
				if ( $count == 1 )
					$this->bcode[$this->bc++] = array( 'checkptc' );
			}
	
			if ( $this->valtype( $item ) != 'empty' )
			{
	    		$this->bcode[$this->bc++] = array( 'error', 'application not a proper list' );
	    		return;
			}
			
			$this->bcode[$this->bc++] = array( 'application', $count - 1 );
    	}
    	else if ( $exp[0] == 'symbol' )
		{
			$this->bcode[$this->bc++] = array( 'toargs', $exp );
			$this->bcode[$this->bc++] = array( 'lookup' );
    	}
    	else
		{
			$this->bcode[$this->bc++] = array( 'toargs', $exp );
    	}
	}
} // END OF Scheme

?>

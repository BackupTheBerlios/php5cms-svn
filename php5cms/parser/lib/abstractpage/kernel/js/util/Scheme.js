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
 * Scheme in Javascript
 * originally written by Luke Gorrie
 *
 * Implementation notes:
 * - Much of the code is somewhat recursive, which won't scale.
 * - BUG: macros are expanded even inside `quote's.
 * 
 * Notice that this is not an R5RS compliant Scheme.
 * It is quite easy to find expressions that are not handled
 * correctly (I tried rationals). But it is nice to play with,
 * and the source code may also be of interest.
 *
 * @package util
 */
 
/**
 * @var object
 * @access public
 */
nil = { type: "nil" };
nil.toString = function()
{
	return "()"; 
};


/**
 * Constructor
 *
 * @access public
 */
Scheme = function()
{
	this.Base = Base;
	this.Base();
};


Scheme.prototype = new Base();
Scheme.prototype.constructor = Scheme;
Scheme.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
Scheme.init = function()
{
	Scheme.add_analysis_expansion( "'",   "quote"            );
	Scheme.add_analysis_expansion( "`",   "quasiquote"       );
	Scheme.add_analysis_expansion( ",",   "unquote"          );
	Scheme.add_analysis_expansion( ",@",  "unquote-splicing" );

	Scheme.add_builtin( "cons",           "Scheme.bi_cons"             );
	Scheme.add_builtin( "car",            "Scheme.bi_car"              );
	Scheme.add_builtin( "cdr",            "Scheme.bi_cdr"              );
	Scheme.add_builtin( "pair?",          "Scheme.bi_pair_p"           );
	Scheme.add_builtin( "list?",          "Scheme.bi_list_p"           );
	Scheme.add_builtin( "symbol?",        "Scheme.bi_symbol_p"         );
	Scheme.add_builtin( "string?",        "Scheme.bi_string_p"         );
	Scheme.add_builtin( "number?",        "Scheme.bi_number_p"         );
	Scheme.add_builtin( "+",              "Scheme.bi_plus"             );
	Scheme.add_builtin( "-",              "Scheme.bi_minus"            );
	Scheme.add_builtin( "*",              "Scheme.bi_times"            );
	Scheme.add_builtin( "/",              "Scheme.bi_divide"           );
	Scheme.add_builtin( "null?",          "Scheme.bi_nullp"            );
	Scheme.add_builtin( "set-cookie!",    "Scheme.bi_set_cookie"       );
	Scheme.add_builtin( "get-cookie",     "Scheme.bi_get_cookie"       );
	Scheme.add_builtin( "string-append",  "Scheme.bi_string_append"    );
	Scheme.add_builtin( "symbol->string", "Scheme.bi_symbol_to_string" );
	Scheme.add_builtin( "obj->string",    "Scheme.bi_to_string"        );
	Scheme.add_builtin( "display",        "Scheme.bi_display"          );
	Scheme.add_builtin( "print-string",   "Scheme.bi_print_string"     );
	Scheme.add_builtin( "=",              "Scheme.bi_eq_p"             ); // `=' same as `eq?' here
	Scheme.add_builtin( "eq?",            "Scheme.bi_eq_p"             );
	Scheme.add_builtin( "equal?",         "Scheme.bi_equal_p"          );
	
	Scheme.env_add_binding( "nil", nil, Scheme.TOP_ENV );
};

/**
 * @access public
 * @static
 */
Scheme.tokenise = function( source )
{
	str = Scheme.discard_comments( source );
	var idx = 0;
	var end = str.length;
	var c;
	var token_acc = nil;
	atom_acc = "";
  
  	while ( idx < end )
	{
    	c = str.charAt( idx );
    
		// check for unquote-splicing
    	if ( ( c == "," ) && ( ( idx + 1 ) < end ) && ( str.charAt( idx + 1 ) == "@" ) )
		{
      		token_acc = Scheme.cons( ",@", Scheme.tokenise_update_acc( atom_acc, token_acc ) );
      		atom_acc  = "";
      		idx += 2;
    	}
		else if ( Scheme.special_char( c ) )
		{
      		token_acc = Scheme.cons( c, Scheme.tokenise_update_acc( atom_acc, token_acc ) );
      		atom_acc  = "";
      		idx++;
    	}
		else if ( Scheme.whitespace_p( c ) )
		{
      		token_acc = Scheme.tokenise_update_acc( atom_acc, token_acc );
      		atom_acc  = "";
      		idx++;
    	}
		else if ( ( c == "\"" ) && ( ( idx == 0 ) || ( str.charAt( idx - 1 ) != "\\" ) ) )
		{
      		var string_acc = "";
      
	  		// advance idx to end of string, adding to the accumulator
      		while ( ( ++idx < end ) && ( ( str.charAt( idx ) != "\"" ) || ( str.charAt( idx - 1 ) == "\\" ) ) )
			{
        		if ( ( str.charAt( idx ) == "\"" ) && ( str.charAt( idx-1 ) == "\\" ) )
					string_acc += "\"";
				else
					string_acc += str.charAt( idx );
      		}
      
	  		if ( ( idx == end ) && ( str.charAt( idx ) != "\"" ) )
			{
				return Base.raiseError( "Unterminated string literal." );
      		}
			else
			{
        		token_acc = Scheme.cons( "\"" + string_acc + "\"", Scheme.tokenise_update_acc( atom_acc, token_acc ) );
        		atom_acc = "";
        		idx++;
      		}
    	}
		else
		{
      		atom_acc += c;
      		idx++;
    	}
  	}

	return Scheme.reverse( Scheme.tokenise_update_acc( atom_acc, token_acc ) );
};

/**
 * @access public
 * @static
 */
Scheme.tokenise_update_acc = function( atom_acc, token_acc )
{
	if ( atom_acc.length == 0 )
    	return token_acc;
  	else
    	return Scheme.cons( atom_acc, token_acc );
};

/**
 * @access public
 * @static
 */
Scheme.special_char = function( c )
{
	if ( ( c == "(" ) ||
		 ( c == ")" ) ||
		 ( c == "'" ) ||
		 ( c == "`" ) ||
		 ( c == "," ) )
	{
    	return true;
  	}
	else
	{
    	return false;
	}
};

/**
 * @access public
 * @static
 */
Scheme.prepend_acc = function( acc, lst )
{
	switch ( acc )
	{
  		case "":
    		return lst;
  
  		default:
    		return Scheme.cons( acc, lst );
	}
};

/**
 * @access public
 * @static
 */
Scheme.digit_p = function( ch )
{
	if ( ch == "" )
		return false;

	return ( "0123456789".indexOf( ch ) >= 0 );
};

/**
 * @access public
 * @static
 */
Scheme.alpha_p = function( ch )
{
	if ( ch == "" )
		return false;

	return ( "ABCDEFGHIJKLMNOPQRSTUVWXYZ-!?_$%".indexOf( ch.toUpperCase() ) >= 0 );
};

/**
 * @access public
 * @static
 */
Scheme.alpha_or_digit_p = function( ch )
{
	if ( ch == "" )
		return false;

	return ( Scheme.digit_p( ch ) || Scheme.alpha_p( ch ) );
};

/**
 * @access public
 * @static
 */
Scheme.whitespace_p = function( ch )
{
	if ( ch == "" )
		return false;

	return ( " \t\n".indexOf( ch ) >= 0 );
};

/**
 * @access public
 * @static
 */
Scheme.discard_comments = function( str )
{
	var idx = 0;
	var start_idx = 0;
	var acc = "";

	while ( idx < str.length )
	{
    	if ( str.charAt( idx ) == ";" )
		{
      		acc += str.substring( start_idx, idx );
      
	  		while ( ( idx < str.length ) && ( str.charAt( idx ) != "\n" ) )
        		idx++;
      
      		start_idx = ++idx;
    	}
		else
		{
      		idx++;
    	}
	}

	return acc + str.substring( start_idx, idx );
};

/**
 * @access public
 * @static
 */
Scheme.add_analysis_expansion = function( Symbol, Name )
{
	Scheme.ANALYSIS_ALIST = Scheme.aset( Symbol, Name, Scheme.ANALYSIS_ALIST );
};

/**
 * @access public
 * @static
 */
Scheme.escapify = function( str )
{
	str = str.replace( /</g, "&lt;" );
	str = str.replace( />/g, "&gt;" );

	return str;
};

/**
 * @access public
 * @static
 */
Scheme.add_builtin = function( Name, FuncName )
{
	var FBody = "return " + FuncName + "(argument);";
	var bi = Scheme.make_builtin( Name, new Function( "argument", FBody ) );
	Scheme.env_add_binding( Name, bi, Scheme.TOP_ENV );
};

/**
 * @access public
 * @static
 */
Scheme.parse_string = function( str )
{
	return Scheme.parse_all( Scheme.tokenise( str ) );
};

/**
 * @access public
 * @static
 */
Scheme.parse_all = function( lst )
{
	var acc   = nil;
	var input = lst;

	while ( !Scheme.null_p( input ) )
	{
    	var x = Scheme.parse( input );
    	acc   = Scheme.cons( Scheme.car( x ), acc );
    	input = Scheme.cdr( x );
  	}

	return Scheme.reverse( acc );
};

/**
 * @access public
 * @static
 */
Scheme.parse = function( lst )
{
	var head = Scheme.car( lst );
	var tail = Scheme.cdr( lst );

	switch ( head )
	{
  		case "(":
    		return Scheme.parse_list( tail );
  
  		case ")":
			Base.raiseError( "Unexpected close-paren in input." );
    		break;
  
  		default:
    		if ( Scheme.digit_p( head.charAt( 0 ) ) )
			{
      			// number
      			return Scheme.cons( parseInt( head ), tail );
    		}
			else if ( head.charAt( 0 ) == "\"" )
			{
      			return Scheme.cons( Scheme.make_string( head.substring( 1, head.length - 1 ) ), tail );
    		}
    		else
			{
      			// symbol
      			return Scheme.cons( head, tail );
    		}
  	}
};

/**
 * `lst' must be a list of tokens directly following a "("
 * Returns: (parsed-list . leftover-tokens)
 *
 * @access public
 * @static
 */
Scheme.parse_list = function( lst )
{
	var acc   = nil;
	var input = lst;
  
  	while ( true )
	{
    	if ( Scheme.null_p( input ) )
		{
			Base.raiseError( "Unterminated list." );
      		return null;
    	}
		else if ( Scheme.car( input ) == ")" )
		{
      		return Scheme.cons( Scheme.reverse( acc ), Scheme.cdr( input ) );
    	}
		else
		{
      		var x = Scheme.parse( input );
      		acc   = Scheme.cons( Scheme.car( x ), acc );
      		input = Scheme.cdr( x );
		}
	}
};

/**
 * @access public
 * @static
 */
Scheme.reverse = function( lst )
{
	var acc   = nil;
	var input = lst;

	while ( !Scheme.null_p( input ) )
	{
    	acc   = Scheme.cons( Scheme.car( input ), acc );
    	input = Scheme.cdr( input );
  	}

	return acc;
};

/**
 * Note: recursive!
 *
 * @access public
 * @static
 */
Scheme.list_length = function( lst )
{
	if ( Scheme.null_p( lst ) )
		return 0;
  	else
    	return 1 + Scheme.list_length( Scheme.cdr( lst ) );
};


// Runtime stuff

/**
 * @access public
 * @static
 */
Scheme.cons = function( Car, Cdr )
{
	var Cons = {
		type: "cons", 
		car:  Car, 
		cdr:  Cdr
	};

	Cons.toString = function()
	{
		return "(" + Scheme.strings_with_spaces( this ) + ")"
	};
	
	return Cons;
};

/**
 * @access public
 * @static
 */
Scheme.car = function( Cons )
{
	if ( !Scheme.cons_p( Cons ) )
		return Base.raiseError( "Bad argument to car: " + Cons );
  
  	return Cons.car;
};

/**
 * @access public
 * @static
 */
Scheme.cdr = function( Cons )
{
	if ( !Scheme.cons_p( Cons ) ) 
		return Base.raiseError( "Bad argument to cdr: " + Cons );
  
  	return Cons.cdr;
};

/**
 * @access public
 * @static
 */
Scheme.cadr = function( Cons )
{
	return Scheme.car( Scheme.cdr( Cons ) );
};

/**
 * @access public
 * @static
 */
Scheme.make_string = function( str )
{
	var obj = {
		type: "string", 
		body: str
	};

	obj.toString = function()
	{
		return "\"" + this.body + "\"";
	}
	
	return obj;
};

/**
 * @access public
 * @static
 */
Scheme.list_append = function( A, B )
{
	if ( Scheme.null_p( A ) )
		return B;
  	else
    	return Scheme.cons(Scheme.car( A ), Scheme.list_append( Scheme.cdr( A ), B ) );
};

/**
 * @access public
 * @static
 */
Scheme.list_p = function( Obj )
{
	if ( Scheme.null_p( Obj ) )
    	return true;
  	else if ( Scheme.cons_p( Obj ) )
    	return Scheme.cons_p( Scheme.cdr( Obj ) );
  	else
    	return false;
};

/**
 * @access public
 * @static
 */
Scheme.cons_p = function( Obj )
{
	return ( typeof Obj == "object" ) && ( Obj.type == "cons" );
};

/**
 * @access public
 * @static
 */
Scheme.to_string = function( Obj )
{
	switch ( Obj )
	{
 		 case true:
    		return "#t";
  
  		case false:
    		return "#f";
  
  		default:
    		if ( typeof Obj == "object" )
      			return Obj.toString ();
    		else
      			return new String( Obj );
  	}
};

/**
 * @access public
 * @static
 */
Scheme.strings_with_spaces = function( lst )
{
	var acc   = "";
	var input = lst;

	while ( !( Scheme.null_p( input ) ) )
	{
    	if ( Scheme.cons_p( Scheme.cdr( input ) ) )
		{
      		acc   += Scheme.to_string( Scheme.car( input ) ) + " ";
      		input  = Scheme.cdr( input );
    	}
		else if ( Scheme.null_p( Scheme.cdr( input ) ) )
		{
      		acc   += Scheme.to_string( Scheme.car( input ) );
      		input  = Scheme.cdr( input );
    	}
		else
		{
      		acc   += Scheme.to_string( Scheme.car( input ) ) + " . " + Scheme.to_string( Scheme.cdr( input ) );
      		input  = nil;
    	}
  	}

	return acc;
};

/**
 * @access public
 * @static
 */
Scheme.null_p = function( Obj )
{
	return Scheme.object_p( Obj ) && ( Obj.type == "nil" );
};

/**
 * @access public
 * @static
 */
Scheme.symbol_p = function( Obj )
{
	return typeof Obj == "string";
};

/**
 * @access public
 * @static
 */
Scheme.number_p = function( Obj )
{
	return typeof Obj == "number";
};

/**
 * @access public
 * @static
 */
Scheme.string_p = function( Obj )
{
	return ( typeof Obj == "object" ) && ( Obj.type == "string" );
};

/**
 * @access public
 * @static
 */
Scheme.atom_p = function( Obj )
{
	return Scheme.symbol_p( Obj ) || Scheme.atom_p( Obj );
};

/**
 * @access public
 * @static
 */
Scheme.object_p = function( Obj )
{
	return ( typeof Obj == "object" ) && ( Obj.type != "undefined" );
};


// Evaluator

/**
 * @access public
 * @static
 */
Scheme.interpret = function( Program )
{
	eval_depth = 0;
	var ParseTree = Scheme.analyse( Scheme.parse_string( Program ) );
	var Form = Scheme.cons( "begin", ParseTree );

	return Scheme.to_string( Scheme.evaluate( Form, Scheme.TOP_ENV ) );
};

/**
 * @access public
 * @static
 */
Scheme.evaluate = function( InputForm, Env )
{
	if ( ++eval_depth >= Scheme.MAX_EVAL_DEPTH )
		return Base.raiseError( "MAX_EVAL_DEPTH exceeded; rewrite evaluator loop and retry" );
  	
	var code = Scheme.cons( InputForm, nil );
  	var code_stack = nil;
  	var env_stack  = nil;
  	var result = nil;
  
  	while ( !Scheme.null_p( code ) || !Scheme.null_p( code_stack ) )
	{
    	// finished at this level?
    	while ( Scheme.null_p( code ) )
		{
      		if ( Scheme.null_p( code_stack ) )
			{
        		--eval_depth;
        		return result;
      		}
      
	  		code = Scheme.car( code_stack );
      		Env  = Scheme.car( env_stack  );
      		
			code_stack = Scheme.cdr( code_stack );
      		env_stack  = Scheme.cdr( env_stack  );
    	}
    
		var Form = Scheme.car( code );
    	code = Scheme.cdr( code );
		
    	if ( Scheme.number_p( Form ) || Scheme.string_p( Form ) )
		{
      		result = Form;
      		continue;
    	}
		else if ( Scheme.symbol_p( Form ) )
		{
      		switch ( Form )
			{
      			case "#t":
        			result = true;
        			continue;
      
	  			case "#f":
        			result = true;
        			continue;
      
	  			default:
        			var x = Scheme.env_lookup( Form, Env );
        
					if ( x == Scheme.NOT_FOUND )
					{
						return Base.raiseError( "Unbound variable: \"" + Form + "\"." );
        			}
					else
					{
          				result = x;
          				continue;
        			}
      		}
    	}
		else if ( Scheme.list_p( Form ) )
		{
      		var FName = Scheme.car( Form );
      		var Args  = Scheme.cdr( Form );
      
	  		switch ( FName )
			{
				// Specials

     			case "define":
        			var Name  = Scheme.car( Args );
        			var Value = Scheme.car( Scheme.cdr( Args ) );
        			Scheme.env_add_binding( Name, Scheme.evaluate( Value, Env ), Env );
        			result = true;
        			continue;
						
      			case "lambda":
        			var LambdaList = Scheme.car( Args );
        			var Body = Scheme.cdr( Args );
        			result = Scheme.make_function( LambdaList, Body, Env );
        			continue;
      
	  			case "if":
        			var TestVal = Scheme.evaluate( Scheme.car( Args ), Env );
        
					if ( TestVal )
					{
          				result = Scheme.evaluate( Scheme.cadr( Args ), Env );
          				continue;
        			}
					else if ( Scheme.null_p( Scheme.cdr( Scheme.cdr( Args ) ) ) )
					{
          				result = false;
          				continue;
        			}
					else
					{
          				result = Scheme.evaluate( Scheme.car( Scheme.cdr( Scheme.cdr( Args ) ) ), Env );
          				continue;
        			}
      
	  			case "quote":
        			result = Scheme.car( Args );
        			continue;
						
      			case "quasiquote":
        			result = Scheme.eval_quasiquote( Scheme.car( Args ), Env );
        			continue;
						
      			case "defmacro":
        			var Name = Scheme.car( Args );
        			var Body = Scheme.cadr( Args );
        			Scheme.env_add_binding( Name, Scheme.evaluate( Body, Env ), Scheme.MACRO_ENV );
        			result = true;
        			continue;
      
	  			case "begin":
        			code_stack = Scheme.cons( code, code_stack );
        			env_stack  = Scheme.cons( Env,  env_stack  );
        			code = Args;
        			continue;
						
      			case "list":
        			// recurse
        			result = Scheme.evaluate_each( Args, Env );
        			continue;
      
	  			default:
        			var Macro = Scheme.env_lookup( FName, Scheme.MACRO_ENV );
        
					if ( Macro != Scheme.NOT_FOUND )
					{
          				var expansion = Scheme.expand_macro( Macro, Args );
          
		  				// recurse
          				code = Scheme.cons( expansion, code );
          				continue;
        			}
        			else
					{
          				// recurse
          				var fun = Scheme.evaluate( FName, Env );
							
          				if ( Scheme.builtin_p( fun ) )
						{
            				var arg_vals = Scheme.evaluate_each( Args, Env );
            				result = fun.body( arg_vals );
            				continue;
          				}
          				else
						{
            				var fargs = nil;
            				var fenv  = nil;
            
							if ( Scheme.special_p( fun ) )
							{
              					fargs = Args;
              					fenv  = Env;
            				}
							else
							{
              					// recurse
              					fargs = Scheme.evaluate_each( Args, Env );
              					fenv  = Scheme.env_extend( fun.args, fargs, Env );
            				}
            
							code_stack = Scheme.cons( code, code_stack );
            				env_stack  = Scheme.cons( Env,  env_stack  );
            
							code = fun.body;
            				Env  = fenv;
            					
							continue;
          				}
        			}
			}
    	}
  	}

	--eval_depth;
	return result;
};

/**
 * @access public
 * @static
 */
Scheme.eval_quasiquote = function( Form, Env )
{
	if ( Scheme.null_p( Form ) )
	{
    	return nil;
  	}
	else if ( Scheme.cons_p( Form ) )
	{
    	var Head = Scheme.car( Form );
    	var Tail = Scheme.cdr( Form );
		
    	if ( Scheme.cons_p( Head ) )
		{
      		var Head2 = Scheme.car( Head );
      		var Tail2 = Scheme.cdr( Head );
			
      		if ( Head2 == "unquote" )
				return Scheme.cons( Scheme.evaluate( Scheme.car( Tail2 ), Env ), Scheme.eval_quasiquote( Tail, Env ) );
			else if ( Head2 == "unquote-splicing" )
				return Scheme.list_append( Scheme.evaluate( Scheme.car( Tail2 ), Env ), Scheme.eval_quasiquote( Tail, Env ) );
 			else
				return Scheme.cons( Scheme.eval_quasiquote( Head, Env ), Scheme.eval_quasiquote( Tail, Env ) );
    	}
		else
		{
      		return Scheme.cons( Scheme.eval_quasiquote( Head, Env ), Scheme.eval_quasiquote( Tail, Env ) );
    	}
  	}
	else
	{
    	return Form;
  	}
};

/**
 * @access public
 * @static
 */
Scheme.evaluate_each = function( Forms, Env )
{
	if ( Scheme.null_p( Forms ) )
    	return nil;
  	else
    	return Scheme.cons( Scheme.evaluate( Scheme.car( Forms ), Env ), Scheme.evaluate_each( Scheme.cdr( Forms ), Env ) );
};

/**
 * @access public
 * @static
 */
Scheme.evaluate_sequence = function( Forms, Env )
{
	if ( Scheme.null_p( Scheme.cdr( Forms ) ) )
	{
    	return Scheme.evaluate( Scheme.car( Forms ), Env );
  	}
	else
	{
    	Scheme.evaluate( Scheme.car( Forms ), Env );
    	return Scheme.evaluate_sequence( Scheme.cdr( Forms ), Env );
	}
};

/**
 * NASTY BUG:
 * Macros are expanded in here, even if they're inside quotes.
 *
 * @access public
 * @static
 */
Scheme.analyse = function( Sexp )
{
	if ( Scheme.null_p( Sexp ) )
	{
    	return nil;
  	}
	else if ( Scheme.cons_p( Sexp ) )
	{
    	var acc   = nil;
    	var input = Sexp;
    
		while ( !Scheme.null_p( input ) ) 
		{
      		var Head = Scheme.car( input );
      		var Tail = Scheme.cdr( input );
      		var rule = Scheme.assoc( Head, Scheme.ANALYSIS_ALIST );
      
	  		if ( rule )
			{
        		var expansion = Scheme.cdr( rule );
        		var TailA = Scheme.analyse( Tail );
				
        		return Scheme.list_append( Scheme.reverse( acc ), Scheme.cons( Scheme.cons( expansion, Scheme.cons( Scheme.car( TailA ), nil ) ), Scheme.cdr( TailA ) ) );
      		}
			// no expansion
			else
			{
        		if ( Scheme.cons_p( Head ) )
          			acc = Scheme.cons( Scheme.analyse( Head ), acc );
        		else
          			acc = Scheme.cons( Head, acc );
        
        		input = Tail;
      		}
    	}
    
		return Scheme.reverse( acc );
	}
	else
	{
    	return Sexp;
	}
};

/**
 * @access public
 * @static
 */
Scheme.expand_macro = function( Fun, Args )
{
	var NewEnv = Scheme.env_extend( Fun.args, Args, Fun.env );
	return Scheme.evaluate_sequence( Fun.body, NewEnv );
};

/**
 * @access public
 * @static
 */
Scheme.apply_fun = function( Fun, Args, Env )
{
	if ( Scheme.function_p( Fun ) )
	{
    	var NewEnv = Scheme.env_extend( Fun.args, Scheme.evaluate_each( Args, Env ), Fun.env );
    	return Scheme.evaluate_sequence( Fun.body, NewEnv );
  	}
	else if ( Scheme.special_p( Fun ) )
	{
    	var NewEnv = Scheme.env_extend( Fun.args, Args, Env );
    	return Scheme.evaluate_sequence( Fun.body, NewEnv );
	}
};

/**
 * @access public
 * @static
 */
Scheme.make_special = function( Name, Args, Body )
{
	return {
		type: "special", 
		name: Name, 
		args: Args, 
		body: Body
	};
};

/**
 * @access public
 * @static
 */
Scheme.special_p = function( Obj )
{
	return ( Scheme.object_p( Obj ) ) && ( Obj.type == "special" );
};

/**
 * @access public
 * @static
 */
Scheme.make_function = function( Args, Body, Env )
{
	var Fn = {
		type: "function", 
		args: Args, 
		body: Body, 
		env:  Env
	};

	Fn.toString = function()
	{
		return "#{function args=" + this.args + "}";
	};

	return Fn;
};

/**
 * @access public
 * @static
 */
Scheme.function_p = function( Obj )
{
	return ( Scheme.object_p( Obj ) ) && ( Obj.type == "function" );
};

/**
 * A builtin is a javascript function that takes an evaluated argument list.
 *
 * @access public
 * @static
 */
Scheme.make_builtin = function( Name, Func )
{
	return {
		type: "builtin", 
		name: Name, 
		body: Func
	}
};

/**
 * @access public
 * @static
 */
Scheme.builtin_p = function( Obj )
{
	return ( Scheme.object_p( Obj ) ) && ( Obj.type == "builtin" );
};


// Environments

/**
 * @access public
 * @static
 */
Scheme.make_env = function( Parent )
{
	var env = {
		parent:   Parent, 
		bindings: nil
	};
	
	env.toString = function()
	{
		return "#{env bindings=" + this.bindings + "}";
	}

	return env;
};

/**
 * @access public
 * @static
 */
Scheme.env_add_binding = function( name, value, env )
{
	env.bindings = Scheme.aset( name, value, env.bindings );
};

/**
 * Lookup the alist cell of a binding.
 *
 * @access public
 * @static
 */
Scheme.env_lookup = function( name, env )
{
	var x = Scheme.assoc( name, env.bindings );

	if ( x == false )
	{
    	if ( Scheme.null_p( env.parent ) )
			return Scheme.NOT_FOUND;
    	else
      		return Scheme.env_lookup( name, env.parent );
  	}
	else
	{
    	return Scheme.cdr( x );
	}
};

/**
 * @access public
 * @static
 */
Scheme.env_extend = function( Names, Values, Env )
{
	var NewEnv = Scheme.make_env( Env );

	if ( Scheme.symbol_p( Names ) ) // (lambda symbol . body)
		Scheme.env_add_binding( Names, Values, Env );
	else // (lambda (symbol ...) . body)
		Scheme.env_extend1( Names, Values, NewEnv );

	return NewEnv;
};

/**
 * @access public
 * @static
 */
Scheme.env_extend1 = function( Names, Values, Env )
{
	if ( Scheme.null_p( Names ) )
	{
    	return true;
  	}
	else if ( Scheme.car( Names ) == "." )
	{
    	Scheme.env_add_binding( Scheme.cadr( Names ), Values, Env );
    	return true;
  	}
	else
	{
    	Scheme.env_add_binding( Scheme.car( Names ), Scheme.car( Values ), Env );
    	return Scheme.env_extend1( Scheme.cdr( Names ), Scheme.cdr( Values ), Env );
	}
};


// Association lists

/**
 * @access public
 * @static
 */
Scheme.assoc = function( name, alist )
{
	if ( Scheme.null_p( alist ) )
	{
    	return false;
  	}
	else
	{
    	var cell = Scheme.car( alist );
    
		if ( Scheme.car( cell ) == name )
      		return cell;
    	else
      		return Scheme.assoc( name, Scheme.cdr( alist ) );
  	}
};

/**
 * @access public
 * @static
 */
Scheme.aremove = function( name, alist )
{
	if ( Scheme.null_p( alist ) )
	{
    	return nil;
  	}
	else
	{
    	var cell = Scheme.car( alist );
    
		if ( Scheme.car( cell ) == name )
      		return Scheme.cdr( alist );
    	else
      		return Scheme.cons( Scheme.car( alist ), Scheme.aremove( name, Scheme.cdr( alist ) ) );
	}
};

/**
 * @access public
 * @static
 */
Scheme.aset = function( name, value, alist )
{
	return Scheme.acons( name, value, Scheme.aremove( name, alist ) );
};

/**
 * @access public
 * @static
 */
Scheme.acons = function( name, value, alist )
{
	return Scheme.cons( Scheme.cons( name, value ), alist );
};


// API functions

/**
 * Write the literal expression and its evaluated result into the document.
 *
 * @access public
 * @static
 */
Scheme.show_expr = function( expr )
{
	document.write( expr + " => " + Scheme.escapify( Scheme.interpret( expr ) ) + "<br>" );
	Scheme.interpret( expr );
};

/**
 * @access public
 * @static
 */
Scheme.show_parse = function( expr )
{
	document.write( expr + " =parse=> " + Scheme.to_string( Scheme.parse_string( expr ) ) + "<br>" );
};

/**
 * @access public
 * @static
 */
Scheme.show_analysis = function( expr )
{
	document.write( expr + "=analyse=> " + Scheme.to_string( Scheme.analyse( Scheme.parse_string( expr ) ) ) + "<br>" );
};

/**
 * @access public
 * @static
 */
Scheme.show_tokens = function( expr )
{
	document.write( expr + "=tokenise=> " + Scheme.tokenise( expr ) + "<br>" );
};


// Builtin functions

/**
 * (cons a b)
 *
 * @access public
 * @static
 */
Scheme.bi_cons = function( Args )
{
	return Scheme.cons( Scheme.car( Args ), Scheme.cadr( Args ) );
};

/**
 * (car x)
 *
 * @access public
 * @static
 */
Scheme.bi_car = function( Args ) 
{
	return Scheme.car( Scheme.car( Args ) );
};

/**
 * (cdr x)
 *
 * @access public
 * @static
 */
Scheme.bi_cdr = function( Args )
{
	return Scheme.cdr( Scheme.car( Args ) );
};

/**
 * (pair? x)
 *
 * @access public
 * @static
 */
Scheme.bi_pair_p = function( Args )
{
	return Scheme.cons_p( Scheme.car( Args ) );
};

/**
 * (list? x)
 *
 * @access public
 * @static
 */
Scheme.bi_list_p = function( Args )
{
	return Scheme.list_p( Scheme.car( Args ) );
};

/**
 * (symbol? x)
 *
 * @access public
 * @static
 */
Scheme.bi_symbol_p = function( Args )
{
	return Scheme.symbol_p( Scheme.car( Args ) );
};

/**
 * (number? x)
 *
 * @access public
 * @static
 */
Scheme.bi_number_p = function( Args )
{
	return Scheme.number_p( Scheme.car( Args ) );
};

/**
 * (string? x)
 *
 * @access public
 * @static
 */
Scheme.bi_string_p = function( Args )
{
	return Scheme.string_p( Scheme.car( Args ) );
};

/**
 * (eq? a b)
 *
 * @access public
 * @static
 */
Scheme.bi_eq_p = function( Args )
{
	return Scheme.car( Args ) == Scheme.cadr( Args );
};

/**
 * (equal? a b)
 *
 * @access public
 * @static
 */
Scheme.bi_equal_p = function( Args )
{
	// how to implement?
	return Scheme.bi_eq_p( Args );
};

/**
 * (+ x y)
 *
 * @access public
 * @static
 */
Scheme.bi_plus = function( Args )
{
	return Scheme.car( Args ) + Scheme.cadr( Args );
};

/**
 * (- x y)
 *
 * @access public
 * @static
 */
Scheme.bi_minus = function( Args )
{
	return Scheme.car( Args ) - Scheme.cadr( Args );
};

/**
 * (/ x y)
 *
 * @access public
 * @static
 */
Scheme.bi_divide = function( Args )
{
	return Scheme.car( Args ) / Scheme.cadr( Args );
};

/**
 * (* x y)
 *
 * @access public
 * @static
 */
Scheme.bi_times = function( Args )
{
	return Scheme.car( Args ) * Scheme.cadr( Args );
};

/**
 * (null? obj)
 *
 * @access public
 * @static
 */
Scheme.bi_nullp = function( Args )
{
	return Scheme.null_p( Scheme.car( Args ) );
};

/**
 * (set-cookie name value)
 *
 * @access public
 * @static
 */
Scheme.bi_set_cookie = function( Args )
{
	CookieUtil.save( Scheme.car( Args ), Scheme.cadr( Args ) );
	return true;
};

/**
 * (get-cookie name)
 *
 * @access public
 * @static
 */
Scheme.bi_get_cookie = function( Args )
{
	return CookieUtil.read( Scheme.car( Args ) );
};

/**
 * (string-append . strings)
 *
 * @access public
 * @static
 */
Scheme.bi_string_append = function( Args )
{
	if ( Scheme.null_p( Args ) )
		return Scheme.make_string( "" );
  	else
    	return Scheme.string_concat( Scheme.car( Args ), Scheme.bi_string_append( Scheme.cdr( Args ) ) );
};

/**
 * @access public
 * @static
 */
Scheme.string_concat = function( A, B )
{
	return Scheme.make_string( A.body + B.body );
};

/**
 * @access public
 * @static
 */
Scheme.bi_symbol_to_string = function( Args )
{
	return Scheme.make_string( Scheme.car( Args ) );
};

/**
 * @access public
 * @static
 */
Scheme.bi_to_string = function( Args )
{
	return Scheme.make_string( Scheme.to_string( Scheme.car( Args ) ) );
};

/**
 * @access public
 * @static
 */
Scheme.bi_display = function( Args )
{
	document.write( Scheme.car( Args ) );
};

/**
 * (print-string string)
 *
 * @access public
 * @static
 */
Scheme.bi_print_string = function( Args )
{
	document.write( Scheme.car( Args ).body );
};


/**
 * @access public
 * @static
 */
Scheme.TOP_ENV = Scheme.make_env( nil );

/**
 * @access public
 * @static
 */
Scheme.MACRO_ENV = Scheme.make_env( nil );

/**
 * Constant for values that aren't found
 *
 * @access public
 * @static
 */
Scheme.NOT_FOUND = null;

/**
 * If we don't limit this, we crash the browser
 *
 * @access public
 * @static
 */
Scheme.MAX_EVAL_DEPTH = 1000;

/**
 * @access public
 * @static
 */
Scheme.ANALYSIS_ALIST = nil;

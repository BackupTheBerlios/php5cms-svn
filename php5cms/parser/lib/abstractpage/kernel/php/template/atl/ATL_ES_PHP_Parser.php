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
|Authors: Laurent Bedubourg <laurent.bedubourg@free.fr>                |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'template.atl.util.ATL_Code' );


/**
 * @var $ATL_EXPRESSION_namespaces
 * @var  array
 *
 * This array contains atl namespaces, it means php:, string:, exists:,
 * not: and path: at this time.
 */
global $ATL_EXPRESSION_namespaces;
$ATL_EXPRESSION_namespaces = array(
	'not', 
	'php', 
	'string', 
	'exists', 
	'path', 
	'not-exists'
);

/**
 * @var $ATL_EXPRESSION_keywords
 * @var  array
 *
 * List of reserved atl keywords.
 */
global $ATL_EXPRESSION_keywords;
$ATL_EXPRESSION_keywords = array(
	'nothing', 
	'default', 
	'structure'
);

global $ATL_EXPRESSION_php_types;
$ATL_EXPRESSION_php_types = array(
	'true', 
	'false', 
	'null'
);


/**
 * @package template_atl
 */
 
class ATL_ES_PHP_Parser extends PEAR
{
	/**
	 * @access private
	 */
    var $_exp;
	
	/**
	 * @access private
	 */
    var $_gen;
	
	/**
	 * @access private
	 */
    var $_str;
	
	/**
	 * @access private
	 */
    var $_aliases = array();
	
	/**
	 * @access private
	 */
    var $_code = "";
	
	/**
	 * @access private
	 */
    var $_last_was_array = false;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
    function ATL_ES_PHP_Parser( &$expression, $str )
    {
        $this->_exp =& $expression;
        $this->_gen =& $expression->_gen;
        $this->_str =  $str;
    }


	/**
	 * @access public
	 */
    function evaluate()
    {
        $value   = $this->_str;
        $strings = array();

        // extract strings and replace context calls
        if ( preg_match_all( '/(\'.*?(?<!\\\)\')/sm', $value, $m ) ) 
		{
            list(,$m) = $m;
            
			foreach ( $m as $str ) 
			{
                $s_rplc    = ATL_ES_path_in_string( $str, "'" );
                $s_rplc    = preg_replace( '/^\' ?\'\. /', '', $s_rplc );
                $s_rplc    = preg_replace( '/\.\'\'$/', '', $s_rplc );
                $value     = str_replace( $str, '#_STRING_'. count( $strings ) . '_#', $value );
                $strings[] = $s_rplc;
            }
        }

        list( $match, $replc ) = ATL_context_accessed( $value );
        $contexts = array();
		
        foreach ( $match as $m ) 
		{
            $i = count( $contexts );
            $contexts[] = $replc[$i];
            $value = str_replace( $m, '#_CONTEXT_'. $i . '_#', $value );
        }

        // replace or, and, lt, gt, etc...
        $value = $this->_php_test_modifiers( $value );
        $value = $this->_php_vars( $value );

        // restore strings
        $i = 0;
        foreach ( $strings as $str ) 
		{
            $value = str_replace( '#_STRING_'. $i . '_#', $str, $value );
            $i++;
        }

        $i = 0;
        foreach ( $contexts as $c ) 
		{
            $value = str_replace( '#_CONTEXT_' . $i . '_#', ATL_ES_path( $this->_exp, $c ), $value );
            $i++;
        }

        // experimental, compile php: content
        $code = new ATL_Code();
        $code->setCode( $value . ";" );
        $err = $code->compile();
        
		if ( PEAR::isError( $err ) )
            return $this->_raiseCompileError( $value, $err );
        
        return $value;
    }

	/**
	 * @access public
	 */
    function pathRequest( $path )
    {
        global $ATL_EXPRESSION_php_types;

        if ( !preg_match( '/[a-z]/i', $path ) ) 
            return $path; 
        
        if ( $this->_last_was_array ) 
            return str_replace( '.', '->', $path ); 
        
        if ( in_array( $path, $ATL_EXPRESSION_php_types ) )
            return $path;

        $concatenate = false;
        $path = str_replace( '.', '/', $path );
		
        if ( substr( $path, -1 ) == '/' ) 
		{ 
            $path = substr( $path, 0, -1 );
            $concatenate = true;
        }

        if ( array_key_exists( $path, $this->_aliases ) ) 
		{
            $res = $this->_aliases[$path];
        } 
		else 
		{
            $res = $this->_gen->newTemporaryVar();
            $this->_aliases[$path] = $res;
            $this->_gen->doContextGet( $res, $path );
        }

        if ( $concatenate ) 
            $res .= ' .';
			
        return $res;
    }

	
	// private methods
	
	/**
	 * @access private
	 */
    function _php_test_modifiers( $exp )
    {
        $exp = preg_replace( '/\bnot\b/i', ' !',   $exp );
        $exp = preg_replace( '/\bne\b/i',  ' != ', $exp );
        $exp = preg_replace( '/\band\b/i', ' && ', $exp );
        $exp = preg_replace( '/\bor\b/i',  ' || ', $exp );
        $exp = preg_replace( '/\blt\b/i',  ' < ',  $exp );
        $exp = preg_replace( '/\bgt\b/i',  ' > ',  $exp );
        $exp = preg_replace( '/\bge\b/i',  ' >= ', $exp );
        $exp = preg_replace( '/\ble\b/i',  ' <= ', $exp );
        $exp = preg_replace( '/\beq\b/i',  ' == ', $exp );

        return $exp;
    }

	/**
	 * @access private
	 */
    function _php_vars( $arg )
    {
        $arg = preg_replace( '/\s*\/\s*/',  ' / ', $arg );
        $arg = preg_replace( "/\s*\(\s*/",  "(",   $arg );
        $arg = preg_replace( '/\s*\)\s*/',  ') ',  $arg );
        $arg = preg_replace( '/\s*\[\s*/',  '[',   $arg );
        $arg = preg_replace( '/\s*\]\s*/',  ']',   $arg );
        $arg = preg_replace( '/\s*,\s*/',   ' , ', $arg );

        $result         = "";
        $path           = false;
        $last_path      = false;
        $last_was_array = false;

        $i = 0;
        while ( $i < strlen( $arg ) ) 
		{
            $c = $arg[$i];
            
			if ( preg_match( '/[a-z_]/i', $c ) ) 
			{
                $path .= $c;
            } 
			else if ( preg_match( '/[0-9]/', $c ) && $path ) 
			{
                $path .= $c;
            } 
			else if ( preg_match( '/[\/\.]/', $c ) && $path ) 
			{
                $last_path = $path;
                $path .= $c;
            } 
			else if ( preg_match( '/[\/\.]/', $c ) && $this->_last_was_array ) 
			{
                $result .= '->';
            } 
			else if ( $c == '(' ) 
			{
                if ( $last_path ) 
				{
                    $result    .= $this->pathRequest( $last_path );
                    $result    .= '->';
                    $path       = substr( $path, strlen( $last_path ) +1 );
                    $last_path  = false;
                }
				
                $result .= $path . '(';
                $this->_last_was_array = false;
                $path = false;
            } 
			else if ( $c == '#' ) 
			{
                if ( $path ) 
				{
                    $result    .= $this->pathRequest( $path );
                    $path       = false;
                    $last_path  = false;
                }
				
                $next    = strpos( $arg, '#', $i + 1 );
                $result .= substr( $arg, $i, $next - $i + 1 );
                $i = $next;
            } 
			else if ( $c == ':' ) 
			{
                if ( $arg[$i+1] != ':' ) 
				{ 
					// error
                }
				
                $i++;
				
                $result    .= $path;
                $result    .= '::';
                $path       = false;
                $last_path  = false;
            } 
			else if ( $c == '@' ) 
			{
                // access to defined variable, append to result
                // up to non word character
                $i++; // skip @ character
                
				do 
				{
                    $c = $arg[$i];
                    $result .= $c;
                    $i++;
                } while ( $i < strlen( $arg ) && preg_match( '/[a-z_0-9]/i', $arg[$i] ) );
                
				$i--;   // reset last character
            } 
			else 
			{
                if ( $path ) 
				{
                    $result .= $this->pathRequest( $path );
                    $path    = false;
                }
				
                $result .= $c;
                
				if ( $c == ']' ) 
				{
                    $last_path = false;
                    $path = false;
                    $this->_last_was_array = true;
                } 
				else 
				{
                    $this->_last_was_array = false;
                }
            }
			
            $i++;
        }
        
        // don't forget the last bit
        if ( isset( $path ) && $path )
            $result .= $this->pathRequest( $path );

        return $result;
    }

	/**
	 * @access private
	 */
    function _raiseCompileError( $code, $err ) 
    {
        $str = sprintf( "Expression 'php:' compilation error in %s:%d" . ATL_STRING_LINEFEED,
			$this->_exp->_tag->_parser->_file,
			$this->_exp->_tag->line
		);
		
        return PEAR::raiseError( $str );
    }
} // END OF ATL_ES_PHP_Parser


/**
 * string: expression type
 */
function ATL_ES_string( &$exp, $value )
{
    $value = ATL_ES_path_in_string( $value );
    $value = '"' . $value . '"';
    $value = preg_replace( '/^\" ?\"\. /', '', $value );
    $value = preg_replace( '/\.\"\"$/', '', $value );
	
    return $value;
}

/**
 * not: expression type
 */
function ATL_ES_not( &$exp, $value )
{
    return "! " . ATL_ES_path( $exp, $value );
}

/**
 * not-exists: expression type
 */
function ATL_ES_not_exists( &$exp, $value ) 
{
    return "! " . ATL_ES_exists( $exp, $value );
}

/**
 * path: expression type
 *
 * @todo default  -- existing text inside tag
 *       options  -- ?? keyword arguments passed to template ??
 *       repeat   -- repeat variables
 *       attrs    -- initial values of tag attributes
 *       CONTEXTS -- list of standard names (above list) in case one of these
 *                   names was hidden by a local variable.
 */
function ATL_ES_path( &$exp, $value )
{
    $value = trim($value);
    
	// special case : 'string' authorized
    if ( $value[0] == "'" )
		return ATL_ES_path_in_string( $value, "'" );
	
    // number case
    if ( preg_match( '/^[0-9\.]*$/', $value ) ) 
		return $value;
		
    // php constants are accessed though @ keyword
    if ( preg_match( '/^@[_a-z][0-9a-z_]*$/i', $value ) ) 
		return substr( $value, 1 );
		
    if ( $value == "nothing") 
		return 'null';
		
    if ( $value == "default" )
		return '$__default__';
		
    $value = ATL_ES_path_in_string( $value );
    return '$__ctx__->get("' . $value . '")';
}

function ATL_ES_path_toString( &$exp, $value )
{
    $value = trim( $value );
	
    if ( $value == "nothing" )
		return 'null';
    
	if ( $value == "default" )
		return '$__default__';
    
	if ( preg_match( '/^@[_a-z][0-9a-z_]*$/i', $value ) )
		return substr( $value, 1 );
    
	return '$__ctx__->getToString("' . $value . '")';
}

/**
 * exists: expression type
 */
function ATL_ES_exists( &$exp, $value )
{
    return '$__ctx__->has("' . trim( $value ) . '")';
}

/**
 * php: expression type
 */
function ATL_ES_php( &$exp, $str )
{
    $parser = new ATL_ES_PHP_Parser( $exp, $str );
    return $parser->evaluate();
}

function ATL_ES_path_in_string( $arg, $c = '"' )
{
    list( $match, $repl ) = ATL_context_accessed( $arg );
	
    for ( $i = 0; $i < count( $match ); $i++ ) 
	{
        $null = "";
        $repl[$i] = $c . ". " . ATL_ES_path_toString( $null, $repl[$i] ) . " ." . $c;
        $pos = strpos( $arg, $match[$i] );
		
        $arg = substr( $arg, 0, $pos )
            .  $repl[$i]
            .  substr( $arg, $pos + strlen( $match[$i] ) );
    }
	
    if ( $c == '"' )
        $arg = str_replace( '$$','\$', $arg );
    else
        $arg = str_replace( '$$', '$', $arg );
    
    return $arg;
}

function ATL_context_accessed( $str )
{
    if ( preg_match_all( '/((?<!\$)\$\{?([@_a-zA-z0-9.\/\-]+)\}?)/', $str, $match ) )
        return array_slice( $match, 1 );
    
    return array( array(), array() );
}

?>

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


define( 'ATL_EXPRESSION_RECEIVER_IS_CONTEXT', 1 );
define( 'ATL_EXPRESSION_RECEIVER_IS_NONE',    2 );
define( 'ATL_EXPRESSION_RECEIVER_IS_TEMP',    4 );
define( 'ATL_EXPRESSION_RECEIVER_IS_OUTPUT',  8 );


/**
 * @package template_atl
 */
 
class ATL_Expression extends PEAR
{
	/**
	 * @access private
	 */	
    var $_tag;
	
	/**
	 * @access private
	 */	
    var $_gen;
	
	/**
	 * @access private
	 */	
    var $_src;
	
	/**
	 * @access private
	 */	
	var $_policy;
	
	/**
	 * @access private
	 */	
    var $_subs = array();
	
	/**
	 * @access private
	 */	
    var $_receiver = false;
	
	/**
	 * @access private
	 */	
    var $_prepared = false;
	
	/**
	 * @access private
	 */	
    var $_structure = false;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
    function ATL_Expression( &$generator, &$tag, $str )
    {
        $this->_tag =& $tag;
        $this->_gen =& $generator;
        $this->_src =  $str;
    }


	/**
	 * @access public
	 */		
    function setPolicy( $policy )
    {
        $this->_policy = $policy;
    }

    /**
     * Prepare the expression.
     *
     * This method explode the expression into sub expression and prepare each 
     * expression for parsing.
     *
     * @throws Error 
	 * @access public
     */
    function prepare()
    {
        if ( $this->_prepared )
			return;
			
        $test = $this->_src;

        // some sub expression detected
        while ( preg_match( '/^(.*?)(?<!;);(?!;)/sm', $test, $m ) ) 
		{
            list( $src, $exp ) = $m;

            $x = new ATL_Expression( $this->_gen, $this->_tag, $exp );
            $x->setPolicy( $this->_policy );
            $x->setReceiver( $this->getReceiver() );
            $err = $x->prepare();
			
            if ( PEAR::isError( $err ) ) 
                return $err; 
            
            $this->_subs[] = $x;
            $test = substr( $test, strlen( $src ) );
        }
		
        // if subs, insert last one
        if ( $this->countSubs() > 0 && strlen( trim( $test ) ) > 0 ) 
		{
            $exp = $test;
            $x   = new ATL_Expression( $this->_gen, $this->_tag, $exp );
            $x->setPolicy( $this->_policy );
            $x->setReceiver( $this->getReceiver() );
            $err = $x->prepare();
			
            if ( PEAR::isError( $err ) ) 
                return $err; 
            
            $this->_subs[] = $x;
        } 
		else 
		{        
            // otherwise, just remove expression delimiters from source
            // and apply the receiver policy
            $exp = $test;
            $exp = str_replace( ';;', ';', $exp );
            $this->_src = $exp;
			
            if ( strlen( $exp ) == 0 ) 
				return;

            $err = $this->_extractReceiver();
			
            if ( PEAR::isError( $err ) ) 
                return $err; 

            if ( !$this->_receiver && ( $this->_policy & ATL_EXPRESSION_RECEIVER_IS_CONTEXT || $this->_policy & ATL_EXPRESSION_RECEIVER_IS_TEMP ) ) 
			{
                $str = sprintf( 'Receiver required in expression \'%s\' from %s:%d',
					$this->_src, 
					$this->_tag->_parser->_file, 
					$this->_tag->line
				);
				
                return PEAR::raiseError( $str );
            }

            if ( $this->_policy & ATL_EXPRESSION_RECEIVER_IS_NONE && $this->_receiver ) 
			{
                $str = sprintf( 'Unexpected receiver \'%s\' in  expression \'%s\' from %s:%d', 
					$this->_receiver, 
					$this->_src,
					$this->_tag->_parser->_file, 
					$this->_tag->line
				);

                return PEAR::raiseError( $str );
            }
        }

        $this->_prepared = true;
    }

    /** 
     * Retrieve the number of sub expressions.
	 *
	 * @access public
     */
    function countSubs()
    {
        return count( $this->_subs );
    }

	/**
	 * @access public
	 */
    function &subs()
    {
        return $this->_subs;
    }

    /**
     * Returns true if a receiver is set for this expression.
	 *
	 * @access public
     */
    function hasReceiver()
    {
        return $this->_receiver != false;
    }

    /**
     * Retrieve receiver's name.
	 *
	 * @access public
     */
    function getReceiver()
    {
        return $this->_receiver;
    }

    /**
     * Set expression receiver.
	 *
	 * @access public
     */
    function setReceiver( $name )
    {
        $this->_receiver = $name;
    }

    /**
     * Generate php code for this expression.
	 *
	 * @access public
     */
    function generate()
    {
        $err = $this->prepare();
		
        if ( PEAR::isError( $err ) )
            return $err;

        if ( $this->countSubs() > 0 ) 
		{
            foreach ( $this->_subs as $sub ) 
			{
                $err = $sub->generate();
                
				if ( PEAR::isError( $err ) )
                    return $err;
            }
        } 
		else 
		{
            $exp = $this->_src;
			
            if ( strlen( $exp ) == 0 ) 
				return;

            // expression may be composed of alternatives | list of expression
            // they are evaluated with a specific policy : ATL_EXPRESSION_SEQUENCE
            //
            // 'string:' break the sequence as no alternative exists after a
            // string which is always true.
            if ( preg_match( '/\s*?\|\s*?string:/sm', $exp, $m ) ) 
			{
                $search = $m[0];
                $str    = strpos( $exp, $search );
                
				$seq    = preg_split( '/(\s*?\|\s*?)/sm', substr( $exp, 0, $str ) );
                $seq[]  = substr( $exp, $str + 2 );
            } 
			else 
			{
                $seq = preg_split( '/(\s*?\|\s*?)/sm', $exp );
            }

            // not a sequence
            if ( count( $seq ) == 1 ) 
			{
                $code = ATL_Expression::_getCode( $this, $exp );
                
				if ( PEAR::isError( $code ) ) 
					return $code;
                
                $temp = $this->_gen->newTemporaryVar();
                $this->_gen->doAffectResult( $temp, $code );
				
                return $this->_useResult( $temp );
            } 
			else 
			{
                return $this->_evaluateSequence( $seq );
            }
        }
    }


	// private methods
	
	/**
	 * @access private
	 */
    function _extractReceiver()
    {
        global $ATL_EXPRESSION_namespaces;
        
        $this->_src = preg_replace( '/^\s+/sm', '', $this->_src ); 
        
		if ( preg_match( '/^([a-z:A-Z_0-9]+)\s+([^\|].*?)$/sm', $this->_src, $m ) ) 
		{
            // the receiver looks like xxxx:aaaa
            // we must ensure that it's not a known atl namespaces
            if ( preg_match( '/^([a-zA-Z_0-9]+):/', $m[1], $sub ) ) 
			{
                $ns = $sub[1];
                
				// known namespace, just break
                if ( function_exists( 'atl_es_' . $ns ) ) 
				{
                    // in_array( strtolower( $ns ), $ATL_EXPRESSION_namespaces ) ) {
                    return;
                }
            }
            
            if ( $this->_receiver ) 
			{
                $str = sprintf( 'Receiver already set to \'%s\' in \'%s\'', 
					$this->_receiver, 
					$this->_src
				);

                return PEAR::raiseError( $str );
            }
            
            $this->_receiver = $m[1];
            
            // that the way to replace: in setters (usually this this should
            // only be used under ap:attributes tag!
            $this->_receiver = str_replace( ':', '__atl_es_dd__', $this->_receiver );
            $this->_src = $m[2];
			
            if ( $this->_receiver == "structure" ) 
			{
                $this->_structure = true;
                $this->_receiver  = false;
                
				$this->_extractReceiver();
            }
        }
    }
	
	/**
	 * @access private
	 */
    function _evaluateSequence( $seq )
    {
        $temp = $this->_gen->newTemporaryVar();
        $this->_gen->doIf( '!$__ctx__->_errorRaised' );
        $this->_gen->doDo();
		
        foreach ( $seq as $s ) 
		{
            // skip empty parts
            if ( strlen( trim( $s ) ) > 0 ) 
			{
                $code = ATL_Expression::_getCode( $this, $s );
				
                if ( PEAR::isError( $code ) ) 
					return $code;

                $this->_gen->doUnset( $temp );
                $this->_gen->doAffectResult( $temp, $code );
                
				$this->_gen->doIf( '!PEAR::isError(' . $temp . ') && ' . 
                                  // $temp .' != false && ' .
                                  $temp . ' !== null' );
                
				$this->_gen->execute( '$__ctx__->_errorRaised = false' );
                $this->_gen->execute( 'break' );
                $this->_gen->endBlock();
            }
        }
		
        $this->_gen->doEndDoWhile( '0' );
		
        // test errorRaised
        $this->_gen->doElse();
        
        // $this->_gen->doAffectResult( $temp, '""' ); // $__ctx__->_errorRaised');
        $this->_gen->doAffectResult( $temp, '$__ctx__->_errorRaised' );
        $this->_gen->execute( '$__ctx__->_errorRaised = false' );
        $this->_gen->endBlock();

        $err = $this->_useResult( $temp );
                                    
        // $this->_gen->endBlock();
        return $err;
    }

	/**
	 * @access private
	 */    
    function _useResult( $temp )
    {
        if ( $this->_policy & ATL_EXPRESSION_RECEIVER_IS_TEMP ) 
            $this->_gen->doReference( $this->_receiver, $temp );
		else if ( $this->_policy & ATL_EXPRESSION_RECEIVER_IS_OUTPUT ) 
            $this->_gen->doPrintVar( $temp, $this->_structure );
		else if ( $this->_policy & ATL_EXPRESSION_RECEIVER_IS_CONTEXT ) 
            $this->_gen->doContextSet( $this->_receiver, $temp );
		else 
            return PEAR::raiseError( "Expression '$this->_src' Don't know what to do with result." );
    }

    /**
     * Retrieve a function namespace for given string and the associated
     * expression.
     *
     * Examples:
     *
     * The function namespace of 'php:XXXX' is 'php'
     * The function namespace of 'XXXX' is 'path'
     * The function namespace of 'foo:bar::baz' is 'foo'
     * 
     * @param string $str 
     *        Expression string without receiver
     *        
     * @return array
     *        An array composed as follow : array('ns', 'exp'),
     *        Where 'ns' is the function namespace and 'exp', is the 
     *        source string without the 'ns:' part.
	 *
	 * @access private
     */
    function _findFunctionNamespace( $str )
    {
        $str = preg_replace( '/^\s/sm', '', $str );
		
        if ( preg_match( '/^([a-z0-9\-]+):(?!>:)(.*?)$/ism', $str, $m ) ) 
		{
            list( $ns, $path ) = array_slice( $m, 1 );
            $ns = str_replace( '-', '_', $ns );
            
			return array( $ns, $path );
        }
		
        return array( 'path', $str );
    }

    /**
     * Get the code for a ns:args string.
	 *
	 * @access private
     */
    function _getCode( &$exp, $str )
    {
        list( $ns, $args ) = ATL_Expression::_findFunctionNamespace( $str );
        $func = "ATL_ES_$ns";
		
        if ( !function_exists( $func ) )
            return PEAR::raiseError( "Unknown function $func in '$str'." );
        
        return $func( $exp, $args );
    }
} // END OF ATL_Expression

?>

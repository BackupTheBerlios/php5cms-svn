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


using( 'template.atl.ATL_ArrayResolver' );
using( 'template.atl.ATL_ObjectResolver' );
using( 'template.atl.ATL_StringResolver' );


/**
 * Template context handler.
 *
 * This object produce a translation between variables passed to the template
 * and regular template pathes.
 *
 * What are Context and Resolvers?
 * 
 * In atl, variables and methods are referenced by path without distinction,
 * also, arrays and hashtable elements are accessed using a path like
 * myarray/10 to reach the 11's element of myarray or myhash/mykey to
 * reach the value of mykey in myhash.
 * 
 * This allow things like 'myobject/mymethod/10/othermethod' which is usefull
 * in templates.
 * 
 * The Context is an array containing all template variables.
 * When asked to reach a path, it look for the variable type of the first path
 * element and generate a resolver matching the variable type.
 * Then this resolver is asked to resolve the rest of the path, etc... up to 
 * the final path element.
 * 
 * Resolver knows how to handle a given path relatively to the object they
 * handle.
 * 
 * For instance, an array resolver can handle access to its array elements
 * given a key (string) or the element id (int).
 * 
 * The array resolver also bring an array method : count which return the
 * number of elements in the array.
 * 
 * Note
 * 
 * If a name conflict occurs between an object's variable and one of its
 * methods, the variable is return in stead of the method.
 *
 * @package template_atl
 */
 
class ATL_Context extends PEAR
{
	/**
	 * @access private
	 */
    var $_array = array();
	
	/**
	 * @access private
	 */
    var $_errorRaised = false;

	
    /**
     * Constructor
     *
     * @param  hashtable $hash (opt)
	 * @access public
     */
    function ATL_Context( $hash = array() )
    {
        $this->_array = $hash;
    }
    
	
    /**
     * Set a value by reference.
     *
     * @param  string $path  Variable path
     * @param  mixed  $value Reference to variable.
	 * @access public
     */
    function setRef( $path, &$value )
    { 
        $this->remove( $path );
        $this->_array[$path] =& $value;
    }

    /**
     * Set a context variable.
     *
     * @param  string $path  Variable path
     * @param  mixed  $value Value
	 * @access public
     */
    function set( $path, $value )
    {
        $this->remove( $path );
        $this->_array[$path] = $value;
    }

    /**
     * Remove a context variable.
     *
     * @param  string $path Context path
	 * @access public
     */
    function remove( $path )
    {
        if ( array_key_exists( $path, $this->_array ) )
            unset( $this->_array[$path] ); 
    }

    /**
     * Test if the context can resolve specified path.
     *
     * @param string $path 
     *        Path to a context resource.
     *
     * @return boolean
	 * @access public
     */
    function has( $path )
    {
        if ( array_key_exists( $path, $this->_array ) )
            return true;
        
        $resp =& ATL_ArrayResolver::get( $this->_array, $path );
		
        if ( PEAR::isError( $resp ) )
            return false;
        else if ( $resp === null )
            return false;
        
        return true;
    }

    /**
     * Return the context associative object.
	 *
	 * @access public
     */
    function &getHash()
    {
        return $this->_array;
    }

    /**
     * Retrieve specified context resource.
     *
     * @param string $path
     *        Path to a context resource.
     *        
     * @return mixed
     * @throws Error
	 * @access public
     */
    function &get( $path )
    {
        if ( array_key_exists( $path, $this->_array ) )
            return $this->_array[$path];
        
        $resp =& ATL_ArrayResolver::get( $this->_array, $path );
		
        if ( PEAR::isError( $resp ) )
            $this->_errorRaised = $resp; 
        else if ( $resp === null )
            $this->_errorRaised = true;
        
        return $resp;
    }

    /**
     * Retrieve specified context resouce as a string object.
     *
     * @param string $path
     *        Path to a context resource.
     *
     * @return string
	 * @access public
     */
    function &getToString( $path )
    {
        $o = $this->get( $path );
		
        if ( is_object( $o ) && method_exists( $o, "toString" ) )
            return $o->toString();
        
        if ( is_object( $o ) || is_array( $o ) ) 
		{
            ob_start();
            print_r( $o ); // var_dump( $o );
            $res = ob_get_contents();
            ob_end_clean();
            
			return $res;
        }
		
        return $o;
    }
    
    /**
     * Tells if an error was raised by this context accessing an unknown path.
     *
     * @return boolean
	 * @access public
     */
    function errorRaised()
    {
        $r = $this->_errorRaised;
        $this->_errorRaised = false;
		
        return $r;
    }

    /**
     * Clean error handler.
	 *
	 * @access public
     */
    function cleanError()
    {
        $this->_errorRaised = false;
    }
	
	
	// static methods
	
	/**
 	 * Retrieve a resolver given a value and a parent.
	 * 
	 * @static
	 * @access public
 	 */
	function &resolve( $path, &$value, &$parent )
	{
    	if ( is_array( $value ) )  
			return ATL_ArrayResolver::get( $value, $path );
    	
		if ( is_object( $value ) ) 
			return ATL_ObjectResolver::get( $value, $path );
    	
		if ( is_string( $value ) ) 
			return ATL_StringResolver::get( $value, $path );
	
		return PEAR::raiseError( "Unable to find adequate resolver for '" . gettype( $value ) . "' remaining path is '$path'." );
	}
	
	/**
 	 * Shift the first element of a atl path.
 	 *
 	 * Returns an array containing the first element of the path and the rest of
 	 * the path.
	 *
	 * @static
	 * @access public
 	 */
	function path_explode( $path )
	{
    	if ( preg_match( '|^(.*?)\/(.*?)$|', $path, $match ) ) 
		{
        	array_shift( $match );
        	return $match;
    	} 
		else 
		{
        	return array( $path, false );
    	}

    	$pos = strpos( $path, "." );
		
    	if ( $pos !== false ) 
		{ 
        	$first = substr( $path, 0, $pos  );
        	$next  = substr( $path, $pos + 1 );
        
			return array( $first, $next );
    	}
    
		return array( $path, false );
	}
} // END OF ATL_Context

?>

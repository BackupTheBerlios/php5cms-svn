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
 * This class provides helpful functions for commandline applications
 * to parse the argument list. It supports short and long options, 
 * e.g. -h or --help
 *
 * @package sys_console
 */

class ArgumentParser extends PEAR
{
	/**
	 * @access public
	 */
    var $list = array();
	
	/**
	 * @access public
	 */
	var $count = 0;
	
	/**
	 * @access public
	 */
	var $string = '';
    
	
    /**
     * Constructor
     * 
     * @access  public
     * @param   array list default null the argv array. If omitted, $_SERVER['argv'] is used
     */
    function ArgumentParser( $list = null ) 
	{
	  	$this->setParams( ( $list? $_SERVER['argv'] : $list) === null );
    }
    
	
    /**
     * Set the parameter string.
     * 
     * @access  public
     * @param   array params
     */  
    function setParams( $params ) 
	{
      	$this->list   = $params;
      	$this->count  = sizeof( $params );
      	$this->string = implode( ' ', $params );
    }
      
    /**
     * Checks whether a parameter is set.
     * 
     * @access  public
     * @param   string long long parameter (w/o --)
     * @param   string short default null Short parameter (w/o -), defaults to the first char of the long param
     * @return  boolean
     */  
    function exists( $long, $short = null ) 
	{
      	if ( is_int( $long ) ) 
			return isset( $this->list[$long] );
      
	  	return ( $this->_find( $long, $short ) !== false );
    }
    
    /**
     * Retrieve the value of a given parameter.
     *
     * Examples:
     * <code>
     *   $p= &new ArgumentParser();
     *   if ( $p->exists( 'help', '?' ) ) {
     *     printf( "Usage: php %s --force-check [--pattern=<pattern>\n", $p->value( 0 ) );
     *     exit( -2 );
     *   }
     * 
     *   $force   = $p->exists( 'force-check', 'f'  );
     *   $pattern = $p->value( 'pattern', 'p', '.*' );
     * 
     *   // ...
     * </code>
     * 
     * @access  public
     * @param   string long long parameter (w/o --)
     * @param   string short default null Short parameter (w/o -), defaults to the first char of the long param
     * @param   string default default null A default value if parameter does not exist
     * @return  string 
     * @throws  Error if parameter does not exist and no default value was supplied.
     */ 
    function value( $long, $short = null, $default = null ) 
	{
      	if ( is_int( $long ) ) 
		{
        	if ( $default === null && !isset( $this->list[$long] ) )
          		return PEAR::raiseError( 'Parameter #' . $long . ' does not exist.' ); 

        	return isset( $this->list[$long] )? $this->list[$long] : $default;
      	}        
  
      	$pos = $this->_find( $long, $short );
      
	  	if ( $pos === false && $default === null )
			return PEAR::raiseError( 'Parameter --' . $long . ' does not exist.' ); 
        
      	return ( $pos !== false? str_replace( "--{$long}=", '', $this->list[$pos] ) : $default );
  	}
	
	
	// private methods
	
    /**
     * Private helper function that iterates through the parameter array.
     * 
     * @access  private
     * @param   string long long parameter (w/o --)
     * @param   string short default null Short parameter (w/o -), defaults to the first char of the long param
     * @return  mixed position on which the parameter is placed or false if nonexistant
     */ 
    function _find( $long, $short = null ) 
	{
      	if ( is_null( $short ) ) 
			$short = $long{0};
      
	  	for ( $i = 0; $i < sizeof( $this->list ); $i++ ) 
		{
        	// Short notation (e.g. -f value)
        	if ( $this->list[$i] == '-' . $short ) 
				return $i + 1;
        
        	// Long notation (e.g. --help, without a value)
        	if ( $this->list[$i] == '--' . $long ) 
				return $i;
        
        	// Long notation (e.g. --file=*.txt)
        	if ( substr( $this->list[$i], 0, strlen( $long ) + 3 ) == '--' . $long . '=' ) 
				return $i;
      	}
      
      	return false;
    }
} // END OF ArgumentParser

?>

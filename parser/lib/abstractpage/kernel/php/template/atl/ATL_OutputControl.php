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


using( 'template.atl.ATL_Template' );


/**
 * @package template_atl
 */
 
class ATL_OutputControl extends PEAR
{
	/**
	 * @access private
	 */	
	var $_context;

	/**
	 * @access public
	 */		
    var $_buffer = "";
	
	/**
	 * @access public
	 */	
    var $_buffers = array();
	
	/**
	 * @access public
	 */	
    var $_quoteStyle = ENT_COMPAT;
	
	/**
	 * @access public
	 */	
    var $_encoding = 'UTF-8';


	/**
	 * Constructor
	 *
	 * @access public
	 */    
    function ATL_OutputControl( &$context, $encoding = 'UTF-8' )
    {
        $this->_context  = $context;
        $this->_encoding = $encoding;
    }

	
	/**
	 * @access public
	 */	
    function pushBuffer()
    {
        $this->_buffers[] = $this->_buffer;
        $this->_buffer    = "";
    }

	/**
	 * @access public
	 */	    
    function popBuffer()
    {
        $res = $this->_buffer;
        $this->_buffer = array_pop( $this->_buffers );
		
        return $res;
    }

	/**
	 * @access public
	 */	    
    function write( &$str )
    {
        if ( is_object( $str ) ) 
		{
            if ( PEAR::isError( $str ) ) 
			{
                $str = "[" . get_class( $str ) . ": " . $str->getMessage() . "]";
                // $this->_context->cleanError();
            } 
			else 
			{
                $str = ATL_Template::toString( $str );
            }
        }

        if ( defined( "ATL_DIRECT_OUTPUT" ) && count( $this->_buffers ) == 0 ) 
            echo htmlentities( $str, $this->_quoteStyle, $this->_encoding );
		else 
            $this->_buffer .= htmlentities( $str, $this->_quoteStyle, $this->_encoding );
    }

	/**
	 * @access public
	 */	
    function writeStructure( $str )
    {
        if ( is_object( $str ) ) 
		{
            if ( PEAR::isError( $str ) ) 
                $str = "[" . $str->message . "]";
			else 
                $str = ATL_Template::toString( $str );
        }
        
        if ( defined( "ATL_DIRECT_OUTPUT" ) && count( $this->_buffers ) == 0 )
            echo $str;
        else
            $this->_buffer .= $str;
    }

	/**
	 * @access public
	 */	
    function &toString()
    {
        return $this->_buffer;
    }
} // END OF ATL_OutputControl

?>

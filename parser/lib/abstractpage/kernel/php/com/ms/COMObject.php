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
 * COM is a technology which allows the reuse of code written in any language 
 * (by any language) using a standard calling convention and hiding behind 
 * APIs the implementation details such as what machine the Component is 
 * stored on and the executable which houses it. It can be thought of as a 
 * super Remote Procedure Call (RPC) mechanism with some basic object roots. 
 * It separates implementation from interface.
 *
 * Example
 *
 * $com = new COMObject( 'Outlook.Application' );
 * $com = new COMObject( 'Word.Application' );
 * $com = new COMObject( 'Excel.Application' );
 * $com = new COMObject( 'WScript.Shell' );
 *
 * @link     http://www.microsoft.com/Com/resources/comdocs.asp COM specification
 * @link     http://www.developmentor.com/dbox/yacl.htm Yet Another COM Library (YACL) 
 * @package  com_ms
 */

class COMObject extends Base
{
	/**
	 * @access protected
	 */
    protected $_com_obj = null;
  
  
    /**
     * Constructor
     *
     * @access  public
     * @param   string identifier
     * @param   string server default null
     */    
    public function __construct( $identifier, $server = null ) 
	{
	  	$this->_com_obj = com_load( $identifier, $server );
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    public function __destruct() 
	{
      	com_release( $this->_com_obj );
      	$this->_com_obj = null;
    }
	
	
    /**
     * Magic interceptor for member read access.
     *
     * @access  magic
     * @param   string name
     * @param   &mixed value
     * @return  bool success
     */
    protected function __get( $name, &$value ) 
	{
      	$value= &com_get( $this->_com_obj, $name );
      	return true;
    }
    
    /**
     * Magic interceptor for member write access.
     *
     * @access  magic
     * @param   string name
     * @param   &mixed value
     * @return  bool success
     */
    protected function __set( $name, &$value ) 
	{
      	com_set( $this->_com_obj, $name, $value );
      	return true;
    }
    
    /**
     * Magic interceptor for member method access.
     *
     * @access  magic
     * @param   string name
     * @param   array args
     * @param   &mixed return
     * @return  bool success
     */
    protected function __call( $name, $args, $return ) 
	{
      	$return = call_user_func_array(
        	'com_invoke', 
        	array_merge( array( &$this->_com_obj, $name ), $args )
      	);
      
	  	return true;
    }
} overload( 'COMObject' ); // END OF COMObject

?>

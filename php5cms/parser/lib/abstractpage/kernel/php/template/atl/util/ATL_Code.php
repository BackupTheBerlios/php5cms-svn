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
using( 'template.atl.util.ATL_Dictionary' );


/**
 * Common php string that extract parameters from an associative array.
 *
 * This string is appended to the begin of the produced php function. It
 * takes function context argument and extract it in named variables.
 * 
 * @access private
 */
define( 'ATL_CODE_EXTRACT_CODE_CONTEXT',
'// BEGIN EXTRACT __context__ 
if (is_array($__context__)) { extract($__context__); }
if (is_object($__context__)) { extract($__context__->toHash()); }
// END EXTRACT __context__
' );

/**
 * Code class handle and evaluate php code.
 *
 * The aim of this class is to dynamically generate executable php code from
 * php string.
 *
 * This kind of object can be safely serialized as the code it represents is
 * stored in the _code member variable.
 *
 * When setting code to this object, a new anonymous function is created
 * waiting to be invoqued using the execute() method.
 *
 * As we can't know how many parameters this function should take, a 'context'
 * hashtable is used as only parameter. This hash  may contains any number
 * of arguments with var name compliant keys.
 *
 * Code object automatically append 'extract' code string to produced code.
 *
 * It's up to the code to assert parameters using 'isset()' php function.
 *
 * $o_code = ATL_Code()
 * $o_code->setCode('return $foo . $bar . $baz;');
 * $res = $o_code->execute(
 *		'foo', 'foo value ',
 *		'baz', 'bar value ',
 *		'baz', 'baz value'
 * );
 *  
 * // will print 'foo value bar value baz value'
 * echo $res, end;
 *
 * @package template_atl_util
 */
 
class ATL_Code extends PEAR
{
	/**
	 * @access private
	 */
    var $_code;
	
	/**
	 * @access private
	 */
    var $_function;

	/**
	 * @access private
	 */	
    var $_compiled = false;


    /**
     * Constructor
     *
     * @param  mixed $code (optional) 
     *         php code string or object implementing toString method.
	 * @access public
     */
    function ATL_Code( $code = false )
    {
        if ( !$code ) 
            return $this; 
        
        if ( is_object( $code ) && get_class( $code ) == 'code' )
            $this->_code = $code->getCode();
        else
            $this->setCode( ATL_Template::toString( $code ) );
    }
    
	
    /**
     * Execute code with specific context.
     *
     * @param  mixed ...
     *         The function execution context. This may be an associative
     *         array, an ATL_Dictionary object, a list of key/value pairs that will 
     *         be transformed into an associative array
     *         
     * @return mixed The execution result.
	 * @access public
     */
    function &execute()
    {
        if ( !$this->_compiled ) 
		{
            $err =& $this->compile();
            
			if ( PEAR::isError( $err ) ) 
                return $err;
        }
		
        $argv = func_get_args();
        $argc = func_num_args();
        
		switch ( $argc ) 
		{
            case 1:
                $context = $argv[0];
                break;
            
			default:
                $context = ATL_Dictionary::arrayToHash( $argv );
                break;
        }
		
        $func = $this->_function;
        return $func( $context );
    }

    /**
     * Compile php code.
     *
     * This function may produce parse errors.
     *
     * @throws Error
	 * @access public
     */
    function compile() 
    {
        ob_start();
        $this->_function = create_function( '$__context__', $this->_code );
        $ret = ob_get_contents();
        ob_end_clean();

        if ( !$this->_function ) {
            return PEAR::raiseError( $ret );
        }
        
        $this->_compiled = true;
    }
    
    /**
     * Set function code.
     *
     * @param  string $str
     *         The php code string
	 * @access public
     */
    function setCode( $str )
    {
        // add extract code to function code
        $str = ATL_CODE_EXTRACT_CODE_CONTEXT . $str;
        $this->_code = $str;
    }
    
    /**
     * Retrieve code.
     *
     * @return string
	 * @access public
     */
    function getCode()
    {
        return $this->_code;
    }

    /**
     * Make a string representation of this object.
     *
     * @return string
	 * @access public
     */
    function toString()
    {
        if ( $this->_compiled )
            return '<Code \''.$this->_function.'\'>';
        else
            return '<Code \'not compiled\'>';
    }
	
    /**
     * On serialization, we store only code, not additional variables.
     * 
     * @access private
     */
    function __sleep()
    {
        return array( "_code" );
    }
} // END OF ATL_Code

?>

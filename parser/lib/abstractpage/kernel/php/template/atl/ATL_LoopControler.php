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


using( 'template.atl.ATL_DBResultIterator' );
using( 'template.atl.util.ATL_Array' );
using( 'template.atl.util.ATL_Dictionary' );


/**
 * Template loop execution controler.
 *
 * This object is instantiated by the template on each loop. 
 *
 * LoopControlers accept different types of loop target.
 * 
 * - array
 * - objects having an getNewIterator() method returning an Iterator object.
 * - Iterator objects which produce isValid(), next(), key() and index() 
 *   methods.
 *   please note that key() return index() for non associative data.
 *
 * Other types are rejected by the loop controler on runtime producing
 * an Error exception.
 *
 * The loop controler install its iterator under the path "repeat/item"
 * where "item" is the name of the output var (defined in the template).
 *
 * Thus, the template can access differents methods of the iterator:
 *    repeat/someitem/key
 *    repeat/someitem/index
 *    repeat/someitem/next
 *    repeat/someitem/last
 *
 * If not all of these methods are implemented in iterators, ask 
 * iterator maintainers.
 *
 * @package template_atl
 */
 
class ATL_LoopControler extends PEAR
{
	/**
	 * @access private
	 */
    var $_context;
	
	/**
	 * @access private
	 */
    var $_data;
	
	/**
	 * @access private
	 */
    var $_data_name;
	
	/**
	 * @access private
	 */
    var $_iterator;
	
	/**
	 * @access private
	 */
    var $_error = null;
 
    
    /**
     * Constructor
     *
     * @param  ATL_Context $context
     *         The template context.
     * @param  string $data_name
     *         The item data name.
     * @param  mixed  $data
     *         Loop resource.
	 * @access public
     */
    function ATL_LoopControler( &$context, $data_name, $data )
    {
        $this->_context   =& $context;
        $this->_data      =& $data;
        $this->_data_name =  $data_name;
        
        // ensure that data is not an error
        if ( PEAR::isError( $data ) ) 
		{
            $this->_error =& $data;
            return $data;
        }
        
        // accepted objects
        // 
        // - iteratable implementing getNewIterator() method
        // - iterator implementing next(), isValid() and index() methods
        // - dbresult produced by DB package
        if ( is_object( $data ) ) 
		{
            if ( method_exists( $data, "getNewIterator" ) ) 
			{    
                $this->_iterator =& $data->getNewIterator();   
            } 
			else if ( is_a( "iterator", $data ) || ( method_exists( $data, 'next' ) && method_exists( $data, 'isValid' ) && method_exists( $data, 'index' ) ) ) 
			{    
                $this->_iterator =& $data;   
            } 
			else if ( get_class( $data ) == 'dbresult' ) 
			{    
                $this->_iterator = new ATL_DBResultIterator($data);
                
            } else {
                
                $err = new PEAR_Error( "ATL loop controler received a non Iterable object (" . get_class( $data ) . ")." );
                $this->_error =& $err;
                
				return $err;
            }
        } 
		else if ( is_array( $data ) ) 
		{
            // array are accepted thanks to ATL_ArrayIterator 
            reset( $data );
			
            if ( count( $data ) > 0 && array_key_exists( 0, $data ) ) 
			{
                $this->_data = new ATL_Array( $data );
                $this->_iterator =& $this->_data->getNewIterator();
            } 
			else 
			{
                $this->_data = new ATL_Dictionary( $data );
                $this->_iterator =& $this->_data->getNewIterator();
            }
        } 
		else 
		{
            $err = new PEAR_Error( "ATL loop controler received a non Iterable value (" . gettype( $data ) . ")." );
            $this->_error =& $err;
 
            return $err;
        }

        // install loop in repeat context array
        $repeat =& $this->_context->get( "repeat" );
		
        if ( array_key_exists( $this->_data_name, $repeat ) )
            unset( $repeat[$this->_data_name] );
        
        $repeat[$this->_data_name] =& $this;        

        // $this->_context->setRef( $this->_data_name, $temp );
        $temp =& $this->_iterator->value();
        $this->_context->set( $this->_data_name, $temp );
		
        return $temp;
    }

    /**
     * Return current item index.
     * 
     * @return int
	 * @access public
     */
    function index()
    {
        return $this->_iterator->index();
    }

    /**
     * Return current item key or index for non associative iterators.
     *
     * @return mixed
	 * @access public
     */
    function key()
    {
        if ( method_exists( $this->_iterator, "key" ) )
            return $this->_iterator->key();
        else
            return $this->_iterator->index();
    }

    /**
     * Index is in range(0, length-1), the number in in range(1, length).
     *
     * @return int
	 * @access public
     */
    function number()
    {
        return $this->index() + 1;
    }

    /**
     * Return true if index is even.
     *
     * @return boolean
	 * @access public
     */
    function even()
    {
        return !$this->odd();
    }

    /**
     * Return true if index is odd.
     *
     * @return boolean
	 * @access public
     */
    function odd()
    {
        return ( $this->index() % 2 );
    }

    /**
     * Return true if at the begining of the loop.
     *
     * @return boolean
	 * @access public
     */
    function start()
    {
        return ( $this->index() == 0 );
    }

    /**
     * Return true if at the end of the loop (no more item).
     *
     * @return boolean
	 * @access public
     */
    function end()
    {
        return ( $this->length() == $this->number() );
    }

	/**
	 * @access public
	 */
    function isValid()
    {
        return $this->_iterator->isValid();
    }

    /**
     * Return the length of the data (total number of iterations). 
     *
     * @return int
	 * @access public
     */
    function length()
    {
        return $this->_data->size();
    }
   
    /**
     * Retrieve next iterator value.
     *
     * @return mixed
	 * @access public
     */
    function &next()
    {
        $temp =& $this->_iterator->next();
        
		if ( !$this->_iterator->isValid() )
            return false;
        
        // $this->_context->setRef( $this->_data_name, $temp );
        $this->_context->set( $this->_data_name, $temp );

        return $temp;
    }
} // END OF ATL_LoopControler

?>

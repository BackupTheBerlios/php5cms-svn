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
 * Semaphore Class
 *
 * @package sys
 */

class Semaphore extends PEAR
{
	/**
	 * @access public
	 */
    var $key = 0;
	
	/**
	 * @access public
	 */
	var $maxAquire = 1;
      
	/**
	 * @access private
	 */
    var $_hdl = null;
      
	  
    /**
     * Get a semaphore.
     *
     * Note: A second call to this function with the same key will actually return
     * the same semaphore
     *
     * @static
     * @access  public
     * @param   int key
     * @param   int maxAquire default 1
     * @param   int permissions default 0666
     * @return  Semaphore
     * @throws  Error
     */
    function &get( $key, $maxAquire = 1, $permissions = 0666 ) 
	{
      	static $semaphores = array();
      
      	if ( !isset( $semaphores[$key] ) ) 
		{
        	$s = &new Semaphore();
        	$s->key = $key;
        	$s->maxAquire = $maxAquire;
        	$s->permissions = $permissions;
        
			if ( ( $s->_hdl = sem_get( $key, $maxAquire, $permissions ) ) === false )
          		return PEAR::raiseError( 'Could not get semaphore ' . $key );
        
        	$semaphores[$key]= &$s;
      }
      
      return $semaphores[$key];
    }
    
    /**
     * Acquire a semaphore - blocks (if necessary) until the semaphore can be acquired. 
     * A process attempting to acquire a semaphore which it has already acquired will 
     * block forever if acquiring the semaphore would cause its max_acquire value to 
     * be exceeded. 
     *
     * @access  public
     * @return  bool success
     * @throws  Error
     */
    function acquire()
	{
      	if ( sem_acquire( $this->_hdl ) === false )
			return PEAR::raiseError( 'Could not acquire semaphore ' . $this->key );
      
      	return true;
    }
    
    /**
     * Release a semaphore.
     * After releasing the semaphore, acquire() may be called to re-acquire it. 
     *
     * @access  public
     * @return  bool success
     * @throws  Error
     */
    function release()
	{
      	if ( sem_release( $this->_hdl ) === false )
        	return PEAR::raiseError( 'Could not release semaphore ' . $this->key );
      
      	return true;
    }
    
    /**
     * Remove a semaphore.
     * After removing the semaphore, it is no more accessible.
     *
     * @access  public
     * @return  bool success
     * @throws  Error
     */
    function remove()
	{
      	if ( sem_remove( $this->_hdl ) === false )
        	return PEAR::raiseError( 'Could not remove semaphore ' . $this->key );
      
      	return true;
    }
} // END OF Semaphore

?>

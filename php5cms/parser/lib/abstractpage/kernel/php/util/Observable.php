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
 * Observable - base class for Model/View/Controller architecture.
 *
 * Example:
 *
 * class ObservableValue extends Observable 
 * {
 *     var $n = 0;
 *     
 *     function ObservableValue( $n ) 
 *     {
 *       	$this->n = $n;
 *     }
 *     
 *     function setValue( $n ) 
 *     {
 *       	$this->n = $n;
 *       	self::setChanged();
 *       	self::notifyObservers();
 *     }
 *     
 *     function getValue() 
 *     {
 *       return $this->n;
 *     }
 * }
 *
 * class TextObserver extends PEAR 
 * {
 *     function update( &$obs, $arg = null ) 
 *     {
 *       	echo __CLASS__, ' was notified of update in value, is now ';
 *       	var_dump( $obs->getValue() );
 *     }
 * }
 * 
 * $value= &new ObservableValue( 3 );
 * $value->addObserver( new TextObserver() );
 * $value->setValue( 5 );
 *
 * The update method gets passed the instance of Observable as its first
 * argument and - if existant - the argument passed to notifyObservers as 
 * its second.
 *
 * @link http://www.javaworld.com/javaworld/jw-10-1996/jw-10-howto.html
 * @package util
 */

class Observable extends PEAR
{
	/**
	 * @access private
	 */
    var $_obs = array();
	
	/**
	 * @access private
	 */
    var $_changed = false;
      
	  
    /**
     * Add an observer.
     *
     * @access  public
     * @param   &util.Observer observer a class implementing the util.Observer interface
     */
    function addObserver( &$observer ) 
	{
      	$this->_obs[]= &$observer;
    }
    
    /**
     * Notify observers.
     *
     * @access  public
     * @param   mixed arg default null
     */
    function notifyObservers( $arg = null ) 
	{
      	if ( !$this->hasChanged() ) 
			return;
      
      	for ( $i = 0, $s = sizeof( $this->_obs ); $i < $s; $i++ )
        	$this->_obs[$i]->update( $this, $arg );
      
      	$this->clearChanged();
    }
    
    /**
     * Sets changed flag.
     *
     * @access  protected
     */
    function setChanged()
	{
      	$this->_changed = true;
    }

    /**
     * Clears changed flag.
     *
     * @access  protected
     */
    function clearChanged()
	{
      	$this->_changed = false;
    }

    /**
     * Checks whether changed flag is set.
     *
     * @access  public
     * @return  bool
     */
    function hasChanged()
	{
      	return $this->_changed;
    }    
} // END OF Observable

?>

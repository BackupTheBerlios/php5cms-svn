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
 * Range Class
 *
 * Used to model the data used when working with sliders, scrollbars and progress bars. 
 * Based on the ideas of the javax.swing.BoundedRangeModel interface defined by Sun for Java 
 * (http://java.sun.com/products/jfc/swingdoc-api-1.0.3/com/sun/java/swing/BoundedRangeModel.html).
 *
 * @package util
 */
 
class Range extends PEAR
{
	/**
	 * @access private
	 */
	var $_value;
	
	/**
	 * @access private
	 */
	var $_minimum;
	
	/**
	 * @access private
	 */
	var $_maximum;
	
	/**
	 * @access private
	 */
	var $_extends;
	
	/**
	 * @access private
	 */
	var $_isChanging;
	
	/**
	 * @access private
	 */
	var $_callback;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Range()
	{
		$this->_value   = 0;
		$this->_minimum = 0;
		$this->_maximum = 100;
		$this->_extent  = 0;
	
		$this->_isChanging = false;
		$this->_callback   = null;
	}
	

	/**
	 * @access public
	 */	
	function setValue( $value )
	{
		if ( !is_numeric( (int)$value ) )
			return;
	
		settype( $value, "integer" );
		
		if ( $this->_value != $value ) 
		{
			if ( $value + $this->_extent > $this->_maximum )
				$this->_value = $this->_maximum - $this->_extent;
			else if ( $value < $this->_minimum )
				$this->_value = $this->_minimum;
			else
				$this->_value = $value;
		
			if ( !$this->_isChanging && function_exists( $this->_callback ) )
				 $this->callback();
		}
	}
	
	/**
	 * @access public
	 */	
	function getValue()
	{
		return $this->_value;
	}
	
	/**
	 * @access public
	 */	
	function setExtent( $extent )
	{
		if ( $this->_extent != $extent ) 
		{
			if ( $extent < 0 )
				$this->_extent = 0;
			else if ( $this->_value + $extent > $this->_maximum )
				$this->_extent = $this->_maximum - $this->_value;
			else
				$this->_extent = $extent;
			
			if ( !$this->_isChanging && function_exists( $this->_callback ) )
				 $this->callback();
		}
	}
	
	/**
	 * @access public
	 */	
	function getExtent()
	{
		return $this->_extend;
	}
	
	/**
	 * @access public
	 */	
	function setMinimum( $minimum )
	{
		if ( $this->_minimum != $minimum ) 
		{
			$oldIsChanging     = $this->_isChanging;
			$this->_isChanging = true;
			$this->_minimum    = $minimum;
		
			if ( $minimum > $this->_value )
				$this->setValue( $minimum );
			
			if ( $minimum > $this->_maximum ) 
			{
				$this->_extent = 0;
				$this->setMaximum( $minimum );
				$this->setValue( $minimum );
			}
		
			if ( $minimum + $this->_extent > $this->_maximum )
				$this->_extent = $this->_maximum - $this->_minimum;

			$this->_isChanging = $oldIsChanging;
		
			if ( !$this->_isChanging && function_exists( $this->_callback ) )
				 $this->callback();
		}
	}
	
	/**
	 * @access public
	 */	
	function getMinimum()
	{
		return $this->_minimum;
	}
	
	/**
	 * @access public
	 */	
	function setMaximum( $maximum )
	{
		if ( $this->_maximum != $maximum ) 
		{
			$oldIsChanging     = $this->_isChanging;
			$this->_isChanging = true;
			$this->_maximum    = $maximum;		
		
			if ( $maximum < $this->_value )
				$this->setValue( $maximum - $this->_extent );

			if ( $maximum < $this->_minimum ) 
			{
				$this->_extent = 0;
				$this->setMinimum( $maximum );
				$this->setValue( $this->_maximum );
			}
		
			if ( $maximum < $this->_minimum + $this->_extent )
				$this->_extent = $this->_maximum - $this->_minimum;

			if ( $maximum < $this->_value + $this->_extent )
				$this->_extent = $this->_maximum - $this->_value;
		
			$this->_isChanging = $oldIsChanging;
		
			if ( !$this->_isChanging && function_exists( $this->_callback ) )
				 $this->callback();
		}
	}
	
	/**
	 * @access public
	 */	
	function getMaximum()
	{
		return $this->_maximum;
	}
	
	/**
	 * @access public
	 */	
	function setCallback( $fn )
	{
		$this->_callback = $fn;
	}
	
	/**
	 * @access public
	 */	
	function getCallback()
	{
		return $this->_callback;
	}
} // END OF Range

?>

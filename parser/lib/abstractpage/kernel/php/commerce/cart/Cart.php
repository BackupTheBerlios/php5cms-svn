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
 * @package commerce
 */
 
class Cart extends PEAR
{
	/**
	 * @access public
	 */
	var $item = array();
	
	/**
	 * @access public
	 */
	var $currentItem = 1;
	
	/**
	 * @access public
	 */
	var $persistent_slots = array(
		"item",
		"currentItem"
	);
	
	
	/**
	 * Return the position and number of units 
	 * of an article in the cart (or false and 0, 
	 * if it is not in there)
	 *
	 * @access public
	 */
  	function check( $art )
	{
    	if ( !is_array( $this->item ) )
			return array( false, 0 );

		reset( $this->item );
		while ( list( $item, $attr ) = each( $this->item ) )
		{
			if ( isset( $attr["art"] ) && ( $attr["art"] == $art ) )
				return array( $item, $attr["num"] );
		}
    
		return array( false, 0 );
	}
	
	/**
	 * Delete all articles from current cart.
	 *
	 * @access public
	 */
	function reset()
	{
		reset( $this->item );
		while ( list( $item, $attr ) = each( $this->item ) )
			unset( $this->item[$item] );

		$this->currentItem = 1;
		return true;
	}

	/**
	 * Add num units of an article to the cart
	 * and return the item number (or false on error).
	 *
	 * @access public
	 */
	function add_item( $art, $num )
	{
		// check to see if we already have some of these
    	list ( $item, $have ) = $this->check( $art );
    
    	// we already have them
    	if ( $item )
		{
      		$this->item[$item]["num"] += $num;
      		return $item;
    	}
    
		// new article
		$item = $this->currentItem++;
		$this->item[$item]["art"] = $art;
		$this->item[$item]["num"] = $num;

		return $item;
	}
  
	/**
	 * Take num units of an article from the cart
	 * and return the item number (or false on error).
	 *
	 * @access public
	 */
	function remove_item( $art, $num )
	{
		// check to see if we have some of these
		list( $item, $have ) = $this->check($art);
    
		// can't take them out
		if ( !$item || ( $num > $have ) )
			return false;
    
		// drop the item
		if ( $num == $have )
		{
			unset( $this->item[$item] );
			return $item;
		}
    
		// take $num out...
		$this->item[$item]["num"] -= $num;
		return $item;
	}

	/**
	 * Set quantity of an article in the cart to exactly $num
	 * and return the item number.
	 *
	 * @access public
	 */
	function set_item( $art, $num )
	{
  		// check to see if we already have some of these
		list($item, $have) = $this->check( $art );
    
		// we already have them
		if ( $item )
		{
			if ( $num > 0 )
				$this->item[$item]["num"] = $num;
 			else
				unset( $this->item[$item] );

			return $item;
		}
    
		if ( $num > 0 )
		{
     		// new article
			$item = $this->currentItem++;
			$this->item[$item]["art"] = $art;
			$this->item[$item]["num"] = $num;
		}

		return $item;
	}

	/**
	 * Return the number of articles in current cart.
	 *
	 * @access public
	 */
	function num_items()
	{
		if ( !is_array( $this->item ) )
			return false;

		return count( $this->item );
	}

	/**
	 * Iterator to show cart contents.
	 *
	 * @access public
	 */
	function show_all()
	{
		if ( !is_array( $this->item ) || ( $this->item ) == 0 )
		{
			$this->show_empty_cart();
			return false;
		}

		reset( $this->item );
		$this->show_cart_open();
		
		while ( list( $item, $attr ) = each( $this->item ) )
			$this->show_item( $attr["art"], $attr["num"] );
    
		$this->show_cart_close();
	}

	/**
	 * @abstract
	 */
	function show_cart_open() 
	{ 
		return PEAR::raiseError( "Abstract method." );
	}

	/**
	 * @abstract
	 */
	function show_cart_close()
	{ 
		return PEAR::raiseError( "Abstract method." );
	}

	/**
	 * @access public
	 */
	function show_item( $art, $num )
	{
		echo( $num . " units of " . $art );
	}
  
  	/**
	 * @access public
	 */
	function show_empty_cart()
	{
		echo( "Your shopping cart is empty." );
	}
} // END OF Cart

?>

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


using( 'commerce.cart.ShoppingCartItem' );


/**
 * Shopping Cart Implementation
 *
 * Example:
 *
 * $cart = new ShoppingCart();
 *
 * // register cart to session (optional)
 * // session_start();
 * // session_register("cart");
 *
 * // add a new product into the cart
 * $cart->add_item( 123, "Productname", 1, 6.95, 16, array( "other descriptions" ) );
 *
 * // show all products from the cart
 * if ( $cart->show() )
 * {
 *		while ( $values = $cart->show_next_item() )
 *			print_r( $values );
 * }
 *
 * // show sum of all products from the cart
 * if ( $values = $cart->sum() )
 *		print_r( $values );
 *
 * // update an product of the cart
 * $cart->update_item( 123, 10 ); 
 *
 * // remove an product of the cart
 * $cart->remove_item( 123 );
 *
 * // clear complete cart
 * $cart->clear();
 *
 * @package commerce_cart
 */

class ShoppingCart extends PEAR
{
	/**
	 * @access public
	 */
	var $items;
	
	/**
	 * @access public
	 */
	var $gtotprice;
	
	/**
	 * @access public
	 */
	var $ntotprice;
	
	/**
	 * @access public
	 */
	var $tax;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ShoppingCart()
	{
		$this->_init();
	}


	/**
	 * @access public
	 */	
	function add_item( $inumber, $name, $quant, $price, $taxqual, $descr = 0 )
	{
		if ( !is_object( $this->items[$inumber] ) )
		{
			$this->items[$inumber] = new ShoppingCartItem( $inumber, $name, $quant, $price, $taxqual, $descr );
			$this->_refresh();
		}
	}

	/**
	 * @access public
	 */
	function update_item( $inumber, $quant )
	{
		if ( is_object( $this->items[$inumber] ) )
		{
			$this->items[$inumber]->update( $quant );
			$this->_refresh();
		}
	}

	/**
	 * @access public
	 */
	function remove_item( $inumber )
	{
		if ( is_object( $this->items[$inumber] ) )
		{
			unset( $this->items[$inumber] );
			$this->_refresh();
		}
	}

	/**
	 * @access public
	 */
	function clear()
	{
		unset( $this->items );
		$this->_init();
	}

	/**
	 * @access public
	 */
	function show()
	{
		reset( $this->items );
		return count( $this->items );
	}

	/**
	 * @access public
	 */
	function show_next_item()
	{
		if ( $item = each( $this->items ) )
			return $item["value"]->get_all();
	}

	/**
	 * @access public
	 */
	function sum()
	{
		if ( count( $this->items ) )
		{
			$values = array();
			$values["cart.sum.ntotprice"] = sprintf( "%0.2f", $this->ntotprice );
			$values["cart.sum.gtotprice"] = sprintf( "%0.2f", $this->gtotprice );
			
			foreach ( $this->tax as $key => $val )
				$values["cart.sum.tax." . $key] = sprintf( "%0.2f", $val );
                
			return $values;
		}
		
		return false;
	}

		
	// private methods

	/**
	 * @access private
	 */		
	function _init()
	{
		$this->items     = array();
		$this->gtotprice = 0.00;
		$this->ntotprice = 0.00;
		$this->tax       = array();
	}

	/**
	 * @access private
	 */
	function _refresh()
	{
		$this->gtotprice = 0.00;
		$this->ntotprice = 0.00;
		$this->tax       = array();
		$count           = 0;
		
		reset( $this->items );
		foreach ( $this->items as $key => $val )
		{
			$this->gtotprice += $val->get_gtotprice();
			$this->ntotprice += $val->get_ntotprice();
			$tax = $val->get_tax();
			$this->tax[$tax["taxqual"]] += $tax["tax"];
		}
		
		reset( $this->items );
	}
} // END OF ShoppingCart

?>

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
 * @package commerce_cart
 */
 
class ShoppingCartItem extends PEAR
{
	/**
	 * @access public
	 */
	var $inumber;
	
	/**
	 * @access public
	 */
	var $name;
	
	/**
	 * @access public
	 */
	var $quant;
	
	/**
	 * @access public
	 */
	var $price;
	
	/**
	 * @access public
	 */
	var $taxqual;
	
	/**
	 * @access public
	 */
	var $descr;
	
	/**
	 * @access public
	 */
	var $gprice;
	
	/**
	 * @access public
	 */
	var $nprice;
	
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
	function ShoppingCartItem( &$inumber, &$name, &$quant, &$price, &$taxqual, &$descr )
	{
		$this->_init();

		$this->inumber = $inumber;
		$this->name    = $name;
		$this->quant   = sprintf( "%d",    $quant   );
		$this->price   = sprintf( "%0.4f", $price   );
		$this->taxqual = sprintf( "%d",    $taxqual );
		
		if ( is_array( $descr ) )
		{
			foreach ( $descr as $key => $val )
				$this->descr[$key] = $val;
		}
		
		$this->_calc();
	}


	/**
	 * @access public
	 */	
	function update( &$quant )
	{
		$this->quant = $quant;
		$this->_calc();
	}

	/**
	 * @access public
	 */
	function get_ntotprice()
	{
		return $this->ntotprice;
	}

	/**
	 * @access public
	 */
	function get_gtotprice()
	{
		return $this->gtotprice;
	}

	/**
	 * @access public
	 */
	function get_tax()
	{
		return @array(
			"taxqual" => $this->taxqual, 
			"tax"     => $this->tax
		);
	}

	/**
	 * @access public
	 */
	function get_all()
	{
		$values = array();
		$values["cart.inumber"]   = $this->inumber;
		$values["cart.name"]      = $this->name;
		$values["cart.quant"]     = sprintf( "%d",    $this->quant     );
		$values["cart.price"]     = sprintf( "%0.2f", $this->price     );
		$values["cart.nprice"]    = sprintf( "%0.2f", $this->nprice    );
		$values["cart.gprice"]    = sprintf( "%0.2f", $this->gprice    );
		$values["cart.ntotprice"] = sprintf( "%0.2f", $this->ntotprice );
		$values["cart.gtotprice"] = sprintf( "%0.2f", $this->gtotprice );
		$values["cart.taxqual"]   = $this->taxqual;
		$values["cart.tax"]       = sprintf( "%0.2f", $this->tax       );
		
		foreach ( $this->descr as $key => $val )
			$values["cart.descr.$key"] = $val;
            
		return $values;
	}

	
	// private methods

	/**
	 * @access private
	 */	
	function _init()
	{
		$this->inumber   = null;
		$this->name      = "";
		$this->quant     = 0;
		$this->price     = 0.00;
		$this->descr     = array();
		$this->taxqual   = 0;
		$this->gprice    = 0.00;
		$this->nprice    = 0.00;
		$this->gtotprice = 0.00;
		$this->ntotprice = 0.00;
		$this->tax       = 0.00;
	}

	/**
	 * @access private
	 */
	function _calc()
	{
		$this->gprice    = sprintf( "%0.4f",   $this->price  - $this->discqual );
		$this->tax       = sprintf( "%0.2f", ( $this->gprice * $this->taxqual / ( 100 + $this->taxqual ) ) * $this->quant );
		$this->nprice    = sprintf( "%0.4f",   $this->gprice - ($this->gprice * $this->taxqual / ( 100 + $this->taxqual ) ) );
		$this->ntotprice = sprintf( "%0.4f",   $this->nprice * $this->quant );
		$this->gtotprice = sprintf( "%0.4f",   $this->gprice * $this->quant );
	}
} // END OF ShoppingCartItem
	
?>

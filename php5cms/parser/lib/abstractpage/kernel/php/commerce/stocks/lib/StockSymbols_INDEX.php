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


using( 'commerce.stocks.lib.StockSymbols' );


/**
 * @package commerce_stocks_lib
 */
 
class StockSymbols_INDEX extends StockSymbols
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StockSymbols_INDEX( $options = array() )
	{
		$this->StockSymbols( $options);
		
		$this->_populate();
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populate()
	{
		$this->_stock_ex = "Global Indices";
		
		$this->_symbols = array(
			"BANK"  => "Nasdaq Bank",
			"COMP"  => "Nasdaq Composite",
			"DAX"   => "Frankfurt Dax",
			"DJI"   => "Dow Jones Industrial",
			"FTIX"  => "Amsterdam AEX Index",
			"FTSE"  => "FTSE 100",
			"GLD"   => "Gold Index",
			"HSI"   => "Hang Seng",
			"IDX"   => "S&P 400 Midcap Index",
			"INDS"  => "Nasdaq Industrial",
			"INSR"  => "Nasdaq Inurance",
			"IXCO"  => "Nasdaq Computer",
			"IXF"   => "Nasdaq Financial 100",
			"IXTC"  => "Nasdaq Telecommunications",
			"KLCI"  => "Kuala Lumpur Composite",
			"NBI"   => "Nasdaq Biotechnology",
			"NCMP"  => "Nasdaq NM Composite",
			"NDY"   => "Nasdaq 100",
			"NI225" => "Nikkei 225",
			"OEX"   => "S&P 100 Index",
			"OFIN"  => "Nasdaq Other Finance",
			"PARI"  => "Paris CAC 40",
			"PLD"   => "Palladium Index",
			"PLT"   => "Platinum Index",
			"SET"   => "Thailand SET",
			"SHANG" => "Shanghai Index",
			"SLV"   => "Silver Index",
			"SP500" => "S&P 500",
			"STI"   => "Straits Times",
			"TOKS"  => "Tokyo TOPIX",
			"TORN"  => "Toronto Composite",
			"TRA"   => "Nasdaq Transportation",
			"TWI"   => "Taiwan Weighted",
			"XATX"  => "Vienna ATX",
			"XBEL"  => "Brussels BEL 20",
			"XIBX"  => "IBEX 35",
			"XMGI"  => "Madrid General Index"
		);
	}
} // END OF StockSymbols_INDEX

?>

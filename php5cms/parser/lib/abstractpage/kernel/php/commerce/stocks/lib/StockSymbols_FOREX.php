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
 
class StockSymbols_FOREX extends StockSymbols
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StockSymbols_FOREX( $options = array() )
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
		$this->_stock_ex = "Foreign Exchange";
		
		$this->_symbols = array(
			"AUDCAD" => "Australian Dollar Canadian Dollar",
			"AUDCHF" => "Australian Dollar Swiss Franc",
			"AUDEUR" => "Australian Dollar Euro",
			"AUDGBP" => "Australian Dollar British Pound",
			"AUDHKD" => "Australian Dollar Hong Kong Dollar",
			"AUDJPY" => "Australian Dollar Japanese Yen",
			"AUDNZD" => "Australian Dollar New Zealand Dollar",
			"AUDSGD" => "Australian Dollar Singapore Dollar",
			"AUDUSD" => "Australian Dollar US Dollar",
			"CADAUD" => "Canadian Dollar Australian Dollar",
			"CADCHF" => "Canadian Dollar Swiss Franc",
			"CADEUR" => "Canadian Dollar Euro",
			"CADGBP" => "Canadian Dollar British Pound",
			"CADHKD" => "Canadian Dollar Hong Kong Dollar",
			"CADJPY" => "Canadian Dollar Japanese Yen",
			"CADNZD" => "Canadian Dollar New Zealand Dollar",
			"CADSGD" => "Canadian Dollar Singapore Dollar",
			"CADUSD" => "Canadian Dollar US Dollar",
			"CHFAUD" => "Swiss Franc Australian Dollar",
			"CHFCAD" => "Swiss Franc Canadian Dollar",
			"CHFEUR" => "Swiss Franc Euro",
			"CHFGBP" => "Swiss Franc British Pound",
			"CHFHKD" => "Swiss Franc Hong Kong Dollar",
			"CHFJPY" => "Swiss Franc Japan Yen",
			"CHFNZD" => "Swiss Franc New Zealand Dollar",
			"CHFSGD" => "Swiss Franc Singapore Dollar",
			"CHFUSD" => "Swiss Franc US Dollar",
			"EURAUD" => "Euro Australian Dollar",
			"EURCAD" => "Euro Canadian Dollar",
			"EURCHF" => "Euro Swiss Franc",
			"EURGBP" => "Euro British Pound",
			"EURHKD" => "Euro Hong Kong Dollar",
			"EURJPY" => "Euro Japan Yen",
			"EURNZD" => "Euro New Zealand Dollar",
			"EURSGD" => "Euro Singapore Dollar",
			"EURUSD" => "Euro US Dollar",
			"GBPAUD" => "British Pound Australian Dollar",
			"GBPCAD" => "British Pound Canadian Dollar",
			"GBPCHF" => "British Pound Swiss Franc",
			"GBPEUR" => "British Pound Euro",
			"GBPHKD" => "British Pound Hong Kong Dollar",
			"GBPJPY" => "British Pound Japan Yen",
			"GBPNZD" => "British Pound New Zealand Dollar",
			"GBPSGD" => "British Pound Singapore Dollar",
			"GBPUSD" => "British Pound US Dollar",
			"HKDAUD" => "Hong Hong Dollar Australian Dollar",
			"HKDCAD" => "Hong Kong Dollar Canadian Dollar",
			"HKDCHF" => "Hong Kong Dollar Swiss Franc",
			"HKDEUR" => "Hong Hong Dollar Euro",
			"HKDGBP" => "Hong Hong Dollar British Pound",
			"HKDJPY" => "Hong Hong Dollar Japan Yen",
			"HKDNZD" => "Hong Kong Dollar New Zealand Dollar",
			"HKDSGD" => "Hong Kong Dollar Singapore Dollar",
			"HKDUSD" => "Hong Hong Dollar US Dollar",
			"JPYAUD" => "Japan Yen Australian Dollar",
			"JPYCAD" => "Japanese Yen Canadian Dollar",
			"JPYCHF" => "Japan Yen Swiss Franc",
			"JPYEUR" => "Japan Yen Euro",
			"JPYGBP" => "Japan Yen British Pound",
			"JPYHKD" => "Japanese Yen Hong Kong Dollar",
			"JPYNZD" => "Japan Yen New Zealand Dollar",
			"JPYSGD" => "Japanese Yen Singapore Dollar",
			"JPYUSD" => "Japan Yen US Dollar",
			"NZDAUD" => "New Zealand Dollar Australian Dollar",
			"NZDCAD" => "New Zealand Dollar Canadian Dollar",
			"NZDCHF" => "New Zealand Dollar Swiss Franc",
			"NZDEUR" => "New Zealand Dollar Euro",
			"NZDGBP" => "New Zealand Dollar British Pound",
			"NZDHKD" => "New Zealand Dollar Hong Kong Dollar",
			"NZDJPY" => "New Zealand Dollar Japan Yen",
			"NZDSGD" => "New Zealand Dollar Singapore Dollar",
			"NZDUSD" => "New Zealand Dollar US Dollar",
			"SGDAUD" => "Singapore Dollar Australian Dollar",
			"SGDCAD" => "Singapore Dollar Canadian Dollar",
			"SGDCHF" => "Singapore Dollar Swiss Franc",
			"SGDEUR" => "Singapore Dollar Euro",
			"SGDGBP" => "Singapore Dollar British Pound",
			"SGDHKD" => "Singapore Dollar Hong Kong Dollar",
			"SGDJPY" => "Singapore Dollar Japan Yen",
			"SGDNZD" => "Singapore Dollar New Zealand Dollar",
			"SGDUSD" => "Singapore Dollar US Dollar",
			"USDATS" => "US Dollar Austrian Schilling",
			"USDAUD" => "US Dollar Australian Dollar",
			"USDBEF" => "US Dollar Belgian Franc",
			"USDCAD" => "US Dollar Canadian Dollar",
			"USDCHF" => "US Dollar Swiss Franc",
			"USDCNY" => "US Dollar Chinese Yuan",
			"USDDKK" => "US Dollar Danish Krone",
			"USDESP" => "US Dollar Spannish Pesata",
			"USDEUR" => "US Dollar Euro",
			"USDFIM" => "US Dollar Finnish Markka",
			"USDFJD" => "US Dollar Fiji Dollar",
			"USDGBP" => "US Dollar British Pound",
			"USDGRD" => "US Dollar Greek Drachma",
			"USDHKD" => "US Dollar Hong Kong Dollar",
			"USDIDR" => "US Dollar Indonesian Rupee",
			"USDINR" => "US Dollar Indian Rupee",
			"USDITL" => "US Dollar Italian Lira",
			"USDJPY" => "US Dollar Japan Yen",
			"USDKRW" => "US Dollar Korean Won",
			"USDMXN" => "US Dollar Mexican New Peso",
			"USDMYR" => "US Dollar Malaysian Ringit",
			"USDNLG" => "US Dollar Netherland Guilder",
			"USDNOK" => "US Dollar Norwegian Krone",
			"USDNZD" => "US Dollar New Zealand Dollar",
			"USDPGK" => "US Dollar Papua New Guinean Kina",
			"USDPHP" => "US Dollar Phillipines Peso",
			"USDPTE" => "US Dollar Portugese Escudo",
			"USDSEK" => "US Dollar Swedish Krona",
			"USDSGD" => "US Dollar Singapore Dollar",
			"USDTHB" => "US Dollar Tailand Bhat",
			"USDTWD" => "US Dollar Taiwanese New Dollar",
			"USDZAR" => "US Dollar South African Rand"
		);
	}
} // END OF StockSymbols_FOREX

?>

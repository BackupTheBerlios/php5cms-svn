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
 * ISO 4217 (Codes for the Representation of Currencies and Funds)
 * defines three-letter abbreviations for world currencies. 
 * The general principle used to construct these abbreviations is to 
 * take the two-letter abbreviations defined in ISO 3166 (Codes for 
 * the Representation of Names of Countries) and append the first 
 * letter of the currency name (e.g., USD for the United States Dollar).
 *
 * In the case of currencies defined by supra-national entities, 
 * ISO 4217 assigns two-letter entity codes starting with "X" to use in 
 * place of country codes (e.g., XCD for the Central Caribbean Dollar). 
 *
 * @todo add a flag for countries with euro as currency
 * @package locale
 */
 
class ISO4217 extends PEAR
{
	/**
	 * @access public
	 * @static
	 */
	function getName( $currency_code = "" )
	{
		static $currency_codes;
		$currency_codes = array(
			"ADP" => "Andorran Peseta",
			"AED" => "U.A.E. Dirham",
			"AFA" => "Afghanistan Afgani",
			"ALL" => "Albanian Lek",
			"AMD" => "Armenia Dram",
			"ANG" => "Netherland Antilles Guilder", 
			"AOK" => "Angolan Kwanza",
			"ARS" => "Argentine Peso",
			"ATS" => "Austrian Schilling",
			"AUD" => "Australian Dollar",
			"AZM" => "Azerbaijan Manat",
			"BBD" => "Barbados Dollar",
			"BDT" => "Bangladesh Taka",
			"BEF" => "Belgian Franc",
			"BGL" => "Bulgarian Lev",
			"BHD" => "Bahraini Dinar",
			"BMD" => "Bermudian Dollar",
			"BND" => "Brunei Dollar",
			"BOP" => "Bolivian Boliviano",
			"BPS" => "Canton & Enderbury Island Pound",
			"BRL" => "Brazil Real",
			"BSD" => "Bahamas Dollar",
			"BTN" => "Bhutan Ngultrum",
			"BWP" => "Botswana Pula",
			"BYR" => "Belarus Rouble",
			"BZD" => "Belize Dollar",
			"CAD" => "Canadian Dollar",
			"CHF" => "Swiss Franc",
			"CLP" => "Chilean Peso",
			"CNY" => "China Renminbi",
			"COP" => "Colombian Peso",
			"CRC" => "Costa Rica Colon",
			"CUP" => "Cuban Peso",
			"CVE" => "Cape Verde Escudo",
			"CYP" => "Cypriot Pound",
			"CZK" => "Czech Koruna",
			"DEM" => "German Mark",
			"DJF" => "Djibouti Franc",
			"DKK" => "Danish Krone",
			"DOP" => "Dominican Republic",
			"DZD" => "Algerian Dinar",
			"ECS" => "Ecuadoran Sucre",
			"EEK" => "Estonian Kroon",
			"EGP" => "Egyptian Pound",
			"ESP" => "Balearic Island Peseta",
			"ETB" => "Ethiopian Birr",
			"EUR" => "European Euro",
			"FIM" => "Finnish Markka",
			"FJD" => "Fiji Dollar",
			"FKP" => "Falkland Island Pound",
			"FRF" => "Reunion Franc",
			"GBP" => "British Pound",
			"GEL" => "Georgian Lari",
			"GHC" => "Ghana Cedi",
			"GIP" => "Gibraltar Pound",
			"GMD" => "Gambian Dalasi",
			"GNS" => "Guinea Franc",
			"GRD" => "Greek Drachma",
			"GTQ" => "Guatemala Quetzal",
			"GWP" => "Guinea Bissau Peso",
			"GYD" => "Guyana Dollar",
			"HKD" => "Hong Kong Dollar",
			"HNL" => "Honduras Lempira",
			"HRK" => "Croatian Kuna",
			"HTG" => "Haiti Gourde",
			"HUF" => "Hungarian Forint",
			"IDR" => "Indonesian Rupiah",
			"IEP" => "Irish Punt",
			"ILS" => "Israeli Shekel",
			"INR" => "Indian Rupee",
			"IQD" => "Iraqi Dinar",
			"IRR" => "Iranian Rial",
			"ISK" => "Iceland Krona",
			"ITL" => "Italian Lira",
			"JMD" => "Jamaica Dollar",
			"JOD" => "Jordanian Dinar",
			"JPY" => "Japanese Yen",
			"KES" => "Kenyan Shilling",
			"KGS" => "Kyrgyzstan Som",
			"KMF" => "Comoros Franc",
			"KRW" => "South Korean Won",
			"KWD" => "Kuwaiti Dinar",
			"KYD" => "Cayman Islands",
			"KZT" => "Kazakhstan Tenge",
			"LAK" => "Laos New Kip",
			"LBP" => "Lebanese Pound",
			"LKR" => "Sri Lankan Rupee",
			"LRD" => "Liberian Dollar",
			"LSL" => "Lesotho Loti",
			"LTL" => "Lithuanian Lit",
			"LUF" => "Luxembourg Franc",
			"LVL" => "Latvian Lat",
			"MAD" => "Moroccan Dirham",
			"MDL" => "Moldova Lei",
			"MMK" => "Myanmar Kyat",
			"MNT" => "Mongolia Tugrik",
			"MOP" => "Macau Pataca",
			"MRO" => "Mauritania Ouguiya",
			"MTL" => "Maltese Lira",
			"MUR" => "Mauritius Rupee",
			"MWK" => "Malawi Kwacha",
			"MXP" => "Mexican Peso",
			"MYR" => "Malaysian Ringgit",
			"MZM" => "Mozambique Metical",
			"NGN" => "Nigeria Naira",
			"NIC" => "Nicaragua Cordoba",
			"NLG" => "Dutch Guilder",
			"NOK" => "Norwegian Krone",
			"NZD" => "New Zealand Dollar",
			"OMR" => "Omani Rial",
			"PAB" => "Panamanian Balboa",
			"PEN" => "Peruvian New Sol",
			"PGK" => "Papua New Guinea Kina",
			"PHP" => "Philippines Peso",
			"PKR" => "Pakistani Rupee",
			"PLZ" => "Polish Zloty",
			"PTE" => "Madeira Escudo",
			"PYG" => "Paraguay Guarani",
			"QAR" => "Qatari Riyal",
			"ROL" => "Romanian Leu",
			"RUB" => "Russian Ruble",
			"RWS" => "Rwanda Franc",
			"SAR" => "Saudi Riyal",
			"SBD" => "Solomon Island Dollar",
			"SCR" => "Seychelles Rupee",
			"SDP" => "Sudanese Pound",
			"SEK" => "Swedish Krona",
			"SGD" => "Singapore Dollar",
			"SHP" => "St. Helena Pound",
			"SIT" => "Slovenia Tolar",
			"SKK" => "Slovakia Koruna",
			"SLL" => "Sierra Leone Leone",
			"SOS" => "Somali Schilling",
			"SRG" => "Surinam Guilder",
			"STD" => "Sao Tome Dobra",
			"SVC" => "El Salvador Colon",
			"SYP" => "Syrian Pound",
			"SZL" => "Swaziland Lilangeni",
			"THB" => "Thai Baht",
			"TJR" => "Tajikistan Ruble",
			"TMM" => "Turkmenistan Manat",
			"TND" => "Tunisian Dinar",
			"TRL" => "Turkish Lira",
			"TTD" => "Trinidad/Tobago Dollar",
			"TWD" => "Taiwan Dollar",
			"TZS" => "Tanzanian Shilling",
			"UAH" => "Ukraine Hryvna",
			"UGX" => "Ugandan Shilling", 
			"USD" => "United States Dollar", 
			"UYP" => "Uruguay Peso",
			"UZS" => "Uzbekistan Sum",
			"VEB" => "Venezuelan Bolivar",
			"VND" => "Vietnam Dong",
			"VUV" => "Vanuatu Vatu",
			"XAF" => "Central African Republic",
			"XCD" => "Antigua Dollar",
			"XDS" => "St. Christopher Dollar",
			"XEU" => "European Currency Unit",
			"XOF" => "Niger Republic Franc",
			"XPF" => "French Pacific Island Franc",
			"YER" => "Yemeni Rial",
			"ZAR" => "South African Rand",
			"ZMK" => "Zambian Kwacha",
			"ZRN" => "Zaire Zaire",
			"ZWD" => "Zimbabwe Dollar"
		);
		
		$code = $currency_codes[strtoupper( $currency_code )];
		
		if ( !$code )
			return false;
		else
			return $code;
	}
} // END OF ISO4217

?>

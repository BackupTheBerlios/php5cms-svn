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
 * @package util_aviation_icao
 */
 
class ICAOCountryCodes extends PEAR
{
	/**
	 * @access private
	 */
	var $_codes;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function ICAOCountryCodes()
	{
		$this->_populate();
	}
	
	
	/**
	 * @access public
	 */
	function get( $code = "" )
	{
		if ( empty( $code ) )
			return false;
			
		$name = $this->_codes[strtoupper( $code )];
		
		if ( $name )
			return $name;
		else
			return false;
	}
	
	
	// private methods

	/**
	 * @access private
	 */	
	function _populate()
	{
		$this->_codes = array(
			"AG" => "Solomon Islands",
			"AN" => "Nauru Islands",
			"AY" => "Papua New Guinea",
			"BG" => "Greenland",
			"BI" => "Iceland",
			"CY" => "Canada",
			"DA" => "Algeria",
			"DB" => "Benin",
			"DF" => "Burkina Faso",
			"DG" => "Ghana",
			"DI" => "Ivory Coast",
			"DN" => "Nigeria",
			"DR" => "Niger",
			"DT" => "Tunisia",
			"DX" => "Togo",
			"EB" => "Belgium",
			"ED" => "Germany - West",
			"EE" => "Estonia",
			"EF" => "Finland",
			"EG" => "United Kingdom",
			"EH" => "Netherlands",
			"EI" => "Ireland",
			"EK" => "Denmark",
			"EL" => "Luxembourg",
			"EN" => "Norway",
			"EP" => "Poland",
			"ES" => "Sweden",
			"ET" => "Germany - East",
			"EV" => "Latvia",
			"EY" => "Lithuania",
			"FA" => "South Africa",
			"FB" => "Botswana",
			"FC" => "Congo",
			"FD" => "Swaziland",
			"FE" => "Republic of Central Africa",
			"FG" => "Equatorial Guinea",
			"FH" => "Ascension Islands",
			"FI" => "Mauritius",
			"FJ" => "Diego Garcia",
			"FK" => "Cameroon",
			"FL" => "Zambia",
			"FM" => "Madagascar",
			"FN" => "Angola",
			"FO" => "Gabon",
			"FP" => "Sao Tome",
			"FQ" => "Mozambique",
			"FS" => "Seychelles",
			"FT" => "Chad",
			"FV" => "Zimbabwe",
			"FW" => "Malawi",
			"FX" => "Lesotho",
			"FY" => "Namibia",
			"FZ" => "Zaire",
			"GA" => "Mali",
			"GB" => "Gambia",
			"GC" => "Canary Islands",
			"GE" => "Melilla",
			"GF" => "Sierra Leone",
			"GG" => "Guinea Bissau",
			"GL" => "Liberia",
			"GM" => "Morocco",
			"GO" => "Senegal",
			"GQ" => "Mauritania",
			"GU" => "Guinea",
			"GV" => "Cape Verde",
			"HA" => "Ethiopia",
			"HB" => "Burundi",
			"HD" => "Djibouti",
			"HE" => "Egypt",
			"HH" => "Eritrea",
			"HK" => "Kenya",
			"HL" => "Libya",
			"HR" => "Rwanda",
			"HS" => "Sudan",
			"HT" => "Tanzania",
			"HU" => "Uganda",
			"K1" => "United States - Northwest",
			"K2" => "United States - Southwest",
			"K3" => "United States - North",
			"K4" => "United States - South",
			"K5" => "United States - Central",
			"K6" => "United States - Northeast",
			"K7" => "United States - Southeast",
			"LA" => "Albania",
			"LB" => "Bulgaria",
			"LC" => "Cyprus",
			"LD" => "Croatia",
			"LE" => "Spain", 
			"LF" => "France", 
			"LG" => "Greece", 
			"LH" => "Hungary", 
			"LI" => "Italy", 
			"LJ" => "Slovenia", 
			"LK" => "Czech Republic", 
			"LL" => "Israel", 
			"LM" => "Malta", 
			"LO" => "Austria", 
			"LP" => "Portugal", 
			"LQ" => "Bosnia and Herzegovinia",
			"LR" => "Romania",
			"LS" => "Switzerland",
			"LT" => "Turkey",
			"LU" => "Moldova",
			"LW" => "Macedonia",
			"LX" => "Gibralter",
			"LY" => "Yugoslavia",
			"LZ" => "Slovakia",
			"MB" => "Turks and Caicos Islands",
			"MD" => "Dominican Republic",
			"MG" => "Guatemala",
			"MH" => "Honduras",
			"MK" => "Jamaica",
			"MM" => "Mexico",
			"MN" => "Nicaragua",
			"MP" => "Panama",
			"MR" => "Costa Rica",
			"MS" => "El Salvador", 
			"MT" => "Haiti", 
			"MU" => "Cuba", 
			"MW" => "Cayman Islands",
			"MY" => "Bahamas",
			"MZ" => "Belize",
			"NC" => "Cook Islands",
			"NF" => "Fiji",
			"NG" => "Kiribati Islands",
			"NI" => "Niue Islands",
			"NL" => "Wallis Islands",
			"NS" => "American Samoa",
			"NT" => "Tahiti",
			"NV" => "Vanuatu",
			"NW" => "Noumea",
			"NZ" => "New Zealand",
			"OA" => "Afghanistan",
			"OB" => "Bahrain",
			"OE" => "Saudi Arabia",
			"OI" => "Iran",
			"OJ" => "Jordan",
			"OK" => "Kuwait",
			"OL" => "Lebanon", 
			"OM" => "United Arab Emirates", 
			"OO" => "Oman", 
			"OP" => "Pakistan", 
			"OR" => "Iraq", 
			"OS" => "Syria", 
			"OT" => "Qatar", 
			"OY" => "Yemen", 
			"PA" => "Alaska", 
			"PG" => "Guam", 
			"PH" => "Hawaii", 
			"PJ" => "Johnston Islands", 
			"PK" => "Marshall Islands", 
			"PL" => "Line Islands", 
			"PM" => "Midway Islands", 
			"PT" => "Micronesia", 
			"PW" => "Wake Islands", 
			"RC" => "Taiwan", 
			"RJ" => "Japan", 
			"RK" => "Korea", 
			"RO" => "Okinawa Islands", 
			"RP" => "Philippines", 
			"SA" => "Argentina", 
			"SB" => "Brazil", 
			"SC" => "Chile", 
			"SE" => "Equador", 
			"SG" => "Paraguay", 
			"SK" => "Colombia", 
			"SL" => "Bolivia", 
			"SM" => "Surinam", 
			"SO" => "French Guyana", 
			"SP" => "Peru", 
			"SU" => "Uruguay", 
			"SV" => "Venezuela", 
			"SY" => "Guyana", 
			"TA" => "Antigua and Barbuda", 
			"TB" => "Barbados", 
			"TD" => "Dominica", 
			"TF" => "French Antilles", 
			"TG" => "Grenada", 
			"TI" => "U.S. Virgin Islands", 
			"TJ" => "Puerto Rico", 
			"TK" => "St. Kitts Islands", 
			"TL" => "St. Lucia", 
			"TN" => "Aruba", 
			"TQ" => "Anguilla", 
			"TT" => "Monserrat Islands", 
			"TU" => "Trinidad and Tobago", 
			"TV" => "U.K Virgin Islands", 
			"TX" => "Bermuda", 
			"UA" => "Kazakhstan and Kyrgyzstan", 
			"UB" => "Azerbiajan", 
			"UE" => "Russian Federation", 
			"UG" => "Armenia and Georgia", 
			"UH" => "Russian Federation", 
			"UI" => "Russian Federation", 
			"UK" => "Ukraine and Moldovia", 
			"UL" => "Russian Federation", 
			"UM" => "Belarus, Latvia and Lithuania", 
			"UN" => "Russian Federation", 
			"UR" => "Russian Federation", 
			"US" => "Russian Federation", 
			"UT" => "Uzbekistan, Turkmenistan and Tadjikistan", 
			"UU" => "Russian Federation", 
			"UW" => "Russian Federation", 
			"VA" => "India - West", 
			"VC" => "Sri Lanka", 
			"VD" => "Cambodia", 
			"VE" => "India - East", 
			"VG" => "Bangladesh", 
			"VH" => "Hongkong", 
			"VI" => "India - North", 
			"VL" => "Laos", 
			"VM" => "Macau", 
			"VN" => "Nepal", 
			"VO" => "India - South", 
			"VR" => "Maldives", 
			"VT" => "Thailand", 
			"VV" => "Vietnam", 
			"VY" => "Myanmar", 
			"WA" => "Indonesia", 
			"WB" => "Brunei", 
			"WI" => "Indonesia", 
			"WM" => "Malaysia", 
			"WR" => "Indonesia - Bali", 
			"WS" => "Singapore", 
			"YB" => "Australia", 
			"YM" => "Australia", 
			"ZB" => "China", 
			"ZG" => "China", 
			"ZH" => "China", 
			"ZK" => "North Korea", 
			"ZL" => "China", 
			"ZM" => "Mongolia", 
			"ZP" => "China", 
			"ZS" => "China", 
			"ZU" => "China", 
			"ZW" => "China", 
			"ZY" => "China"
		);
	}
} // END OF ICAOCountryCodes

?>

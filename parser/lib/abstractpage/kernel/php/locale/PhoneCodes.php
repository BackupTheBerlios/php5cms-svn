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
 * @todo Implement some iso mapping so that we can easily pick up a 
 *       specific phonecode
 * @package locale
 */
 
class PhoneCodes extends PEAR
{
	/**
	 * @access private
	 */
	var $_phonecodes;
	
	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function PhoneCodes()
	{
		$this->_populate();
	}
	

	/**
	 * @access public
	 */
	function getList()
	{
		return $this->_phonecodes;
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populate()
	{
		// Current array recognizes ~232 country codes.
		$this->_phonecodes = array(			
			"93"	=> "Afghanistan",
			"355"	=> "Albania",
			"213"	=> "Algeria",
			"684"	=> "American Samoa",
			"376"	=> "Andorra",
			"244"	=> "Angola",
			"1264"	=> "Anguilla",
			"1268"	=> "Antigua and Barbuda",
			"54"	=> "Argentina",
			"374" 	=> "Armenia",
			"297" 	=> "Aruba",
			"61" 	=> "Australia",
			"43" 	=> "Austria",
			"994" 	=> "Azerbaijan",
			"242" 	=> "Bahamas",
			"973" 	=> "Bahrain",
			"880" 	=> "Bangladesh",
			"1246" 	=> "Barbados",
			"375" 	=> "Belarus",
			"32" 	=> "Belgium",
			"501" 	=> "Belize",
			"229" 	=> "Benin",
			"1441" 	=> "Bermuda",
			"975" 	=> "Bhutan",
			"591" 	=> "Bolivia",
			"387" 	=> "Bosnia and Herzegowina",
			"267" 	=> "Botswana",
			"55" 	=> "Brazil",
			"673" 	=> "Brunei Darussalam",
			"359" 	=> "Bulgaria",
			"226" 	=> "Burkina Faso",
			"257" 	=> "Burundi",
			"855" 	=> "Cambodia",
			"237" 	=> "Cameroon",
			"1" 	=> "Canada",
			"238" 	=> "Cape Verde",
			"1345" 	=> "Cayman Islands",
			"236" 	=> "Central African Republic",
			"235" 	=> "Chad",
			"56" 	=> "Chile",
			"86" 	=> "China",
			"57" 	=> "Colombia",
			"269" 	=> "Comoros",
			"242" 	=> "Congo",
			"243" 	=> "Congo, Democratic Republic",
			"682" 	=> "Cook Islands",
			"506" 	=> "Costa Rica",
			"225" 	=> "Cote D'Ivoire",
			"385" 	=> "Croatia",
			"53" 	=> "Cuba",
			"357" 	=> "Cyprus",
			"420" 	=> "Czech Republic",
			"45" 	=> "Denmark",
			"253" 	=> "Djibouti",
			"1767" 	=> "Dominica",
			"1809" 	=> "Dominican Republic",
			"593" 	=> "Ecuador",
			"20" 	=> "Egypt",
			"503" 	=> "El Salvador",
			"240" 	=> "Equatorial Guinea",
			"291" 	=> "Eritrea",
			"372" 	=> "Estonia",
			"251" 	=> "Ethiopia",
			"389" 	=> "F.Y.R Macedonia",
			"500" 	=> "Falkland Islands (Malvinas)",
			"298" 	=> "Faroe Islands",
			"679" 	=> "Fiji",
			"358" 	=> "Finland",
			"33" 	=> "France",
			"594" 	=> "French Guiana",
			"689" 	=> "French Polynesia",
			"241" 	=> "Gabon",
			"220" 	=> "Gambia",
			"995" 	=> "Georgia",
			"49" 	=> "Germany",
			"233" 	=> "Ghana",
			"350" 	=> "Gibraltar",
			"30" 	=> "Greece",
			"299" 	=> "Greenland",
			"1473" 	=> "Grenada",
			"590" 	=> "Guadeloupe",
			"671" 	=> "Guam",
			"502" 	=> "Guatemala",
			"224" 	=> "Guinea",
			"245" 	=> "Guinea-Bissau",
			"592" 	=> "Guyana",
			"509" 	=> "Haiti",
			"504" 	=> "Honduras",
			"852" 	=> "Hong Kong",
			"36" 	=> "Hungary",
			"354" 	=> "Iceland",
			"91" 	=> "India",
			"62" 	=> "Indonesia",
			"98" 	=> "Iran, Islamic Republic of",
			"964" 	=> "Iraq",
			"353" 	=> "Ireland",
			"972" 	=> "Israel",
			"39" 	=> "Italy",
			"1876" 	=> "Jamaica",
			"81" 	=> "Japan",
			"962" 	=> "Jordan",
			"7" 	=> "Kazakhstan",
			"254" 	=> "Kenya",
			"686" 	=> "Kiribati",
			"850" 	=> "Korea, People's Republic",
			"82" 	=> "Korea, Republic",
			"965" 	=> "Kuwait",
			"7" 	=> "Kyrgyzstan",
			"371" 	=> "Latvia",
			"961" 	=> "Lebanon",
			"266" 	=> "Lesotho",
			"231" 	=> "Liberia",
			"423" 	=> "Liechtenstein",
			"370" 	=> "Lithuania",
			"352" 	=> "Luxembourg",
			"853" 	=> "Macau",
			"261" 	=> "Madagascar",
			"265" 	=> "Malawi",
			"60" 	=> "Malaysia",
			"960" 	=> "Maldives",
			"223" 	=> "Mali ",
			"356" 	=> "Malta",
			"692" 	=> "Marshall Islands",
			"596" 	=> "Martinique",
			"222" 	=> "Mauritania",
			"230" 	=> "Mauritius",
			"269" 	=> "Mayotte",
			"52" 	=> "Mexico",
			"691" 	=> "Micronesia",
			"373" 	=> "Moldova",
			"377" 	=> "Monaco",
			"976" 	=> "Mongolia",
			"1664" 	=> "Montserrat",
			"212" 	=> "Morocco",
			"258" 	=> "Mozambique",
			"95" 	=> "Myanmar",
			"264" 	=> "Namibia",
			"674" 	=> "Nauru",
			"977" 	=> "Nepal",
			"31" 	=> "Netherlands",
			"599" 	=> "Netherlands Antilles",
			"687" 	=> "New Caledonia",
			"64" 	=> "New Zealand",
			"505" 	=> "Nicaragua",
			"227" 	=> "Niger",
			"234" 	=> "Nigeria",
			"683" 	=> "Niue",
			"6723" 	=> "Norfolk Island",
			"670" 	=> "Northern Mariana Islands",
			"47" 	=> "Norway",
			"968" 	=> "Oman",
			"92" 	=> "Pakistan",
			"680" 	=> "Palau",
			"507" 	=> "Panama",
			"675" 	=> "Papua New Guinea",
			"595" 	=> "Paraguay",
			"51" 	=> "Peru",
			"63" 	=> "Philippines",
			"48" 	=> "Poland",
			"351" 	=> "Portugal",
			"1787" 	=> "Puerto Rico",
			"974" 	=> "Qatar",
			"262" 	=> "Reunion",
			"40" 	=> "Romania",
			"7" 	=> "Russian Federation",
			"250" 	=> "Rwanda",
			"1869" 	=> "Saint Kitts and Nevis",
			"1758" 	=> "Saint Lucia",
			"685" 	=> "Samoa",
			"378" 	=> "San Marino",
			"239" 	=> "Sao Tome and Principe",
			"966" 	=> "Saudi Arabia",
			"221" 	=> "Senegal",
			"248" 	=> "Seychelles",
			"232" 	=> "Sierra Leone",
			"65" 	=> "Singapore",
			"421" 	=> "Slovakia",
			"386" 	=> "Slovenia",
			"677" 	=> "Solomon Islands",
			"27" 	=> "South Africa",
			"34" 	=> "Spain",
			"94" 	=> "Sri Lanka",
			"290" 	=> "St. Helena",
			"508" 	=> "St. Pierre And Miquelon",
			"1809" 	=> "St. Vincent and Grenadines",
			"249" 	=> "Sudan",
			"597" 	=> "Suriname",
			"268" 	=> "Swaziland",
			"46" 	=> "Sweden",
			"41" 	=> "Switzerland",
			"963" 	=> "Syrian Arab Republic",
			"886" 	=> "Taiwan",
			"7" 	=> "Tajikistan",
			"255" 	=> "Tanzania",
			"66" 	=> "Thailand",
			"228" 	=> "Togo",
			"676" 	=> "Tonga",
			"868" 	=> "Trinidad and Tobago",
			"216" 	=> "Tunisia",
			"90" 	=> "Turkey",
			"993" 	=> "Turkmenistan",
			"1809" 	=> "Turks and Caicos Islands",
			"688" 	=> "Tuvalu",
			"256" 	=> "Uganda",
			"380" 	=> "Ukraine",
			"971" 	=> "United Arab Emirates",
			"44" 	=> "United Kingdom",
			"1" 	=> "United States",
			"598" 	=> "Uruguay",
			"7" 	=> "Uzbekistan",
			"678" 	=> "Vanuatu",
			"58" 	=> "Venezuela",
			"84" 	=> "Vietnam",
			"1809" 	=> "Virgin Islands",
			"681" 	=> "Wallis and Futuna Islands",
			"967" 	=> "Yemen",
			"381" 	=> "Yugoslavia",
			"260" 	=> "Zambia",
			"263" 	=> "Zimbabwe"
		);	
	}
} // END OF PhoneCodes

?>

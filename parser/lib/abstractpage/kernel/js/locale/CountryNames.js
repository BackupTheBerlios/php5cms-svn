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
 * @package locale
 */
 
/**
 * Constructor
 *
 * @access public
 */
CountryNames = function()
{
	this.Dictionary = Dictionary;
	this.Dictionary();

	this._populate( "en" );
};


CountryNames.prototype = new Dictionary();
CountryNames.prototype.constructor = CountryNames;
CountryNames.superclass = Dictionary.prototype;


// private methods

/**
 * @access private
 */
CountryNames.prototype._populate = function( lang )
{	
	switch( lang )
	{
		case "en":
		
		default:
			this.add( "US", "United States of America" );
			this.add( "AF", "Afghanistan" );
			this.add( "AL", "Albania" );
			this.add( "DZ", "Algeria" );
			this.add( "AS", "American Samoa" );
			this.add( "AD", "Andorra" );
			this.add( "AO", "Angola" );
			this.add( "AI", "Anguilla" );
			this.add( "AQ", "Antarctica" );
			this.add( "AG", "Antigua and Barbuda" );
			this.add( "AR", "Argentina" );
			this.add( "AM", "Armenia" );
			this.add( "AW", "Aruba" );
			this.add( "AU", "Australia" );
			this.add( "AT", "Austria" );
			this.add( "AZ", "Azerbaijan" );
			this.add( "BS", "Bahama" );
			this.add( "BH", "Bahrain" );
			this.add( "BD", "Bangladesh" );
			this.add( "BB", "Barbados" );
			this.add( "BY", "Belarus" );
			this.add( "BY", "Belgium" );
			this.add( "BZ", "Belize" );
			this.add( "BJ", "Benin" );
			this.add( "BM", "Bermuda" );
			this.add( "BT", "Bhutan" );
			this.add( "BO", "Bolivia" );
			this.add( "BA", "Bosnia and Herzegovina" );
			this.add( "BW", "Botswana" );
			this.add( "BV", "Bouvet Island" );
			this.add( "BR", "Brazil" );
			this.add( "IO", "British Indian Ocean Territory" );
			this.add( "VG", "British Virgin Islands" );
			this.add( "BN", "Brunei Darussalam" );
			this.add( "BG", "Bulgaria" );
			this.add( "BF", "Burkina Faso" );
			this.add( "BI", "Burundi" );
			this.add( "KH", "Cambodia" );
			this.add( "CM", "Cameroon" );
			this.add( "CA", "Canada" );
			this.add( "CV", "Cape Verde" );
			this.add( "KY", "Cayman Islands" );
			this.add( "CF", "Central African Republic" );
			this.add( "TD", "Chad" );
			this.add( "CL", "Chile" );
			this.add( "CN", "China" );
			this.add( "CX", "Christmas Island" );
			this.add( "CC", "Cocos (Keeling) Islands" );
			this.add( "CO", "Colombia" );
			this.add( "KM", "Comoros" );
			this.add( "CG", "Congo" );
			this.add( "CK", "Cook Islands" );
			this.add( "CR", "Costa Rica" );
			this.add( "HR", "Croatia" );
			this.add( "CI", "Ctte D'ivoire (Ivory Coast)" );
			this.add( "CU", "Cuba" );
			this.add( "CY", "Cyprus" );
			this.add( "CZ", "Czech Republic" );
			this.add( "DK", "Denmark" );
			this.add( "DJ", "Djibouti" );
			this.add( "DM", "Dominica" );
			this.add( "DO", "Dominican Republic" );
			this.add( "TP", "East Timor" );
			this.add( "EC", "Ecuador" );
			this.add( "EG", "Egypt" );
			this.add( "SV", "El Salvador" );
			this.add( "GQ", "Equatorial Guinea" );
			this.add( "ER", "Eritrea" );
			this.add( "EE", "Estonia" );
			this.add( "ET", "Ethiopia" );
			this.add( "FK", "Falkland Islands (Malvinas)" );
			this.add( "FO", "Faroe Islands" );
			this.add( "FJ", "Fiji" );
			this.add( "FI", "Finland" );
			this.add( "FX", "France, Metropolitan" );
			this.add( "FR", "France" );
			this.add( "GF", "French Guiana" );
			this.add( "PF", "French Polynesia" );
			this.add( "TF", "French Southern Territories" );
			this.add( "GA", "Gabon" );
			this.add( "GM", "Gambia" );
			this.add( "GE", "Georgia" );
			this.add( "DE", "Germany" );
			this.add( "GH", "Ghana" );
			this.add( "GI", "Gibraltar" );
			this.add( "GR", "Greece" );
			this.add( "GL", "Greenland" );
			this.add( "GD", "Grenada" );
			this.add( "GP", "Guadeloupe" );
			this.add( "GU", "Guam" );
			this.add( "GT", "Guatemala" );
			this.add( "GW", "Guinea-Bissau" );
			this.add( "GN", "Guinea" );
			this.add( "GY", "Guyana" );
			this.add( "HT", "Haiti" );
			this.add( "HM", "Heard and McDonald Islands" );
			this.add( "HN", "Honduras" );
			this.add( "HK", "Hong Kong" );
			this.add( "HU", "Hungary" );
			this.add( "IS", "Iceland" );
			this.add( "IN", "India" );
			this.add( "ID", "Indonesia" );
			this.add( "IQ", "Iraq" );
			this.add( "IE", "Ireland" );
			this.add( "IR", "Islamic Republic of Iran" );
			this.add( "IL", "Israel" );
			this.add( "IT", "Italy" );
			this.add( "JP", "Jamaica" );
			this.add( "JP", "Japan" );
			this.add( "JO", "Jordan" );
			this.add( "KZ", "Kazakhstan" );
			this.add( "KE", "Kenya" );
			this.add( "KI", "Kiribati" );
			this.add( "KP", "Korea, Democratic People's Republic of" );
			this.add( "KR", "Korea, Republic of" );
			this.add( "KW", "Kuwait" );
			this.add( "KG", "Kyrgyzstan" );
			this.add( "LA", "Lao People's Democratic Republic" );
			this.add( "LV", "Latvia" );
			this.add( "LB", "Lebanon" );
			this.add( "LS", "Lesotho" );
			this.add( "LR", "Liberia" );
			this.add( "LY", "Libyan Arab Jamahiriya" );
			this.add( "LI", "Liechtenstein" );
			this.add( "LT", "Lithuania" );
			this.add( "LU", "Luxembourg" );
			this.add( "MO", "Macau" );
			this.add( "MG", "Madagascar" );
			this.add( "MW", "Malawi" );
			this.add( "MY", "Malaysia" );
			this.add( "MV", "Maldives" );
			this.add( "ML", "Mali" );
			this.add( "MT", "Malta" );
			this.add( "MH", "Marshall Islands" );
			this.add( "MQ", "Martinique" );
			this.add( "MR", "Mauritania" );
			this.add( "MU", "Mauritius" );
			this.add( "YT", "Mayotte" );
			this.add( "MX", "Mexico" );
			this.add( "FM", "Micronesia" );
			this.add( "MD", "Moldova, Republic of" );
			this.add( "MC", "Monaco" );
			this.add( "MN", "Mongolia" );
			this.add( "MS", "Monserrat" );
			this.add( "MA", "Morocco" );
			this.add( "MZ", "Mozambique" );
			this.add( "MM", "Myanmar" );
			this.add( "NA", "Nambia" );
			this.add( "NR", "Nauru" );
			this.add( "NP", "Nepal" );
			this.add( "AN", "Netherlands Antilles" );
			this.add( "NL", "Netherlands" );
			this.add( "NC", "New Caledonia" );
			this.add( "NZ", "New Zealand" );
			this.add( "NI", "Nicaragua" );
			this.add( "NE", "Niger" );
			this.add( "NG", "Nigeria" );
			this.add( "NU", "Niue" );
			this.add( "NF", "Norfolk Island" );
			this.add( "MP", "Northern Mariana Islands" );
			this.add( "NO", "Norway" );
			this.add( "OM", "Oman" );
			this.add( "PK", "Pakistan" );
			this.add( "PW", "Palau" );
			this.add( "PA", "Panama" );
			this.add( "PG", "Papua New Guinea" );
			this.add( "PY", "Paraguay" );
			this.add( "PE", "Peru" );
			this.add( "PH", "Philippines" );
			this.add( "PN", "Pitcairn" );
			this.add( "PL", "Poland" );
			this.add( "PT", "Portugal" );
			this.add( "PR", "Puerto Rico" );
			this.add( "QA", "Qatar" );
			this.add( "RE", "Riunion" );
			this.add( "RO", "Romania" );
			this.add( "RU", "Russian Federation" );
			this.add( "RW", "Rwanda" );
			this.add( "LC", "Saint Lucia" );
			this.add( "WS", "Samoa" );
			this.add( "SM", "San Marino" );
			this.add( "ST", "Sao Tome and Principe" );
			this.add( "SA", "Saudi Arabia" );
			this.add( "SN", "Senegal" );
			this.add( "SC", "Seychelles" );
			this.add( "SL", "Sierra Leone" );
			this.add( "SG", "Singapore" );
			this.add( "SK", "Slovakia" );
			this.add( "SI", "Slovenia" );
			this.add( "SB", "Solomon Islands" );
			this.add( "SO", "Somalia" );
			this.add( "ZA", "South Africa" );
			this.add( "GS", "South Georgia and the South Sandwich Islands" );
			this.add( "ES", "Spain" );
			this.add( "LK", "Sri Lanka" );
			this.add( "SH", "St. Helena" );
			this.add( "KN", "St. Kitts and Nevis" );
			this.add( "PM", "St. Pierre and Miquelon" );
			this.add( "VC", "St. Vincent and the Grenadines" );
			this.add( "SD", "Sudan" );
			this.add( "SR", "Suriname" );
			this.add( "SJ", "Svalbard and Jan Mayen Islands" );
			this.add( "SZ", "Swaziland" );
			this.add( "SE", "Sweden" );
			this.add( "CH", "Switzerland" );
			this.add( "SY", "Syrian Arab Republic" );
			this.add( "TW", "Taiwan, Province of China" );
			this.add( "TJ", "Tajikistan" );
			this.add( "TZ", "Tanzania, United Republic of" );
			this.add( "TH", "Thailand" );
			this.add( "TO", "Togo" );
			this.add( "TK", "Tokelau" );
			this.add( "TO", "Tonga" );
			this.add( "TT", "Trinidad and Tobago" );
			this.add( "TN", "Tunisia" );
			this.add( "TR", "Turkey" );
			this.add( "TM", "Turkmenistan" );
			this.add( "TC", "Turks and Caicos Islands" );
			this.add( "TV", "Tuvalu" );
			this.add( "UG", "Uganda" );
			this.add( "UA", "Ukraine" );
			this.add( "AE", "United Arab Emirates" );
			this.add( "GB", "United Kingdom (Great Britain)" );
			this.add( "UM", "United States Minor Outlying Islands" );
			this.add( "VI", "United States Virgin Islands" );
			this.add( "UY", "Uruguay" );
			this.add( "UZ", "Uzbekistan" );
			this.add( "VU", "Vanuatu" );
			this.add( "VA", "Vatican City State (Holy See)" );
			this.add( "VE", "Venezuela" );
			this.add( "VN", "Viet Nam" );
			this.add( "WF", "Wallis and Futuna Islands" );
			this.add( "EH", "Western Sahara" );
			this.add( "YE", "Yemen" );
			this.add( "YU", "Yugoslavia" );
			this.add( "ZR", "Zaire" );
			this.add( "ZM", "Zambia" );
			this.add( "ZW", "Zimbabwe" );
	 
			break;
	}
};

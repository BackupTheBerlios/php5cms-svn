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
 * @package util_aviation_icao_stations
 */
 
class Stations extends PEAR
{
	/**
	 * @access public
	 */
	var $country = null;
	
	/**
	 * @access public
	 */
	var $icaos = null;
	
	
	/**
	 * @access public
	 * @static
	 */
	function &factory( $station = "" )
	{
		$class = "Stations_" . strtoupper( $station );
			
		using( 'util.aviation.icao.stations.'. $class );
			
		if ( class_registered( $class ) )
			return new $class;
		else
			return PEAR::raiseError( "Unable to load driver." );
	}

	/**
	 * @access public
	 */
	function getStation( $icao )
	{
		return ( $this->icaos[$icao] || "" );
	}
	
	/**
	 * @access public
	 */
	function getCountry()
	{
		return $this->country;
	}

	/**
	 * @access public
	 */
	function getAll()
	{
		return $this->icaos;
	}
	
	
	// private methods
	
	/**
	 * @access public
	 * @static
	 */
	function _getAvailCountries()
	{
		static $countries;
		$countries = array(
			'AF' => 'Afghanistan',
  			'AL' => 'Albania',
  			'DZ' => 'Algeria',
  			'AS' => 'American Samoa',
  			'AD' => 'Andorra',
  			'AO' => 'Angola',
  			'AI' => 'Anguilla',
  			'AQ' => 'Antarctica',
  			'AG' => 'Antigua and Barbuda',
  			'AR' => 'Argentina',
  			'AM' => 'Armenia',
  			'AW' => 'Aruba',
  			'AU' => 'Australia',
  			'AT' => 'Austria',
  			'AZ' => 'Azerbaijan',
  			'BS' => 'Bahamas',
  			'BH' => 'Bahrain',
  			'BD' => 'Bangladesh',
  			'BB' => 'Barbados',
  			'BY' => 'Belarus',
  			'BE' => 'Belgium',
  			'BZ' => 'Belize',
  			'BJ' => 'Benin',
  			'BM' => 'Bermuda',
  			'BT' => 'Bhutan',
  			'BO' => 'Bolivia',
  			'BA' => 'Bosnia and Herzegovina',
  			'BW' => 'Botswana',
  			'BV' => 'Bouvet Island',
  			'BR' => 'Brazil',
  			'IO' => 'British Indian Ocean Territory',
  			'BN' => 'Brunei Darussalam',
  			'BG' => 'Bulgaria',
  			'BF' => 'Burkina Faso',
  			'BI' => 'Burundi',
  			'KH' => 'Cambodia',
  			'CM' => 'Cameroon',
  			'CA' => 'Canada',
  			'CV' => 'Cape Verde',
  			'KY' => 'Cayman Islands',
  			'CF' => 'Central African Republic',
  			'TD' => 'Chad',
  			'CL' => 'Chile',
  			'CN' => 'China',
  			'CX' => 'Christmas Island',
  			'CC' => 'Cocos (Keeling) Islands',
  			'CO' => 'Colombia',
  			'KM' => 'Comoros',
  			'CG' => 'Congo',
  			'CD' => 'Congo, The Democratic Republic of',
  			'CK' => 'Cook Islands',
  			'CR' => 'Costa Rica',
  			'CI' => 'Cote D\'Ivoire',
  			'HR' => 'Croatia',
  			'CU' => 'Cuba',
  			'CY' => 'Cyprus',
  			'CZ' => 'Czech Republic',
  			'DK' => 'Denmark',
  			'DJ' => 'Djibouti',
  			'DM' => 'Dominica',
  			'DO' => 'Dominican Republic',
  			'TL' => 'East Timor',
  			'EC' => 'Ecuador',
  			'EG' => 'Egypt',
  			'SV' => 'El Salvador',
  			'GQ' => 'Equatorial Guinea',
  			'ER' => 'Eritrea',
  			'EE' => 'Estonia',
  			'ET' => 'Ethiopia',
  			'FK' => 'Falkland Islands (Malvinas)',
  			'FO' => 'Faroe Islands',
  			'FJ' => 'Fiji',
  			'FI' => 'Finland',
  			'FR' => 'France',
  			'GF' => 'French Guiana',
  			'PF' => 'French Polynesia',
  			'TF' => 'French Southern Territories',
  			'GA' => 'Gabon',
  			'GM' => 'Gambia',
  			'GE' => 'Georgia',
  			'DE' => 'Germany',
  			'GH' => 'Ghana',
  			'GI' => 'Gibraltar',
  			'GR' => 'Greece',
  			'GL' => 'Greenland',
  			'GD' => 'Grenada',
  			'GP' => 'Guadeloupe',
  			'GU' => 'Guam',
  			'GT' => 'Guatemala',
  			'GN' => 'Guinea',
  			'GW' => 'Guinea-Bissau',
  			'GY' => 'Guyana',
  			'HT' => 'Haiti',
  			'HM' => 'Heard Island and McDonald Islands',
  			'VA' => 'Holy See (Vatican City State)',
  			'HN' => 'Honduras',
  			'HK' => 'Hong Kong',
  			'HU' => 'Hungary',
  			'IS' => 'Iceland',
  			'IN' => 'India',
  			'ID' => 'Indonesia',
  			'IR' => 'Iran, Islamic Republic of',
  			'IQ' => 'Iraq',
  			'IE' => 'Ireland',
  			'IL' => 'Israel',
  			'IT' => 'Italy',
  			'JM' => 'Jamaica',
  			'JP' => 'Japan',
  			'JO' => 'Jordan',
  			'KZ' => 'Kazakstan',
  			'KE' => 'Kenya',
  			'KI' => 'Kiribati',
  			'KP' => 'Korea, Democratic People\'s Republic of',
  			'KR' => 'Korea, Republic of',
  			'KW' => 'Kuwait',
  			'KG' => 'Kyrgyzstan',
  			'LA' => 'Lao People\'s Democratic Republic',
  			'LV' => 'Latvia',
  			'LB' => 'Lebanon',
  			'LS' => 'Lesotho',
  			'LR' => 'Liberia',
  			'LY' => 'Libyan Arab Jamahiriya',
  			'LI' => 'Liechtenstein',
  			'LT' => 'Lithuania',
  			'LU' => 'Luxembourg',
  			'MO' => 'Macau',
  			'MK' => 'Macedonia, The Former Yugoslav Republic of',
  			'MG' => 'Madagascar',
  			'MW' => 'Malawi',
  			'MY' => 'Malaysia',
  			'MV' => 'Maldives',
  			'ML' => 'Mali',
  			'MT' => 'Malta',
  			'MH' => 'Marshall Islands',
  			'MQ' => 'Martinique',
  			'MR' => 'Mauritania',
  			'MU' => 'Mauritius',
  			'YT' => 'Mayotte',
  			'MX' => 'Mexico',
  			'FM' => 'Micronesia, Federated States of',
  			'MD' => 'Moldova, Republic of',
  			'MC' => 'Monaco',
  			'MN' => 'Mongolia',
  			'MS' => 'Montserrat',
  			'MA' => 'Morocco',
  			'MZ' => 'Mozambique',
  			'MM' => 'Myanmar',
  			'NA' => 'Namibia',
  			'NR' => 'Nauru',
  			'NP' => 'Nepal',
  			'NL' => 'Netherlands',
  			'AN' => 'Netherlands Antilles',
  			'NC' => 'New Caledonia',
  			'NZ' => 'New Zealand',
  			'NI' => 'Nicaragua',
  			'NE' => 'Niger',
  			'NG' => 'Nigeria',
  			'NU' => 'Niue',
  			'NF' => 'Norfolk Island',
  			'MP' => 'Northern Mariana Islands',
  			'NO' => 'Norway',
  			'OM' => 'Oman',
  			'PK' => 'Pakistan',
  			'PW' => 'Palau',
  			'PS' => 'Palestinian Territory, Occupied',
  			'PA' => 'Panama',
  			'PG' => 'Papua New Guinea',
  			'PY' => 'Paraguay',
  			'PE' => 'Peru',
  			'PH' => 'Philippines',
  			'PN' => 'Pitcairn',
  			'PL' => 'Poland',
  			'PT' => 'Portugal',
  			'PR' => 'Puerto Rico',
  			'QA' => 'Qatar',
  			'RE' => 'Reunion',
  			'RO' => 'Romania',
  			'RU' => 'Russian Federation',
  			'RW' => 'Rwanda',
  			'SH' => 'Saint Helena',
  			'KN' => 'Saint Kitts and Nevis',
  			'LC' => 'Saint Lucia',
  			'PM' => 'Saint Pierre and Miquelon',
  			'VC' => 'Saint Vincent and the Grenadines',
  			'WS' => 'Samoa',
  			'SM' => 'San Marino',
  			'ST' => 'Sao Tome and Principe',
  			'SA' => 'Saudi Arabia',
  			'SN' => 'Senegal',
  			'SC' => 'Seychelles',
  			'SL' => 'Sierra Leone',
  			'SG' => 'Singapore',
  			'SK' => 'Slovakia',
  			'SI' => 'Slovenia',
  			'SB' => 'Solomon Islands',
  			'SO' => 'Somalia',
  			'ZA' => 'South Africa',
  			'GS' => 'South Georgia and the South Sandwich Islands',
  			'ES' => 'Spain',
  			'LK' => 'Sri Lanka',
  			'SD' => 'Sudan',
  			'SR' => 'Suriname',
  			'SJ' => 'Svalbard and Jan Mayen',
  			'SZ' => 'Swaziland',
  			'SE' => 'Sweden',
  			'CH' => 'Switzerland',
  			'SY' => 'Syrian Arab Republic',
  			'TW' => 'Taiwan, Province of China',
  			'TJ' => 'Tajikistan',
  			'TZ' => 'Tanzania, United Republic of',
  			'TH' => 'Thailand',
  			'TG' => 'Togo',
  			'TK' => 'Tokelau',
  			'TO' => 'Tonga',
  			'TT' => 'Trinidad and Tobago',
  			'TN' => 'Tunisia',
  			'TR' => 'Turkey',
  			'TM' => 'Turkmenistan',
  			'TC' => 'Turks and Caicos Islands',
  			'TV' => 'Tuvalu',
  			'UG' => 'Uganda',
  			'UA' => 'Ukraine',
  			'AE' => 'United Arab Emirates',
  			'GB' => 'United Kingdom',
  			'US' => 'United States',
  			'UM' => 'United States Minor Outlying Islands',
  			'UY' => 'Uruguay',
  			'UZ' => 'Uzbekistan',
  			'VU' => 'Vanuatu',
  			'VE' => 'Venezuela',
  			'VN' => 'Viet Nam',
  			'VG' => 'Virgin Islands, British',
  			'VI' => 'Virgin Islands, U.S.',
  			'WF' => 'Wallis and Futuna',
  			'EH' => 'Western Sahara',
  			'YE' => 'Yemen',
  			'YU' => 'Yugoslavia',
  			'ZM' => 'Zambia',
  			'ZW' => 'Zimbabwe',
		);
		
		return $countries;
	}
} // END OF Stations

?>

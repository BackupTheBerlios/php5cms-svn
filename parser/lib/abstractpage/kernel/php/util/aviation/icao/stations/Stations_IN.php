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


using( 'util.aviation.icao.stations.Stations' );


/**
 * @package util_aviation_icao_stations
 */
 
class Stations_IN extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_IN()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'India';

		$this->icaos = array(
			'VEAT' => 'Agartala',
			'VIAG' => 'Agra',
			'VAAH' => 'Ahmadabad',
			'VAAK' => 'Akola',
			'VIAL' => 'Allahabad/Bamhrauli',
			'VIAR' => 'Amritsar',
			'VAAU' => 'Aurangabad Chikalthan Aerodrome',
			'VIBY' => 'Bareilly',
			'VABM' => 'Belgaum/Sambra',
			'VOBI' => 'Bellary',
			'VABV' => 'Bhaunagar',
			'VABP' => 'Bhopal/Bairagarh',
			'VEBS' => 'Bhubaneswar',
			'VABJ' => 'Bhuj-Rudramata',
			'VABI' => 'Bilaspur',
			'VABB' => 'Bombay/Santacruz',
			'VECC' => 'Calcutta/Dum Dum',
			'VECX' => 'Car Nicobar',
			'VOCC' => 'Cochin/Willingdon',
			'VOCB' => 'Coimbatore/Peelamedu',
			'VOCP' => 'Cuddapah',
			'VEMN' => 'Dibrugarh/Mohanbari',
			'VEGT' => 'Gauhati',
			'VEGY' => 'Gaya',
			'VAGO' => 'Goa/Dabolim Airport',
			'VEGK' => 'Gorakhpur',
			'VIGR' => 'Gwalior',
			'VIHR' => 'Hissar',
			'VOHY' => 'Hyderabad Airport',
			'VEIM' => 'Imphal Tulihal',
			'VAID' => 'Indore',
			'VAJB' => 'Jabalpur',
			'VIJP' => 'Jaipur/Sanganer',
			'VEJS' => 'Jamshedpur',
			'VIJN' => 'Jhansi',
			'VEJH' => 'Jharsuguda',
			'VIJO' => 'Jodhpur',
			'VICX' => 'Kanpur/Chakeri',
			'VAKD' => 'Khandwa',
			'VAKP' => 'Kolhapur',
			'VIKO' => 'Kota Aerodrome',
			'VILK' => 'Lucknow/Amausi',
			'VERC' => 'M. O. Ranchi',
			'VOMM' => 'Madras/Minambakkam',
			'VOMD' => 'Madurai',
			'VOML' => 'Mangalore/Bajpe',
			'VANP' => 'Nagpur Sonegaon',
			'VIDP' => 'New Delhi/Palam',
			'VIDD' => 'New Delhi/Safdarjung',
			'VELR' => 'North Lakhimpur',
			'VEPT' => 'Patna',
			'VEPB' => 'Port Blair',
			'VARK' => 'Rajkot',
			'VIST' => 'Satna',
			'VASL' => 'Sholapur',
			'VEBD' => 'Siliguri',
			'VOTV' => 'Thiruvananthapuram',
			'VOTR' => 'Tiruchchirapalli',
			'VIUD' => 'Udaipur Dabok',
			'VIBN' => 'Varanasi/Babatpur',
			'VOVR' => 'Vellore',
			'VOBZ' => 'Vijayawada/Gannavaram'
		);
	}
} // END OF Stations_IN

?>

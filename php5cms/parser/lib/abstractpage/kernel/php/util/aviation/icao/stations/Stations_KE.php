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
 
class Stations_KE extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_KE()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Kenya';

		$this->icaos = array(
			'HKEL' => 'Eldoret',
			'HKEM' => 'Embu',
			'HKGA' => 'Garissa',
			'HKKG' => 'Kakamega',
			'HKKR' => 'Kericho',
			'HKKS' => 'Kisii',
			'HKKI' => 'Kisumu',
			'HKKT' => 'Kitale',
			'HKLU' => 'Lamu',
			'HKLO' => 'Lodwar',
			'HKMU' => 'Makindu',
			'HKML' => 'Malindi',
			'HKMA' => 'Mandera',
			'HKMB' => 'Marsabit',
			'HKME' => 'Meru',
			'HKMO' => 'Mombasa',
			'HKMY' => 'Moyale',
			'HKNC' => 'Nairobi/Dagoretti',
			'HKJK' => 'Nairobi/Kenyatta Airport',
			'HKNW' => 'Nairobi/Wilson',
			'HKNA' => 'Nairobi ACC/FIC/RCC/MET/COM/',
			'HKNK' => 'Nakuru',
			'HKNO' => 'Narok',
			'HKNI' => 'Nyeri',
			'HKVO' => 'Voi',
			'HKWJ' => 'Wajir'
		);
	}
} // END OF Stations_KE

?>

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
 
class Stations_SA extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_SA()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Saudi Arabia';

		$this->icaos = array(
			'OEAB' => 'Abha',
			'OEAH' => 'Al Ahsa',
			'OEBA' => 'Al Baha',
			'OEPA' => 'Al Qaysumah',
			'OESK' => 'Al-Jouf',
			'OERR' => 'Arar',
			'OEBH' => 'Bisha',
			'OEDW' => 'Dawadmi',
			'OEDR' => 'Dhahran',
			'OEGS' => 'Gassim',
			'OEGN' => 'Gizan',
			'OEGT' => 'Guriat',
			'OEKK' => 'Hafr Al-Batin',
			'OEHL' => 'Hail',
			'OEJD' => 'Jeddah',
			'OEJN' => 'Jeddah King Abdul Aziz International Airport',
			'OEKM' => 'Khamis Mushait',
			'OERK' => 'King Khaled International Airport',
			'OEMA' => 'Madinah',
			'OEMK' => 'Makkah',
			'OENG' => 'Najran',
			'OERF' => 'Rafha',
			'OERY' => 'Riyadh',
			'OESH' => 'Sharurah',
			'OETB' => 'Tabuk',
			'OETF' => 'Taif',
			'OETR' => 'Turaif',
			'OEWD' => 'Wadi Al Dawasser Airport',
			'OEWJ' => 'Wejh',
			'OEYN' => 'Yenbo'
		);
	}
} // END OF Stations_SA

?>

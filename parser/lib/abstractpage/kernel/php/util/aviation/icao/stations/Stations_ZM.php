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
 
class Stations_ZM extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_ZM()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Zambia';

		$this->icaos = array(
			'FLCP' => 'Chipata',
			'FLCH' => 'Choma',
			'FLIK' => 'Isoka',
			'FLPO' => 'Kabompo',
			'FLKW' => 'Kabwe',
			'FLKO' => 'Kaoma',
			'FLKS' => 'Kasama',
			'FLPA' => 'Kasempa',
			'FLKB' => 'Kawambwa',
			'FLLI' => 'Livingstone',
			'FLLD' => 'Lundazi',
			'FLLC' => 'Lusaka City Airport',
			'FLLS' => 'Lusaka Internationalairport',
			'FLMA' => 'Mansa',
			'FLBA' => 'Mbala',
			'FLMG' => 'Mongu',
			'FLMP' => 'Mpika',
			'FLMW' => 'Mwinilunga',
			'FLND' => 'Ndola',
			'FLPE' => 'Petauke',
			'FLSN' => 'Senanga',
			'FLSE' => 'Serenje',
			'FLSS' => 'Sesheke',
			'FLSW' => 'Solwezi',
			'FLZB' => 'Zambezi'
		);
	}
} // END OF Stations_ZM

?>

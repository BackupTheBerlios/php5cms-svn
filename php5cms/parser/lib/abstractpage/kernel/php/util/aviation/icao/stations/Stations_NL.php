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
 
class Stations_NL extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_NL()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Netherlands';

		$this->icaos = array(
			'EHAM' => 'Amsterdam Airport Schiphol',
			'EHDB' => 'De Bilt',
			'EHKD' => 'De Kooy',
			'EHDL' => 'Deelen',
			'EHEH' => 'Eindhoven',
			'EHGR' => 'Gilze-Rijen',
			'EHGG' => 'Groningen Airport Eelde',
			'EHLW' => 'Leeuwarden',
			'EHBK' => 'Maastricht Airport Zuid Limburg',
			'EHRD' => 'Rotterdam Airport Zestienhoven',
			'EHSB' => 'Soesterberg',
			'EHTW' => 'Twenthe',
			'EHVB' => 'Valkenburg',
			'EHVL' => 'Vlieland',
			'EHVK' => 'Volkel',
			'EHWO' => 'Woensdrecht'
		);
	}
} // END OF Stations_NL

?>

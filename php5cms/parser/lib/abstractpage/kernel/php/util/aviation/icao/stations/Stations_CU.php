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
 
class Stations_CU extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_CU()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Cuba';

		$this->icaos = array(
			'MUHA' => 'Aeropuerto Jose Marti, Rancho-Boyeros, Habana',
			'MUBA' => 'Baracoa, Oriente',
			'MUBY' => 'Bayamo',
			'MUCM' => 'Camaguey Aeropuerto',
			'MUCL' => 'Cayo Largo Del Sur',
			'MUCF' => 'Cienfuegos, Las Villas',
			'MUGT' => 'Guantanamo, Oriente',
			'MUGM' => 'Guantanamo, Oriente',
			'MUHG' => 'Holguin Civ/Mil',
			'MUVT' => 'Las Tunas, Las Tunas',
			'MUMZ' => 'Manzanillo, Oriente',
			'MUMO' => 'Moa Military',
			'MUNG' => 'Nueva Gerona, Isla De Pinos',
			'MUPR' => 'Pinar Del Rio, Pinar Del Rio',
			'MUCU' => 'Santiago De Cuba, Oriente',
			'MUVR' => 'Varadero, Matanzas',
			'MUCA' => 'Venezuela, Ciego De Avila'
		);
	}
} // END OF Stations_CU

?>

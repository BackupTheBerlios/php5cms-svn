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
 
class Stations_PE extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_PE()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Peru';

		$this->icaos = array(
			'SPHY' => 'Andahuayla',
			'SPHZ' => 'Anta Huaraz',
			'SPQU' => 'Arequipa',
			'SPAY' => 'Atalaya',
			'SPHO' => 'Ayacucho',
			'SPJR' => 'Cajamarca',
			'SPPY' => 'Chachapoyas',
			'SPHI' => 'Chiclayo',
			'SPEO' => 'Chimbote',
			'SPZO' => 'Cuzco',
			'SPNC' => 'Huanuco',
			'SPQT' => 'Iquitos',
			'SPJI' => 'Juanjui',
			'SPJL' => 'Juliaca',
			'SPIM' => 'Lima-Callao/Aerop. Internacional Jorgechavez',
			'SPSO' => 'Pisco',
			'SPUR' => 'Piura',
			'SPCL' => 'Pucallpa',
			'SPTU' => 'Puerto Maldonado',
			'SPJA' => 'Rioja',
			'SPJN' => 'San Juan',
			'SPTN' => 'Tacna',
			'SPYL' => 'Talara',
			'SPST' => 'Tarapoto',
			'SPGM' => 'Tingo Maria',
			'SPRU' => 'Trujillo',
			'SPME' => 'Tumbes',
			'SPMS' => 'Yurimaguas'
		);
	}
} // END OF Stations_PE

?>

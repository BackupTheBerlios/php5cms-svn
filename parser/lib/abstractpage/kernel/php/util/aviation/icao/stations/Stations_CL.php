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
 
class Stations_CL extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_CL()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Chile';

		$this->icaos = array(
			'SCFA' => 'Antofagasta',
			'SCAR' => 'Arica',
			'SCBA' => 'Balmaceda',
			'SCRA' => 'Chanaral',
			'SCCC' => 'Chile Chico',
			'SCCH' => 'Chillan',
			'SCHR' => 'Cochrane',
			'SCIE' => 'Concepcion',
			'SCHA' => 'Copiapo',
			'SCCY' => 'Coyhaique',
			'SCIC' => 'Curico',
			'SCDA' => 'Iquique/Diego Arac',
			'SCIP' => 'Isla De Pascua',
			'SCSE' => 'La Serena',
			'SCEL' => 'Pudahuel',
			'SCTE' => 'Puerto Montt',
			'SCCI' => 'Punta Arenas',
			'SCER' => 'Quintero Santiago',
			'SCTC' => 'Temuco',
			'SCVD' => 'Valdivia',
			'SCLL' => 'Vallenar'
		);
	}
} // END OF Stations_CL

?>

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
 
class Stations_BO extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_BO()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Bolivia';

		$this->icaos = array(
			'SLAP' => 'Apolo',
			'SLAS' => 'Ascencion De Guarayos',
			'SLCA' => 'Camiri',
			'SLCN' => 'Charana',
			'SLCO' => 'Cobija',
			'SLCB' => 'Cochabamba',
			'SLCP' => 'Concepcion',
			'SLGY' => 'Guayaramerin',
			'SLLP' => 'La Paz/Alto',
			'SLMG' => 'Magdalena',
			'SLOR' => 'Oruro',
			'SLPO' => 'Potosi',
			'SLPS' => 'Puerto Suarez',
			'SLRY' => 'Reyes',
			'SLRI' => 'Riberalta',
			'SLRB' => 'Robore',
			'SLRQ' => 'Rurrenabaque',
			'SLSB' => 'San Borja',
			'SLSM' => 'San Ignacio De Moxos',
			'SLSI' => 'San Ignacio De Velasco',
			'SLJV' => 'San Javier',
			'SLJO' => 'San Joaquin',
			'SLJE' => 'San Jose De Chiquitos',
			'SLSA' => 'Santa Ana',
			'SLET' => 'Santa Cruz/El Trompillo',
			'SLSU' => 'Sucre',
			'SLTJ' => 'Tarija',
			'SLTR' => 'Trinidad',
			'SLVM' => 'Villamontes',
			'SLVR' => 'Viru-Viru',
			'SLYA' => 'Yacuiba'
		);
	}
} // END OF Stations_BO

?>

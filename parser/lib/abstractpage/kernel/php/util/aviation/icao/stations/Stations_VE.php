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
 
class Stations_VE extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_VE()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Venezuela';

		$this->icaos = array(
			'SVAC' => 'Acarigua',
			'SVBC' => 'Barcelona',
			'SVBI' => 'Barinas',
			'SVBM' => 'Barquisimeto',
			'SVCL' => 'Calabozo',
			'SVFM' => 'Caracas/La Carlota',
			'SVMI' => 'Caracas/Maiquetia Aerop. Intl. Simon Bolivar',
			'SVCS' => 'Caracas/Oscar Macha',
			'SVCB' => 'Ciudad Bolivar',
			'SVCR' => 'Coro',
			'SVCU' => 'Cumana',
			'SVGU' => 'Guanare',
			'SVGD' => 'Guasdualito',
			'SVPR' => 'Guayana/Manuel Car',
			'SVGI' => 'Guiria',
			'SVHG' => 'Higuerote',
			'SVLF' => 'La Fria',
			'SVLO' => 'La Orchila',
			'SVMC' => 'Maracaibo-La Chinita',
			'SVBS' => 'Maracay-B. A. Sucre',
			'SVMG' => 'Margarita/Del Carib',
			'SVMT' => 'Maturin',
			'SVMN' => 'Mene Grande',
			'SVMD' => 'Merida',
			'SVMP' => 'Metropolitano Private',
			'SVJC' => 'Paraguana/Josefa',
			'SVPM' => 'Paramillo Private',
			'SVPC' => 'Pto. Cabello',
			'SVPA' => 'Puerto Ayacucho',
			'SVSA' => 'San Antonio Del Tachira',
			'SVSP' => 'San Felipe',
			'SVSR' => 'San Fernando De Apure',
			'SVJM' => 'San Juan De Los Morros',
			'SVST' => 'San Tome Private',
			'SVSZ' => 'Santa Barbara Zulia',
			'SVSE' => 'Santa Elena De Uairen',
			'SVSO' => 'Sto. Domingo',
			'SVTR' => 'Temblador',
			'SVTM' => 'Tumeremo',
			'SVVA' => 'Valencia',
			'SVVL' => 'Valera',
			'SVVP' => 'Valle De La Pascua'
		);
	}
} // END OF Stations_VE

?>

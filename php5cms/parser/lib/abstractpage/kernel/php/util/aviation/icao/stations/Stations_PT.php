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
 
class Stations_PT extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_PT()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Portugal';

		$this->icaos = array(
			'LPBJ' => 'Beja',
			'LPBG' => 'Braganca',
			'LPFR' => 'Faro/Aeroporto',
			'LPFL' => 'Flores Acores',
			'LPFU' => 'Funchal/S. Catarina',
			'LPHR' => 'Horta/Castelo Branco Acores',
			'LPLA' => 'Lajes Acores',
			'LPPT' => 'Lisboa/Portela',
			'LPPD' => 'Ponta Delgada/Nordela Acores',
			'LPPR' => 'Porto/Pedras Rubras',
			'LPPS' => 'Porto Santo',
			'LPAZ' => 'Santa Maria Acores',
			'LPVR' => 'Vila Real'
		);
	}
} // END OF Stations_PT

?>

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
 
class Stations_EC extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_EC()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Ecuador';

		$this->icaos = array(
			'SEAM' => 'Ambato/Chachoan',
			'SEBC' => 'Bahia De Caraquez',
			'SELO' => 'Catamayo/Camilo Ponce Enriquez',
			'SECU' => 'Cuenca/Mariscal Lamar',
			'SEES' => 'Esmeraldas-Tachina',
			'SEGU' => 'Guayaquil/Simon Bolivar',
			'SEIB' => 'Ibarra/Atahualpa',
			'SELT' => 'Latacunga',
			'SEMA' => 'Macara/J. M. Velasco I.',
			'SEMH' => 'Machala/General M. Serrano',
			'SEMT' => 'Manta',
			'SEPA' => 'Pastaza/Rio Amazonas',
			'SEQU' => 'Quito/Mariscal Sucre',
			'SESA' => 'Salinas/General Ulpiano Paez',
			'SEST' => 'San Cristobal Galapagos',
			'SETI' => 'Tiputini',
			'SETU' => 'Tulcan/El Rosal'
		);
	}
} // END OF Stations_EC

?>

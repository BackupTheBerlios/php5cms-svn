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
 
class Stations_TZ extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_TZ()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Tanzania, United Republic of';

		$this->icaos = array(
			'HTAR' => 'Arusha',
			'HTBU' => 'Bukoba',
			'HTDA' => 'Dar Es Salaam Airport',
			'HTDO' => 'Dodoma',
			'HTIR' => 'Iringa',
			'HTKA' => 'Kigoma',
			'HTKJ' => 'Kilimanjaro Airport',
			'HTMB' => 'Mbeya',
			'HTMO' => 'Mombo',
			'HTMG' => 'Morogoro',
			'HTMS' => 'Moshi',
			'HTMT' => 'Mtwara',
			'HTMU' => 'Musoma',
			'HTMW' => 'Mwanza',
			'HTNA' => 'Nachingwea',
			'HTPE' => 'Pemba/Karume Airport',
			'HTSE' => 'Same',
			'HTSY' => 'Shinyanga',
			'HTSO' => 'Songea',
			'HTTB' => 'Tabora Airport',
			'HTTG' => 'Tanga',
			'HTZA' => 'Zanzibar/Kisauni'
		);
	}
} // END OF Stations_TZ

?>

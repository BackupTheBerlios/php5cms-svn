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
 
class Stations_DZ extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_DZ()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Algeria';

		$this->icaos = array(
			'DAUA' => 'Adrar',
			'DABB' => 'Annaba',
			'DABT' => 'Batna',
			'DAOR' => 'Bechar',
			'DAAE' => 'Bejaia',
			'DAUB' => 'Biskra',
			'DAAD' => 'Bou-Saada',
			'DAOI' => 'Chlef',
			'DABC' => 'Constantine',
			'DAAG' => 'Dar-El-Beida',
			'DAAJ' => 'Djanet',
			'DAFI' => 'Djelfa',
			'DAUE' => 'El Golea',
			'DAUO' => 'El Oued',
			'DAUG' => 'Ghardaia',
			'DAUH' => 'Hassi-Messaoud',
			'DAAP' => 'Illizi',
			'DAUZ' => 'In Amenas',
			'DAAV' => 'Jijel Achouat',
			'DAUL' => 'Laghouat',
			'DAAY' => 'Mecheria',
			'DAOO' => 'Oran/Es Senia',
			'DAUU' => 'Ouargla',
			'DAAS' => 'Setif',
			'DABP' => 'Skikda',
			'DAAT' => 'Tamanrasset/Aguenna',
			'DABS' => 'Tebessa',
			'DAOB' => 'Tiaret',
			'DAUT' => 'Timimoun',
			'DAOF' => 'Tindouf',
			'DAON' => 'Tlemcen Zenata',
			'DAUK' => 'Touggourt'
		);
	}
} // END OF Stations_DZ

?>

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
 
class Stations_CM extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_CM()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Cameroon';

		$this->icaos = array(
			'FKAG' => 'Abong-Mbang',
			'FKAF' => 'Bafia',
			'FKKV' => 'Bamenda',
			'FKAB' => 'Banyo',
			'FKKI' => 'Batouri',
			'FKAO' => 'Betare-Oya',
			'FKKD' => 'Douala Obs.',
			'FKKR' => 'Garoua',
			'FKKM' => 'Koundja',
			'FKKB' => 'Kribi',
			'FKAL' => 'Lomie',
			'FKKF' => 'Mamfe',
			'FKKA' => 'Maroua-Salak',
			'FKAM' => 'Meiganga',
			'FKKN' => 'Ngaoundere',
			'FKAN' => 'Nkongsamba',
			'FKKC' => 'Tiko',
			'FKYS' => 'Yaounde',
			'FKAY' => 'Yoko'
		);
	}
} // END OF Stations_CM

?>

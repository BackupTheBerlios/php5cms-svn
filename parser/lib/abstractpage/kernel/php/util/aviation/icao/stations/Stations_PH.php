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
 
class Stations_PH extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_PH()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Philippines';

		$this->icaos = array(
			'RPXT' => 'Alabat',
			'RPUA' => 'Aparri',
			'RPUB' => 'Baguio',
			'RPUR' => 'Baler',
			'RPUO' => 'Basco',
			'RPWE' => 'Butuan',
			'RPWL' => 'Cagayan De Oro',
			'RPUK' => 'Calapan',
			'RPVF' => 'Catarman',
			'RPMK' => 'Clark Ab',
			'RPWC' => 'Cotobato',
			'RPUD' => 'Daet',
			'RPMD' => 'Davao Airport',
			'RPWG' => 'Dipolog',
			'RPVD' => 'Dumaguete',
			'RPWB' => 'Gen. Santos',
			'RPVG' => 'Guiuan',
			'RPUI' => 'Iba',
			'RPVI' => 'Iloilo',
			'RPLI' => 'Laoag',
			'RPMP' => 'Legaspi',
			'RPMT' => 'Mactan',
			'RPWY' => 'Malaybalay',
			'RPVM' => 'Masbate',
			'RPLL' => 'Ninoy Aquino Inter-National Airport',
			'RPVP' => 'Puerto Princesa',
			'RPMR' => 'Romblon',
			'RPVR' => 'Roxas',
			'RPUH' => 'San Jose',
			'RPMS' => 'Sangley Point',
			'RPLB' => 'Subic Bay Weather Station',
			'RPWS' => 'Surigao',
			'RPVA' => 'Tacloban',
			'RPVT' => 'Tagbilaran',
			'RPUT' => 'Tuguegarao',
			'RPUQ' => 'Vigan',
			'RPUV' => 'Virac',
			'RPMZ' => 'Zamboanga'
		);
	}
} // END OF Stations_PH

?>

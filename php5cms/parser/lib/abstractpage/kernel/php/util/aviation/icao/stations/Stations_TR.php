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
 
class Stations_TR extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_TR()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Turkey';

		$this->icaos = array(
			'LTAG' => 'Adana/Incirlik',
			'LTAF' => 'Adana/Sakirpasa',
			'LTAH' => 'Afyon',
			'LTBT' => 'Akhisar',
			'LTAC' => 'Ankara/Esenboga',
			'LTAD' => 'Ankara/Etimesgut',
			'LTAI' => 'Antalya',
			'LTBD' => 'Aydin',
			'LTBF' => 'Balikesir',
			'LTBG' => 'Bandirma',
			'LTCJ' => 'Batman',
			'LTBV' => 'Bodrum',
			'LTBE' => 'Bursa',
			'LTBH' => 'Canakkale',
			'LTBU' => 'Corlu',
			'LTBS' => 'Dalaman',
			'LTCC' => 'Diyarbakir',
			'LTCA' => 'Elazig',
			'LTCD' => 'Erzincan',
			'LTCE' => 'Erzurum',
			'LTBI' => 'Eskisehir',
			'LTAJ' => 'Gaziantep',
			'LTAK' => 'Iskenderun',
			'LTBM' => 'Isparta',
			'LTBA' => 'Istanbul/Ataturk',
			'LTBJ' => 'Izmir/Adnan Menderes',
			'LTBL' => 'Izmir/Cigli',
			'LTCF' => 'Kars',
			'LTAU' => 'Kayseri/Erkilet',
			'LTAN' => 'Konya',
			'LTAT' => 'Malatya/Erhac',
			'LTAP' => 'Merzifon',
			'LTAE' => 'Murted Tur-Afb',
			'LTCK' => 'Mus Tur-Afb',
			'LTAQ' => 'Samsun',
			'LTAR' => 'Sivas',
			'LTAV' => 'Sivrihisar',
			'LTAW' => 'Tokat',
			'LTBQ' => 'Topel Tur-Afb',
			'LTCG' => 'Trabzon',
			'LTCH' => 'Urfa',
			'LTBO' => 'Usak',
			'LTCI' => 'Van',
			'LTAS' => 'Zonguldak'
		);
	}
} // END OF Stations_TR

?>

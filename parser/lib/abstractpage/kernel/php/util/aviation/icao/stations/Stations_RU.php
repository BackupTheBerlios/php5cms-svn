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
 
class Stations_RU extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_RU()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Russian Federation';

		$this->icaos = array(
			'URSS' => 'Adler',
			'UHMA' => 'Anadyr',
			'ULAA' => 'Arhangel\'Sk',
			'UNBB' => 'Barnaul',
			'UIKB' => 'Bodajbo',
			'UUBP' => 'Brjansk',
			'UHMD' => 'Buhta Providenja',
			'UIAA' => 'Chita',
			'UELL' => 'Cul\'Man',
			'USSS' => 'Ekaterinburg',
			'UHBP' => 'Ekimchan',
			'URWI' => 'Elista',
			'UNII' => 'Enisejsk',
			'UHHH' => 'Habarovsk',
			'USHH' => 'Hanty-Mansijsk',
			'UIUH' => 'Horinsk',
			'UIII' => 'Irkutsk',
			'UEEE' => 'Jakutsk',
			'UHSS' => 'Juzhno-Sahalinsk',
			'UIKK' => 'Kirensk',
			'ULAK' => 'Kotlas',
			'URKK' => 'Krasnodar',
			'UHMM' => 'Magadan',
			'URMM' => 'Mineral\'Nye Vody',
			'UUEE' => 'Moscow/Sheremet\'Ye',
			'UUWW' => 'Moscow/Vnukovo',
			'ULMM' => 'Murmansk',
			'UINN' => 'Nizhneudinsk',
			'UNNN' => 'Novosibirsk',
			'UWPP' => 'Penza',
			'UHPP' => 'Petropavlovsk-Kamchatskij',
			'URRR' => 'Rostov-Na-Donu',
			'UWWW' => 'Samara',
			'ULLI' => 'St. Peterburg',
			'USRR' => 'Surgut',
			'UUYY' => 'Syktyvkar',
			'ULWT' => 'Tot\'Ma',
			'UHHO' => 'Troickoe',
			'UUEM' => 'Tver',
			'UIUU' => 'Ulan-Ude',
			'UUYT' => 'Ust\', Kulom',
			'UIIO' => 'Ust\'Ordynskij',
			'ULOL' => 'Velikie Luki',
			'UHWW' => 'Vladivostok',
			'URWW' => 'Volgograd',
			'ULWW' => 'Vologda',
			'UUOO' => 'Voronez'
		);
	}
} // END OF Stations_RU

?>

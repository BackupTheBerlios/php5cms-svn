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
 
class Stations_ZA extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_ZA()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'South Africa';

		$this->icaos = array(
			'FAAB' => 'Alexander Bay',
			'FAAN' => 'Aliwal North',
			'FABY' => 'Beaufort West',
			'FABM' => 'Bethlehem Airport',
			'FABL' => 'Bloemfontein J. B. M. Hertzog',
			'FACV' => 'Calvinia',
			'FACT' => 'Cape Town D. F. Malan',
			'FACL' => 'Carolina',
			'FADA' => 'De Aar',
			'FADN' => 'Durban Louis Botha',
			'FAEL' => 'East London',
			'FAER' => 'Ellisras',
			'FAFF' => 'Frankfort',
			'FAFR' => 'Fraserburg',
			'FAGG' => 'George Airport',
			'FAGE' => 'Gough Island',
			'FAGR' => 'Graaff Reinet',
			'FAHS' => 'Hoedspruit',
			'FAJS' => 'Jan Smuts',
			'FAKM' => 'Kimberley',
			'FALY' => 'Ladysmith',
			'FALW' => 'Langebaanweg',
			'FALT' => 'Lichtenburg',
			'FAME' => 'Marion Island',
			'FAMB' => 'Middelburg',
			'FAMM' => 'Mmabatho Airport',
			'FAMO' => 'Mossel Bay Cape Saint Blaize',
			'FANS' => 'Nelspruit',
			'FAOH' => 'Oudtshoorn',
			'FAPH' => 'Phalaborwa',
			'FAPB' => 'Pietersburg',
			'FAPE' => 'Port Elizabeth',
			'FAPJ' => 'Port St Johns',
			'FAPR' => 'Pretoria',
			'FAIR' => 'Pretoria Irene',
			'FAQT' => 'Queenstown',
			'FARB' => 'Richard Bay',
			'FASB' => 'Springbok',
			'FATC' => 'Tristan Da Cunha',
			'FAUT' => 'Umtata',
			'FAUP' => 'Upington',
			'FAVR' => 'Vredendal',
			'FAVB' => 'Vryburg',
			'FAWK' => 'Waterkloof Lmb',
			'FAWM' => 'Welkom'
		);
	}
} // END OF Stations_ZA

?>

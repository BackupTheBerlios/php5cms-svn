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
 
class Stations_SE extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_SE()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Sweden';

		$this->icaos = array(
			'ESDB' => 'Angelholm',
			'ESSD' => 'Borlange',
			'ESNG' => 'Gallivare',
			'ESSK' => 'Gavle/Sandviken Air Force Base',
			'ESGG' => 'Goteborg/Landvetter',
			'ESGP' => 'Goteborg/Save',
			'ESPD' => 'Gunnarn',
			'ESMV' => 'Hagshult',
			'ESMT' => 'Halmstad Swedish Air Force Base',
			'ESSF' => 'Hultsfred Swedish Air Force Base',
			'ESNJ' => 'Jokkmokk',
			'ESGJ' => 'Jonkoping Flygplats',
			'ESMQ' => 'Kalmar',
			'ESSQ' => 'Karlstad Flygplats',
			'ESNQ' => 'Kiruna Airport',
			'ESNK' => 'Kramfors Flygplats',
			'ESMK' => 'Kristianstad/Everod',
			'ESCF' => 'Linkoping/Malmen',
			'ESDA' => 'Ljungbyhed',
			'ESPA' => 'Lulea/Kallax',
			'ESMS' => 'Malmo/Sturup',
			'ESSP' => 'Norrkoping',
			'ESNO' => 'Ornskoldsvik Airport',
			'ESPC' => 'Ostersund/Froson',
			'ESDF' => 'Ronneby',
			'ESIB' => 'Satenas',
			'ESNS' => 'Skelleftea Airport',
			'ESGR' => 'Skovde Flygplats',
			'ESCL' => 'Soderhamn',
			'ESSA' => 'Stockholm/Arlanda',
			'ESSB' => 'Stockholm/Bromma',
			'ESNN' => 'Sundsvall-Harnosand Flygplats',
			'ESGT' => 'Trollhattan Private',
			'ESNU' => 'Umea Flygplats',
			'ESCM' => 'Uppsala',
			'ESOW' => 'Vasteras/Hasslo',
			'ESMX' => 'Vaxjo',
			'ESPE' => 'Vidsel',
			'ESSV' => 'Visby Flygplats'
		);
	}
} // END OF Stations_SE

?>

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
 
class Stations_HU extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_HU()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Hungary';

		$this->icaos = array(
			'LHBC' => 'B�k�scsaba',
			'LHBS' => 'Buda�rs',
			'LHBP' => 'Budapest/Ferihegy',
			'LHBM' => 'Budapest Met Center',
			'LHDC' => 'Debrecen',
			'LHKV' => 'Kaposv�r',
			'LHKE' => 'Kecskem�t',
			'LHMC' => 'Miskolc',
			'LHNY' => 'Nyiregyh�za/Napkor',
			'LHPA' => 'P�pa',
			'LHPP' => 'P�cs/Pog�ny',
			'LHSK' => 'Si�fok',
			'LHUD' => 'Szeged',
			'LHSN' => 'Szolnok',
			'LHSY' => 'Szombathely'
		);
	}
} // END OF Stations_HU

?>

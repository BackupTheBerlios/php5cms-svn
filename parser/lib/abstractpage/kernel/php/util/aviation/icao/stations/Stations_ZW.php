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


class Stations_ZW extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_ZW()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Zimbabwe';

		$this->icaos = array(
			'FVBB' => 'Beitbridge',
			'FVBI' => 'Binga',
			'FVCZ' => 'Buffalo Range',
			'FVBU' => 'Bulawayo Airport',
			'FVCH' => 'Chipinge',
			'FVGO' => 'Gokwe',
			'FVTL' => 'Gweru',
			'FVHA' => 'Harare Kutsaga',
			'FVWN' => 'Hwange National Park',
			'FVKB' => 'Kariba',
			'FVKA' => 'Karoi',
			'FVMV' => 'Masvingo',
			'FVMT' => 'Mutoko',
			'FVRU' => 'Rusape',
			'FVFA' => 'Victoria Falls'
		);
	}
} // END OF Stations_ZW

?>

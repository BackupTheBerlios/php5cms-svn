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
 
class Stations_GR extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_GR()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Greece';

		$this->icaos = array(
			'LGPZ' => 'Aktion Airport',
			'LGAL' => 'Alexandroupoli Airport',
			'LGBL' => 'Anchialos Airport',
			'LGAD' => 'Andravida Airport',
			'LGRX' => 'Araxos Airport',
			'LGAT' => 'Athinai Airport',
			'LGHI' => 'Chios Airport',
			'LGKV' => 'Chrysoupoli Airport',
			'LGEL' => 'Elefsis Airport',
			'LGIR' => 'Heraklion Airport',
			'LGKL' => 'Kalamata Airport',
			'LGKP' => 'Karpathos Airport',
			'LGKA' => 'Kastoria Airport',
			'LGKF' => 'Kefalhnia Airport',
			'LGKR' => 'Kerkyra Airport',
			'LGKO' => 'Kos Airport',
			'LGKZ' => 'Kozani Airport',
			'LGLR' => 'Larissa Airport',
			'LGLM' => 'Limnos Airport',
			'LGMT' => 'Mytilini Airport',
			'LGRP' => 'Rhodes Airport',
			'LGSM' => 'Samos Airport',
			'LGSR' => 'Santorini Island',
			'LGSK' => 'Skiathos Island',
			'LGSA' => 'Souda Airport',
			'LGTG' => 'Tanagra Airport',
			'LGTT' => 'Tatoi',
			'LGTS' => 'Thessaloniki Airport',
			'LGTP' => 'Tripolis Airport',
			'LGZA' => 'Zakinthos Airport'
		);
	}
} // END OF Stations_GR

?>

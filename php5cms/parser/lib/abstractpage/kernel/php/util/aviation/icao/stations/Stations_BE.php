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
 
class Stations_BE extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_BE()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Belgium';

		$this->icaos = array(
			'EBAW' => 'Antwerpen/Deurne',
			'EBBE' => 'Beauvechain',
			'EBBX' => 'Bertrix Bel-Afb',
			'EBLG' => 'Bierset',
			'EBBT' => 'Brasschaat',
			'EBBR' => 'Bruxelles National',
			'EBCI' => 'Charleroi/Gosselies',
			'EBCV' => 'Chievres',
			'EBLB' => 'Elsenborn',
			'EBFS' => 'Florennes',
			'EBZW' => 'Genk',
			'EBGT' => 'Gent/Industrie-Zone',
			'EBTN' => 'Goetsenhoven',
			'EBBL' => 'Kleine Brogel',
			'EBFN' => 'Koksijde',
			'EBMB' => 'Melsbroek Bel-Afb',
			'EBMT' => 'Munte',
			'EBOS' => 'Oostende Airport',
			'EBDT' => 'Schaffen',
			'EBST' => 'Sint-Truiden',
			'EBSP' => 'Spa/La Sauveniere',
			'EBSU' => 'St-Hubert',
			'EBWE' => 'Weelde Military'
		);
	}
} // END OF Stations_BE

?>

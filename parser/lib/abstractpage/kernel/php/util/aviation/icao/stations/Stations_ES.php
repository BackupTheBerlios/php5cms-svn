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
 
class Stations_ES extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_ES()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Spain';

		$this->icaos = array(
			'LEAB' => 'Albacete/Los Llanos',
			'LEAL' => 'Alicante/El Altet',
			'LEAM' => 'Almeria/Aeropuerto',
			'LEAS' => 'Asturias/Aviles',
			'LEBZ' => 'Badajoz/Talavera La Real',
			'LEBL' => 'Barcelona/Aeropuerto',
			'LEBB' => 'Bilbao/Sondica',
			'LEBG' => 'Burgos/Villafria',
			'LECH' => 'Calamocha',
			'LEBA' => 'Cordoba/Aeropuerto',
			'GCFV' => 'Fuerteventura/Aeropuerto',
			'LEGE' => 'Gerona/Costa Brava',
			'LEGR' => 'Granada/Aeropuerto',
			'GCHI' => 'Hierro/Aeropuerto',
			'LEHI' => 'Hinojosa Del Duque',
			'LEIB' => 'Ibiza/Es Codola',
			'LEJR' => 'Jerez De La Fronteraaeropuerto',
			'LECO' => 'La Coruna/Alvedro',
			'GCLA' => 'La Palma/Aeropuerto',
			'GCRR' => 'Lanzarote/Aeropuerto',
			'GCLP' => 'Las Palmas De Gran Canaria/Gando',
			'LELN' => 'Leon/Virgen Del Camino',
			'LELO' => 'Logrono/Agoncillo',
			'LECV' => 'Madri-Colmenar',
			'LEMD' => 'Madrid/Barajas',
			'LEVS' => 'Madrid/Cuatro Vientos',
			'LEGT' => 'Madrid/Getafe',
			'LETO' => 'Madrid/Torrejon',
			'LEMG' => 'Malaga/Aeropuerto',
			'GEML' => 'Melilla',
			'LEMH' => 'Menorca/Mahon',
			'LEMO' => 'Moron De La Frontera',
			'LERI' => 'Murcia/Alcantarilla',
			'LELC' => 'Murcia/San Javier',
			'LEPA' => 'Palma De Mallorca/Son San Juan',
			'LEPP' => 'Pamplona/Noain',
			'LERS' => 'Reus/Aeropuerto',
			'LERT' => 'Rota',
			'LESA' => 'Salamanca/Matacan',
			'LESO' => 'San Sebastian/Fuenterrabia',
			'LEXJ' => 'Santander/Parayas',
			'LEST' => 'Santiago/Labacolla',
			'LEZL' => 'Sevilla/San Pablo',
			'GCXO' => 'Tenerife/Los Rodeos',
			'GCTS' => 'Tenerife Sur',
			'LEVC' => 'Valencia/Aeropuerto',
			'LEVD' => 'Valladolid/Villanubla',
			'LEVX' => 'Vigo/Peinador',
			'LEVT' => 'Vitoria',
			'LEZG' => 'Zaragoza/Aeropuerto'
		);
	}
} // END OF Stations_ES

?>

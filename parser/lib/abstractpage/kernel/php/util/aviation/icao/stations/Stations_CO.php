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
 
class Stations_CO extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_CO()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Colombia';

		$this->icaos = array(
			'SKLC' => 'Apartado/Los Cedros',
			'SKUC' => 'Arauca/Santiago Perez',
			'SKAR' => 'Armenia/El Eden',
			'SKEJ' => 'Barrancabermeja/Yariguies',
			'SKBQ' => 'Barranquilla/Ernestocortissoz',
			'SKBO' => 'Bogota/Eldorado',
			'SKBG' => 'Bucaramanga/Palonegro',
			'SKBU' => 'Buenaventura',
			'SKCL' => 'Cali/Alfonso Bonillaaragon',
			'SKCG' => 'Cartagena/Rafael Nunez',
			'SKCC' => 'Cucuta/Camilo Daza',
			'SKIB' => 'Ibague/Perales',
			'SKIP' => 'Ipiales/San Luis',
			'SKLT' => 'Leticia/Vasquez Cobo',
			'SKMD' => 'Medellin/Olaya Herrera',
			'SKMU' => 'Mitu',
			'SKMR' => 'Monteria/Los Garzones',
			'SKNV' => 'Neiva/Benito Salas',
			'SKPS' => 'Pasto/Antonio Narin',
			'SKPE' => 'Pereira/Matecana',
			'SKPP' => 'Popayan/Guillermo',
			'SKPV' => 'Providencia Isla/El Embrujo',
			'SKAS' => 'Puerto Asis',
			'SKPC' => 'Puerto Carreno/A. Guauquea',
			'SKUI' => 'Quibdo/El Carano',
			'SKRH' => 'Riohacha/Almirante Padilla',
			'SKRG' => 'Rionegro/J. M. Cordova',
			'SKSP' => 'San Andres Isla/Sesquicentenario',
			'SKSJ' => 'San Jose Del Guaviare',
			'SKSM' => 'Santa Marta/Simon Bolivar',
			'SKVP' => 'Valledupar/Alfonso Lopez',
			'SKVV' => 'Villavicencio/Vanguardia'
		);
	}
} // END OF Stations_CO

?>

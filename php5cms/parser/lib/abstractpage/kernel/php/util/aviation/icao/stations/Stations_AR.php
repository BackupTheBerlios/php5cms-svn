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
 
class Stations_AR extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_AR()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Argentina';

		$this->icaos = array(
			'SABE' => 'Aeroparque Bs. As. Aerodrome',
			'SAZA' => 'Azul Airport',
			'SAZB' => 'Bahia Blanca Aerodrome',
			'SAZS' => 'Bariloche Aerodrome',
			'SABA' => 'Buenos Aires Observatorio',
			'SANC' => 'Catamarca Aero.',
			'SANW' => 'Ceres Aerodrome',
			'SAZY' => 'Chapelco',
			'SACP' => 'Chepes',
			'SANO' => 'Chilecito',
			'SAVC' => 'Comodoro Rivadavia Aerodrome',
			'SAAC' => 'Concordia Aerodrome',
			'SACO' => 'Cordoba Aerodrome',
			'SARC' => 'Corrientes Aero.',
			'SATU' => 'Curuzu Cuatia Aerodrome',
			'SAZD' => 'Dolores Aerodrome',
			'SADD' => 'Don Torcuato Aerodrome',
			'SAVB' => 'El Bolson Aerodrome',
			'SADP' => 'El Palomar Aerodrome',
			'SAVE' => 'Esquel Aerodrome',
			'SAEZ' => 'Ezeiza Aerodrome',
			'SARF' => 'Formosa Aerodrome',
			'SAZG' => 'General Pico Aerodrome',
			'SAWR' => 'Gobernador Gregores Aerodrome',
			'SAAG' => 'Gualeguaychu Aerodrome',
			'SARI' => 'Iguazu Aerodrome',
			'SAMJ' => 'Jachal',
			'SASJ' => 'Jujuy Aerodrome',
			'SAAJ' => 'Junin Aerodrome',
			'SADL' => 'La Plata Aerodrome',
			'SASQ' => 'La Quiaca Observatorio',
			'SANL' => 'La Rioja Aero.',
			'SAOL' => 'Laboulaye',
			'SAWA' => 'Lago Argentino Aerodrome',
			'SATK' => 'Las Lomitas',
			'SAMM' => 'Malargue Aerodrome',
			'SAZM' => 'Mar Del Plata Aerodrome',
			'SAOM' => 'Marcos Juarez Aerodrome',
			'SAME' => 'Mendoza Aerodrome',
			'SARM' => 'Monte Caseros Aerodrome',
			'SAZN' => 'Neuquen Aerodrome',
			'SASO' => 'Oran Aerodrome',
			'SAAP' => 'Parana Aerodrome',
			'SAVP' => 'Paso De Indios',
			'SARL' => 'Paso De Los Libres Aerodrome',
			'SAZP' => 'Pehuajo Aerodrome',
			'SAWP' => 'Perito Moreno Aerodrome',
			'SAZE' => 'Pigue Aerodrome',
			'SACI' => 'Pilar Observatorio',
			'SARP' => 'Posadas Aero.',
			'SARS' => 'Presidencia Roque Saenz Pena Aer',
			'SAWD' => 'Puerto Deseado Aerodrome',
			'SARE' => 'Resistencia Aero.',
			'SAZQ' => 'Rio Colorado',
			'SAOC' => 'Rio Cuarto Aerodrome',
			'SAWG' => 'Rio Gallegos Aerodrome',
			'SAWE' => 'Rio Grande B. A.',
			'SASR' => 'Rivadavia',
			'SAAR' => 'Rosario Aerodrome',
			'SASA' => 'Salta Aerodrome',
			'SAVO' => 'San Antonio Oeste Aerodrome',
			'SAMS' => 'San Carlos',
			'SANU' => 'San Juan Aerodrome',
			'SAWJ' => 'San Julian Aerodrome',
			'SAOU' => 'San Luis Aerodrome',
			'SAMI' => 'San Martin',
			'SAMR' => 'San Rafael Aerodrome',
			'SAWU' => 'Santa Cruz Aerodrome',
			'SAZR' => 'Santa Rosa Aerodrome',
			'SANE' => 'Santiago Del Estero Aero.',
			'SAAV' => 'Sauce Viejo Aerodrome',
			'SAZT' => 'Tandil Aerodrome',
			'SAST' => 'Tartagal Aerodrome',
			'SANI' => 'Tinogasta',
			'SAVT' => 'Trelew Aerodrome',
			'SAZH' => 'Tres Arroyos',
			'SANT' => 'Tucuman Aerodrome',
			'SAWH' => 'Ushuaia Aerodrome',
			'SAMU' => 'Uspallata',
			'SAVV' => 'Viedma Aerodrome',
			'SACV' => 'Villa De Maria Del Rio Seco',
			'SAOD' => 'Villa Dolores Aerodrome',
			'SAZV' => 'Villa Gesell',
			'SAOR' => 'Villa Reynolds Aerodrome',
			'SAAU' => 'Villaguay Aerodrome'
		);
	}
} // END OF Stations_AR

?>

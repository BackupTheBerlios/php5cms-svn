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
 
class Stations_NO extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_NO()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Norway';

		$this->icaos = array(
			'ENAL' => 'Alesund/Vigra',
			'ENAT' => 'Alta Lufthavn',
			'ENAN' => 'Andoya',
			'ENNA' => 'Banak',
			'ENDU' => 'Bardufoss',
			'ENBR' => 'Bergen/Flesland',
			'ENBV' => 'Berlevag',
			'ENBJ' => 'Bjornoya',
			'ENBO' => 'Bodo Vi',
			'ENBN' => 'Bronnoysund/Bronnoy',
			'ENDI' => 'Dagali',
			'ENEK' => 'Ekofisk Oil Platform',
			'ENEV' => 'Evenes',
			'ENFG' => 'Fagernes Leirin',
			'ENFL' => 'Floro',
			'ENBL' => 'Forde/Bringeland',
			'ENFR' => 'Frigg',
			'ENGC' => 'Gullfax Platform',
			'ENHF' => 'Hammerfest',
			'ENHD' => 'Haugesund/Karmoy',
			'ENHV' => 'Honningsvag/Valan',
			'ENHO' => 'Hopen',
			'ENJA' => 'Jan Mayen',
			'ENKA' => 'Kautokeino',
			'ENKR' => 'Kirkenes Lufthavn',
			'ENCN' => 'Kristiansand/Kjevik',
			'ENKB' => 'Kristiansund/Kvernberget',
			'ENLK' => 'Leknes',
			'ENLI' => 'Lista Flyplass',
			'ENMH' => 'Mehamn',
			'ENRA' => 'Mo I Rana/Rossvoll',
			'ENML' => 'Molde/Aro',
			'ENMS' => 'Mosjoen Kjaerstad',
			'ENNM' => 'Namsos Lufthavn',
			'ENNK' => 'Narvik Iii',
			'ENNO' => 'Notodden',
			'ENAS' => 'Ny-Alesund Ii',
			'ENOL' => 'Orland Iii',
			'ENOV' => 'Orsta-Volda/Hovden',
			'ENOA' => 'Oseberg',
			'ENFB' => 'Oslo/Fornebu',
			'ENGM' => 'Oslo/Gardermoen',
			'ENRO' => 'Roros Lufthavn',
			'ENRM' => 'Rorvik/Ryum',
			'ENRS' => 'Rost Flyplass',
			'ENRY' => 'Rygge',
			'ENSD' => 'Sandane/Anda',
			'ENST' => 'Sandnessjoen/Stokka',
			'ENSN' => 'Skien-Geiteryggen',
			'ENSG' => 'Sogndal/Haukasen',
			'ENSR' => 'Sorkjosen',
			'ENZV' => 'Stavanger/Sola',
			'ENSO' => 'Stord/Soerstokken',
			'ENSK' => 'Storkmarknes/Skagen',
			'ENSB' => 'Svalbard Lufthavn',
			'ENSS' => 'Svartnes',
			'ENSH' => 'Svolvaer/Helle',
			'ENTO' => 'Torp',
			'ENTC' => 'Tromso/Langnes',
			'ENVA' => 'Trondheim/Vaernes',
			'ENVD' => 'Vadso',
			'ENBM' => 'Voss-Bo'
		);
	}
} // END OF Stations_NO

?>

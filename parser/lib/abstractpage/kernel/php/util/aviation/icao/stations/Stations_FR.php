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
 
class Stations_FR extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_FR()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'France';

		$this->icaos = array(
			'LFOI' => 'Abbeville',
			'LFBA' => 'Agen',
			'LFMA' => 'Aix Les Milles',
			'LFKJ' => 'Ajaccio',
			'LFCI' => 'Albi',
			'LFOF' => 'Alencon',
			'LFXA' => 'Amberieu',
			'LFRA' => 'Angers',
			'LFXI' => 'Apt/Saint Christol',
			'LFDH' => 'Auch',
			'LFLW' => 'Aurillac',
			'LFLA' => 'Auxerre',
			'LFSB' => 'Bale-Mulhouse',
			'LFKB' => 'Bastia',
			'LFOB' => 'Beauvais',
			'LFSQ' => 'Belfort',
			'LFBE' => 'Bergerac',
			'LFSA' => 'Besancon',
			'LFMU' => 'Beziers/Vias',
			'LFBZ' => 'Biarritz',
			'LFBS' => 'Biscarosse',
			'LFBD' => 'Bordeaux/Merignac',
			'LFLD' => 'Bourges',
			'LFRB' => 'Brest',
			'LFBV' => 'Brive',
			'LFRK' => 'Caen',
			'LFKC' => 'Calvi',
			'LFQI' => 'Cambrai',
			'LFMD' => 'Cannes',
			'LFMK' => 'Carcassonne',
			'LFIG' => 'Cassagnes-Begonhes',
			'LFBC' => 'Cazaux',
			'LFLB' => 'Chambery/Aix-Les-Bains',
			'LFQV' => 'Charleville',
			'LFOR' => 'Chartres',
			'LFOC' => 'Chateaudun',
			'LFLX' => 'Chateauroux',
			'LFQH' => 'Chatillon-Sur-Seine',
			'LFRC' => 'Cherbourg/Maupertus',
			'LFLC' => 'Clermont-Ferrand',
			'LFBG' => 'Cognac',
			'LFSC' => 'Colmar',
			'LFPC' => 'Creil Fafb',
			'LFBY' => 'Dax',
			'LFSD' => 'Dijon',
			'LFRD' => 'Dinard',
			'LFOE' => 'Evreux',
			'LFKF' => 'Figari',
			'LFLS' => 'Grenoble/St. Geoirs',
			'NLWW' => 'Hihifo Ile Wallis',
			'LFTH' => 'Hyeres',
			'LFMI' => 'Istres',
			'LFOH' => 'La Heve',
			'LFRI' => 'La Roche-Sur-Yon',
			'LFBH' => 'La Rochelle',
			'LFRJ' => 'Landivisiau',
			'LFRH' => 'Lann Bihoue',
			'LFRO' => 'Lannion/Servel',
			'LFRL' => 'Lanveoc Poulmic',
			'LFMC' => 'Le Luc',
			'LFRM' => 'Le Mans',
			'LFHP' => 'Le Puy',
			'LFAT' => 'Le Touquet',
			'LFQQ' => 'Lille',
			'LFBL' => 'Limoges',
			'LFSX' => 'Luxeuil',
			'LFLY' => 'Lyon/Bron',
			'LFLL' => 'Lyon/Satolas',
			'LFLM' => 'Macon',
			'NLWF' => 'Maopoopo Ile Futuna',
			'LFML' => 'Marseille/Marignane',
			'LFPM' => 'Melun',
			'LFNB' => 'Mende/Brenoux',
			'LFSF' => 'Metz/Frescaty',
			'LFJL' => 'Metz-Nancy-Lorraine',
			'LFBM' => 'Mont-De-Marsan',
			'LFLQ' => 'Montelimar',
			'LFBK' => 'Montlucon/Gueret',
			'LFMT' => 'Montpellier',
			'LFRU' => 'Morlaix/Ploujean',
			'LFSN' => 'Nancy/Essey',
			'LFSO' => 'Nancy/Ochey',
			'LFRS' => 'Nantes',
			'LFQG' => 'Nevers',
			'LFMN' => 'Nice',
			'LFME' => 'Nimes/Courbessac',
			'LFTW' => 'Nimes/Garons',
			'LFBN' => 'Niort',
			'LFMO' => 'Orange',
			'LFOJ' => 'Orleans',
			'LFPB' => 'Paris/Le Bourget',
			'LFPW' => 'Paris Met Center',
			'LFPG' => 'Paris-Aeroport Charles De Gaulle',
			'LFPO' => 'Paris-Orly',
			'LFBP' => 'Pau',
			'LFBX' => 'Perigueux',
			'LFMP' => 'Perpignan',
			'LFBI' => 'Poitiers',
			'LFRQ' => 'Quimper',
			'LFSR' => 'Reims',
			'LFRN' => 'Rennes',
			'LFCR' => 'Rodez',
			'LFYR' => 'Romorantin',
			'LFOP' => 'Rouen',
			'LFRT' => 'Saint-Brieuc',
			'LFOW' => 'Saint-Quentin',
			'LFLN' => 'Saint-Yan',
			'LFMY' => 'Salon',
			'LFKS' => 'Solenzara',
			'LFMX' => 'St-Auban-Sur-Durance',
			'LFSI' => 'St-Dizier',
			'LFMH' => 'St-Etienne Boutheon',
			'LFCG' => 'St-Girons',
			'LFRZ' => 'St-Nazaire',
			'LFTU' => 'St-Raphael',
			'LFST' => 'Strasbourg',
			'LFFS' => 'Suippes Range Met',
			'LFBT' => 'Tarbes/Ossun',
			'LFSL' => 'Toul/Rosieres',
			'LFBO' => 'Toulouse/Blagnac',
			'LFBF' => 'Toulouse/Francazal',
			'LFOT' => 'Tours',
			'LFPN' => 'Toussus Le Noble',
			'LFQB' => 'Troyes',
			'LFLV' => 'Vichy',
			'LFPV' => 'Villacoublay',
			'LFOS' => 'Vittefleur/St. Vale'
		);
	}
} // END OF Stations_FR

?>

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
 
class Stations_DE extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_DE()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Germany';

		$this->icaos = array(
			'ETBA' => 'Aachen/Merzbruck',
			'ETHA' => 'Altenstadt',
			'ETEB' => 'Ansbach/Katterbach',
			'EDMA' => 'Augsburg',
			'ETEH' => 'Bad Kreuznach',
			'EDFM' => 'Baden Wurttemberg, Neuostheim',
			'EDQD' => 'Bayreuth',
			'ETGB' => 'Bergen',
			'EDDB' => 'Berlin-Schoenefeld',
			'EDDT' => 'Berlin-Tegel',
			'EDDI' => 'Berlin-Tempelhof',
			'EDVE' => 'Braunschweig',
			'EDDW' => 'Bremen',
			'ETUR' => 'Brueggen',
			'ETSB' => 'Buechel',
			'ETHB' => 'Bueckeburg',
			'ETHC' => 'Celle',
			'ETOR' => 'Coleman Mannheim',
			'ETHT' => 'Cottbus Flugplatz',
			'ETND' => 'Diepholz',
			'EDLW' => 'Dortmund/Wickede',
			'EDDC' => 'Dresden-Klotzsche',
			'EDDL' => 'Duesseldorf',
			'ETME' => 'Eggebek',
			'ETSE' => 'Erding',
			'EDDE' => 'Erfurt-Bindersleben',
			'EDZE' => 'Essen',
			'ETHS' => 'Fassberg',
			'EDDF' => 'Frankfurt/M-Flughafen',
			'EDNY' => 'Friedrichshafen',
			'ETHF' => 'Fritzlar',
			'ETSF' => 'Fuerstenfeldbruck',
			'ETNG' => 'Geilenkirchen',
			'ETEU' => 'Giebelstadt',
			'ETGG' => 'Gluecksburg/Meierwik',
			'ETIC' => 'Grafenwoehr',
			'ETUO' => 'Guetersloh',
			'EDFH' => 'Hahn',
			'EDHI' => 'Hamburg-Finkenwerder',
			'EDDH' => 'Hamburg-Fuhlsbuettel',
			'ETID' => 'Hanau',
			'EDDV' => 'Hannover',
			'ETIE' => 'Heidelberg',
			'EDQM' => 'Hof',
			'ETIH' => 'Hohenfels',
			'ETNH' => 'Hohn',
			'ETSH' => 'Holzdorf',
			'ETNP' => 'Hopsten',
			'ETGI' => 'Idar-Oberstein',
			'ETIK' => 'Illesheim',
			'ETSI' => 'Ingolstadt',
			'ETHI' => 'Itzehoe',
			'ETNJ' => 'Jever',
			'ETGY' => 'Kalkar',
			'EDVK' => 'Kassel/Calden',
			'ETMK' => 'Kiel-Holtenau',
			'ETIN' => 'Kitzingen Usa  Af',
			'EDDK' => 'Koeln/Bonn',
			'EDTZ' => 'Konstanz',
			'ETGK' => 'Kuemmersruck',
			'ETNL' => 'Laage',
			'ETUL' => 'Laarbruch',
			'ETSA' => 'Landsberg',
			'ETHL' => 'Laupheim',
			'ETSL' => 'Lechfeld',
			'EDDP' => 'Leipzig-Schkeuditz',
			'EDWD' => 'Lemwerder',
			'EDHL' => 'Luebeck-Blankensee',
			'EDOP' => 'Mecklenburg-Vorpommern, Parchim',
			'ETSM' => 'Memmingen',
			'ETHM' => 'Mendig',
			'ETGZ' => 'Messstetten',
			'EDLN' => 'Monchengladbach',
			'EDDG' => 'Muenster/Osnabrueck',
			'EDDM' => 'Munich/Riem',
			'ETSN' => 'Neuburg/Donau',
			'ETHN' => 'Niederstetten',
			'ETNN' => 'Noervenich',
			'ETMN' => 'Nordholz',
			'ETUN' => 'Nordhorn',
			'EDDN' => 'Nuernberg',
			'EDMO' => 'Oberpfaffenhofen',
			'EDLP' => 'Paderborn/Lippstadt',
			'ETSP' => 'Pferdsfeld',
			'ETNR' => 'Preschen',
			'ETAR' => 'Ramstein',
			'ETHE' => 'Rheine-Bentlage',
			'ETHR' => 'Roth',
			'EDDR' => 'Saarbruecken/Ensheim',
			'ETNS' => 'Schleswig-Jagel',
			'ETAS' => 'Sembach United States Air Force',
			'ETAD' => 'Spangdahlem',
			'EDDS' => 'Stuttgart-Echterdingen',
			'ETNU' => 'Trollenhagen',
			'EDXW' => 'Westerland/Sylt',
			'ETOU' => 'Wiesbaden',
			'ETNT' => 'Wittmundhaven',
			'ETGW' => 'Wittstock',
			'ETNW' => 'Wunstorf'
		);
	}
} // END OF Stations_DE

?>

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
 
class Stations_IT extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_IT()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Italy';

		$this->icaos = array(
			'LIMG' => 'Albenga',
			'LIEA' => 'Alghero',
			'LIBA' => 'Amendola',
			'LIQB' => 'Arezzo',
			'LIPA' => 'Aviano',
			'LIYW' => 'Aviano Usaf',
			'LIBD' => 'Bari/Palese Macchie',
			'LIME' => 'Bergamo/Orio Al Serio',
			'LIPE' => 'Bologna/Borgo Panigale',
			'LIPB' => 'Bolzano',
			'LIBW' => 'Bonifati',
			'LIPL' => 'Brescia/Ghedi',
			'LIBR' => 'Brindisi',
			'LIEE' => 'Cagliari/Elmas',
			'LIBS' => 'Campobasso',
			'LIEB' => 'Capo Bellavista',
			'LIEH' => 'Capo Caccia',
			'LIEC' => 'Capo Carbonara',
			'LIEF' => 'Capo Frasca',
			'LIMU' => 'Capo Mele',
			'LIQK' => 'Capo Palinuro',
			'LIEL' => 'Capo S. Lorenzo',
			'LIQC' => 'Capri',
			'LICC' => 'Catania/Fontanarossa',
			'LICZ' => 'Catania/Sigonella',
			'LIPC' => 'Cervia',
			'LIQJ' => 'Civitavecchia',
			'LICO' => 'Cozzo Spadaro',
			'LIBC' => 'Crotone',
			'LIED' => 'Decimomannu',
			'LIVD' => 'Dobbiaco',
			'LICE' => 'Enna',
			'LIPY' => 'Falconara',
			'LIPF' => 'Ferrara',
			'LIRQ' => 'Firenze/Peretola',
			'LIEN' => 'Fonni',
			'LIPK' => 'Forli',
			'LIVF' => 'Frontone',
			'LIRH' => 'Frosinone',
			'LICL' => 'Gela',
			'LIMJ' => 'Genova/Sestri',
			'LIBV' => 'Gioia Del Colle',
			'LIRM' => 'Grazzanise',
			'LIRS' => 'Grosseto',
			'LIBG' => 'Grottaglie',
			'LIEG' => 'Guardiavecchia',
			'LIRG' => 'Guidonia',
			'LIQP' => 'Isola Di Palmaria',
			'LICA' => 'Lamezia Terme',
			'LICD' => 'Lampedusa',
			'LIRL' => 'Latina',
			'LIBU' => 'Latronico',
			'LIBN' => 'Lecce',
			'LIRJ' => 'M. Calamita',
			'LIBH' => 'Marina Di Ginosa',
			'LICF' => 'Messina',
			'LIML' => 'Milano/Linate',
			'LIMC' => 'Milano/Malpensa',
			'LIQO' => 'Monte Argentario',
			'LIMO' => 'Monte Bisbino',
			'LIVC' => 'Monte Cimone',
			'LIMY' => 'Monte Malanotte',
			'LIBE' => 'Monte S. Angelo',
			'LIBQ' => 'Monte Scuro',
			'LIRK' => 'Monte Terminillo',
			'LIRN' => 'Napoli/Capodichino',
			'LIMN' => 'Novara/Cameri',
			'LIEO' => 'Olbia/Costa Smeralda',
			'LIVP' => 'Paganella',
			'LICJ' => 'Palermo/Punta Raisi',
			'LICP' => 'Palermo Boccadifalco',
			'LICG' => 'Pantelleria',
			'LIMV' => 'Passo Dei Giovi',
			'LIMT' => 'Passo Della Cisa',
			'LIVR' => 'Passo Rolle',
			'LIEP' => 'Perdasdefogu',
			'LIRZ' => 'Perugia',
			'LIBP' => 'Pescara',
			'LIMS' => 'Piacenza',
			'LIMH' => 'Pian Rosa',
			'LIRP' => 'Pisa/S. Giusto',
			'LIQZ' => 'Ponza',
			'LIBZ' => 'Potenza',
			'LIRE' => 'Pratica Di Mare',
			'LIVM' => 'Punta Marina',
			'LIQR' => 'Radicofani',
			'LICR' => 'Reggio Calabria',
			'LIVE' => 'Resia Pass',
			'LIQN' => 'Rieti',
			'LIPR' => 'Rimini',
			'LIRA' => 'Roma/Ciampino',
			'LIRU' => 'Roma/Urbe',
			'LIRF' => 'Roma Fiumicino',
			'LIPQ' => 'Ronchi Dei Legionari',
			'LIBY' => 'S. Maria Di Leuca',
			'LIQW' => 'Sarzana/Luni',
			'LIVO' => 'Tarvisio',
			'LIBT' => 'Termoli',
			'LIMK' => 'Torino/Bric Della Croce',
			'LIMF' => 'Torino/Caselle',
			'LICT' => 'Trapani/Birgi',
			'LIRT' => 'Trevico',
			'LIPS' => 'Treviso/Istrana',
			'LIPH' => 'Treviso/S. Angelo',
			'LIVT' => 'Trieste',
			'LIPD' => 'Udine/Campoformido',
			'LIPI' => 'Udine/Rivolto',
			'LICU' => 'Ustica',
			'LIPZ' => 'Venezia/Tessera',
			'LIPX' => 'Verona/Villafranca',
			'LIPT' => 'Vicenza',
			'LIRB' => 'Vigna Di Valle',
			'LIRV' => 'Viterbo',
			'LIQV' => 'Volterra'
		);
	}
} // END OF Stations_IT

?>

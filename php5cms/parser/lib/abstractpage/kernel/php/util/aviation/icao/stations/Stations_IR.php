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
 
class Stations_IR extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_IR()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Iran, Islamic Republic of';

		$this->icaos = array(
			'OIAA' => 'Abadan',
			'OISA' => 'Abadeh',
			'OIAW' => 'Ahwaz',
			'OIHR' => 'Arak',
			'OIKM' => 'Bam',
			'OIBL' => 'Bandar Lengeh',
			'OIKB' => 'Bandarabbass',
			'OIMB' => 'Birjand',
			'OIMN' => 'Bojnourd',
			'OIBB' => 'Bushehr Civ/Afb',
			'OIZC' => 'Chahbahar',
			'OIFM' => 'Esfahan',
			'OISF' => 'Fasa',
			'OIAH' => 'Gach Saran Du Gunbadan',
			'OICG' => 'Ghasre-Shirin',
			'OIIK' => 'Ghazvin',
			'OING' => 'Gorgan',
			'OIZI' => 'Iranshahr',
			'OIZJ' => 'Jask',
			'OIFK' => 'Kashan',
			'OIKK' => 'Kerman',
			'OICC' => 'Kermanshah',
			'OITK' => 'Khoy',
			'OIMM' => 'Mashhad',
			'OIAI' => 'Masjed-Soleyman',
			'OIAG' => 'Omidieh',
			'OITR' => 'Orumieh',
			'OINR' => 'Ramsar',
			'OIGG' => 'Rasht',
			'OIMS' => 'Sabzevar',
			'OITS' => 'Saghez',
			'OICS' => 'Sanandaj',
			'OIIS' => 'Semnan',
			'OIFS' => 'Shahre-Kord',
			'OISS' => 'Shiraz',
			'OIMT' => 'Tabas',
			'OITT' => 'Tabriz',
			'OIII' => 'Tehran-Mehrabad',
			'OIMH' => 'Torbat-Heydarieh',
			'OIYY' => 'Yazd',
			'OIZB' => 'Zabol',
			'OIZH' => 'Zahedan',
			'OITZ' => 'Zanjan'
		);
	}
} // END OF Stations_IR

?>

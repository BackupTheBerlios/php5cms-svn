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
 
class Stations_TH extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_TH()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Thailand';

		$this->icaos = array(
			'VTUC' => 'Chaiyaphum',
			'VTBC' => 'Chanthaburi',
			'VTCC' => 'Chiang Mai',
			'VTCR' => 'Chiang Rai',
			'VTBS' => 'Chon Buri',
			'VTSD' => 'Chumphon',
			'VTBD' => 'Don Muang',
			'VTSS' => 'Hat Yai',
			'VTPH' => 'Hua Hin',
			'VTBG' => 'Kanchanaburi',
			'VTUK' => 'Khon Kaen',
			'VTCL' => 'Lampang',
			'VTUL' => 'Loei',
			'VTCH' => 'Mae Hong Son',
			'VTCS' => 'Mae Sariang',
			'VTPM' => 'Mae Sot',
			'VTUB' => 'Mukdahan',
			'VTUP' => 'Nakhon Phanom',
			'VTUN' => 'Nakhon Ratchasima',
			'VTPN' => 'Nakhon Sawan',
			'VTSN' => 'Nakhon Si Thammarat',
			'VTCN' => 'Nan',
			'VTUM' => 'Nong Khai',
			'VTSK' => 'Pattani',
			'VTBJ' => 'Phetchaburi',
			'VTPS' => 'Phitsanulok',
			'VTCP' => 'Phrae',
			'VTSP' => 'Phuket Airport',
			'VTBI' => 'Prachin Buri',
			'VTBP' => 'Prachuap Khirikhan',
			'VTSR' => 'Ranong',
			'VTBU' => 'Rayong',
			'VTUR' => 'Roi Et',
			'VTUS' => 'Sakon Nakhon',
			'VTSA' => 'Satun',
			'VTSH' => 'Songkhla',
			'VTSB' => 'Surat Thani',
			'VTPT' => 'Tak',
			'VTST' => 'Trang',
			'VTUU' => 'Ubon Ratchathani',
			'VTUD' => 'Udon Thani',
			'VTPU' => 'Uttaradit'
		);
	}
} // END OF Stations_TH

?>

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
 
class Stations_AU extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_AU()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Australia';

		$this->icaos = array(
			'YPAD' => 'Adelaide Airport',
			'YPAL' => 'Albany Airport',
			'YMAY' => 'Albury Airport',
			'YBAS' => 'Alice Springs Aerodrome',
			'YBAM' => 'Amberley Aerodrome',
			'YBAF' => 'Archerfield Aerodrome',
			'YSBK' => 'Bankstown Airport Aws',
			'YPPH' => 'Belmont Perth Airport',
			'YBBN' => 'Brisbane Airport M. O',
			'YPBH' => 'Broken Hill Patton Street',
			'YBRM' => 'Broome Airport',
			'YPEA' => 'Bullsbrook Pearce Amo',
			'YBCS' => 'Cairns Airport',
			'YSCN' => 'Camden Airport',
			'YSCB' => 'Canberra',
			'YPCD' => 'Ceduna Airport',
			'YBCV' => 'Charleville Airport',
			'YSCH' => 'Coffs Harbour Mo',
			'YBCG' => 'Coolangatta Airport Aws',
			'YSCM' => 'Cooma',
			'YPDN' => 'Darwin Airport',
			'YPDB' => 'Derby',
			'YMDV' => 'Devonport East',
			'YSDU' => 'Dubbo',
			'YMES' => 'East Sale Aerodrome',
			'YPED' => 'Edinburgh M. O.',
			'YPFT' => 'Forrest Airport',
			'YPGN' => 'Geraldton Airport',
			'YBGL' => 'Gladstone',
			'YMEN' => 'Goldstream Aws',
			'YDGV' => 'Gove Airport',
			'YMHB' => 'Hobart Airport',
			'YPKG' => 'Kalgoorlie Boulder Amo',
			'YPTN' => 'Katherine Aerodrome',
			'YPKU' => 'Kununurra Kununurra Aws',
			'YMLT' => 'Launceston Airport',
			'YMLV' => 'Laverton Aerodrome',
			'YPLM' => 'Learmonth Airport',
			'YPLC' => 'Leigh Creek Airport',
			'YBLR' => 'Longreach Airport',
			'YBMK' => 'Mackay Mo',
			'YMMQ' => 'Macquarie Island',
			'YPMR' => 'Meekatharra Airport',
			'YMML' => 'Melbourne Airport',
			'YMMI' => 'Mildura Airport',
			'YMMB' => 'Moorabbin Airport Aws',
			'YMMG' => 'Mount Gambier Aerodrome',
			'YBMA' => 'Mount Isa Amo',
			'YSNF' => 'Norfolk Island Airport',
			'YSNW' => 'Nowra Ran Air Station',
			'YBOK' => 'Oakey Aerodrome',
			'YPPF' => 'Parafield Airport',
			'YPPD' => 'Port Hedland Pardoo',
			'YBPN' => 'Proserpine Airport',
			'YSRI' => 'Richmond Aus-Afb',
			'YBRK' => 'Rockhampton Airport',
			'YSSY' => 'Sydney Airport',
			'YSTW' => 'Tamworth Airport',
			'YDTC' => 'Tennant Creek Airport',
			'YBTL' => 'Townsville Amo',
			'YSWG' => 'Wagga Airport',
			'YBWP' => 'Weipa City',
			'YSWM' => 'Williamtown Aerodrome',
			'YPWR' => 'Woomera Aerodrome',
			'YMWY' => 'Wynyard West',
			'YDYL' => 'Yulara Aws'
		);
	}
} // END OF Stations_AU

?>

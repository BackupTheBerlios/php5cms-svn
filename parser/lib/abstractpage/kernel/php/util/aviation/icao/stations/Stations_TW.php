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
 
class Stations_TW extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_TW()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Taiwan';

		$this->icaos = array(
			'RCFS' => 'Chia Tung',
			'RCTP' => 'Chiang Kai Shek',
			'RCKU' => 'Chiayi Tw-Afb',
			'RCQS' => 'Chihhang Tw-Afb',
			'RCBS' => 'Chinmem/Shatou Air Force Base',
			'RCLM' => 'Dongsha',
			'RCNO' => 'Dongshi',
			'RCFN' => 'Feng Nin Tw-Afb',
			'RCKW' => 'Hengchun',
			'RCPO' => 'Hsinchu Tw-Afb',
			'RCYU' => 'Hulien Ab',
			'RCMS' => 'Ilan',
			'RCAY' => 'Kangshan Tw-Afb',
			'RCKH' => 'Kaohsiung International Airport',
			'RCQC' => 'Makung Ab',
			'RCFG' => 'Mazu',
			'RCUK' => 'Pa Kuei/Bakuai',
			'RCSQ' => 'Pingtung North Air Force Base',
			'RCDC' => 'Pingtung South Air Force Base',
			'RCSS' => 'Sungshan/Taipei',
			'RCLG' => 'Taichung Tw-Afb',
			'RCNN' => 'Tainan Tw-Afb',
			'RCGM' => 'Taoyuan Ab = 589650',
			'RCMQ' => 'Wuchia Observatory'
		);
	}
} // END OF Stations_TW

?>

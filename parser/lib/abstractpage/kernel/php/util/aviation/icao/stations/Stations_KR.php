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
 
class Stations_KR extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_KR()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Korea, Republic of';

		$this->icaos = array(
			'RKTA' => 'Andong',
			'RKSX' => 'Camp Stanley/H-207',
			'RKPC' => 'Cheju International Airport',
			'RKTU' => 'Chongju Ab',
			'RKNC' => 'Chunchon Ab',
			'RKNH' => 'Hoengsong Ab',
			'RKTI' => 'Jung Won Rok-Ab',
			'RKNN' => 'Kangnung Ab',
			'RKSN' => 'Koon-Ni Range',
			'RKJK' => 'Kunsan Ab',
			'RKJJ' => 'Kwangju Ab',
			'RKTM' => 'Mangilsan Ab',
			'RKPM' => 'Mosulpo Ab',
			'RKSO' => 'Osan Ab',
			'RKTB' => 'Paekado',
			'RKSP' => 'Paengnyongdo Ab',
			'RKTH' => 'Pohang Ab',
			'RKPK' => 'Pusan/Kimhae International Airport',
			'RKSG' => 'Pyongtaek Ab',
			'RKPS' => 'Sach\'On Ab',
			'RKTS' => 'Sangju',
			'RKSL' => 'Seoul',
			'RKSS' => 'Seoul/Kimp\'O International Airport',
			'RKSF' => 'Seoul/Yongdungp\'O Rokaf Wc',
			'RKSM' => 'Seoul E Ab',
			'RKTE' => 'Songmu Ab',
			'RKSW' => 'Suwon Ab',
			'RKTT' => 'Taegu',
			'RKTN' => 'Taegu Ab',
			'RKTF' => 'Taejon',
			'RKTD' => 'Taejon Kor-Afb',
			'RKSB' => 'Tonghae Radar Site',
			'RKPU' => 'Ulsan',
			'RKNW' => 'Wonju',
			'RKTW' => 'Woong Cheon',
			'RKTY' => 'Yechon Ab',
			'RKSU' => 'Yeoju Range',
			'RKSQ' => 'Yeonpyeungdo',
			'RKSY' => 'Yongsan/H-208 Hp',
			'RKJY' => 'Yosu Airport'
		);
	}
} // END OF Stations_KR

?>

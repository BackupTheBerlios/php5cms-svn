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
 
class Stations_JP extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_JP()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Japan';

		$this->icaos = array(
			'RJOE' => 'Akeno Ab',
			'RJSK' => 'Akita Airport',
			'RJKA' => 'Amami Airport',
			'RJSA' => 'Aomori Airport',
			'RJCA' => 'Asahikawa Ab',
			'RJEC' => 'Asahikawa Airport',
			'RJFA' => 'Ashiya Ab',
			'RJTA' => 'Atsugi United States Naval Air Station',
			'RJAO' => 'Chichijima',
			'RJCC' => 'Chitose Ab',
			'RJCJ' => 'Chitose Japanese Air Self Defense Force',
			'RJTF' => 'Chofu Airport',
			'RJAT' => 'Fuji Ab',
			'RJFE' => 'Fukue Airport',
			'RJNF' => 'Fukui Airport',
			'RJFF' => 'Fukuoka Airport',
			'RJSF' => 'Fukushima Airport',
			'ROTM' => 'Futenma Marine Corps Air Facilit',
			'RJNG' => 'Gifu Ab',
			'RJTH' => 'Hachijojima Airport',
			'RJSH' => 'Hachinohe Ab',
			'RJCH' => 'Hakodate Airport',
			'RJNH' => 'Hamamatsu Ab',
			'ROHF' => 'Hamby U. S. Army Airfield',
			'RJSI' => 'Hanamaki Airport',
			'RJOA' => 'Hiroshima Airport',
			'RJOF' => 'Hofu Ab',
			'RJAH' => 'Hyakuri Ab',
			'RJAI' => 'Ichikawa',
			'RODE' => 'Iejima Auxiliary Ab',
			'RJDB' => 'Iki Airport',
			'RJTJ' => 'Iruma Ab',
			'ROIG' => 'Ishigakijima',
			'RJOI' => 'Iwakuni Marine Corps Air Station',
			'RJOW' => 'Iwami Airport',
			'RJAW' => 'Iwojima',
			'RJOC' => 'Izumo Airport',
			'RODN' => 'Kadena Ab',
			'RJFK' => 'Kagoshima Airport',
			'RJDK' => 'Kamigoto',
			'RJFY' => 'Kanoya Ab',
			'RJBB' => 'Kansai International Airport',
			'RJAK' => 'Kasumigaura Ab',
			'RJSU' => 'Kasuminome Ab',
			'RJKI' => 'Kikai Island',
			'RJTK' => 'Kisarazu Ab',
			'RJFR' => 'Kitakyushu Airport',
			'RJOK' => 'Kochi Airport',
			'RJNK' => 'Komatsu Ab',
			'RJOP' => 'Komatsujima Ab',
			'RJFT' => 'Kumamoto Airport',
			'ROKJ' => 'Kumejima',
			'RJCS' => 'Kushiro',
			'RJCK' => 'Kushiro Airport',
			'RJAF' => 'Matsumoto Airport',
			'RJST' => 'Matsushima Ab',
			'RJOM' => 'Matsuyama Airport',
			'RJCM' => 'Memambetsu Airport',
			'RJDM' => 'Metabaru Ab',
			'RJOH' => 'Miho Ab',
			'ROMD' => 'Minamidaitojima',
			'RJAM' => 'Minamitorishima',
			'RJSM' => 'Misawa Ab',
			'RJTQ' => 'Miyakejima Airport',
			'ROMY' => 'Miyakojima',
			'RJFM' => 'Miyazaki Airport',
			'RJEB' => 'Mombetsu Airport',
			'RJCY' => 'Muroran',
			'RJFU' => 'Nagasaki Airport',
			'RJNN' => 'Nagoya Airport',
			'ROAH' => 'Naha Airport',
			'RJCN' => 'Nakashibetsu Airport',
			'RJBD' => 'Nankishirahama Airport',
			'RJAA' => 'New Tokyo Inter-National Airport',
			'RJSN' => 'Niigata Airport',
			'RJFN' => 'Nyutabaru Ab',
			'RJCB' => 'Obihiro Airport',
			'RJFO' => 'Oita Airport',
			'RJDO' => 'Ojika Island',
			'RJOB' => 'Okayama Airport',
			'RJNO' => 'Oki Airport',
			'RJKB' => 'Okinoerabu',
			'RJEO' => 'Okushiri Island',
			'RJSO' => 'Ominato Ab',
			'RJOO' => 'Osaka International Airport',
			'RJTO' => 'Oshima Airport',
			'RJOZ' => 'Ozuki Ab',
			'RJCR' => 'Rebun Island',
			'RJER' => 'Rishiri Island',
			'RJSD' => 'Sado Airport',
			'RJCO' => 'Sapporo Ab',
			'RJFW' => 'Sasebo Usn',
			'RJSS' => 'Sendai Airport',
			'RJTL' => 'Shimofusa Ab',
			'RJNY' => 'Shizuhama Ab',
			'RJTC' => 'Tachikawa Ab',
			'RJBT' => 'Tajima',
			'RJOT' => 'Takamatsu Airport',
			'RJFG' => 'Tanegashima Airport',
			'RJTE' => 'Tateyama Ab',
			'RJCT' => 'Tokachi Japanese Ground Self Defense Force',
			'RJKN' => 'Tokunoshima Island',
			'RJOS' => 'Tokushima Ab',
			'RJTD' => 'Tokyo',
			'RJTI' => 'Tokyo Heliport',
			'RJTT' => 'Tokyo International Airport',
			'RJOR' => 'Tottori Airport',
			'RJNT' => 'Toyama Airport',
			'RJFZ' => 'Tsuiki Ab',
			'RJDT' => 'Tsushima Airport',
			'RJTU' => 'Utsunomiya Ab',
			'RJCW' => 'Wakkanai Airport',
			'RJFC' => 'Yakushima',
			'RJSC' => 'Yamagata Airport',
			'RJDC' => 'Yamaguchi Ube Airport',
			'RJOY' => 'Yao Airport',
			'RJTX' => 'Yokosuka Fwf',
			'RJTY' => 'Yokota Ab',
			'ROYN' => 'Yonaguni Airport',
			'RJTR' => 'Zama Airfield'
		);
	}
} // END OF Stations_JP

?>

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
 
class Stations_ID extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_ID()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Indonesia';

		$this->icaos = array(
			'WRKM' => 'Alor/Mali',
			'WAPA' => 'Amahai',
			'WAPP' => 'Ambon/Pattimura',
			'WRRA' => 'Ampenan/Selaparang',
			'WRLL' => 'Balikpapan/Sepinggan',
			'WITT' => 'Banda Aceh/Blangbintang',
			'WIIB' => 'Bandung/Husein',
			'WRBB' => 'Banjarmasin/Syamsuddin Noor',
			'WAAB' => 'Bau-Bau/Beto Ambiri',
			'WIPL' => 'Bengkulu/Padangkemiling',
			'WABB' => 'Biak/Mokmer',
			'WRRB' => 'Bima',
			'WIIL' => 'Cilacap',
			'WIIA' => 'Curug/Budiarto',
			'WRRR' => 'Denpasar/Ngurah-Rai',
			'WABT' => 'Enarotali',
			'WASF' => 'Fak-Fak/Torea',
			'WAMA' => 'Galela/Gamarmalamu',
			'WAMG' => 'Gorontalo/Jalaluddin',
			'WIMB' => 'Gunung Sitoli/Binaka',
			'WIII' => 'Jakarta/Soekarno-Hatta',
			'WIIH' => 'Jakarta Halim Perdanakusuma',
			'WIPA' => 'Jambi/Sultan Taha',
			'WAJJ' => 'Jayapura/Sentani',
			'WIIJ' => 'Jogyakarta/Adisucipto',
			'WASK' => 'Kaimana/Utarom',
			'WIIK' => 'Kalijati',
			'WAAU' => 'Kendari/Woltermon-Ginsidi',
			'WIPH' => 'Kerinci/Depati Parbo',
			'WIOK' => 'Ketapang/Rahadi Usmaman',
			'WABN' => 'Kokonao/Timuka',
			'WRBK' => 'Kotabaru',
			'WRKK' => 'Kupang/El Tari',
			'WAPH' => 'Labuha/Taliabu',
			'WRKL' => 'Larantuka',
			'WITM' => 'Lhokseumawe/Malikussaleh',
			'WRLB' => 'Longbawan/Juvai Semaring',
			'WAMW' => 'Luwuk/Bubung',
			'WIAR' => 'Madiun/Iswahyudi',
			'WIAS' => 'Malang/Abdul Rahkmansaleh',
			'WASR' => 'Manokwari/Rendani',
			'WRKC' => 'Maumere/Wai Oti',
			'WIMM' => 'Medan/Polonia',
			'WAMM' => 'Menado/Dr. Sam Ratulangi',
			'WIAG' => 'Menggala/Astra Ksetra',
			'WAKK' => 'Merauke/Mopah',
			'WITC' => 'Meulaboh/Cut Nyak Dhien',
			'WRBM' => 'Muaratewe/Beringin',
			'WABI' => 'Nabire',
			'WAPR' => 'Namlea',
			'WIMG' => 'Padang/Tabing',
			'WIBB' => 'Pakanbaru/Simpangtiga',
			'WRBP' => 'Palangkaraya/Panarung',
			'WIPP' => 'Palembang/Talangbetutu',
			'WAML' => 'Palu/Mutiara',
			'WRBI' => 'Pangkalan Bun/Iskandar',
			'WIKK' => 'Pangkalpinang/Pangkalpinang',
			'WIOO' => 'Pontianak/Supadio',
			'WAMP' => 'Poso/Kasiguncu',
			'WION' => 'Ranai/Ranai',
			'WIPR' => 'Rengat/Japura',
			'WRKR' => 'Rote/Baa',
			'WIAA' => 'Sabang/Cut Bau',
			'WRKS' => 'Sabu/Tardamu',
			'WRLS' => 'Samarinda/Temindung',
			'WAPN' => 'Sanana',
			'WAJI' => 'Sarmi',
			'WAPI' => 'Saumlaki',
			'WIIS' => 'Semarang/Ahmadyani',
			'WABO' => 'Serui/Yendosa',
			'WIMS' => 'Sibolga/Pinangsori',
			'WIOI' => 'Singkawang Ii',
			'WIKS' => 'Singkep/Dabo',
			'WIOS' => 'Sintang',
			'WASS' => 'Sorong/Jefman',
			'WRRS' => 'Sumbawa Besar/Sumbawa Besar',
			'WRSS' => 'Surabaya',
			'WRSJ' => 'Surabaya/Juanda',
			'WRSP' => 'Surabaya/Perak',
			'WRSQ' => 'Surakarta/Adisumarmo',
			'WAMH' => 'Tahuna',
			'WAKT' => 'Tanah Merah/Tanah Merah',
			'WRLK' => 'Tanjung Redep/Berau',
			'WRLG' => 'Tanjung Selor',
			'WIKD' => 'Tanjungpandan/Buluh Tumbang',
			'WIKN' => 'Tanjungpinang/Kijang',
			'WRLR' => 'Tarakan/Juwata',
			'WIAM' => 'Tasikmalaya/Cibeureum',
			'WIIT' => 'Telukbetung/Beranti',
			'WAMT' => 'Ternate/Babullah',
			'WAMI' => 'Toli-Toli/Lalos',
			'WAAA' => 'Ujung Pandang/Hasanuddin',
			'WRRW' => 'Waingapu/Mau Hau',
			'WAJW' => 'Wamena/Wamena'
		);
	}
} // END OF Stations_ID

?>

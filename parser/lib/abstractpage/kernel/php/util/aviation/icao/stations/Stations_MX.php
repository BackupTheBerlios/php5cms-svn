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
 
class Stations_MX extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_MX()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Mexico';

		$this->icaos = array(
			'MMAA' => 'Acapulco/G. Alvarez',
			'MMMD' => 'Aerop. Internacional Merida, Yuc',
			'MMAN' => 'Aerop. Internacional Monterrey, N. L.',
			'MMAS' => 'Aguascalientes, Ags.',
			'MMBT' => 'Bahias De Huatulco',
			'MMCP' => 'Campeche, Camp.',
			'MMUN' => 'Cancun International Airport',
			'MMCM' => 'Chetumal, Q. Roo',
			'MMCU' => 'Chihuahua International Airport',
			'MMCE' => 'Ciudad Del Carmen',
			'MMCS' => 'Ciudad Juarez International',
			'MMCV' => 'Ciudad Victoria Airport',
			'MMIA' => 'Colima',
			'MMCZ' => 'Cozumel Civ/Mil',
			'MMCB' => 'Cuernavaca, Mor.',
			'MMCL' => 'Culiacan, Sin.',
			'MMLO' => 'Del Bajio/Leon',
			'MMGL' => 'Don Miguel/Guadalaj',
			'MMDO' => 'Durango Airport',
			'MMVR' => 'Gen. Heriberto Jara',
			'MMGM' => 'Guaymas International Airport',
			'MMHO' => 'Hermosillo, Son.',
			'MMZH' => 'Ixtapa-Zihuatanejo',
			'MMLP' => 'La Paz International Airport',
			'MMLT' => 'Loreto, B. C. S.',
			'MMLM' => 'Los Mochis Airport',
			'MMZO' => 'Manzanillo International',
			'MMMA' => 'Matamoros International',
			'MMMZ' => 'Mazatlan/G. Buelna',
			'MMML' => 'Mexicali International Airport',
			'MMMX' => 'Mexico City/Licenci',
			'MMMT' => 'Minatitlan',
			'MMMV' => 'Monclova, Coah.',
			'MMMY' => 'Monterrey/Gen Maria',
			'MMMM' => 'Morelia New',
			'MMNL' => 'Nuevo Laredo International',
			'MMOX' => 'Oaxaca/Xoxocotlan',
			'MMPG' => 'Piedras Negras, Coah.',
			'MMPB' => 'Puebla, Pue.',
			'MMPS' => 'Puerto Escondido',
			'MMPR' => 'Puerto Vallarta/Lic',
			'MMQT' => 'Queretaro, Qro.',
			'MMRX' => 'Reynosa International Airport',
			'MMIO' => 'Saltillo, Coah.',
			'MMSD' => 'San Jose Del Cabo',
			'MMSP' => 'San Luis Potosi, S. L. P.',
			'MMCN' => 'Santa Rosalia, B. C. S.',
			'MMTM' => 'Tampico/Gen Fj Mina',
			'MMTP' => 'Tapachula',
			'MMEP' => 'Tepic, Nay.',
			'MMTJ' => 'Tijuana International Airport',
			'MMTO' => 'Toluca/Jose Maria',
			'MMTC' => 'Torreon, Coah.',
			'MMTL' => 'Tulancingo',
			'MMTG' => 'Tuxtla Gutierrez, Chis.',
			'MMPN' => 'Uruapan/Gen Rayon',
			'MMVA' => 'Villahermosa',
			'MMZC' => 'Zacatecas Airport'
		);
	}
} // END OF Stations_MX

?>

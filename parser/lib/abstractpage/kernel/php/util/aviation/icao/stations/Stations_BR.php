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
 
class Stations_BR extends Stations
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Stations_BR()
	{
		$this->_populate();
	}


	// private methods

	/**
	 * @access private
	 */
	function _populate()
	{
		$this->country = 'Brazil';

		$this->icaos = array(
			'SBAF' => 'Afonsos Aeroporto',
			'SBAT' => 'Alta Floresta Aeroporto',
			'SBHT' => 'Altamira',
			'SBAN' => 'Anapolis Braz-Afb',
			'SBAR' => 'Aracaju Aeroporto',
			'SBBG' => 'Bage Aeroporto',
			'SBBQ' => 'Barbacena',
			'SWBC' => 'Barcelos',
			'SBBW' => 'Barra Do Garcas',
			'SBBU' => 'Bauru',
			'SBBE' => 'Belem Aeroporto',
			'SBCF' => 'Belo Horizonte',
			'SBBH' => 'Belo Horizonte Aeroporto',
			'SBBC' => 'Benjamin Constant',
			'SBBV' => 'Boa Vista Aeropor-To',
			'SBLP' => 'Bom Jesus Da Lapa',
			'SBBR' => 'Brasilia Aeroporto',
			'SBKG' => 'Campina Grande',
			'SBKP' => 'Campinas Aeroporto',
			'SBCG' => 'Campo Grande Aeroporto',
			'SBCP' => 'Campos',
			'SBCV' => 'Caravelas Aeropor-To',
			'SBAA' => 'Conceicao Do Araguaia',
			'SBCR' => 'Corumba',
			'SBCZ' => 'Cruzeiro Do Sul',
			'SBCY' => 'Cuiaba Aeroporto',
			'SBBI' => 'Curitiba',
			'SBCT' => 'Curitiba Aeroporto',
			'SBEG' => 'Eduardo Gomes International',
			'SBFN' => 'Fernando De Noronha',
			'SBFL' => 'Florianopolis Aeroporto',
			'SBFZ' => 'Fortaleza Aeropor-To',
			'SBFI' => 'Foz Do Iguacu Aeroporto',
			'SBGL' => 'Galeao',
			'SBGA' => 'Gama',
			'SBGO' => 'Goiania Aeroporto',
			'SBGW' => 'Guaratingueta',
			'SBGR' => 'Guarulhos Civ/Mil',
			'SBYA' => 'Iauarete',
			'SBIL' => 'Ilheus Aeroporto',
			'SBIZ' => 'Imperatriz',
			'SBIH' => 'Itaituba',
			'SBEK' => 'Jacareacanga',
			'SBJP' => 'Joao Pessoa',
			'SBJF' => 'Juiz De Fora',
			'SBLO' => 'Londrina Aeroporto',
			'SBME' => 'Macae',
			'SBMQ' => 'Macapa',
			'SBMO' => 'Maceio Aeroporto',
			'SBMN' => 'Manaus Aeroporto',
			'SBMY' => 'Manicore',
			'SBMA' => 'Maraba',
			'SBCI' => 'Maranhao/Carolina Airport',
			'SBMG' => 'Maringa',
			'SBMT' => 'Marte Civ/Mil',
			'SBMS' => 'Mocoro/17 Rosado',
			'SBMK' => 'Montes Claros',
			'SBNT' => 'Natal Aeroporto',
			'SBOI' => 'Oiapoque',
			'SBPG' => 'Paranagua',
			'SBPB' => 'Parnaiba Aeroporto',
			'SBPF' => 'Passo Fundo',
			'SBUF' => 'Paulo Afonso',
			'SBPK' => 'Pelotas',
			'SBPL' => 'Petrolina Aeropor-To',
			'SBYS' => 'Pirassununga',
			'SBPC' => 'Pocos De Caldas',
			'SBPP' => 'Ponta Pora Aeropor-To',
			'SBCO' => 'Porto Alegre',
			'SBPA' => 'Porto Alegre Aero-Porto',
			'SBPN' => 'Porto Nacional Aeroporto',
			'SBPV' => 'Porto Velho Aeroporto',
			'SBDN' => 'Presidente Prudente',
			'SBRF' => 'Recife Aeroporto',
			'SBRS' => 'Resende',
			'SBJR' => 'Rio/Jacarepagua',
			'SBRB' => 'Rio Branco',
			'SBRJ' => 'Rio De Janeiro Aeroporto',
			'SBES' => 'S. P. Aldeia Aerodrome',
			'SBSV' => 'Salvador Aeroporto',
			'SBSC' => 'Santa Cruz Aeropor-To',
			'SBSM' => 'Santa Maria Aero-Porto',
			'SBSN' => 'Santarem-Aeroporto',
			'SBST' => 'Santos Aeroporto',
			'SBSA' => 'Sao Carlos',
			'SBUA' => 'Sao Gabriel Da Cachoeira',
			'SBSJ' => 'Sao Jose Dos Campo',
			'SBSL' => 'Sao Luiz Aeroporto',
			'SBSP' => 'Sao Paulo Aeropor-To',
			'SBTT' => 'Tabatinga',
			'SBTK' => 'Tarauaca',
			'SBTF' => 'Tefe',
			'SBTE' => 'Teresina Aeroporto',
			'SBTU' => 'Tucurui',
			'SBUR' => 'Uberaba',
			'SBUG' => 'Uruguaiana Aeroporto',
			'SBVH' => 'Vilhena Aeroporto',
			'SBVT' => 'Vitoria Aeroporto',
			'SBQV' => 'Vitoria Da Conquista',
			'SBXV' => 'Xavantina'
		);
	}
} // END OF Stations_BR

?>

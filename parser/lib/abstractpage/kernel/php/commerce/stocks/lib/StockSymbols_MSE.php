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


using( 'commerce.stocks.lib.StockSymbols' );


/**
 * @package commerce_stocks_lib
 */
 
class StockSymbols_MSE extends StockSymbols
{
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function StockSymbols_MSE( $options = array() )
	{
		$this->StockSymbols( $options );
		
		$this->_populate();
	}
	
	
	// private methods
	
	/**
	 * @access private
	 */
	function _populate()
	{
		$this->_stock_ex = "Madrid Stock Exchange";
		
		$this->_symbols = array(
			"ABG"	=> "Abengoa",
			"ACE"	=> "Acesa",
			"ACR"	=> "Aceralia",
			"ACS"	=> "Acs Cons Y Serv",
			"ACX"	=> "Acerinox",
			"ADZ"	=> "Adolfo Dominguez",
			"AGS"	=> "Aguas Barna",
			"ALB"	=> "Corp Fin Alba",
			"ALD"	=> "Aldeasa",
			"ALT"	=> "Altadis",
			"AMP"	=> "Amper",
			"AMS"	=> "Amadeus Gtd",
			"ANA"	=> "Acciona",
			"AND"	=> "Bk Andalucia",
			"APLI"	=> "Apli",
			"ARA"	=> "Aragonesas",
			"ASA"	=> "Algodonera",
			"ATL"	=> "Bk Atlantico",
			"AUM"	=> "Aurea Conces Inf",
			"AZK"	=> "Azkoyen",
			"BAM"	=> "Bami",
			"BBVA"	=> "Bbva",
			"BDL"	=> "Baron De Ley",
			"BES"	=> "Industrias Besos",
			"BIO"	=> "Puleva Biotech",
			"BKT"	=> "Bankinter",
			"BMA"	=> "Befesa",
			"BTO"	=> "Banesto",
			"BVA"	=> "Bk Valencia",
			"BYB"	=> "Bodegas Bebidas",
			"CAF"	=> "Cons Aux Ferr",
			"CAN"	=> "Cantabrico",
			"CAS"	=> "Bk Castilla",
			"CBL"	=> "Bk Cdto Balear",
			"CEP"	=> "Cepsa",
			"CGI"	=> "Gral Inversiones",
			"CIB"	=> "Tecnocom",
			"CIEA"	=> "Cie Automotive",
			"COL"	=> "Inmob Colonial",
			"CPF"	=> "Cons Campofrio",
			"CPL"	=> "Cem Portland",
			"CRF"	=> "Carrefour",
			"CTF"	=> "Cortefiel",
			"CUN"	=> "Vinicola Del Nor",
			"DGI"	=> "Dogi International Fabrics",
			"DIN"	=> "Dinamia",
			"DRC"	=> "Grupo Dragados",
			"ECR"	=> "Ercros",
			"ELE"	=> "Endesa",
			"ENA"	=> "Enaco",
			"ENAG"	=> "Enagas",
			"ENC"	=> "Empresarial Ence",
			"ENOR"	=> "Elecnor",
			"ESF"	=> "Banco Esfinge",
			"EUR"	=> "Europistas",
			"EVA"	=> "Ebro Puleva",
			"FAE"	=> "Faes Farma",
			"FCC"	=> "Fomento De Const",
			"FER"	=> "Grupo Ferrovial",
			"FIL"	=> "Filo",
			"FTX"	=> "Fastibex",
			"FUN"	=> "Funespana",
			"GAL"	=> "Bk Galicia",
			"GAM"	=> "Gamesa",
			"GAS"	=> "Gas Natural",
			"GCO"	=> "Cat Occidente",
			"GSW"	=> "Globl Steel Wire",
			"GUI"	=> "Bk Guipuzcoano",
			"HKN"	=> "Heineken Espana",
			"IBE"	=> "Iberdrola",
			"IBG"	=> "Iberpapel",
			"IBLA"	=> "Iberia Lineas Aereas",
			"IBP"	=> "Iberpistas",
			"IDO"	=> "Indo Intl",
			"IDR"	=> "Indra Sistemas",
			"ITX"	=> "Inditex",
			"JAZ"	=> "Jazztel",
			"KOI"	=> "Koipe",
			"LGT"	=> "Lingotes Esp",
			"LOG"	=> "Logista",
			"MAP"	=> "Mapfre",
			"MCM"	=> "Miquel Y Costas",
			"MDF"	=> "Duro Felguera",
			"MLX"	=> "Mecalux Sa",
			"MOC"	=> "Grupo Inmocaral",
			"MPV"	=> "Mapfre Vida",
			"MT4"	=> "Meta4 Nv",
			"MVC"	=> "Metrovacesa",
			"NAT"	=> "Natra",
			"NEA"	=> "Nicolas Correa",
			"NHH"	=> "Nh Hoteles",
			"NMQ"	=> "Montana Quijano",
			"OHL"	=> "Obr Huarte Lain",
			"OMS"	=> "Omsa Aliment.",
			"ONO"	=> "Cableuropa",
			"PAC"	=> "Pap Y Cart Euro",
			"PAS"	=> "Bk Pastor",
			"PAT"	=> "Fed Paternina",
			"POP"	=> "Bk Popular",
			"PQR"	=> "Parques Reunidos",
			"PRS"	=> "Prisa",
			"PSG"	=> "Prosegur",
			"PVA"	=> "Pescanova",
			"REC"	=> "Recoletos",
			"REE"	=> "Red Electrica",
			"REP"	=> "Repsol Ypf",
			"RIO"	=> "Bodegas Riojanas",
			"SAB"	=> "Bco De Sabadell",
			"SAN"	=> "Bsch",
			"SED"	=> "Seda Barcelona",
			"SGC"	=> "Sogecable",
			"SNC"	=> "Sniace",
			"SOL"	=> "Sol Melia",
			"SOS"	=> "Sos Cuetara",
			"SPSL"	=> "Svc Point Sltns",
			"STG"	=> "Sotogrande",
			"TAZ"	=> "Azkar",
			"TEF"	=> "Telefonica Sa",
			"TEM"	=> "Telefonica Movil",
			"TFI"	=> "Tableros Fibras",
			"TPI"	=> "Telef Pub & Info",
			"TPZ"	=> "Telepizza",
			"TRR"	=> "Terra Networks",
			"TST"	=> "Testa Inmuebles",
			"TUB"	=> "Tubacex",
			"TUD"	=> "Tudor",
			"UBS"	=> "Urbas",
			"UND"	=> "Uniland",
			"UNF"	=> "U Fenosa",
			"UPL"	=> "Unipapel",
			"URA"	=> "Uralita",
			"URB"	=> "Inmob Urbis",
			"VAL"	=> "Vallehermoso",
			"VAS"	=> "Bk De Vasconia",
			"VDR"	=> "Valderrivas",
			"VID"	=> "Vidrala",
			"VIS"	=> "Viscofan",
			"ZEL"	=> "Zeltia",
			"ZNC"	=> "Espanola De Zinc",
			"ZOT"	=> "Zardoya Otis",
			"ZRG"	=> "Bk Zaragozano"
		);
	}
} // END OF StockSymbols_MSE

?>

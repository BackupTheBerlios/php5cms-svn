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


using( 'locale.map.lib.LanguageMap' );


/**
 * @package locale_map_lib
 */
 
class LanguageMap_es_Latin1Bible extends LanguageMap
{
	/**
	 * Constructor
	 */
	function LanguageMap_es_Latin1Bible()
	{
		$this->language = "es";
		$this->charset  = "latin1_bible";
		
		$this->_populate();
	}
	
	
	// private methods
	
	function _populate()
	{
		$this->map = array(
			"e_" => 113809,
			"s_" => 101838,
			"a_" => 89253,
			"_d" => 77480,
			"o_" => 72084,
			"os" => 70654,
			"de" => 69779,
			"_e" => 66912,
			"_s" => 65796,
			",_" => 65312,
			"_de" => 61853,
			"_l" => 60314,
			"en" => 58656,
			"es" => 57898,
			"n_" => 51893,
			"er" => 50165,
			"ue" => 48842,
			"_a" => 48537,
			"ia_d" => 48107,
			"vosotr" => 47261,
			"os_" => 47246,
			"de_" => 44463,
			"l_" => 44433,
			"_de_" => 44116,
			"_c" => 43974,
			"ibr" => 41978,
			"ue_d" => 40897,
			"_y" => 40018,
			"ra" => 39264,
			"ar" => 38541,
			"as" => 38098,
			"_y_" => 36349,
			"re" => 35757,
			"_t" => 35726,
			"qu" => 34846,
			"_h" => 34570,
			"esias" => 34441,
			"do" => 32935,
			"el_" => 32931,
			"_m" => 31880,
			"on" => 31220,
			"an" => 29959,
			"or" => 28820,
			"que" => 28621,
			"ue_" => 28160,
			"nt" => 27592,
			"al" => 27088,
			"ie" => 26861,
			"Ciert" => 26482,
			"to" => 26343,
			"ro" => 26213,
			"_qu" => 25690,
			"co" => 25611,
			"que_" => 25429,
			"_q" => 24990,
			"_el" => 24794,
			"mi_" => 24525,
			",_y" => 24460,
			"st" => 24420,
			"te" => 23680,
			"ad" => 23593,
			"_en" => 23552,
			"ta" => 23541,
			",_y_" => 22867,
			"no" => 22764,
			"tr" => 22764,
			"_que_" => 22692,
			"_que" => 22272,
			"los" => 22268,
			"r_" => 22208,
			"as_" => 21982,
			"_�l" => 21882,
			"_el_" => 21845,
			"se" => 21822,
			"_lo" => 21800,
			"la_" => 21241,
			"_co" => 21227,
			"ci" => 20527,
			"on_" => 20193,
			"_a_" => 19926,
			"_v" => 19605,
			"da" => 19483,
			"en_" => 19303,
			"_la_" => 19219,
			"s_d" => 19090,
			"e_l" => 19000,
			"e_e" => 18790,
			"s," => 18607,
			"_los_" => 18590,
			"_los" => 18292,
			"di" => 18287,
			"ros" => 18252,
			"nd" => 18021,
			",_ven" => 17821,
			"ca" => 17764,
			"s,_" => 17579,
			"sa" => 17338,
			"o_e" => 17226,
			"ab" => 17114,
			"a_d" => 17004,
			"minuy" => 16753,
			"pa" => 16707,
			"_n" => 16595,
			"_su" => 16575,
			"iud" => 16573,
			"s_de_" => 16542,
			"_ha" => 16413,
			"ha" => 16390,
			"_en_" => 16360,
			"ra_" => 16244,
			"le" => 16231,
			"s_de" => 15788,
			"_es" => 15743,
			"ce" => 15573,
			"do_" => 15472,
			"u_part" => 15439,
			"po" => 15327,
			"mi" => 14811,
			"_o" => 14742,
			"_se" => 14711,
			"est" => 14262,
			"ti" => 14065,
			"Y_" => 14034,
			"so" => 13914,
			"o:_" => 13781,
			"is" => 13700,
			"gu" => 13652,
			"o_d" => 13641,
			"con" => 13544,
			"_al" => 13440,
			"mo" => 13349,
			"o," => 13335,
			"ent" => 13228,
			"am" => 13147,
			"br" => 13132,
			"se_" => 13120,
			"ien" => 13063,
			"no_" => 13005,
			"_di" => 12894,
			"r�" => 12808,
			"_con" => 12801,
			"_Si_al" => 12723,
			"u_" => 12712,
			"_g" => 12680,
			"in" => 12640,
			"ac" => 12636,
			"ri" => 12593,
			"_pa" => 12423,
			"o_de" => 12407,
			"and" => 12393,
			"_de_l" => 12303,
			"de_l" => 12156,
			"a_l" => 12017,
			"_j" => 11980,
			"a_con_" => 11978,
			"us" => 11976,
			"_po" => 11958,
			"ll" => 11956,
			"_S" => 11924,
			"tod" => 11834,
			"rac" => 11797,
			"n_fuer" => 11778,
			"un" => 11769,
			"io" => 11734,
			"ell" => 11730,
			"ier" => 11711,
			"_to" => 11678,
			"te_" => 11628,
			"_Jes" => 11443,
			"vi" => 11405,
			"e_d" => 11306,
			"rq" => 11249,
			"del" => 11165,
			"a_s" => 11160,
			"�_s" => 11146,
			"s_pala" => 11112,
			"_Es" => 11060,
			"sc" => 11052,
			"_r" => 11049,
			"_del_" => 10890,
			"od" => 10887,
			"ij" => 10879,
			"_f" => 10851,
			"ec" => 10844,
			"s_e" => 10809,
			"n_e" => 10764,
			"or_" => 10740,
			"sus_" => 10718,
			"ado" => 10659,
			"_no" => 10658,
			"�a" => 10639,
			"_J" => 10633,
			"ir" => 10574,
			"_ma" => 10538,
			"._Y" => 10511,
			"a_de" => 10438,
			"_cu" => 10375,
			"res" => 10289,
			"todos_" => 10288,
			"y_de_l" => 10242,
			"a," => 10215,
			"_b" => 10205,
			"ijo" => 10068,
			"a_c" => 10059,
			"e_n" => 10012,
			"ic" => 9936,
			"mient" => 9898,
			"s_c" => 9825,
			"ba" => 9801,
			"_ca" => 9740,
			"os_d" => 9730,
			"que_h" => 9711,
			"re_" => 9675,
			"si" => 9657,
			"re_d" => 9620,
			"pu" => 9596,
			"s_po" => 9584,
			"a,_" => 9567,
			"hi" => 9565,
			"os," => 9512,
			"por" => 9477,
			"lo_" => 9446,
			"e_s" => 9440,
			"a_a" => 9388,
			"�_" => 9388,
			"na" => 9359,
			"em" => 9359,
			"tie" => 9345,
			"uest" => 9313,
			"o_de_" => 9308,
			"e_a" => 9301,
			"go" => 9230,
			"ne" => 9225,
			"_tod" => 9160,
			"_M" => 9096,
			"_escri" => 9095,
			"o_s" => 9081,
			"erca." => 9060,
			"ia" => 9048,
			"ant" => 9038,
			"ndo" => 9008,
			"inuy" => 8997,
			"gra" => 8965,
			"_su_" => 8948,
			"_cont" => 8875,
			"su_" => 8856,
			"i_" => 8849,
			"n," => 8849,
			"e_la" => 8835,
			"s." => 8790,
			"_re" => 8787,
			"s,_y" => 8727,
			"nte" => 8725,
			"ua" => 8703,
			"adier" => 8693,
			"_he" => 8660,
			"ia_de_" => 8649,
			"a_que_" => 8606,
			"_pro" => 8567,
			"id," => 8478,
			"us_" => 8458,
			"_est" => 8452,
			"ero" => 8452,
			"par" => 8380,
			"bi" => 8373,
			"s,_y_" => 8371,
			":_" => 8346,
			"sta" => 8309,
			"_tu" => 8291,
			"al_" => 8204,
			"as_pre" => 8195,
			"e_de" => 8148,
			"s_p" => 8148,
			"ara" => 8131,
			"s_s" => 8110,
			"li" => 8110,
			"_hagas" => 8110,
			"bre" => 8103,
			"_mi" => 8103,
			"dos" => 8090,
			"e_h" => 8032,
			"arte_" => 7988,
			"_te" => 7984,
			"OR" => 7972,
			"e_un" => 7905,
			"nto" => 7904,
			"_la_r" => 7885,
			"quiere" => 7873,
			"bras" => 7864,
			"_ti" => 7821,
			"r_a_c" => 7808,
			"o_p" => 7803,
			"ve" => 7801,
			"s,_di" => 7794,
			"y_el_q" => 7762,
			"a_de_" => 7761,
			"ob" => 7740,
			"n." => 7733,
			"a_e" => 7721,
			"o_a" => 7720,
			"bir�_t" => 7715,
			"viere_" => 7701,
			"_ama_y" => 7699,
			"tro" => 7664,
			"�n" => 7641,
			"ni" => 7601,
			"a_t" => 7555,
			"ilo;_" => 7515,
			"en_e" => 7498,
			"_D" => 7494,
			"ch" => 7483,
			"ente_l" => 7474,
			"_las" => 7472,
			"�n_" => 7456,
			"�a_" => 7450,
			"s_a" => 7436,
			"otenci" => 7405,
			"s_q" => 7372,
			"las" => 7368,
			"ot" => 7354,
			"_Cr" => 7349,
			"�_" => 7346,
			"aga" => 7337,
			"este_l" => 7327,
			"at" => 7319,
			"palab" => 7311,
			"_las_" => 7300,
			"pe" => 7280,
			"ran_c" => 7231,
			"rt" => 7230,
			"dr" => 7213,
			"e_t" => 7196,
			"para" => 7146,
			"vo" => 7144,
			"SE�OR" => 7144,
			"para,_" => 7133,
			"_por_" => 7112,
			"o_y_" => 7112,
			"_so" => 7099,
			"_no_" => 7098,
			"idas," => 7086,
			"cua" => 7076,
			"pr" => 7071,
			";_y" => 7063,
			"_al_" => 7023,
			"_sa" => 7019,
			"hij" => 7015,
			"gr" => 7005,
			"cero,_" => 6955,
			"_Di" => 6947,
			"_vi" => 6928,
			"ren" => 6918,
			"os." => 6903,
			"to_" => 6893,
			"tas" => 6892,
			"_para" => 6870,
			"_dij" => 6866,
			"que_e" => 6862,
			"y_l" => 6821,
			"n_el" => 6805,
			"im" => 6788,
			"o." => 6764,
			"e_la_" => 6764,
			"sus" => 6736,
			"r�_" => 6733,
			"cada_" => 6719,
			"el_S" => 6704,
			"_fu" => 6699,
			"ol" => 6640,
			"e_a_" => 6613,
			"n_cer" => 6611,
			"las_" => 6593,
			"�n" => 6590,
			"os_de_" => 6556,
			"_Je" => 6551,
			"to_s" => 6538,
			"_y_cu" => 6534,
			"it" => 6531,
			"ui" => 6527,
			"_Crist" => 6522,
			"tura" => 6522,
			"e_los_" => 6511,
			"e_se" => 6488,
			"a_que" => 6484,
			"an_" => 6455,
			"tar" => 6396,
			"s,_cua" => 6390,
			"_con_" => 6388,
			"odo" => 6386,
			"aro" => 6377,
			"n,_" => 6376,
			"ta_" => 6368,
			"_sus" => 6343,
			"as_nun" => 6343,
			"_mu" => 6342,
			"eron" => 6330,
			"_Ju" => 6319,
			"_A" => 6312,
			"Crist" => 6297,
			"ra_q" => 6287,
			"_agua_" => 6286,
			"uyere_" => 6282,
			"e_nues" => 6263,
			"he" => 6257,
			"todo" => 6253,
			"dad_n" => 6241,
			";_porq" => 6234,
			"_Dios" => 6231,
			"odos" => 6229,
			"pue" => 6211,
			"ar�" => 6199,
			"inci" => 6198,
			",_p" => 6193,
			"str" => 6181,
			"�l" => 6177,
			"s_t" => 6155,
			"_pr" => 6133,
			"o_di" => 6126,
			"man" => 6108,
			"n_s" => 6093,
			"_Jes�" => 6080,
			"s_qu" => 6070,
			"con_" => 6042,
			"tres_" => 6041,
			"_sus_" => 6037,
			"a�adie" => 6015,
			"des" => 6005,
			"_pu" => 5990,
			"n_a" => 5985,
			"os_hom" => 5982,
			"ara_" => 5974,
			"Por" => 5972,
			"_el_qu" => 5967,
			"ame" => 5964,
			"Cris" => 5947,
			"ios" => 5945,
			"Di" => 5914,
			"Dios" => 5890,
			"a." => 5886,
			"icida" => 5883,
			"os_h" => 5878,
			"com" => 5872,
			"s_el_" => 5870,
			"_a_l" => 5868,
			"y_c" => 5864,
			"da_" => 5855,
			"o_el_" => 5850,
			"er�" => 5849,
			"nsar" => 5839,
			"r_l" => 5839,
			"_y_ha" => 5816,
			"o_Je" => 5810,
			"_en_el" => 5807,
			",_que" => 5783,
			"a_el" => 5775,
			"e," => 5766,
			"_de_lo" => 5742,
			"s_lo" => 5722,
			"la_vi" => 5714,
			"ue_ha_" => 5687,
			"_hijo" => 5676,
			"s_m" => 5666,
			"todos" => 5661,
			"gun" => 5658,
			"ue_la_" => 5646,
			"ome_de" => 5645,
			"d_de_" => 5629,
			"a_y" => 5617,
			"osa_" => 5615,
			"s_que" => 5608,
			"l_r" => 5598,
			"dad" => 5590,
			"os_po" => 5587,
			"_pue" => 5586,
			"cr" => 5584,
			"ues" => 5582,
			"imero_" => 5571,
			"ea_" => 5564,
			"_cua" => 5561,
			"tu_" => 5552,
			"o_en" => 5548,
			"cie" => 5547,
			"_guard" => 5544,
			"qui" => 5523,
			"_pre" => 5521,
			"n_los_" => 5520,
			"ur" => 5508,
			"ros_" => 5506,
			"a_qu" => 5501,
			"mo_" => 5501,
			"e,_" => 5501,
			"de_los" => 5498,
			"_todos" => 5496,
			"David" => 5496,
			"l_d" => 5484,
			"en_la" => 5472,
			"nmigo" => 5466,
			"r_a_ca" => 5464,
			"ube_" => 5455,
			"dos_" => 5448
		);
	}
} // END OF LanguageMap_es_Latin1Bible

?>

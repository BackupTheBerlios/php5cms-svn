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
 
class LanguageMap_pt_Latin1 extends LanguageMap
{
	/**
	 * Constructor
	 */
	function LanguageMap_pt_Latin1()
	{
		$this->language = "pt";
		$this->charset  = "latin1";
		
		$this->_populate();
	}
	
	
	// private methods
	
	function _populate()
	{
		$this->map = array(
			"a_" => 171557,
			"e_" => 165238,
			"o_" => 164087,
			"s_" => 114837,
			"_d" => 109877,
			",_" => 97451,
			"_s" => 95800,
			"_a" => 92476,
			"a_p" => 81043,
			"_e" => 80949,
			"de" => 80234,
			"ra" => 77662,
			"_p" => 74076,
			"os" => 70034,
			"_c" => 68078,
			"do" => 67936,
			"as" => 65577,
			"en" => 65212,
			"ar" => 63310,
			"m_" => 62008,
			"er" => 57896,
			"_de" => 54623,
			"re" => 51780,
			"_o" => 51560,
			"nt" => 51518,
			"co" => 49904,
			"da" => 49416,
			"qu" => 49094,
			"se" => 49054,
			"te" => 47729,
			"or" => 46744,
			"_m" => 45294,
			"ue" => 44890,
			"em" => 44795,
			"an" => 44471,
			"de_" => 44413,
			"uma_" => 43197,
			"os_" => 43029,
			"_dinhe" => 42588,
			"me" => 42196,
			"to" => 41854,
			"_q" => 41466,
			"o_esfo" => 40393,
			"que" => 40087,
			"ia" => 39690,
			"_n" => 38914,
			"el" => 38189,
			"_qu" => 38058,
			"in" => 37975,
			"ad" => 37960,
			"_t" => 37606,
			"do_" => 37026,
			"_co" => 36670,
			"r_" => 36449,
			"_de_" => 36012,
			"_que" => 34294,
			"om" => 33820,
			"nd" => 33709,
			"_a_" => 33442,
			"._" => 32726,
			"am" => 32715,
			"ri" => 32701,
			",_os_d" => 32315,
			"�o" => 31873,
			"ue_" => 31786,
			"ro" => 31768,
			"a_d" => 31514,
			"al" => 31443,
			"r_q" => 31191,
			"is" => 30215,
			"_o_" => 30021,
			"tr" => 29501,
			"_se" => 29373,
			"pa" => 29294,
			"_f" => 29119,
			"um" => 29086,
			"que_" => 28237,
			"la" => 28134,
			"ent" => 28071,
			"_e_" => 27437,
			"sa" => 27423,
			"o,_" => 27268,
			"ei" => 27108,
			"_que_" => 26942,
			"o," => 26532,
			"on" => 26499,
			"o_d" => 26288,
			"ra_" => 26101,
			"no�" => 26018,
			"Ate" => 25530,
			"�o_" => 25419,
			"mp" => 25330,
			"a," => 25207,
			"esespe" => 24891,
			"_at" => 24863,
			"_g" => 24783,
			"va" => 24590,
			"_dois_" => 24217,
			"pe" => 23803,
			"da_" => 23752,
			"a,_" => 23637,
			"_u" => 23625,
			"u_" => 23511,
			"_Ega_u" => 23480,
			"vem_u" => 23400,
			"a_v" => 23314,
			"mo" => 23302,
			"na" => 23235,
			"_�am" => 23211,
			"ve" => 23159,
			"_l" => 23022,
			"Ega_um" => 22631,
			"lh" => 22490,
			"_r" => 22443,
			"ir" => 22364,
			"so" => 22040,
			"com" => 21502,
			"o_e" => 21346,
			"s," => 21251,
			"ras" => 21178,
			"em_" => 21166,
			"ho" => 21108,
			"ha" => 21071,
			"de_San" => 21048,
			"_do" => 20876,
			"_ef" => 20506,
			"nte" => 20333,
			"E_foi" => 20293,
			"le" => 20258,
			"e_a" => 20184,
			"a_a" => 20085,
			"_da" => 19922,
			"s_d" => 19884,
			"_com" => 19798,
			"fu" => 19744,
			"_po" => 19711,
			"_pa" => 19684,
			"ndo" => 19508,
			"rom" => 19483,
			"gu" => 19466,
			"ndo_" => 19319,
			"ng" => 19259,
			"_do_" => 19251,
			"_um" => 19198,
			"ant" => 19156,
			"_ma" => 18784,
			"a_e" => 18637,
			"a_c" => 18557,
			"r_o_�a" => 18522,
			"_es" => 18393,
			"ia_" => 18277,
			"ci" => 18111,
			"ge" => 18054,
			"id" => 17942,
			"ua" => 17914,
			"ai" => 17899,
			"o_ap" => 17558,
			"te_" => 17475,
			"io" => 17437,
			"im" => 17414,
			"aze" => 17410,
			"to_" => 17194,
			"ava" => 16954,
			"luar_q" => 16917,
			"e_d" => 16864,
			"nc" => 16713,
			"li" => 16631,
			"_ca" => 16559,
			"he" => 16555,
			"o_a" => 16525,
			"_pe" => 16497,
			"ara" => 16383,
			"ec" => 16349,
			"o_p" => 16347,
			"ricano" => 16271,
			"ado" => 16240,
			"lo" => 15902,
			"pr" => 15898,
			"iu._E" => 15860,
			"mi" => 15780,
			"erad" => 15778,
			"nda_" => 15641,
			"e_e" => 15522,
			"est" => 15499,
			"_me" => 15447,
			"o_c" => 15441,
			"at" => 15430,
			"o_s" => 15415,
			"ame" => 15384,
			"r_h" => 15303,
			"es_" => 15287,
			"gra" => 15077,
			"e," => 15060,
			"i_" => 14950,
			"ic" => 14920,
			"_E" => 14915,
			"con" => 14535,
			"men" => 14477,
			"r_um" => 14388,
			"ia_da" => 14317,
			"a." => 14311,
			"ta_" => 14297,
			"ur" => 14277,
			"um_" => 14152,
			"_di" => 14130,
			",_e" => 14120,
			"e_p" => 14006,
			"ade" => 13980,
			"..." => 13933,
			"us" => 13890,
			"sp" => 13716,
			"ell" => 13692,
			"ga" => 13672,
			"FIM" => 13652,
			"e,_" => 13541,
			"su" => 13494,
			"_no" => 13426,
			"pazes_" => 13388,
			"amer" => 13363,
			"longe" => 13223,
			"gem" => 13199,
			"si" => 13162,
			"o." => 12991,
			"ass" => 12875,
			"rr" => 12870,
			"ente" => 12867,
			"ze" => 12861,
			"a_u" => 12806,
			"ar_que" => 12770,
			"_da_" => 12734,
			"_as" => 12684,
			"_perna" => 12682,
			"e_s" => 12656,
			"tu" => 12627,
			"_A" => 12534,
			"un" => 12467,
			"or_" => 12432,
			"ar_" => 12420,
			"_em" => 12419,
			"sc" => 12413,
			"ada" => 12394,
			"uma" => 12366,
			"ram_a" => 12363,
			",_a" => 12293,
			"am_o" => 12224,
			"go" => 12193,
			"sta" => 12142,
			"a_prim" => 12100,
			"_par" => 12078,
			"_De_" => 12047,
			"ns" => 12045,
			"m_q" => 12022,
			"oi" => 12006,
			"perna" => 12001,
			"rad" => 11981,
			"o_de" => 11961,
			"nto" => 11879,
			"s_de" => 11841,
			"a_g" => 11733,
			"_um_" => 11729,
			"dos" => 11724,
			"com_" => 11709,
			"_te" => 11695,
			"lhe_" => 11642,
			"vo" => 11611,
			"ndo_a" => 11603,
			"od" => 11593,
			"_pr" => 11553,
			"nte_" => 11535,
			"_lu" => 11504,
			"a_que_" => 11484,
			"ment" => 11447,
			"tra" => 11434,
			"ob" => 11418,
			"m_a_" => 11397,
			"_al" => 11370,
			"gr" => 11310,
			"era" => 11290,
			"ap" => 11185,
			"s_e" => 11171,
			"a_de" => 11130,
			"�a,_o" => 11130,
			"a�" => 11130,
			"_n�o" => 11114,
			"am_" => 11087,
			",_c" => 11075,
			"des" => 10986,
			"amp" => 10944,
			"ess" => 10853,
			"gem_" => 10812,
			"is_" => 10771,
			"_su" => 10757,
			"Se_" => 10727,
			"ou_" => 10718,
			"a_do_" => 10691,
			"_na" => 10668,
			"amen" => 10586,
			"por" => 10501,
			"De" => 10491,
			"fim,_" => 10482,
			"eu_" => 10406,
			"ria" => 10394,
			"s,_co" => 10390,
			"_os_" => 10375,
			"ois" => 10372,
			"ran" => 10359,
			"_os" => 10347,
			"_char" => 10337,
			"fo" => 10334,
			"s_p" => 10332,
			"e_de" => 10308,
			"fi" => 10274,
			"s_a" => 10273,
			"o,_par" => 10256,
			"a_f" => 10243,
			"fa" => 10219,
			"o_m" => 10217,
			"_em_" => 10201,
			"sse" => 10131,
			"antos_" => 10118,
			"ista" => 10114,
			"nha" => 10079,
			"er_" => 10077,
			"ara_" => 10075,
			"nha_" => 10071,
			"ob_" => 10069,
			"ubia._" => 10045,
			"_C" => 10027,
			"ll" => 10023,
			"_so" => 9992,
			"do_�a" => 9984,
			"nte._E" => 9967,
			"_h" => 9932,
			"m_qu" => 9912,
			"r_no_" => 9896,
			"va_" => 9892,
			"eir" => 9885,
			"alm" => 9868,
			"_fa" => 9849,
			"nta" => 9835,
			"encon" => 9828,
			"_J" => 9814,
			"s_e_" => 9777,
			"s." => 9722,
			"aga" => 9720,
			"_e_um" => 9703,
			"_S" => 9697,
			"bi" => 9654,
			"ida" => 9642,
			"ando_" => 9641,
			"t�o" => 9633,
			"a_um" => 9623,
			"_por" => 9620,
			"_ve" => 9589,
			"_est" => 9579,
			"dos_" => 9528,
			"per" => 9515,
			"n�o" => 9496,
			"os,_" => 9489,
			"nho" => 9487,
			"e_l" => 9478,
			"Sant" => 9464,
			"ut" => 9460,
			"nde" => 9406,
			"_as_" => 9377,
			"ia._Co" => 9374,
			"agr" => 9364,
			"e_n" => 9319,
			"rd" => 9304,
			"ig" => 9292,
			"no_" => 9288,
			"os," => 9285,
			"a_de_" => 9265,
			"_no_es" => 9248,
			"ter" => 9248,
			"por_" => 9223,
			",_d" => 9160,
			"o_qu" => 9149,
			"ira" => 9125,
			"do_a" => 9099,
			"iva" => 9089,
			"ar_qu" => 9081,
			"m_Jo" => 9033,
			"�_" => 9032,
			"for�" => 9026,
			"._Por" => 8999,
			"s_s" => 8997,
			"m_a" => 8995,
			"_mu" => 8994,
			"ava_" => 8986,
			"a._" => 8977,
			"ni" => 8949,
			"_uma_" => 8944,
			"ntos_" => 8921,
			"_vi" => 8897,
			"ro_" => 8893,
			"os_d" => 8886,
			"_n�" => 8871,
			"ela" => 8838,
			"para" => 8824,
			"_in" => 8792,
			"eiros_" => 8776,
			"starem" => 8766,
			"cu" => 8763,
			",_q" => 8759,
			"r_c" => 8747,
			"dade" => 8729,
			"na_" => 8718,
			"nos" => 8703,
			"o_q" => 8688,
			"_para_" => 8687,
			"em_b" => 8685,
			"ug" => 8680,
			"n�o_" => 8679,
			"ul" => 8662,
			"_Ainda" => 8662,
			"_lua" => 8656,
			"_n�o_" => 8655,
			"mo_" => 8653,
			"itiv" => 8638,
			",_co" => 8635,
			"ha_" => 8625,
			"_uma" => 8588,
			"la_" => 8569,
			"migo" => 8561,
			"ist" => 8557,
			"ari" => 8531,
			"pre" => 8523,
			"os." => 8516,
			"_des" => 8506,
			",_p" => 8471,
			"para_" => 8462,
			"la_r" => 8452,
			"�a" => 8452,
			"mente" => 8440,
			"oi_" => 8432,
			"_P" => 8408,
			"_para" => 8405,
			"amou" => 8387,
			"_an" => 8368,
			"e_a_" => 8356,
			"mpa_" => 8348,
			"a_t" => 8346,
			"rm" => 8345,
			"como_" => 8329,
			"r_a" => 8309,
			"_fu" => 8306,
			"tili" => 8299,
			"em_p" => 8287,
			"_nem" => 8280,
			"omp" => 8275,
			"omo" => 8257,
			"..._" => 8255,
			"alvez" => 8219,
			"ora" => 8215,
			"erd" => 8207,
			"a_da_" => 8205,
			"age" => 8192,
			"a_co" => 8180,
			"Co" => 8171,
			"nda" => 8118,
			"E_" => 8102,
			"_noite" => 8053,
			"estar" => 8025,
			"_N" => 8014,
			"-_Aind" => 7991,
			"ais_" => 7957,
			"tos" => 7951,
			"me_" => 7950,
			"_ao" => 7921,
			"!_N�" => 7890,
			"ami" => 7872,
			"e_o_" => 7851,
			"ha_do_" => 7849,
			"o_u" => 7845,
			"_mante" => 7825,
			"inha" => 7821,
			"er_na" => 7809,
			"out" => 7808,
			"a_tard" => 7800,
			"-s" => 7796,
			"pass" => 7765,
			"em_a" => 7742,
			"iv" => 7716,
			"a_o_" => 7680,
			"as,_as" => 7678,
			"o._" => 7675,
			"ena_fa" => 7670,
			"o_n" => 7661,
			"r_de" => 7635,
			"ente,_" => 7601,
			"s_o" => 7574,
			"r_e" => 7573,
			"._E" => 7571,
			"inha_" => 7554,
			"_como" => 7519,
			"como" => 7516,
			"_cl" => 7510,
			"ndo-s" => 7507,
			"dad" => 7504
		);
	}
} // END OF LanguageMap_pt_Latin1

?>

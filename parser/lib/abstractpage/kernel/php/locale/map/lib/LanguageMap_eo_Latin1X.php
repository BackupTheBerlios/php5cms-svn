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
 
class LanguageMap_eo_Latin1X extends LanguageMap
{
	/**
	 * Constructor
	 */
	function LanguageMap_eo_Latin1X()
	{
		$this->language = "eo";
		$this->charset  = "latin1x";
		
		$this->_populate();
	}
	
	
	// private methods
	
	function _populate()
	{
		$this->map = array(
			"_l" => 78933,
			"a_" => 76505,
			"_k" => 73256,
			"j_" => 72290,
			"aj" => 70319,
			"la" => 68448,
			"n_" => 66779,
			"s_" => 65039,
			",_" => 63959,
			"_la" => 59462,
			"aj_" => 54184,
			"on" => 53236,
			"la_" => 52988,
			"e_" => 52116,
			"_s" => 51318,
			"_d" => 50336,
			"i_" => 50175,
			"is" => 50079,
			"o_" => 49001,
			"_la_" => 48711,
			"en" => 44549,
			"ka" => 43795,
			"li" => 41568,
			"oj" => 40906,
			"an" => 40626,
			"_e" => 40548,
			"_ka" => 40425,
			"_p" => 38884,
			"de" => 37636,
			"ia" => 37016,
			"al" => 36976,
			"kaj" => 36298,
			"ta" => 35980,
			"_a" => 35180,
			"ro" => 34403,
			"_kaj" => 34179,
			"kaj_" => 33970,
			"er" => 33393,
			"is_" => 33106,
			"vi" => 32564,
			"es" => 32403,
			"_de" => 32384,
			"_m" => 32353,
			"st" => 32268,
			"_kaj_" => 32244,
			"oj_" => 31999,
			"_v" => 31820,
			"as" => 31572,
			"or" => 30607,
			"lo" => 29474,
			"ren" => 29436,
			"ri" => 29121,
			",_k" => 28800,
			"in" => 28350,
			"l_" => 28308,
			"ti" => 27915,
			"xi" => 27829,
			"on;" => 26999,
			"de_" => 26509,
			"ar" => 25958,
			"te" => 25563,
			"_de_" => 25455,
			"cx" => 25370,
			"to" => 25364,
			"_t" => 25009,
			"ra" => 24407,
			"_vi" => 23758,
			"u_" => 23141,
			"nt" => 23024,
			"el" => 22987,
			"il" => 22642,
			"_al" => 22139,
			"iu" => 22126,
			"mi" => 22086,
			"o," => 21985,
			"r_" => 21540,
			"ur" => 21332,
			"io_" => 21252,
			"as_" => 21087,
			"o,_" => 21067,
			"ki" => 21022,
			"os" => 20996,
			"j_en_c" => 20945,
			"no" => 20775,
			"_Je" => 20514,
			"_u" => 20477,
			"do" => 20437,
			"est" => 20374,
			"ir" => 20321,
			"_i" => 20219,
			"._" => 20033,
			"_f" => 19756,
			"jn" => 19498,
			"ni" => 19479,
			"re" => 19271,
			"di" => 18955,
			"a_vor" => 18322,
			"_es" => 17622,
			"al_" => 17499,
			"_est" => 17449,
			"ul" => 17233,
			"_li" => 17232,
			"n," => 16740,
			"nd" => 16713,
			"anto_d" => 16568,
			"o_d" => 16360,
			"ko" => 16253,
			"xo" => 16114,
			"Ame" => 16097,
			"_al_" => 16043,
			"_r" => 15975,
			"_ki" => 15921,
			"ant" => 15913,
			"Ka" => 15869,
			"ia_" => 15848,
			"_g" => 15534,
			"e_l" => 15516,
			"mo" => 15478,
			"_c" => 15475,
			"ajxo_" => 15432,
			",_kaj_" => 15339,
			"ili" => 15155,
			"li_" => 15033,
			"aux" => 14915,
			"_cx" => 14901,
			"o_de" => 14782,
			"ux" => 14745,
			";_" => 14715,
			"ian_a" => 14697,
			"Kaj" => 14667,
			"nu" => 14519,
			",_ka" => 14502,
			",_kaj" => 14479,
			"sta" => 14404,
			"n,_" => 14382,
			"_mi" => 14371,
			"_po" => 14314,
			"ter" => 14003,
			"ajn_en" => 13773,
			"en_" => 13716,
			"Kaj_" => 13700,
			"_Jes" => 13676,
			"am" => 13468,
			"o." => 13400,
			"iri" => 13173,
			"_il" => 12990,
			"e_la" => 12836,
			"gxi" => 12834,
			"6_" => 12773,
			"_en" => 12619,
			"o_de_" => 12618,
			"a_h" => 12584,
			"un" => 12536,
			"o_di" => 12439,
			"pr" => 12372,
			"ulo" => 12369,
			"_ili" => 12301,
			"om" => 12185,
			"se" => 12179,
			"pordeg" => 12163,
			"ojn" => 12149,
			"uj" => 12143,
			"si" => 12117,
			"at" => 12093,
			"_Kaj" => 12075,
			"os_" => 11971,
			"ie" => 11935,
			"jn_" => 11790,
			"i_fa" => 11741,
			"_se_i" => 11728,
			"s_la_" => 11716,
			"_Ka" => 11705,
			"_ti" => 11700,
			"s." => 11610,
			"estan" => 11573,
			"_en_" => 11545,
			"iaj" => 11420,
			"rok" => 11419,
			"_de_la" => 11410,
			"sa" => 11406,
			"eg" => 11380,
			"_ma" => 11323,
			"_de_l" => 11127,
			"sx" => 10993,
			"pa" => 10957,
			"oj," => 10949,
			"de_l" => 10899,
			"ol" => 10859,
			"s_l" => 10830,
			"_Kaj_" => 10810,
			"da" => 10732,
			"s_a" => 10691,
			"toj" => 10644,
			"n." => 10639,
			",_ki" => 10543,
			"rof" => 10538,
			"pe" => 10534,
			"_Se" => 10479,
			"tu" => 10466,
			"on," => 10461,
			"su" => 10431,
			"ux_" => 10381,
			"e,_" => 10346,
			"j_l" => 10307,
			"de_la" => 10291,
			"cxi" => 10290,
			"rn" => 10259,
			"a_a" => 10215,
			"estas" => 10170,
			"taj_" => 10148,
			"tas_" => 10131,
			"libro," => 10119,
			"e_la_" => 10119,
			"ru" => 9998,
			"iu_" => 9979,
			"_j" => 9962,
			"va" => 9949,
			"_M" => 9942,
			"el_" => 9911,
			"l_l" => 9876,
			"_di" => 9823,
			"_h" => 9803,
			"a_g" => 9695,
			"al_l" => 9693,
			"na" => 9680,
			"raux_" => 9668,
			"os_a" => 9659,
			"ku" => 9655,
			"s_la" => 9648,
			"aj_l" => 9648,
			"por" => 9645,
			"esta" => 9639,
			"l_ili," => 9637,
			"xi_" => 9520,
			"n_l" => 9515,
			"rofeta" => 9513,
			"j," => 9505,
			"fa" => 9503,
			"x_" => 9493,
			"_el" => 9483,
			"an_" => 9456,
			"_pr" => 9420,
			"ve" => 9415,
			"_J" => 9381,
			"me" => 9359,
			"iuj" => 9355,
			"s_al_" => 9304,
			"kiu" => 9295,
			"it" => 9287,
			"_la_ar" => 9254,
			"mal" => 9230,
			"_obser" => 9218,
			"stas" => 9141,
			"s_al" => 9119,
			"n_d" => 9090,
			"_cxi" => 9086,
			"soifan" => 9056,
			"dis_m" => 9052,
			"age_" => 9041,
			"rt" => 9021,
			"is_a" => 8996,
			"j_k" => 8981,
			"de_la_" => 8959,
			"vadr" => 8958,
			"rolis_" => 8952,
			"aux_" => 8946,
			"stas_" => 8881,
			"_L" => 8877,
			"18" => 8874,
			"dir" => 8862,
			"_kiu" => 8857,
			"._1" => 8828,
			"_la_pl" => 8820,
			"a_s" => 8807,
			"ne_" => 8805,
			"a._" => 8803,
			"_kiu_" => 8798,
			"esti_" => 8776,
			"ibro_" => 8776,
			"n_k" => 8713,
			"u_au" => 8686,
			"eni" => 8645,
			"_La" => 8636,
			"_estas" => 8630,
			"_san" => 8622,
			"ro_" => 8615,
			"vo" => 8610,
			"ta_" => 8598,
			"_se" => 8519,
			"gxo" => 8505,
			"onos_" => 8500,
			"as_al" => 8485,
			"iris" => 8468,
			"o_de_D" => 8464,
			":_" => 8449,
			"_mi_e" => 8372,
			"test" => 8367,
			"rist" => 8362,
			"_esta" => 8355,
			"_al_l" => 8295,
			"oj;_" => 8283,
			"et" => 8232,
			"s_tie" => 8193,
			"s," => 8189,
			"iza" => 8176,
			"_dir" => 8163,
			"j,_" => 8161,
			"s_pr" => 8087,
			"oron." => 8076,
			"s,_" => 8058,
			"estas_" => 8046,
			"Alf" => 8033,
			"ua_" => 8012,
			"lc" => 7991,
			"_li_" => 7981,
			"_si" => 7975,
			"_kun_v" => 7961,
			"tan:" => 7956,
			"a_ates" => 7953,
			"Je" => 7946,
			"_ne_" => 7850,
			"nkta" => 7845,
			"r_l" => 7844,
			"diri" => 7839,
			"mi_" => 7822,
			"Mi" => 7819,
			"ank" => 7810,
			"e_tio_" => 7807,
			"_S" => 7738,
			"_por" => 7734,
			"lia" => 7732,
			"ro;_" => 7719,
			"ojn_" => 7689,
			"a_k" => 7686,
			"l_la_" => 7679,
			"i_e" => 7644,
			"o:_Se_" => 7636,
			"uj_" => 7626,
			"l_la" => 7600,
			"per" => 7598,
			"_su" => 7588,
			"iaj_" => 7566,
			"xe" => 7557,
			"ian_" => 7548,
			"bo" => 7533,
			"un_" => 7495,
			"a_Sin" => 7470,
			"av" => 7460,
			"j_la" => 7449,
			"ian" => 7439,
			"n_la" => 7419,
			"as:_Ve" => 7412,
			"nk" => 7402,
			"fo" => 7399,
			"ro,_l" => 7369,
			"La" => 7367,
			"apid" => 7351,
			"kso;_l" => 7342,
			"ort" => 7332,
			"o_es" => 7305,
			"aro" => 7290,
			"cxiuj" => 7276,
			"is_al" => 7263,
			"kon" => 7222,
			"or_" => 7221,
			"aj_se" => 7217,
			"ald" => 7186,
			"oj_d" => 7125,
			"fi" => 7124,
			"Lin_ad" => 7103,
			"ap" => 7087,
			"r_la_" => 7080,
			"taj" => 7065,
			"ti_" => 7050,
			"n_de_" => 7014,
			"loj" => 7004,
			"r_la" => 7003,
			"_mal" => 7003,
			"l_gx" => 6991,
			"kiu_au" => 6981,
			"Ekster" => 6957,
			",_Di" => 6951,
			"por_" => 6938,
			"_D" => 6930,
			"vas" => 6926,
			"o_de_l" => 6920,
			"go" => 6918,
			"ke_" => 6892,
			"nto" => 6892,
			"xiuj_" => 6880,
			"u:_Ve" => 6868,
			"tos" => 6866,
			"o._" => 6864,
			"o;_" => 6854,
			"vid" => 6841,
			"s_e" => 6827,
			"nis" => 6826,
			"is_al_" => 6802,
			",_kiu" => 6767,
			",_po" => 6766,
			"sto" => 6751,
			"enos._" => 6750,
			"e_(cx" => 6750,
			"o_e" => 6728,
			"oj,_" => 6717,
			"ras:_V" => 6706,
			"ok" => 6703,
			"matena" => 6682,
			"iam_" => 6682,
			"n_el_" => 6667,
			"fetajx" => 6666,
			"Cx" => 6663,
			"_an" => 6633,
			"a_l" => 6626,
			"ras" => 6618,
			"_viv" => 6610,
			"_pe" => 6551,
			"is,_" => 6544,
			"al_vi" => 6524,
			"gra" => 6519,
			"kaj_l" => 6518,
			"fu" => 6499,
			"j_e" => 6498,
			"aj_la" => 6497,
			"_pa" => 6497,
			"_via" => 6478,
			"_do" => 6471,
			"on_d" => 6452,
			"n_de" => 6448,
			"ne_pl" => 6447,
			"ag" => 6447,
			"is_l" => 6412,
			"am_" => 6403,
			"ven" => 6384,
			"injoro" => 6372,
			"re,_mi" => 6368,
			"sur" => 6361,
			"apart" => 6360,
			"orpre" => 6360,
			"don" => 6355,
			"far" => 6346,
			"j_esto" => 6338,
			"en_l" => 6310,
			"nta" => 6291,
			"esuo" => 6284,
			"egxo" => 6280,
			"venos." => 6279,
			"por_r" => 6276,
			"_A" => 6258,
			"kt" => 6248,
			"xa_e" => 6230,
			"iam" => 6226,
			"jx" => 6225,
			"j_de" => 6208,
			"pre" => 6178,
			"is," => 6160,
			"_en_l" => 6154,
			"ur_" => 6149,
			"mi_r" => 6147,
			"aj_la_" => 6142,
			";_kiu" => 6132,
			"uj_sa" => 6124,
			"rofet" => 6104,
			"al_gx" => 6087,
			"_kaj_l" => 6076,
			"ankta" => 6070,
			"_ha" => 6055,
			"idoj" => 6049,
			"mon" => 6045,
			"ili_" => 6036,
			"Dio" => 6030,
			"_ke" => 6028,
			"kaj_s" => 6026,
			"ion" => 6016,
			"faru_" => 6004,
			"_ili_" => 6000,
			"a._1" => 5993,
			"n_kaj" => 5988,
			"via" => 5964,
			"a_ido" => 5959,
			"n_ka" => 5935,
			"s_s" => 5930,
			"per_" => 5922,
			"_mia" => 5921,
			"ge" => 5909,
			"_pro" => 5899,
			"sti" => 5876,
			"diris" => 5874,
			"_Sinjo" => 5842,
			"oi" => 5835,
			"lanko" => 5831,
			"per_al" => 5826,
			"ero" => 5823,
			"aj_nor" => 5811,
			"ro_Jes" => 5786,
			"ono" => 5785,
			"im" => 5773,
			"ist" => 5753,
			"op" => 5750,
			"_fi" => 5749
		);
	}
} // END OF LanguageMap_eo_Latin1X

?>

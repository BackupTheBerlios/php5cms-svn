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
 
class LanguageMap_ga_Latin1 extends LanguageMap
{
	/**
	 * Constructor
	 */
	function LanguageMap_ga_Latin1()
	{
		$this->language = "ga";
		$this->charset  = "latin1";
		
		$this->_populate();
	}
	
	
	// private methods
	
	function _populate()
	{
		$this->map = array(
			"_a" => 5036,
			"n_" => 3795,
			"a_" => 3731,
			"ai" => 3640,
			"ch" => 3453,
			"in" => 3222,
			"_s" => 2739,
			"ir" => 2709,
			"r_" => 2600,
			"ac" => 2493,
			"h_" => 2422,
			"ar" => 2379,
			"th" => 2309,
			"s_" => 2296,
			"an" => 2293,
			"_c" => 2279,
			"e_" => 2208,
			"_d" => 2139,
			"ha" => 2095,
			"ea" => 2024,
			"/_" => 1974,
			",_" => 1954,
			"ichi�" => 1932,
			"_i" => 1873,
			"_n" => 1700,
			"_t" => 1693,
			"_b" => 1633,
			"_is_l" => 1604,
			"na" => 1592,
			"nn" => 1582,
			"._" => 1582,
			"_g" => 1560,
			"il" => 1493,
			"_f" => 1490,
			"o_" => 1482,
			"e/" => 1482,
			"sinn_" => 1401,
			"in_" => 1397,
			"_a_" => 1356,
			"ach" => 1344,
			"he" => 1341,
			"is" => 1308,
			"d_" => 1293,
			"t_" => 1288,
			"le" => 1271,
			"bh" => 1245,
			"it" => 1239,
			"_l" => 1222,
			"id" => 1214,
			"ra" => 1214,
			"ui" => 1201,
			"ar_" => 1191,
			"/i" => 1165,
			"ol." => 1143,
			"an_" => 1102,
			"ad" => 1092,
			"is_" => 1078,
			"m_" => 1064,
			"la" => 1054,
			"ch_" => 1045,
			"or" => 1043,
			"_an" => 1035,
			"i_" => 1020,
			"re" => 1018,
			"dh" => 1015,
			"ei" => 1012,
			"as" => 1010,
			"ch_sao" => 996,
			"ag" => 994,
			"ri" => 987,
			"l_" => 973,
			"en" => 943,
			"_na" => 937,
			"_ch" => 930,
			"ith" => 910,
			"inn" => 893,
			"ne" => 852,
			"de" => 851,
			"_r" => 831,
			"gu" => 813,
			"gca" => 810,
			"on" => 809,
			"g_" => 797,
			"hu" => 789,
			"im" => 787,
			"sa" => 775,
			"am" => 774,
			"tha" => 773,
			"do" => 771,
			"hi" => 758,
			"nn_" => 746,
			"ig" => 745,
			"al" => 730,
			"ach_" => 729,
			"gus" => 728,
			"at�_ar" => 728,
			"_an_" => 714,
			"o/" => 708,
			"at" => 704,
			"si" => 693,
			"oc" => 692,
			"us" => 689,
			"r_nea" => 688,
			"r_i" => 685,
			"da" => 683,
			"ga" => 682,
			"_ar" => 678,
			"s_fhea" => 677,
			"r_a" => 676,
			"�r_bhf" => 667,
			"ait" => 665,
			"li" => 663,
			"h_a" => 657,
			"ca" => 656,
			"na_" => 653,
			"ba" => 650,
			"ro" => 647,
			"eo" => 645,
			"_le" => 644,
			"ir_d" => 644,
			"_ag" => 641,
			"_bh" => 639,
			"mh" => 637,
			"�_l" => 633,
			"as_" => 632,
			"aid" => 624,
			"ain" => 621,
			"_in" => 620,
			"_do" => 620,
			"_i_" => 616,
			"cu" => 612,
			"ht" => 607,
			"co" => 607,
			"hai" => 602,
			"_dh" => 597,
			"_do_" => 596,
			"me" => 595,
			"te" => 595,
			"n-" => 594,
			"tsa" => 584,
			"dol_ri" => 581,
			"ll" => 579,
			"ce" => 578,
			"di" => 577,
			"_th" => 570,
			"cht" => 568,
			"r_ne" => 564,
			"us_" => 562,
			"_ar_" => 560,
			"ao" => 559,
			"sc" => 555,
			"th_" => 552,
			"_si" => 544,
			"be" => 534,
			"ni" => 532,
			"_o" => 525,
			"ia" => 525,
			"agu" => 522,
			"ire" => 520,
			"_na_" => 516,
			"\rs_ag" => 516,
			"mo" => 513,
			"il_" => 512,
			"ant" => 510,
			"chu/?_" => 510,
			"aca_" => 509,
			"_ma" => 508,
			"amar" => 506,
			"ann" => 505,
			"rt_dha" => 504,
			"idh_G" => 501,
			"hf�ic" => 500,
			"fa" => 500,
			"_agus_" => 499,
			"a_d" => 499,
			"dtaga_" => 498,
			"_C" => 494,
			"ic" => 494,
			"_d�" => 492,
			"le_" => 490,
			"a_s" => 489,
			"rt" => 484,
			"nt" => 477,
			"ad_" => 477,
			"s_a" => 476,
			"on_" => 474,
			"ir_i" => 473,
			"a_a" => 473,
			"e/i" => 472,
			"_mo" => 468,
			"_dtag" => 468,
			",_a" => 466,
			"air_" => 465,
			"h_mi" => 463,
			"e/a" => 461,
			"h_leam" => 455,
			"imid_d" => 450,
			"rad" => 450,
			"e_a" => 447,
			"_gcat" => 447,
			"ile" => 446,
			"cha" => 445,
			"._N" => 444,
			"_is" => 441,
			"ti" => 439,
			"id_" => 438,
			"_a_bh" => 438,
			"aga" => 436,
			"the" => 435,
			"inn_" => 434,
			"ean" => 430,
			"ht_" => 430,
			"ith_" => 426,
			"a_c" => 426,
			"st" => 425,
			"d�r_bh" => 424,
			"gus_" => 423,
			"ol_" => 423,
			",_tr" => 420,
			"go" => 420,
			"Agu" => 419,
			"dh_" => 419,
			"_ai" => 417,
			"_n-" => 415,
			"an_R�o" => 414,
			"n," => 414,
			"bha" => 412,
			"ol" => 411,
			"n,_" => 408,
			"_thu_" => 405,
			"sin" => 404,
			"nach" => 399,
			"_ar_an" => 396,
			"fiacha" => 394,
			"ala" => 393,
			"inne" => 389,
			"Ame" => 389,
			"_mo_" => 389,
			"ua" => 387,
			"ed" => 385,
			"n_t" => 385,
			"_se" => 384,
			"_co" => 384,
			"_de" => 382,
			"ine" => 382,
			"ag_e/i" => 381,
			".._" => 381,
			"_be" => 379,
			"chioth" => 375,
			"toili" => 375,
			"_T" => 373,
			"a_tha_" => 372,
			"mi._T" => 371,
			"agus_" => 370,
			"_ar_ne" => 368,
			"_agus" => 368,
			"niste" => 366,
			"_a_b" => 365,
			"adh" => 365,
			"m,"_" => 364,
			"_go" => 364,
			"cht_" => 363,
			"amh:_" => 363,
			"l_na_s" => 363,
			"ha_" => 363,
			"-s" => 362,
			"n." => 362,
			"�i" => 360,
			"iu" => 359,
			"_an_C" => 359,
			"/r" => 358,
			"no" => 357,
			"_agu" => 356,
			"n_c" => 356,
			"n_d" => 356,
			"aith" => 355,
			"'_" => 354,
			"uid" => 354,
			"ach_s" => 354,
			"sa_" => 353,
			"_tal" => 352,
			"du" => 352,
			"agus" => 352,
			"aoi" => 351,
			"mo_" => 350,
			"_neam" => 350,
			"n_cana" => 349,
			"_h-" => 348,
			"_n�" => 348,
			"fh" => 348,
			"u_" => 348,
			"_in_" => 347,
			"ainn_i" => 346,
			"_me" => 346,
			"_is_" => 345,
			"oir" => 345,
			"bhai" => 345,
			"_A_h-u" => 344,
			"ithe" => 343,
			"aig" => 343,
			"do_ri" => 342,
			"rom_" => 341,
			"ne_" => 338,
			"ire,_" => 338,
			"_at" => 338,
			"ol_si" => 337,
			"on_.." => 337,
			"tr" => 337,
			"e," => 336,
			"sa_bh" => 336,
			"cho" => 334,
			"las_s" => 333,
			"_fe" => 332,
			"am_" => 332,
			"hua" => 332,
			"go_" => 329,
			"nac" => 329,
			"ma_dhe" => 329,
			"r_s" => 328,
			"an_eil" => 328,
			"ruith" => 328,
			"_ca" => 327,
			"_sa" => 326,
			"_inn" => 326,
			"-a" => 324,
			"each" => 323,
			"aof" => 323,
			"he_" => 323,
			"os" => 321,
			"e/_t" => 321,
			"_Cha_d" => 321,
			"ibh" => 320,
			"�l_" => 320,
			"_D" => 320,
			"_�_" => 318,
			"_nam_m" => 318,
			"ath" => 315,
			"l_a" => 315,
			"och" => 314,
			"do_" => 311,
			"gcath�" => 311,
			"har" => 310,
			"_nea" => 310,
			"ill" => 309,
			"inn_�_" => 309,
			"hn" => 309,
			"eth" => 308,
			"_A" => 308,
			"mho/r_" => 307,
			"es" => 305,
			"r�" => 304,
			"b_" => 303,
			"et" => 302,
			"_h" => 301,
			"lai" => 300,
			"_an_R" => 300,
			"acht" => 299,
			"Ch" => 298,
			"nd" => 298,
			"agan_" => 297,
			"_fa" => 297,
			"dh,_" => 297,
			"_bheag" => 297,
			"_gu" => 296,
			"la_" => 296,
			"_go_" => 296,
			"to" => 295,
			"ntar_a" => 294,
			"fi" => 293,
			"hf" => 293,
			"o_n" => 293,
			"mar" => 292,
			"hl" => 292,
			"_da" => 291,
			"_ta" => 290,
			"s_an" => 287,
			"_da\_" => 287,
			"n_i" => 285,
			"ar_ar" => 285,
			"a_mio" => 285,
			"thi" => 284,
			"�_ar_n" => 284,
			"_e/" => 284,
			"._A" => 283,
			"r_n" => 282,
			"oir_gu" => 282,
			":_g" => 281,
			"dui" => 281,
			"r_d" => 280,
			"_M" => 280,
			"a_f" => 280,
			"e." => 279,
			"mac" => 279,
			"_rud_" => 279,
			"aon" => 278,
			"Si" => 278,
			"t_a" => 278,
			"_an_D" => 278,
			"ol_na" => 277,
			"ci" => 277,
			"lle" => 277,
			"_sin" => 277,
			"r_b" => 276,
			"ar_a" => 276,
			"a_bh" => 276,
			"thaini" => 275,
			"aire" => 275,
			"noth" => 274,
			"igh" => 274,
			"_tha" => 273,
			"_na_s" => 273,
			"hi_a_f" => 272,
			"dhi_" => 271,
			"che" => 271,
			"._.." => 270,
			"idh" => 270,
			"s_i" => 270,
			"/_an_" => 269,
			"en_" => 269,
			"nai" => 269,
			"a'" => 269,
			"athai" => 268,
			"h,_" => 268,
			"rama" => 268,
			"is_lea" => 268,
			"�n" => 268,
			"um" => 267,
			"reach" => 267,
			"el" => 266,
			"he/" => 266,
			"ideann" => 266,
			"hf�i" => 266,
			"deoch_" => 266,
			"nuair_" => 266,
			"h_i" => 266,
			"d_a" => 265,
			"_cho" => 265,
			"oin" => 264,
			"ara" => 264,
			"_chu" => 263,
			"_d�" => 263,
			"h_s" => 263,
			"mi" => 263,
			"ir_s" => 263,
			"a_ch" => 262,
			"h," => 262,
			"a_r" => 261,
			"hne" => 261,
			"int" => 261,
			"_ag_" => 260,
			"nea" => 260,
			"_tharr" => 259,
			"�r" => 255,
			"irt_" => 255,
			"ugho" => 255,
			"o_c" => 254,
			"ada" => 254,
			"fio" => 253,
			"iad_" => 253,
			"mar_" => 253,
			"bhe" => 253,
			"do_rio" => 253,
			"h_g" => 253,
			"_G" => 252,
			"ir,_t" => 252,
			"ha_gu" => 252,
			"l._Ach" => 252,
			"a_chu" => 252,
			"_Gh" => 250,
			"i_a_" => 250,
			"e._" => 249,
			"e_t" => 248,
			"ana" => 248,
			"aoin" => 248,
			"sin_c" => 247,
			"�r_bh" => 247,
			"a_th" => 247,
			"an-dra" => 246,
			"ud" => 246,
			"a_bhio" => 246,
			"_ruib" => 245,
			"abha" => 244,
			"h_d" => 244,
			"hainig" => 244,
			"_air" => 244,
			"h_iad_" => 244,
			"hur" => 244,
			"bu" => 244,
			"odh_fa" => 243,
			":_" => 243,
			"�_" => 243,
			"_f�i" => 243,
			"o-" => 243,
			"hair" => 242,
			"-_" => 242,
			"tu" => 242,
			"n-a" => 242,
			"aor" => 242,
			"s_m" => 241,
			"r�_s" => 241
		);
	}
} // END OF LanguageMap_ga_Latin1

?>

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
 
class LanguageMap_de_Latin1 extends LanguageMap
{
	/**
	 * Constructor
	 */
	function LanguageMap_de_Latin1()
	{
		$this->language = "de";
		$this->charset  = "latin1";
		
		$this->_populate();
	}
	
	
	// private methods
	
	function _populate()
	{
		$this->map = array(
			"en" => 1425934,
			"n_" => 1286127,
			"er" => 1241882,
			"ch" => 1166475,
			"e_" => 983180,
			"r_" => 886973,
			"_d" => 877565,
			"hmer_" => 774935,
			"de" => 765852,
			"ei" => 723241,
			",_" => 691651,
			"in" => 668820,
			"te" => 665129,
			"_s" => 647375,
			"er_" => 641750,
			"ie" => 634415,
			"_w" => 580068,
			"s_" => 550959,
			"ge" => 548604,
			"t_" => 533561,
			"nd" => 527188,
			"ic" => 491306,
			"un" => 462971,
			"_e" => 458232,
			"ich" => 456282,
			"ne" => 433637,
			"es" => 428062,
			"_u" => 422894,
			"h_" => 416426,
			"ein" => 410306,
			"_Regie" => 395121,
			"_de" => 388973,
			"ch_" => 383429,
			"_i" => 376804,
			"in_" => 375172,
			"he" => 371332,
			"be" => 369859,
			"cht_n" => 353645,
			"d_" => 353172,
			"st" => 342600,
			"an" => 338971,
			"ie_" => 324294,
			"nd_" => 310236,
			"t,_" => 309583,
			"re" => 303107,
			"_un" => 299046,
			"di" => 291328,
			"der" => 291258,
			"au" => 286397,
			"roc" => 286232,
			"und" => 273628,
			"or" => 267987,
			"m_" => 266554,
			"_m" => 257431,
			"ht" => 256994,
			"le" => 256457,
			"el" => 255578,
			"._" => 255111,
			"_di" => 249680,
			"sc" => 248853,
			"it" => 247901,
			"da" => 246653,
			"sch" => 243783,
			"die" => 243752,
			"_und" => 240916,
			"cht" => 239602,
			"_n" => 236779,
			"und_" => 234412,
			"_und_" => 232178,
			"che" => 231985,
			"um_e" => 226021,
			"ine" => 224424,
			"_die" => 222439,
			"ich_" => 218756,
			"den" => 218448,
			"_h" => 217759,
			"n," => 215678,
			"der_" => 214023,
			"ng" => 213836,
			"eine" => 212984,
			"n,_" => 212099,
			"si" => 211113,
			"al" => 210126,
			"_ein" => 208423,
			"li" => 207003,
			"_ei" => 205270,
			"_v" => 200075,
			"_b" => 198295,
			"_da" => 197744,
			"gen" => 197195,
			"zu" => 196983,
			"hr" => 196962,
			"em" => 194970,
			"is" => 194180,
			"_z" => 192083,
			"zu_" => 190262,
			"_S" => 189799,
			"n_d" => 189680,
			"_der_" => 188781,
			"_we" => 188632,
			"ten" => 184649,
			"eh" => 184448,
			"es_" => 183949,
			"(Fia" => 182799,
			"die_" => 182424,
			"_der" => 180816,
			"we" => 179585,
			"�rung" => 179462,
			"ll" => 179045,
			"en," => 176377,
			"_die_" => 175774,
			"ar" => 174398,
			"en,_" => 173360,
			"ig" => 171346,
			",_d" => 171217,
			"ir" => 169739,
			"f_wort" => 169391,
			"_si" => 167294,
			"_ge" => 166613,
			"ra" => 164499,
			"on" => 164460,
			"ni" => 159464,
			"us" => 157122,
			"_zu" => 156859,
			"et" => 156695,
			"nt" => 155888,
			"u_" => 155856,
			"mi" => 155658,
			"icht" => 152918,
			"den_" => 151932,
			"ht_" => 151403,
			"wi" => 151168,
			"ter" => 151071,
			"hen" => 147962,
			"_au" => 147596,
			"nde" => 147119,
			"ns" => 146775,
			"wa" => 146324,
			"_er" => 145009,
			"vouier" => 143598,
			"t," => 142290,
			"uf" => 142240,
			"so" => 138011,
			"rt" => 136107,
			"gleic" => 135443,
			"vor,_e" => 132509,
			"ri" => 131997,
			"n_v" => 131365,
			"st_" => 131165,
			"_vom" => 131092,
			"_f" => 129822,
			"ten_" => 129676,
			"och" => 128764,
			"g_" => 128267,
			"lt" => 127092,
			"ke" => 127078,
			"r_h" => 127059,
			"_mi" => 125321,
			"gef�" => 124906,
			"nen" => 124807,
			"_vor" => 124377,
			"vo" => 123774,
			"onder" => 123628,
			"_in" => 123383,
			"_A" => 123300,
			"lic" => 123284,
			"geg" => 123072,
			"n." => 122972,
			"schw" => 122932,
			"_k" => 122756,
			"_auf" => 122170,
			"hwie" => 121050,
			"inzi" => 121037,
			"em_" => 120859,
			"ac" => 120773,
			"_Er" => 120685,
			"wu" => 120574,
			"end" => 120451,
			"arde" => 119532,
			"and" => 118906,
			"tt" => 118655,
			"ber" => 118624,
			"e," => 118591,
			"_G" => 118345,
			"rs" => 117567,
			"cht_" => 117418,
			"ab" => 117173,
			"em_w" => 117133,
			"m_Pano" => 116809,
			"_zu_" => 116466,
			"n_wel" => 116423,
			"ne_" => 116277,
			"ebroch" => 115920,
			"ach" => 115662,
			"chen" => 115596,
			"_sei" => 115232,
			"_al" => 115007,
			"Be" => 114859,
			"itr" => 113778,
			"fe" => 113123,
			"eb" => 113069,
			"nd_di" => 112979,
			"oc" => 112456,
			"tte" => 111826,
			"rd" => 111349,
			"_B" => 111028,
			"_be" => 110743,
			"n,_das" => 110471,
			"_in_" => 110045,
			"ste" => 109820,
			"lich" => 109666,
			"r_i" => 108674,
			"bei" => 108263,
			"r_a" => 108255,
			"rung" => 108243,
			"ig._" => 107245,
			"n_m�s" => 106777,
			"w�ru" => 106573,
			"ber_" => 106425,
			"icht_" => 106112,
			"r_d" => 105614,
			"_Ge" => 105062,
			",_w" => 104887,
			"_H" => 104777,
			"a_d" => 104268,
			"_er_" => 104229,
			"_W" => 104004,
			"en._" => 103831,
			"_so" => 103810,
			"la" => 103658,
			"rne" => 103382,
			"re_la" => 103277,
			"dem" => 103223,
			"a�" => 102934,
			"vor," => 102828,
			"sich" => 102124,
			"hre" => 101933,
			"_eine" => 101885,
			"gen_" => 101396,
			"ung" => 101060,
			"ag" => 101048,
			"nge" => 100839,
			"lle" => 100773,
			"auf" => 100486,
			"n_und_" => 100427,
			"_M" => 100262,
			"eu" => 98728,
			"ben" => 98673,
			"eine_" => 98462,
			"den._D" => 98240,
			"hi" => 97690,
			"_den_" => 97542,
			"rn" => 97316,
			"nd_d" => 97194,
			"ei_" => 97059,
			"e_Berl" => 96641,
			"hts" => 96628,
			"_E" => 96611,
			"eit" => 96511,
			"f�" => 96408,
			"ti" => 96383,
			"ema" => 95874,
			"ss" => 95519,
			"ll_" => 95466,
			"sic" => 95120,
			"en." => 95003,
			"_an_di" => 94906,
			"hn" => 94867,
			"n_de" => 94824,
			"e_denn" => 94694,
			"_falsc" => 94651,
			"rungsc" => 94634,
			"ien,_" => 94503,
			"er_s" => 94410,
			"_wa" => 94067,
			"tz" => 93870,
			"n._" => 93835,
			"er_i" => 93697,
			"r_s" => 93479,
			"um" => 93440,
			"ner" => 93322,
			"ck" => 93108,
			"h_dar" => 92762,
			"_nic" => 92251,
			"ine_" => 92079,
			"ist" => 92039,
			"e_d" => 91790,
			"zt" => 91071,
			"_L" => 90936,
			"_sic" => 90461,
			"_181." => 89952,
			"ers" => 89944,
			"na" => 89870,
			"_K" => 89840,
			"ihn_er" => 89466,
			"art" => 89432,
			"mm" => 89390,
			"peri" => 89135,
			",_de" => 88864,
			"he_V" => 88743,
			"mit" => 88535,
			"_je" => 88297,
			"ver" => 87865,
			"._D" => 87813,
			"_sch" => 87662,
			"ese" => 87428,
			"f_" => 87298,
			"_sc" => 87092,
			"_Wei" => 86989,
			"chen_" => 86486,
			"das" => 86442,
			"ige" => 86225,
			"sen" => 86052,
			"_sie" => 85862,
			"inanze" => 85750,
			"_Mo" => 85333,
			"sa" => 85323,
			"_dem_" => 85240,
			"war" => 85010,
			"dem_" => 84965,
			"iema" => 84958,
			"ndli" => 84786,
			"ef" => 84609,
			"_an" => 84565,
			"lte" => 84435,
			"mer" => 84381,
			"sie" => 84071,
			"ren" => 84002,
			"aus" => 83623,
			"r," => 83337,
			"_ver" => 83186,
			"rme" => 82642,
			"rneh" => 82336,
			"st_er_" => 82274,
			"abe" => 82268,
			"hef_u" => 82172,
			"ten,_" => 82124,
			"men" => 81797,
			"von" => 81757,
			"nn_" => 81693,
			",_da" => 81676,
			"wisch" => 81571,
			"hwiege" => 81541,
			"erungs" => 81233,
			"chts" => 81169,
			"mb" => 80928,
			"ru" => 80593,
			"dere" => 80524,
			"_sein" => 80429,
			"n_der" => 80365,
			"wieder" => 80173,
			"sche" => 80002,
			",_und_" => 79996,
			"_ich" => 79928,
			"m_M" => 79839,
			"Ca" => 79689,
			"Ge" => 79654,
			"ut" => 79637,
			"rde" => 79474,
			"r,_" => 79429,
			"ls_" => 79307,
			"e_be" => 78683,
			"als" => 78625,
			"h_d" => 78597,
			"_ve" => 78507,
			"ksamt" => 78199,
			"ter_" => 78174,
			"n_w" => 78032,
			"sse" => 77861,
			"_mit" => 77836,
			"_ich_" => 77324,
			"am" => 77306,
			"_sich" => 77276,
			"n_u" => 77267,
			"in_d" => 77163,
			"rei" => 77103,
			"nte" => 77009,
			"im" => 77001,
			"ta" => 76957,
			"�t" => 76356,
			"uch" => 76273,
			"t_e" => 76255,
			"her" => 76050,
			"gu" => 75991,
			"ur_" => 75978,
			"eit_" => 75835,
			"ern" => 75826,
			"rte" => 75445,
			"wie" => 75125,
			"_sel" => 74799,
			"all" => 74469,
			"gest" => 74294,
			"_es" => 74207,
			"n_i" => 74103,
			"von_" => 74101,
			"_ist" => 74070,
			"_sich_" => 73910,
			"och_" => 73764,
			"mit_" => 73719,
			"ah" => 73621,
			",_s" => 73260,
			"m_w" => 73040,
			"gt" => 72961,
			"z)_" => 72246,
			"age" => 72229,
			"sich_" => 72137,
			"hm" => 72126,
			",_un" => 71868,
			"tr" => 71804,
			"vor" => 71685,
			"ebe" => 71644,
			"_T" => 71574,
			"nstig" => 71461,
			"eist_s" => 71424,
			"lei" => 71260,
			"_N" => 71165,
			"inem_" => 70846,
			"all_de" => 70570,
			"_allem" => 70462,
			"n,_w�" => 70325,
			"eic" => 70260,
			"81." => 70207,
			"ist_" => 70132,
			"nem" => 70102,
			"n_a" => 70095,
			"_mit_" => 69978,
			"en_w" => 69901,
			"_immer" => 69803,
			"kei" => 69583,
			"_V" => 69177,
			"ges" => 69164,
			"ere" => 69100,
			"je" => 69063,
			"_alle" => 69038,
			"do" => 68761,
			"twe" => 68753,
			"seine" => 68714,
			"nder" => 68513,
			"_is" => 68277,
			"mei" => 67550,
			"he_" => 67539,
			"iche" => 67518,
			"_Unt" => 67417,
			"er_zu" => 67407,
			"_es_" => 67039,
			"e_vie" => 67003,
			"te_S" => 66801,
			"en_e" => 66716,
			",_und" => 66688,
			"einen_" => 66557,
			"�r" => 66483,
			"auc" => 66404,
			"emals" => 66392,
			"_ein_" => 66382,
			"e_i" => 66291,
			"a_" => 66158,
			"wei" => 66147,
			"e_S" => 66143,
			"r_e" => 66046,
			"an_" => 66021,
			"_wo" => 65985,
			"Dies_i" => 65905,
			"uf_" => 65820,
			"isc" => 65772,
			"se_" => 65724,
			"n_S" => 65496,
			"_Da" => 65273,
			"e_Reg" => 65268,
			"en_u" => 65147,
			"Wei" => 65119,
			"lien," => 65016,
			"sagen," => 64972,
			"e_a" => 64946,
			"ner_" => 64907,
			"nne" => 64852,
			"_St" => 64758,
			"erkomm" => 64720,
			"auf_" => 64446,
			"_me" => 64422,
			"_Die" => 64410,
			"he_d" => 64377,
			"_auf_" => 64186,
			"n,_d" => 64143,
			"tig" => 63942,
			"gen,_" => 63916,
			"lb" => 63897,
			"oh" => 63874,
			"t_n" => 63802,
			"�h" => 63699,
			".006.0" => 63678,
			"dige" => 63668,
			"ens" => 63552,
			"_f�nf" => 63464,
			"_und_s" => 63096,
			"rhal" => 63060,
			"_werde" => 62968,
			"om_Za" => 62912,
			"e_G" => 62898,
			"rg" => 62394,
			"e_Med" => 62296
		);
	}
} // END OF LanguageMap_de_Latin1

?>

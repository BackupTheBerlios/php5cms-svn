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
 
class LanguageMap_sl_ASCII extends LanguageMap
{
	/**
	 * Constructor
	 */
	function LanguageMap_sl_ASCII()
	{
		$this->language = "sl";
		$this->charset  = "ascii";
		
		$this->_populate();
	}
	
	
	// private methods
	
	function _populate()
	{
		$this->map = array(
			"e_" => 54597,
			"_s" => 48702,
			"a_" => 46798,
			"o_" => 39457,
			"i_" => 39051,
			"_p" => 33309,
			"je" => 32907,
			",_" => 29175,
			"_n" => 26554,
			"_j" => 24828,
			"se" => 23527,
			"meshch" => 23320,
			"je_" => 23126,
			"_v" => 21272,
			"ujena_" => 20980,
			"._" => 20868,
			"na" => 19428,
			",_da_b" => 18660,
			"ni" => 18562,
			"_k" => 18529,
			"il" => 17972,
			"st" => 17840,
			"al" => 17546,
			"ra" => 17385,
			"em" => 17376,
			"ik_" => 16938,
			"po" => 16913,
			"l_" => 16846,
			"_d" => 16681,
			"ko" => 16393,
			"re" => 16260,
			"_se" => 16108,
			"el" => 15727,
			"en" => 15492,
			"la" => 15418,
			"_t" => 15313,
			"_b" => 15230,
			"n_" => 15017,
			"ne" => 15012,
			"in" => 14994,
			"_i" => 14762,
			"m_" => 14486,
			"li" => 14297,
			"_po" => 14246,
			"._S" => 13622,
			"da" => 13473,
			"ti" => 13321,
			"_m" => 13313,
			"_o" => 13243,
			"es" => 12769,
			"_na" => 12742,
			"ka" => 12388,
			"ri" => 12375,
			"te" => 12303,
			"in_" => 12276,
			"aj" => 12192,
			"ve" => 12089,
			"se_" => 12023,
			"O_J" => 11992,
			"za" => 11758,
			"no" => 11704,
			"ov" => 11181,
			"_in" => 11130,
			"ar" => 11080,
			"an" => 11039,
			"bi" => 11032,
			"mu" => 10824,
			"a_s" => 10662,
			"ce" => 10649,
			"ed" => 10604,
			"_se_" => 10475,
			"le" => 10375,
			"me" => 10306,
			"lo" => 10296,
			"v_" => 9948,
			"ak" => 9881,
			"od" => 9812,
			"_in_" => 9793,
			"os" => 9767,
			"va" => 9592,
			"av" => 9580,
			"pa" => 9568,
			"at" => 9557,
			"is" => 9385,
			"j_" => 9329,
			"ja" => 9257,
			"nj" => 9068,
			"_za" => 8979,
			"as" => 8741,
			"ga" => 8525,
			"em_" => 8474,
			"ame" => 8457,
			"so" => 8390,
			"mo" => 8324,
			"iescat" => 8314,
			"lj" => 8260,
			"or" => 8157,
			"a_d" => 8145,
			"am" => 8126,
			"escat_" => 7992,
			"vo" => 7966,
			"upaj" => 7887,
			"_bi" => 7867,
			"ol" => 7801,
			",_da_j" => 7723,
			"_mo" => 7703,
			"di" => 7691,
			"sk" => 7610,
			"vi" => 7554,
			"il_" => 7550,
			"u_" => 7445,
			"i_s" => 7413,
			"de" => 7388,
			"o_s" => 7366,
			"ob" => 7357,
			"ic" => 7346,
			"_da" => 7293,
			"ko_" => 7272,
			"ot" => 7223,
			"im" => 7209,
			",_k" => 7198,
			"e_s" => 7196,
			"er" => 7190,
			"na_" => 7182,
			"h_" => 7157,
			"da_" => 7009,
			"oj" => 6967,
			"la_" => 6897,
			"ej" => 6887,
			"mi" => 6880,
			"sa" => 6824,
			"_v_" => 6797,
			"si" => 6746,
			"_ne" => 6666,
			"_pa" => 6572,
			"ost" => 6501,
			"om" => 6478,
			"r_" => 6396,
			"r_c" => 6387,
			""_" => 6381,
			"orc" => 6368,
			"e_p" => 6356,
			"b_ka" => 6330,
			"bo" => 6327,
			"marsi" => 6289,
			"ih" => 6279,
			"ek" => 6278,
			"go" => 6203,
			"ris" => 6188,
			"_do" => 6161,
			"ilo_ZP" => 6116,
			"ec" => 6116,
			"ci" => 6102,
			"tu" => 6062,
			"se_v" => 6021,
			"az" => 6006,
			"ik" => 5996,
			"e_b" => 5992,
			"oc" => 5988,
			"ru_p" => 5950,
			"i," => 5942,
			"o." => 5920,
			"lov" => 5898,
			"ki" => 5872,
			"ni_" => 5822,
			"o_do" => 5817,
			"tr" => 5794,
			"ne_" => 5788,
			"aj_" => 5760,
			"pri" => 5753,
			"_c" => 5752,
			"ca" => 5751,
			"_da_" => 5732,
			",_d" => 5701,
			"al_" => 5698,
			"bil" => 5658,
			"ti_" => 5646,
			"o," => 5638,
			"no_" => 5597,
			"z_" => 5573,
			"sr" => 5551,
			"_T" => 5548,
			"sem" => 5479,
			"ako" => 5454,
			"_ka" => 5452,
			"sl" => 5423,
			"m_za_" => 5354,
			"nja" => 5326,
			"pre" => 5325,
			"i,_" => 5287,
			"ev" => 5275,
			"cat_i" => 5260,
			"hel_" => 5250,
			"potni" => 5235,
			"jo" => 5227,
			"sp" => 5213,
			"_l" => 5193,
			"a," => 5192,
			",_da" => 5163,
			"ha" => 5159,
			"on" => 5147,
			"ez" => 5143,
			"so_" => 5121,
			"_sv" => 5112,
			"na_dus" => 5106,
			"sem_" => 5099,
			"t_" => 5071,
			"gl" => 5048,
			"o,_" => 5044,
			"s_" => 5029,
			"e,_" => 5008,
			"nashe" => 4979,
			"a,_" => 4968,
			"ud" => 4962,
			"_SP" => 4960,
			"_ve" => 4942,
			"ze" => 4933,
			"el_" => 4923,
			"da_mor" => 4917,
			"op" => 4901,
			"_ni" => 4872,
			"pe" => 4852,
			",_da_" => 4829,
			"_pri" => 4822,
			"_P" => 4811,
			"_ko" => 4778,
			"_bil" => 4747,
			"e_n" => 4741,
			"rav" => 4733,
			"d_" => 4703,
			"e_v" => 4700,
			"iz" => 4678,
			"da_ne_" => 4652,
			"a_j" => 4652,
			"pa_" => 4617,
			"ob_kat" => 4603,
			"im_" => 4586,
			"oz" => 4584,
			"gr" => 4560,
			"be" => 4528,
			"i_n" => 4511,
			"i_p" => 4511,
			"lo_" => 4507,
			"_me" => 4498,
			"padl" => 4488,
			"_pa_" => 4437,
			"shch" => 4434,
			"o,_d" => 4385,
			"_bo" => 4377,
			"efl" => 4349,
			"a_je_" => 4345,
			"e_z" => 4300,
			"da_bi_" => 4292,
			"i_na_k" => 4240,
			"zi" => 4213,
			"k_se" => 4190,
			"_bi_" => 4176,
			"jo_" => 4171,
			",_s" => 4156,
			"us" => 4154,
			"SPO" => 4137,
			"dr" => 4124,
			"a_je" => 4102,
			"_u" => 4080,
			"hevo" => 4072,
			"ij" => 4057,
			"ac" => 4021,
			"che" => 4001,
			"za_" => 3975,
			"nje" => 3947,
			"ali" => 3944,
			"ki_" => 3940,
			"e_je_" => 3898,
			"dn" => 3897,
			"_te" => 3846,
			"_ta" => 3843,
			"vs" => 3841,
			"ju" => 3818,
			"je_bil" => 3806,
			"vedel" => 3803,
			"_KA" => 3787,
			"ila_" => 3782,
			"n_s" => 3779,
			"i_v" => 3748,
			"a._" => 3729,
			"tim" => 3718,
			"i._" => 3706,
			"bi_" => 3706,
			"e_d" => 3697,
			"a_b" => 3613,
			"_h" => 3613,
			"ako_" => 3611,
			"ila" => 3605,
			"ove" => 3604,
			"e_bil" => 3603,
			"res" => 3603,
			"orcev" => 3600,
			"e." => 3598,
			"dragi_" => 3598,
			"ova" => 3595,
			"led" => 3553,
			"eseto_" => 3545,
			"pi" => 3536,
			"c_" => 3534,
			"se_j" => 3527,
			"ega_" => 3526,
			"di_" => 3522,
			"slov" => 3517,
			"o_v" => 3514,
			"a_k" => 3513,
			"_si" => 3510,
			"e_bi" => 3510,
			"je_b" => 3417,
			"_pace" => 3415,
			"ru" => 3413,
			"tak" => 3413,
			"e_t" => 3409,
			"k_" => 3373,
			"i_pa" => 3361,
			"_se_je" => 3354,
			"odo" => 3351,
			"raz" => 3347,
			"_Ni" => 3336,
			"ko,_v_" => 3323,
			"mi_" => 3315,
			"prav" => 3314,
			"e_m" => 3309,
			"to_" => 3308,
			"ati" => 3306,
			"rn" => 3299,
			"_ga_je" => 3298,
			"o._" => 3291,
			"je_s" => 3287,
			"_skozi" => 3285,
			"hel" => 3281,
			"mer,_s" => 3281,
			"riz" => 3264,
			"anj" => 3264,
			"o_n" => 3252,
			"je_gr" => 3251,
			"_ob" => 3248,
			"zn" => 3240,
			".:_T" => 3234,
			"_M" => 3232,
			"o_j" => 3217,
			"pos" => 3206,
			"l," => 3199,
			"lj_" => 3197,
			"_ne_" => 3186,
			"_mi" => 3185,
			"br" => 3183,
			"nik" => 3180,
			"id" => 3163,
			"zgo" => 3156,
			"._P" => 3150,
			"se_je" => 3147,
			"ob_" => 3139,
			"_ki_" => 3136,
			"oj_" => 3133,
			"_z_nag" => 3125,
			"."" => 3119,
			"_je_bi" => 3115,
			"ug" => 3105,
			"si_" => 3104,
			"e_pr" => 3101,
			"_sl" => 3095,
			"amido." => 3092,
			"ku" => 3079,
			"je_z" => 3070,
			"otni" => 3064,
			"_sk" => 3058,
			"._Pi" => 3043,
			"m_prej" => 3040,
			"nas" => 3030,
			"e_o" => 3018,
			"avim_" => 3013,
			"s,_da" => 2998,
			"l,_" => 2997,
			"eb" => 2986,
			"tili" => 2983,
			"zd" => 2978,
			"iti" => 2978,
			"u." => 2975,
			"rish" => 2970,
			"_re" => 2967,
			""_ve_p" => 2953,
			"m," => 2953,
			"kaj" => 2952,
			"_ga" => 2950,
			"a_t" => 2943,
			"ROCHIL" => 2940,
			"je_bi" => 2939,
			"enj" => 2933,
			"o_po" => 2927,
			"_"" => 2926,
			"ti._" => 2924,
			"apisal" => 2910,
			"_rezid" => 2908,
			"e_sem" => 2898,
			"ilo" => 2894,
			"_K" => 2893,
			"S.:_Te" => 2893,
			"esede_" => 2886,
			"da_s" => 2884,
			"_pa,_d" => 2879,
			"ter" => 2875,
			"eli" => 2871,
			"_je_b" => 2867,
			"voj" => 2857,
			"pace" => 2857,
			"udi" => 2856,
			"olan." => 2835,
			"_a" => 2833,
			"so_vs" => 2831,
			"i_je" => 2823,
			"ih_raz" => 2820,
			"r_je_" => 2818,
			"_iz" => 2807,
			"del" => 2798,
			"nichev" => 2797,
			"_R" => 2796,
			"._Kot" => 2793,
			"_dr" => 2786,
			"KAKO" => 2785,
			"_S" => 2783,
			"rat" => 2776,
			"hnem_z" => 2772,
			"mord" => 2771,
			"gache" => 2762,
			",_ke" => 2755,
			"ho" => 2753,
			"l_v" => 2749,
			"at_in" => 2747,
			"tovar" => 2746,
			"IVETI." => 2744,
			"_bil_" => 2742,
			"ska" => 2739,
			"ch" => 2735,
			"le_" => 2734,
			"gre" => 2731,
			"er_" => 2731,
			"traj_k" => 2728,
			"ta,_v_" => 2720,
			"drag" => 2717,
			"te_" => 2715,
			"om_" => 2713,
			"je," => 2712,
			"sto" => 2707,
			"m_s" => 2706,
			"_ves" => 2704,
			"eni" => 2702,
			"sledn" => 2691,
			"v_miru" => 2689,
			"pot" => 2685,
			"_sklad" => 2684,
			"ihop" => 2678,
			"ce_" => 2651,
			"i_je_" => 2650,
			"_ki" => 2642,
			"i_ni_" => 2635,
			"_nas" => 2627,
			",_p" => 2627,
			"jen" => 2624,
			"han" => 2624,
			"je_vs" => 2621,
			"pajo" => 2604,
			"a_Rot" => 2604,
			"ujen" => 2599,
			"vim_P" => 2596,
			"a_po" => 2595,
			"at_" => 2590,
			"._Sk" => 2588,
			"gla" => 2583,
			"ili" => 2578,
			"_to" => 2578,
			"_tak" => 2568,
			"nic" => 2567,
			"azil" => 2566,
			"_z_" => 2564,
			"_zapi" => 2563,
			"up" => 2561,
			"ena_" => 2560,
			"zh" => 2556,
			"._M" => 2556,
			"ujejo," => 2553,
			"atere" => 2549,
			"jujejo" => 2545,
			"avi" => 2543,
			"ja_" => 2542,
			"mo_" => 2542,
			"tav" => 2542,
			"sh,_s" => 2539,
			"v_bi_m" => 2534,
			"ir" => 2533,
			"cu" => 2529,
			"lo_dop" => 2528,
			"l." => 2527,
			"jenju" => 2526,
			"_Ven" => 2524,
			"o_so" => 2522
		);
	}
} // END OF LanguageMap_sl_ASCII

?>

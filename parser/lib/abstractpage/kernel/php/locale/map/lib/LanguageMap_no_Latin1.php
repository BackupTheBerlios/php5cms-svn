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
 
class LanguageMap_no_Latin1 extends LanguageMap
{
	/**
	 * Constructor
	 */
	function LanguageMap_no_Latin1()
	{
		$this->language = "no";
		$this->charset  = "latin1";
		
		$this->_populate();
	}
	
	
	// private methods
	
	function _populate()
	{
		$this->map = array(
			"e_" => 69074,
			"en" => 62892,
			"de" => 59202,
			"er" => 55803,
			"_s" => 52214,
			"r_" => 51232,
			"t_" => 48391,
			"n_" => 47236,
			"g_" => 36062,
			"et" => 34517,
			"_d" => 33617,
			"en_" => 30649,
			"er_" => 29832,
			"te" => 29461,
			"re" => 28201,
			"om_" => 27888,
			"an" => 27288,
			"_h" => 27288,
			"r_v" => 26077,
			"_de" => 25527,
			"_o" => 25434,
			"_e" => 25154,
			",_" => 24726,
			"ar" => 24029,
			"_f" => 23579,
			"le" => 23193,
			"ne" => 22244,
			"in" => 22199,
			"_a" => 22116,
			"om" => 21862,
			"og" => 21784,
			"et_" => 21764,
			"st" => 21755,
			"me" => 21710,
			"_m" => 21664,
			"so" => 21626,
			"nd" => 21432,
			"_i" => 20513,
			"a_" => 19689,
			"ge" => 19594,
			"_v" => 19382,
			"Elie" => 19307,
			"og_" => 19281,
			"let_N" => 18976,
			"._" => 18607,
			"_t" => 18336,
			"ke" => 17902,
			"de_" => 17704,
			"m_" => 17456,
			"_og" => 17141,
			"ve" => 17036,
			"_og_" => 16806,
			"ams_o" => 16769,
			"se" => 16716,
			"te._B" => 16572,
			"d_" => 16504,
			"ti" => 16244,
			"il" => 15770,
			"_ha" => 15533,
			"li" => 15054,
			"_b" => 14763,
			"sk" => 14702,
			"ha" => 14094,
			"eg" => 13929,
			"ig" => 13682,
			"_det" => 13347,
			"l_" => 13106,
			"s_" => 13073,
			"es" => 13052,
			"_k" => 12995,
			"al" => 12889,
			"at" => 12682,
			"_i_" => 12645,
			"is" => 12501,
			"ei" => 12283,
			"_g" => 12143,
			"den" => 12030,
			"fo" => 12009,
			"det" => 11977,
			"ra" => 11970,
			"nn" => 11774,
			"var" => 11750,
			",_bare" => 11701,
			"_me" => 11669,
			"tt" => 11586,
			"_Fr" => 11492,
			"ar_" => 11365,
			"ll" => 11284,
			"je" => 11036,
			"ns" => 10998,
			"for" => 10975,
			"_at" => 10866,
			"av" => 10817,
			"_so" => 10777,
			"va" => 10737,
			"ikke_" => 10698,
			"_l" => 10676,
			"vi" => 10590,
			"ik" => 10550,
			"nde" => 10307,
			"_for" => 10305,
			"ri" => 10146,
			"_g�" => 10053,
			"te_" => 9995,
			"un" => 9942,
			"r_h" => 9908,
			"ctrics" => 9860,
			"ta" => 9810,
			"det_" => 9662,
			"la" => 9621,
			"id" => 9591,
			"si" => 9588,
			"he" => 9498,
			"_fo" => 9470,
			"fyrddi" => 9420,
			"som_" => 9374,
			"il_" => 9317,
			"e_s" => 9303,
			"der" => 9206,
			"it" => 9181,
			"_kaldt" => 9170,
			""ne" => 9169,
			"som" => 9096,
			"_som" => 9033,
			"at_no" => 8786,
			"kk" => 8760,
			"ne_" => 8729,
			"Elie_S" => 8671,
			"n_det" => 8600,
			"_ti" => 8521,
			"sa" => 8482,
			"re_" => 8477,
			"s_ikke" => 8471,
			"an_" => 8344,
			"_S" => 8298,
			"gra" => 8256,
			"ka" => 8217,
			"_den" => 8197,
			"at_" => 8186,
			"_som_" => 8158,
			"_det_" => 8056,
			"ed_" => 8054,
			"den_" => 8048,
			"ing" => 7978,
			"am" => 7969,
			"_F" => 7959,
			"_jeg" => 7944,
			"ur" => 7849,
			"or_" => 7753,
			"rt" => 7710,
			"ke_" => 7570,
			"_en" => 7566,
			"da" => 7556,
			"_va" => 7539,
			"_til" => 7535,
			"ver" => 7502,
			"ste" => 7469,
			"ter" => 7430,
			"ls" => 7338,
			"v_" => 7272,
			"ld" => 7259,
			"_er_" => 7242,
			"_er" => 7229,
			"kke" => 7189,
			"_til_" => 7096,
			"tte" => 7094,
			"r_d" => 7077,
			"em" => 7075,
			"eg_" => 6987,
			"r_o" => 6951,
			"ke_ra" => 6950,
			"k_" => 6935,
			"ag" => 6905,
			"lske_T" => 6854,
			"na" => 6800,
			"med" => 6639,
			"_st" => 6578,
			"uske_" => 6566,
			"nge" => 6511,
			"_r" => 6507,
			"ale" => 6499,
			"_av" => 6467,
			"r_s" => 6460,
			"_tre_" => 6442,
			"end" => 6432,
			"ten" => 6378,
			"rs" => 6368,
			"_at_" => 6339,
			"�r" => 6331,
			"_han" => 6331,
			"op" => 6324,
			"te," => 6284,
			"n_s" => 6276,
			"_he" => 6274,
			"rd" => 6235,
			"lig" => 6208,
			"er_b�" => 6186,
			"e_inn" => 6171,
			"v_f" => 6158,
			"o_" => 6134,
			"_den_" => 6104,
			"orten_" => 6093,
			"ikk" => 6059,
			"ster._" => 6055,
			"lt" => 6042,
			"_sa" => 6037,
			"rekk" => 6010,
			"di" => 6000,
			"ad" => 5992,
			"_var" => 5950,
			"han" => 5944,
			"_vi" => 5863,
			"ko" => 5851,
			"slik" => 5837,
			"t_og_" => 5827,
			"t_s" => 5786,
			"-_" => 5773,
			"age" => 5750,
			"rne_fj" => 5744,
			"ga" => 5735,
			"_sk" => 5718,
			"tr" => 5710,
			"ter_" => 5706,
			"e," => 5705,
			"ie" => 5679,
			"ut" => 5657,
			"De" => 5653,
			"for_" => 5651,
			"_D" => 5646,
			"ellet_" => 5604,
			"_komm" => 5564,
			"fr" => 5541,
			"for_at" => 5536,
			"_se" => 5528,
			"rdi" => 5525,
			"ere" => 5516,
			"e,_" => 5461,
			"to" => 5453,
			"_var_" => 5422,
			"ge_" => 5415,
			"de_den" => 5411,
			"cs_ret" => 5398,
			"n_a" => 5381,
			"gen" => 5369,
			"top" => 5326,
			"ene_re" => 5320,
			"e_o" => 5318,
			"tting" => 5317,
			"mm" => 5305,
			"g_s" => 5295,
			"illiam" => 5291,
			"e,_i" => 5282,
			"bl" => 5275,
			"ig_" => 5262,
			"ene" => 5251,
			"_vi_vi" => 5247,
			"kt" => 5236,
			",_s" => 5229,
			"du" => 5225,
			"bart,_" => 5216,
			"ir_Cae" => 5214,
			"�r" => 5196,
			"i_den" => 5184,
			"_al" => 5176,
			"e_f" => 5101,
			"_H" => 5100,
			"as" => 5095,
			"sen" => 5086,
			"ov" => 5079,
			"nr�m" => 5072,
			"_je" => 5070,
			"kke_" => 5067,
			"r_a" => 5033,
			"tis" => 5020,
			"e_til" => 4993,
			"tene_o" => 4991,
			"jener" => 4986,
			"mi" => 4970,
			"t_rapp" => 4957,
			"_ve" => 4945,
			"._D" => 4921,
			"ens" => 4920,
			"ihet" => 4920,
			"i_�st" => 4915,
			"mme" => 4883,
			"en_s" => 4874,
			"kj" => 4871,
			"t," => 4869,
			"ni" => 4814,
			"_for_" => 4802,
			"od" => 4769,
			"lott,_" => 4760,
			"ter._T" => 4746,
			"lvt" => 4741,
			"e_h" => 4734,
			"_fr" => 4729,
			"_fram" => 4718,
			"pe" => 4712,
			"av_" => 4700,
			"eg_s" => 4685,
			"_M" => 4685,
			"enn" => 4676,
			"Tim_Wi" => 4673,
			"_av_" => 4672,
			"_vann" => 4663,
			"ren" => 4651,
			"r_sli" => 4646,
			"ak" => 4622,
			"tisk" => 4607,
			"og_s" => 4591,
			"_ik" => 4576,
			"pp" => 4576,
			"tter" => 4573,
			"ir_" => 4558,
			"t_e" => 4557,
			"gt" => 4553,
			"n_i" => 4544,
			"g_de" => 4535,
			"_De" => 4526,
			"en._" => 4525,
			"t_i" => 4522,
			"gg" => 4504,
			"sl" => 4490,
			"sj" => 4477,
			"ten_" => 4458,
			"ett" => 4455,
			"ik_" => 4434,
			"_T" => 4432,
			"n," => 4427,
			"t_v" => 4424,
			"r." => 4419,
			"ob" => 4410,
			"drei" => 4404,
			"os" => 4394,
			"r_e" => 4394,
			"_bl" => 4363,
			"_ikk" => 4357,
			"p�" => 4344,
			"_hu" => 4314,
			"e_av" => 4306,
			"e_d" => 4304,
			"nk" => 4301,
			"ikke" => 4299,
			"_innr" => 4276,
			"topp" => 4267,
			"ell" => 4258,
			"jo" => 4255,
			"kje" => 4253,
			"ende" => 4251,
			"_for_d" => 4251,
			"ric" => 4242,
			"ds" => 4239,
			"nde_" => 4238,
			"r_all" => 4230,
			"ske" => 4228,
			"_B" => 4228,
			"_all" => 4215,
			"ger" => 4213,
			"ske_in" => 4209,
			"est" => 4208,
			"over" => 4198,
			"fra" => 4190,
			"ukorre" => 4181,
			"es_" => 4176,
			"e_m" => 4173,
			"rer" => 4142,
			"_tros" => 4140,
			"e_t" => 4136,
			"_og_at" => 4129,
			"gs" => 4126,
			"rt_" => 4111,
			"eldig" => 4104,
			"ist" => 4101,
			"de_s" => 4095,
			"e_k" => 4092,
			"bare" => 4047,
			"ep" => 4042,
			"agsavi" => 4034,
			"rn" => 4021,
			"ur." => 4017,
			"e_p" => 4006,
			"rk" => 3990,
			"ove" => 3986,
			"_�" => 3985,
			"_de_" => 3985,
			"e._" => 3968,
			"e_i" => 3962,
			"ns_" => 3958,
			"e_i_" => 3918,
			"er_s" => 3914,
			"e_ha" => 3912,
			"et_e" => 3911,
			"_-_" => 3906,
			"_at_de" => 3884,
			"ust" => 3874,
			"llan" => 3874,
			"t_f" => 3863,
			"te_d" => 3855,
			"len" => 3849,
			"vo" => 3837,
			"ide" => 3831,
			"e_a" => 3822,
			"wy" => 3822,
			"_j" => 3813,
			"u_" => 3806,
			"_og_de" => 3790,
			"me,_i" => 3775,
			"og_de" => 3754,
			"_Og_ti" => 3750,
			"._Det_" => 3750,
			"t_o" => 3746,
			"Me" => 3741,
			"opp" => 3737,
			"els" => 3736,
			"t_N" => 3733,
			"_der" => 3732,
			"n_e" => 3725,
			"t." => 3714,
			"e_de" => 3714,
			"_et" => 3706,
			"t_d" => 3697,
			"oryen" => 3696,
			"n._" => 3695,
			"elle" => 3695,
			",_o" => 3694,
			"en_e" => 3693,
			"_li" => 3684,
			"rie" => 3677,
			"tte_" => 3674,
			"_be" => 3672,
			"bo_s" => 3670,
			"sikker" => 3666,
			"v_e" => 3661,
			"n,_" => 3659,
			"r,_" => 3656,
			"ors" => 3649,
			"fa" => 3645,
			"en." => 3644,
			"e_b" => 3640,
			"St" => 3633,
			"g_o" => 3605,
			"tilbak" => 3597,
			"e_ikk" => 3592,
			"ang" => 3591,
			"g_d" => 3583,
			"hol" => 3576,
			"et_ikk" => 3575,
			"g_h" => 3572,
			"_ikke_" => 3570,
			"_kom" => 3562,
			"mer" => 3558,
			"ce" => 3556,
			"_L" => 3555,
			"ette" => 3553,
			"nen" => 3549,
			"ap" => 3546,
			"ra_" => 3546,
			"n_o" => 3544,
			"ler_" => 3538,
			"t_de" => 3515,
			"kroke" => 3509,
			"hans_" => 3507,
			"_og_s" => 3506,
			"tagnsk" => 3506,
			""Nei_G" => 3499,
			"rekke" => 3496,
			"fe" => 3496,
			"-_ikke" => 3493,
			"n,_men" => 3491,
			"_mot_g" => 3487,
			"en," => 3483,
			"_G" => 3482,
			"porten" => 3481,
			"_el" => 3479,
			"en_f�" => 3474,
			"roblem" => 3471,
			"er_i" => 3465,
			"ers" => 3461,
			"lige" => 3461,
			"r_det" => 3445,
			"vil" => 3441,
			"Tre" => 3440,
			"frih" => 3439,
			"t_er_v" => 3437,
			"alis" => 3423,
			"�re" => 3421,
			"_ko" => 3414,
			"Og_d" => 3411,
			"er." => 3405,
			"tt_" => 3395,
			"g,_" => 3391,
			"gi" => 3385,
			"en_a" => 3380,
			"_Ge" => 3373,
			"at_rap" => 3372,
			"_Da" => 3371,
			"ind" => 3339,
			"_ikke" => 3337,
			"sse" => 3333,
			"nen..." => 3332
		);
	}
} // END OF LanguageMap_no_Latin1

?>

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
 
class LanguageMap_el_Greek extends LanguageMap
{
	/**
	 * Constructor
	 */
	function LanguageMap_el_Greek()
	{
		$this->language = "el";
		$this->charset  = "greek";
		
		$this->_populate();
	}
	
	
	// private methods
	
	function _populate()
	{
		$this->map = array(
			"�_" => 52832,
			"_�" => 46030,
			"�_" => 32741,
			"�_" => 30888,
			"�_���_" => 27211,
			"��" => 24843,
			"_�" => 23664,
			"�_����" => 21833,
			"_�" => 21557,
			"��" => 21132,
			"��" => 20137,
			"��" => 19992,
			"_�" => 19700,
			"���" => 19117,
			"_��" => 18983,
			"_�" => 18357,
			"�_" => 17846,
			"�_" => 17721,
			"��_" => 16963,
			",_" => 16698,
			"��" => 16537,
			"��" => 16490,
			"���" => 16171,
			"��" => 15547,
			"����" => 15150,
			"����" => 15032,
			"_�" => 14776,
			"�_��" => 14723,
			"_��" => 14696,
			"��" => 14057,
			"��" => 13303,
			"��_" => 12926,
			"��" => 12779,
			"������" => 12534,
			"���" => 12232,
			"��" => 11982,
			"���_" => 11873,
			"��" => 11779,
			"_�" => 11712,
			"��_(�" => 11231,
			"�_" => 11167,
			"��" => 11141,
			"���" => 10784,
			"�_" => 10658,
			"_���_" => 10431,
			"����" => 10355,
			"������" => 10123,
			"��" => 10006,
			"_���" => 9965,
			"��" => 9940,
			"�_" => 9809,
			"_�����" => 9713,
			"��_VI_" => 9647,
			"._" => 9635,
			"_���_" => 9633,
			"���" => 9514,
			"��" => 9512,
			"�_" => 9357,
			"�_" => 9273,
			"���_" => 9245,
			"_���" => 9168,
			"��" => 9150,
			"��" => 8997,
			"��" => 8910,
			"��" => 8811,
			"�_�" => 8800,
			"��" => 8757,
			"��" => 8738,
			"��" => 8563,
			"��" => 8445,
			"_��" => 8434,
			"��_" => 8414,
			"��" => 8218,
			"���_" => 8174,
			"����_" => 8131,
			"��" => 8037,
			".12_" => 7804,
			"_��" => 7761,
			"_��" => 7714,
			"��" => 7637,
			"�_�" => 7630,
			"��" => 7558,
			"�_����" => 7525,
			"_����" => 7491,
			"��_" => 7456,
			"��" => 7451,
			"����_" => 7357,
			"_��" => 7171,
			"�����" => 7155,
			"����" => 7128,
			"��_" => 7104,
			"����_" => 7092,
			"������" => 7038,
			"��" => 7019,
			"��," => 6971,
			"���" => 6916,
			"��" => 6861,
			"��" => 6843,
			"����" => 6835,
			"���_" => 6812,
			"����" => 6785,
			"��" => 6772,
			"��" => 6659,
			"_��" => 6643,
			"��" => 6598,
			"�����" => 6547,
			"������" => 6514,
			"���_��" => 6505,
			"��" => 6439,
			"�_" => 6375,
			"��" => 6342,
			"����_-" => 6267,
			"��_" => 6253,
			"����" => 6236,
			"*_�" => 6223,
			"��" => 6214,
			"�_�" => 6183,
			"���" => 6180,
			"������" => 6145,
			"_�" => 6119,
			"_�" => 6097,
			"����" => 6023,
			"�_�" => 6006,
			"��" => 5984,
			"�����" => 5968,
			"��" => 5948,
			"�_" => 5908,
			"_�����" => 5896,
			"��_" => 5861,
			"��" => 5853,
			"��" => 5839,
			"��" => 5835,
			"������" => 5798,
			"��" => 5798,
			"��" => 5787,
			"_�" => 5785,
			"��" => 5782,
			"*_���" => 5756,
			"_�" => 5726,
			"�_�" => 5705,
			"�," => 5688,
			"�_���_" => 5667,
			"����" => 5666,
			"�����" => 5666,
			"_��" => 5648,
			"�_���_" => 5630,
			"��" => 5626,
			"����" => 5623,
			"�����" => 5576,
			"�_�" => 5574,
			"��_�" => 5574,
			"������" => 5524,
			"��_��" => 5509,
			"_���" => 5478,
			"���" => 5418,
			"_����" => 5391,
			"���" => 5314,
			"���_�" => 5312,
			"_��" => 5301,
			"��" => 5289,
			"�_��" => 5276,
			"����_" => 5219,
			"��" => 5195,
			"��" => 5191,
			"��_" => 5169,
			"����" => 5143,
			"�_�" => 5074,
			"���" => 5072,
			"�_���" => 5062,
			"��" => 5024,
			"��" => 4982,
			"�,_" => 4963,
			"�_��" => 4954,
			"_�_��" => 4921,
			"*_���" => 4883,
			"�_�" => 4882,
			"�_�" => 4880,
			"�_�" => 4861,
			"���_" => 4839,
			"��" => 4797,
			"��" => 4782,
			"��_" => 4773,
			"��_���" => 4758,
			"�_��" => 4741,
			"���._�" => 4732,
			"��" => 4729,
			"������" => 4723,
			"���_" => 4690,
			"���" => 4681,
			"��" => 4676,
			"���" => 4660,
			"��" => 4659,
			"�_�" => 4658,
			"��" => 4656,
			"�_����" => 4653,
			"���_" => 4635,
			"_��" => 4631,
			"��" => 4629,
			"��_" => 4620,
			"��" => 4612,
			"���" => 4576,
			"_���" => 4575,
			"�_���" => 4572,
			"_���" => 4565,
			"��" => 4536,
			"���" => 4522,
			"��" => 4515,
			"��" => 4489,
			"��" => 4455,
			"���_�" => 4453,
			"�����" => 4445,
			"�����" => 4431,
			"��" => 4429,
			"��_" => 4425,
			"��_��" => 4422,
			"��_" => 4417,
			"_�" => 4415,
			"���" => 4413,
			"���" => 4396,
			"_����" => 4389,
			"�����_" => 4367,
			"����" => 4343,
			"���" => 4329,
			"_��" => 4322,
			"��" => 4303,
			"���_�" => 4291,
			"���" => 4270,
			"������" => 4262,
			"_��" => 4242,
			"��_" => 4227,
			"��" => 4223,
			"_�����" => 4221,
			"�_" => 4211,
			"��" => 4193,
			"�����" => 4183,
			"�����" => 4147,
			"���" => 4145,
			"_�" => 4143,
			"��" => 4142,
			"_��" => 4138,
			"�_����" => 4135,
			"��" => 4132,
			"������" => 4126,
			"���" => 4113,
			"_��_�" => 4099,
			"����" => 4096,
			"��" => 4094,
			"��" => 4086,
			"������" => 4082,
			"�_�" => 4080,
			"��_" => 4064,
			"�_�" => 4053,
			"���" => 4052,
			"�����" => 4041,
			"��" => 4041,
			"��" => 4035,
			"��" => 4033,
			"��_��" => 4030,
			"���_��" => 4001,
			"����_�" => 3989,
			"���_" => 3984,
			"�_��" => 3976,
			"���_�" => 3976,
			"_��" => 3970,
			"�_��_�" => 3969,
			"��" => 3955,
			"_��" => 3946,
			"��" => 3939,
			"����_�" => 3937,
			"�_�" => 3930,
			"��" => 3915,
			"���" => 3904,
			"��" => 3893,
			"��_�" => 3884,
			"����" => 3875,
			"�����" => 3865,
			"��" => 3854,
			"��" => 3829,
			"�����" => 3823,
			"_���" => 3821,
			"����" => 3820,
			"����" => 3801,
			"��" => 3795,
			"�_���" => 3780,
			"_�" => 3779,
			"��" => 3779,
			"��" => 3770,
			"��_���" => 3765,
			"��_�" => 3753,
			"��" => 3753,
			"��" => 3753,
			"��" => 3753,
			"������" => 3744,
			"���" => 3730,
			"��_" => 3729,
			"_��_" => 3724,
			"���" => 3715,
			"��" => 3713,
			"��" => 3710,
			"���" => 3708,
			"�_�" => 3706,
			"���" => 3702,
			"��" => 3702,
			"_��" => 3687,
			"��_" => 3685,
			"�_�" => 3685,
			"�����" => 3682,
			"���" => 3676,
			"���_��" => 3662,
			"��" => 3649,
			"�_��" => 3648,
			"�.�.)" => 3646,
			"_����" => 3645,
			"��" => 3635,
			"������" => 3634,
			"�_���" => 3632,
			"���_�" => 3632,
			"��" => 3620,
			"�_�" => 3601,
			"��" => 3601,
			"��" => 3587,
			"��_�" => 3576,
			"���" => 3569,
			"��" => 3562,
			"�,_�_" => 3557,
			"���_" => 3556,
			"��" => 3548,
			"����_�" => 3547,
			"_���_" => 3545,
			"*_��" => 3542,
			"_����_" => 3537,
			"�_�" => 3529,
			"���" => 3521,
			"����" => 3519,
			"����_�" => 3516,
			"�_�" => 3511,
			"����" => 3509,
			"��_" => 3508,
			"���_" => 3497,
			"_��_��" => 3492,
			"���" => 3483,
			"�����" => 3482,
			"��_" => 3464,
			"_���_" => 3456,
			"_�" => 3449,
			"_���" => 3447,
			"������" => 3422,
			"��" => 3415,
			"���" => 3412,
			"��" => 3409,
			"��" => 3399,
			"������" => 3390,
			"_�" => 3383,
			"���_�" => 3375,
			"_���" => 3374,
			"����" => 3366,
			"_�_" => 3362,
			"��_���" => 3360,
			"_�" => 3355,
			"_�" => 3350,
			"���" => 3342,
			"���" => 3342,
			"_��" => 3336,
			"��" => 3334,
			"���" => 3323,
			"�_��" => 3299,
			"��" => 3279,
			"�����" => 3273,
			"��" => 3267,
			"������" => 3263,
			"��" => 3261,
			"���" => 3258,
			"����" => 3225,
			"���" => 3219,
			"���_�" => 3205,
			"_���" => 3201,
			"_��)" => 3200,
			"�_����" => 3189,
			"_�" => 3182,
			"��" => 3181,
			"�����" => 3180,
			"_����" => 3179,
			"_��" => 3169,
			"�_�" => 3168,
			"*_�" => 3167,
			"��" => 3166,
			"�_����" => 3158,
			"_��" => 3158,
			"��" => 3155,
			"_����" => 3143,
			"���_��" => 3140,
			"��" => 3135,
			"���" => 3132,
			"������" => 3128,
			"��_�" => 3113,
			"_�_" => 3113,
			"���" => 3109,
			"�����_" => 3102,
			"_��_��" => 3091,
			"��_���" => 3090,
			"��_�" => 3084,
			"��_�" => 3075,
			"���_" => 3074,
			"���" => 3070,
			"���" => 3063,
			"��" => 3054,
			"��" => 3052,
			"����_" => 3049,
			"���_��" => 3048,
			"_��" => 3043,
			"_��_" => 3041,
			"�����" => 3037,
			"�_��" => 3025,
			"���" => 3019,
			"���_�" => 3015,
			"���" => 3012,
			"���" => 3011,
			"�����" => 3010,
			"��" => 3008,
			"���_�" => 3008,
			"���" => 3007,
			"���_�" => 3004,
			"���" => 3001,
			"�_�" => 2995,
			"_���_�" => 2984,
			"��" => 2982,
			"_��" => 2954,
			"�_��" => 2953,
			"���" => 2950,
			"_���" => 2944,
			"����" => 2942,
			"���" => 2936,
			"��" => 2935,
			"�����" => 2933,
			"�_(�" => 2928,
			"��,_��" => 2925,
			"�����_" => 2924,
			"���" => 2922,
			"���" => 2921,
			"�_����" => 2910,
			"���" => 2902,
			"��" => 2901,
			"���" => 2898,
			"��" => 2891,
			"_���" => 2882,
			"������" => 2875,
			"�_�" => 2865,
			"_��" => 2857,
			"��_��" => 2855,
			"������" => 2854,
			"��" => 2838,
			"�,_" => 2837,
			"_��" => 2837,
			"��" => 2833,
			"_����" => 2832,
			"���" => 2828,
			"����" => 2822,
			"��" => 2816,
			"�_���" => 2813,
			"�_�" => 2812,
			"������" => 2810,
			"�����" => 2805,
			"��" => 2805,
			"��" => 2801,
			"�_���" => 2799,
			"������" => 2795,
			"��" => 2789,
			"��" => 2787,
			"�_�" => 2786,
			"�_�" => 2786,
			"_�" => 2784,
			"IIIA_�" => 2777,
			"_���_�" => 2775,
			"�_��" => 2769,
			"_��" => 2765,
			"*_���" => 2759,
			"���" => 2757,
			"��_" => 2754,
			"������" => 2753,
			"_���" => 2750,
			"����" => 2744,
			"��" => 2730,
			"_��" => 2724,
			"��" => 2713,
			"���_" => 2709,
			"����_" => 2705,
			"��_��" => 2705,
			"���" => 2704,
			"����" => 2704,
			"���" => 2701,
			"���" => 2701,
			"�_��" => 2700,
			"�_�" => 2695,
			"�_��" => 2689,
			"������" => 2681,
			"����_" => 2680,
			"��_�_" => 2666,
			"�," => 2664,
			"��_��_" => 2657,
			"_�_�" => 2656
		);
	}
} // END OF LanguageMap_el_Greek

?>

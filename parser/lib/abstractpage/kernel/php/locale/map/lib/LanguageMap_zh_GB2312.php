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
 
class LanguageMap_zh_GB2312 extends LanguageMap
{
	/**
	 * Constructor
	 */
	function LanguageMap_zh_GB2312()
	{
		$this->language = "zh";
		$this->charset  = "gb2312";
		
		$this->_populate();
	}
	
	
	// private methods
	
	function _populate()
	{
		$this->map = array(
			"，" => 79368,
			"1/" => 70202,
			"/4" => 57723,
			"的" => 54642,
			"。" => 46273,
			"1/2" => 44946,
			"/2" => 40782,
			"　" => 39131,
			"1/4" => 31077,
			"3/" => 29039,
			"璤" => 28961,
			"3/4" => 28828,
			"一" => 23146,
			"Ａ艘" => 21955,
			"我" => 20653,
			"了" => 20344,
			"不" => 18459,
			"是" => 17936,
			"”" => 16883,
			"“" => 16828,
			"一�" => 16160,
			"在" => 15148,
			"离开" => 14941,
			"他" => 14351,
			"有" => 13582,
			"国�" => 12180,
			"中" => 12156,
			"说" => 11252,
			"岢雒姹" => 11068,
			"r)" => 11040,
			"(r" => 10967,
			"们" => 10858,
			"来" => 10832,
			"这" => 10693,
			"" => 10615,
			"且�" => 10183,
			"上" => 10082,
			"" => 10056,
			"要" => 9316,
			"个" => 9294,
			"" => 9007,
			"(c)" => 8889,
			"，�" => 8834,
			"、" => 8834,
			"�1" => 8727,
			"�1/" => 8616,
			"乙蚬" => 8519,
			"谝�3" => 8512,
			"(c" => 8494,
			"岷" => 8398,
			"以" => 8372,
			"也" => 8354,
			"？" => 8298,
			"时" => 8175,
			"，1/" => 8005,
			"缺德的" => 7941,
			"保" => 7872,
			"/4�" => 7784,
			"担" => 7775,
			"�1/2邮" => 7769,
			"“�" => 7716,
			"你" => 7703,
			"了一�" => 7673,
			"。�" => 7593,
			"为" => 7452,
			"！�" => 7419,
			"Ｎ" => 7277,
			"_�" => 7151,
			"毛" => 7089,
			"对" => 6969,
			"恕" => 6952,
			"，�" => 6937,
			"" => 6899,
			"瘢" => 6883,
			"当" => 6878,
			"麽" => 6870,
			"耍" => 6868,
			"里" => 6839,
			"去" => 6744,
			"下" => 6708,
			"我�" => 6693,
			"4�" => 6677,
			"按" => 6627,
			"�" => 6603,
			"会" => 6558,
			"1/2！" => 6555,
			"出" => 6520,
			"" => 6513,
			"子" => 6505,
			"，�" => 6483,
			"了，" => 6379,
			"还" => 6350,
			"。”" => 6347,
			"年" => 6319,
			"，�" => 6315,
			"党" => 6289,
			"挥" => 6129,
			"桓�" => 6109,
			"”_" => 6070,
			"" => 6024,
			"3/4�" => 6022,
			"前消" => 6000,
			"说�" => 5920,
			"希�" => 5903,
			"盻" => 5875,
			"党员”" => 5823,
			"颐" => 5823,
			"了。" => 5820,
			"�1" => 5797,
			"嵋�" => 5780,
			"不�" => 5768,
			"恕�" => 5710,
			"骸�" => 5697,
			"" => 5675,
			"长" => 5662,
			"矍跋" => 5645,
			"：“" => 5644,
			"男" => 5592,
			"知" => 5570,
			"耍�" => 5568,
			"鎋这�" => 5527,
			"能" => 5511,
			"�3/" => 5507,
			"_“" => 5461,
			"频" => 5392,
			"道，" => 5386,
			"膊" => 5377,
			"c)有�" => 5364,
			"薄奥碇" => 5354,
			"，我" => 5349,
			"一个" => 5340,
			"梦�1/" => 5337,
			"蛋盐野" => 5336,
			"小" => 5314,
			"说：�" => 5306,
			"" => 5300,
			"" => 5294,
			"从" => 5289,
			"" => 5276,
			"忠�1" => 5269,
			"崾焙蛳" => 5184,
			"" => 5179,
			"*1/2" => 5146,
			"" => 5138,
			"可" => 5123,
			"" => 5115,
			"前训" => 5103,
			"�" => 5097,
			"竿" => 5060,
			"过" => 5049,
			"/4绦�" => 5029,
			"" => 5018,
			"1/4�" => 5005,
			"们一开" => 4988,
			"拇" => 4966,
			"了�" => 4965,
			"哪且" => 4964,
			"，�" => 4945,
			"心" => 4924,
			"以前" => 4923,
			"，1" => 4859,
			"谋" => 4849,
			"崃" => 4846,
			"鹤" => 4840,
			"逡巫" => 4837,
			"墒�" => 4833,
			"诔�" => 4819,
			"2剐�" => 4817,
			"牵" => 4809,
			"睿" => 4808,
			"薄" => 4804,
			"自" => 4800,
			"了�" => 4771,
			"重演�" => 4770,
			"�(r)" => 4766,
			"开�1" => 4757,
			"�" => 4750,
			"还�" => 4746,
			"_我的�" => 4739,
			"事" => 4738,
			"4绦�" => 4737,
			"开" => 4728,
			"�" => 4727,
			"唷" => 4720,
			"岽" => 4695,
			"别" => 4692,
			"/2" => 4688,
			"" => 4684,
			"�1/" => 4677,
			"第一" => 4675,
			"，�" => 4667,
			"澳" => 4666,
			"土硗庖" => 4632,
			"，3/" => 4626,
			"保�" => 4625,
			"�1/" => 4619,
			"出面�" => 4600,
			"日" => 4586,
			"担骸�" => 4585,
			"幔" => 4583,
			"岬�" => 4579,
			"/4�" => 4562,
			"：“�" => 4551,
			"荨" => 4550,
			"话" => 4527,
			"成" => 4511,
			"回" => 4504,
			"な�" => 4482,
			"拢" => 4457,
			"臺能" => 4453,
			"二" => 4442,
			"诹�" => 4442,
			"道" => 4440,
			"把" => 4439,
			"苋琛Ｄ" => 4437,
			"面" => 4437,
			"幸" => 4435,
			"1/4被" => 4422,
			"前" => 4420,
			"�1/4" => 4419,
			"" => 4418,
			"岢" => 4413,
			"" => 4406,
			"*1" => 4403,
			"，�" => 4393,
			"厥" => 4387,
			"" => 4382,
			"荨�" => 4378,
			"被" => 4377,
			"�*凑" => 4377,
			"黄" => 4366,
			"皇" => 4365,
			"用�" => 4354,
			"苟�1" => 4329,
			"庋�3" => 4316,
			"氖" => 4313,
			"锹" => 4295,
			"。�" => 4262,
			"挪用�" => 4258,
			"难" => 4239,
			"Ｄ" => 4225,
			"，他" => 4209,
			"国�*�" => 4202,
			"4�" => 4201,
			"汉�" => 4188,
			"模" => 4182,
			"只" => 4176,
			"な�3/4" => 4168,
			"略" => 4163,
			"ㄑ�" => 4161,
			"鹦" => 4160,
			"友矍跋" => 4157,
			"把凳�" => 4149,
			"利�1/2" => 4145,
			"竦" => 4144,
			"，�" => 4142,
			"拿�" => 4131,
			"公" => 4129,
			"守�" => 4122,
			"�*�" => 4120,
			"的�" => 4115,
			"�" => 4111,
			"实" => 4105,
			"，�" => 4101,
			"消失了" => 4098,
			"" => 4095,
			"习逡" => 4071,
			"脑袋" => 4065,
			"悴灰" => 4059,
			"亿元窟" => 4053,
			"欢" => 4048,
			"的合*�" => 4046,
			"何�" => 4044,
			"ざ" => 4040,
			"黑" => 4018,
			"以" => 3991,
			"校" => 3989,
			"他�" => 3987,
			"蚬芾�" => 3984,
			"彩悄" => 3976,
			"*1/" => 3971,
			"他们" => 3969,
			"蟛1" => 3961,
			"铀" => 3955,
			"4颓" => 3953,
			"打" => 3953,
			"挥�" => 3943,
			"冢�" => 3939,
			"频目�" => 3934,
			"�(r)" => 3925,
			"坏" => 3922,
			"他从" => 3918,
			"煸" => 3914,
			"像" => 3914,
			"八" => 3909,
			"在�" => 3896,
			"前�" => 3895,
			"娜" => 3893,
			"里面�" => 3889,
			"鲈" => 3888,
			"是坦" => 3886,
			"不过�" => 3880,
			"等" => 3878,
			"哪" => 3877,
			"人�" => 3874,
			"�(" => 3872,
			"档墓磺" => 3862,
			"�3" => 3860,
			"现" => 3856,
			"挥�" => 3847,
			"虎和" => 3844,
			"　盻�" => 3844,
			"俊" => 3836,
			"冢�" => 3835,
			"4绦" => 3832,
			"题！" => 3829,
			"" => 3825,
			"1/4被�" => 3825,
			"幌" => 3824,
			"拖" => 3823,
			"背" => 3816,
			"�1/4" => 3815,
			"堑" => 3813,
			"的�" => 3813,
			"灰" => 3803,
			"闹" => 3789,
			"给" => 3787,
			"冶" => 3783,
			"4�" => 3778,
			"！盻" => 3767,
			"Ｎ�" => 3760,
			"1/2" => 3751,
			"艹" => 3748,
			"�1" => 3734,
			"" => 3718,
			"八氖" => 3711,
			"重3/" => 3707,
			"希�" => 3705,
			"正" => 3696,
			"�1/" => 3692,
			"铩“" => 3690,
			"倨鹚" => 3688,
			"樱" => 3685,
			"叵�" => 3684,
			"/2�" => 3684,
			"次" => 3684,
			"锹�" => 3679,
			"岷�" => 3675,
			"�*�" => 3672,
			"�1" => 3671,
			"3/4�" => 3666,
			"惶" => 3661,
			"三" => 3660,
			"淮" => 3658,
			"�*1/2" => 3653,
			"。_“" => 3651,
			"Ｄ悖" => 3650,
			"悴�" => 3647,
			"亿根" => 3636,
			"澹�" => 3636,
			"�1" => 3629,
			"焉用�" => 3619,
			"岷" => 3618,
			"什" => 3614,
			"黄岷" => 3609,
			"因管理" => 3603,
			"待�" => 3599,
			"" => 3592,
			"婺�" => 3591,
			"全" => 3583,
			"名" => 3582,
			"且�" => 3577,
			"矗�1/" => 3570,
			"蛋" => 3570,
			"把一�" => 3568,
			"高" => 3565,
			"胰" => 3562,
			"无" => 3553,
			"�1/" => 3553,
			"4�1" => 3549,
			"谀阏馐" => 3546,
			"”_�" => 3540,
			"虎" => 3540,
			"？�" => 3539,
			"常" => 3538,
			"了华�" => 3537,
			"政" => 3537,
			"用" => 3534,
			"去�" => 3526,
			"/4�" => 3526,
			"去�" => 3525,
			"百" => 3519,
			"�" => 3517,
			"∮�" => 3517,
			"话�" => 3510,
			"分" => 3508,
			"ィ" => 3507,
			"第一次" => 3496,
			"，�" => 3493,
			"盻�" => 3492,
			"爸" => 3488,
			"�3/" => 3488,
			"陌⑺暮" => 3487,
			"定" => 3486,
			"" => 3479,
			"的眼�" => 3479,
			"车" => 3476,
			"�" => 3473,
			"保" => 3473,
			"琠" => 3472,
			"/4倨鹚" => 3472,
			"去说" => 3465,
			"乱蹦" => 3460,
			"�" => 3460,
			"≡" => 3459,
			"煌" => 3459,
			"活动。" => 3458,
			"蚬�" => 3457,
			"没�" => 3456,
			"3/4仆" => 3454,
			"纱�" => 3454,
			"" => 3453,
			"頮终�" => 3450,
			"的�" => 3449,
			"硕1/4" => 3447,
			"4�" => 3446,
			"辏" => 3442,
			"！”_" => 3440,
			"族”�" => 3434,
			"唬" => 3432,
			"，我3/" => 3430,
			"数字一" => 3428,
			"庖" => 3426,
			"�" => 3422,
			"知道�" => 3421,
			"。华" => 3416,
			"，�" => 3415,
			"我3/4�" => 3410,
			"不共�" => 3409,
			"再" => 3405,
			"谝" => 3404,
			"愕男彰" => 3401,
			"太" => 3399,
			"凳�" => 3398,
			"檬堋�" => 3396,
			"的阿四" => 3394,
			"，往" => 3391,
			"/2�" => 3391,
			"矗" => 3390,
			"溃" => 3383,
			"族”" => 3382,
			"勖抢献" => 3374,
			"⑹歉" => 3373,
			"" => 3373,
			"铀" => 3369,
			"个小" => 3365,
			"样" => 3365,
			"" => 3365,
			"的�" => 3359,
			"矗�" => 3359,
			"沼" => 3355,
			"鹬" => 3355,
			"，�" => 3355,
			"" => 3355,
			"/2�" => 3353,
			"宜档墓" => 3348,
			"。”_" => 3345,
			"盏恼" => 3343,
			"在*" => 3341,
			"/2他�*" => 3337,
			"至" => 3335,
			"梦�" => 3333,
			"" => 3325,
			"1/2行�" => 3324,
			"仓" => 3318,
			"惶欣" => 3318,
			"1/2�" => 3316,
			"矗" => 3316,
			"拔" => 3315,
			"四和�" => 3314,
			"矍爸" => 3313,
			"�1/" => 3311,
			"勖" => 3310,
			"*了�" => 3309,
			"遥" => 3308,
			"铩" => 3305,
			"沃�" => 3305,
			"岢�" => 3305,
			"恼" => 3301,
			"面前，" => 3301,
			"/4�" => 3300,
			"�" => 3300,
			"铮" => 3299,
			"矍耙" => 3297,
			"厚�" => 3296,
			"终" => 3296,
			"面拿东" => 3296,
			"陈" => 3294,
			"为�" => 3293,
			"闱" => 3290
		);
	}
} // END OF LanguageMap_zh_GB2312

?>

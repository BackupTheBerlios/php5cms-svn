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
 
class LanguageMap_ru_CP1251 extends LanguageMap
{
	/**
	 * Constructor
	 */
	function LanguageMap_ru_CP1251()
	{
		$this->language = "ru";
		$this->charset  = "cp1251";
		
		$this->_populate();
	}
	
	
	// private methods
	
	function _populate()
	{
		$this->map = array(
			"_" => 21836,
			"�" => 5818,
			"�" => 4506,
			"�" => 4258,
			"�" => 3769,
			"�" => 3394,
			"�" => 3254,
			"�" => 2594,
			"�" => 2470,
			"�" => 2346,
			"�" => 2227,
			"�" => 1798,
			"�" => 1709,
			"�" => 1673,
			"�" => 1638,
			"�" => 1377,
			"," => 1187,
			",_" => 1172,
			"�_" => 1160,
			"�" => 1059,
			"�" => 1024,
			"�" => 1006,
			"�_" => 966,
			"�" => 945,
			"_�" => 937,
			"�" => 910,
			"_�" => 907,
			"�_" => 896,
			"��" => 878,
			"�" => 856,
			"_�" => 828,
			"_�" => 818,
			"�_" => 801,
			"�" => 795,
			"." => 789,
			"._" => 761,
			"��" => 655,
			"��" => 617,
			"_�" => 616,
			"��" => 575,
			"�" => 568,
			"�" => 565,
			"��" => 549,
			"��" => 526,
			"��" => 524,
			"�_" => 522,
			"_�" => 521,
			"_�" => 497,
			"_�" => 496,
			"�" => 484,
			"_��" => 477,
			"_�" => 477,
			"��" => 472,
			"��" => 470,
			"�_" => 461,
			"��" => 456,
			"��" => 454,
			"��" => 452,
			"��" => 432,
			"��" => 431,
			"��" => 425,
			"�" => 413,
			"��" => 403,
			"��" => 402,
			"_�" => 396,
			"_�" => 396,
			"��" => 395,
			"��" => 388,
			"��_" => 384,
			"�_" => 383,
			"_��" => 378,
			"_�_" => 375,
			"��" => 369,
			"��" => 367,
			"_��" => 365,
			"�_" => 364,
			"��" => 363,
			"��" => 357,
			"�_" => 357,
			"��" => 353,
			"��" => 352,
			"��" => 350,
			"��" => 344,
			"��" => 344,
			"�_" => 343,
			"��" => 338,
			"��" => 337,
			"��" => 334,
			"��" => 330,
			"��" => 328,
			"��" => 326,
			"��" => 326,
			"��" => 323,
			"-" => 322,
			"��" => 320,
			"��" => 317,
			"�" => 312,
			"��" => 308,
			"��" => 308,
			"�_" => 302,
			"_�" => 299,
			"�_" => 296,
			"��" => 292,
			"��_" => 292,
			"��" => 291,
			"��" => 291,
			"_�" => 291,
			"��" => 286,
			"��" => 283,
			"��" => 283,
			"_��" => 274,
			"��" => 264,
			"��" => 263,
			"�_" => 260,
			"��" => 253,
			"_-" => 245,
			"_�" => 245,
			"�_" => 244,
			"��" => 240,
			"��_" => 238,
			"��" => 235,
			"_��_" => 235,
			"��" => 233,
			"��" => 229,
			"-_" => 225,
			"��" => 219,
			"_�_" => 217,
			"��" => 217,
			"��" => 215,
			"��" => 215,
			"��" => 211,
			"��" => 211,
			"��" => 211,
			"��" => 209,
			"��_" => 209,
			"��" => 208,
			"_�" => 206,
			"�" => 206,
			"��" => 205,
			"�" => 205,
			"���" => 203,
			"��" => 203,
			"��" => 202,
			"_-_" => 202,
			"_��" => 199,
			"_���" => 199,
			"��" => 197,
			"��" => 197,
			"��_" => 197,
			"_�" => 195,
			"��" => 192,
			"_�" => 191,
			"��" => 190,
			"��" => 190,
			"��" => 188,
			"��" => 188,
			"��" => 185,
			"_��" => 184,
			"_�" => 183,
			"��" => 182,
			"_��" => 181,
			"��" => 175,
			"�" => 174,
			"_��" => 172,
			"��" => 170,
			"_�" => 169,
			"��_" => 168,
			"!" => 168,
			"���_" => 165,
			"��" => 165,
			"��_" => 164,
			"��" => 164,
			"�_" => 164,
			"��" => 163,
			"���" => 163,
			"�" => 162,
			"_���_" => 162,
			"_��" => 159,
			"��" => 158,
			"��" => 157,
			"��" => 156,
			"��" => 154,
			"��" => 154,
			"���" => 153,
			"��" => 153,
			"��" => 153,
			"��" => 153,
			"��" => 152,
			"��" => 150,
			"_��_" => 149,
			"�_" => 148,
			"��" => 148,
			"_��" => 146,
			"��_" => 146,
			"��_" => 146,
			"_�" => 144,
			"���" => 143,
			"��" => 141,
			"�_" => 141,
			"��_" => 141,
			"!_" => 139,
			"��" => 139,
			"��" => 139,
			"��" => 137,
			"���" => 136,
			"���" => 136,
			"���_" => 135,
			"_��" => 134,
			"_��" => 131,
			"��" => 131,
			"���" => 130,
			"��_" => 129,
			"�," => 128,
			"_��" => 128,
			"�,_" => 127,
			"��" => 127,
			"��" => 126,
			"��" => 126,
			"_��" => 126,
			"��" => 125,
			"��" => 125,
			"�" => 124,
			"��" => 123,
			"��" => 122,
			"_��" => 122,
			"_��" => 121,
			"_��" => 120,
			"���" => 120,
			"��" => 120,
			"��" => 120,
			"_�" => 119,
			"���" => 119,
			"���" => 119,
			"��" => 118,
			"�" => 117,
			"_���" => 117,
			"�," => 115,
			"��" => 115,
			"_��" => 115,
			"��_" => 114,
			"_�" => 114,
			"�,_" => 114,
			"_�" => 113,
			"_�" => 113,
			"��" => 112,
			"���" => 112,
			"���" => 110,
			"��" => 110,
			"_��" => 110,
			"��" => 109,
			"��" => 109,
			"���" => 109,
			"_���" => 109,
			"_��" => 108,
			"���" => 108,
			"��" => 107,
			"��" => 107,
			"��" => 107,
			"��" => 106,
			"�," => 106,
			"�" => 106,
			"��" => 105,
			"���" => 105,
			"��" => 105,
			"_��" => 105,
			"_���" => 104,
			"��" => 104,
			"��_" => 104,
			"��" => 104,
			"��" => 104,
			"�" => 103,
			"�,_" => 103,
			"��" => 103,
			"��" => 102,
			"���" => 102,
			"_��" => 102,
			"_�" => 101,
			"���" => 101,
			"��" => 101,
			"���" => 101,
			"�_" => 101,
			"���" => 100,
			"_�_" => 100,
			"�," => 100,
			"��_" => 100,
			"���" => 100,
			"��" => 100,
			"��" => 100,
			"��" => 100,
			"_�" => 100,
			"_�" => 99,
			"�," => 99,
			"��" => 99,
			"���" => 99,
			"��_" => 99,
			"_��" => 99,
			"��" => 98,
			"��" => 98,
			"_��" => 98,
			"���_" => 98,
			"�" => 97,
			"���" => 97,
			"��" => 97,
			"_�" => 97,
			"�,_" => 97,
			"��" => 96,
			"_��" => 96,
			"�,_" => 96,
			"��" => 96,
			"��" => 96,
			"��_" => 95,
			"��" => 95,
			"��" => 94,
			"��_" => 94,
			"_��" => 94,
			"��" => 94,
			"���" => 93,
			"��" => 93,
			"��_" => 92,
			"_���" => 91,
			"��" => 91,
			"��" => 90,
			"��" => 90,
			"_��" => 90,
			"?" => 90,
			"���" => 89,
			"���" => 89,
			"_�_" => 89,
			"��" => 89,
			"�," => 89,
			"��" => 88,
			"�" => 88,
			"_���" => 88,
			"��" => 88,
			"��" => 88,
			"��_" => 87,
			"���" => 87,
			"�,_" => 87,
			"��_" => 86,
			"�," => 86,
			"�,_" => 86,
			"���" => 86,
			"���" => 86,
			"���" => 86,
			"��" => 86,
			"�" => 86,
			"��" => 85,
			"��_" => 85,
			"���" => 85,
			"��" => 84,
			"_��_" => 84,
			"_���" => 84,
			"��" => 84,
			"_���" => 84,
			"_�" => 84,
			"�_" => 83,
			"��" => 83,
			"��" => 83,
			"�" => 83,
			"���" => 82,
			"��_" => 82,
			""" => 82,
			"���" => 82,
			"���_" => 81,
			"���" => 81,
			"���" => 81,
			"��" => 81,
			"��" => 81,
			"���" => 81,
			"���" => 81,
			"���" => 80,
			"��_" => 79,
			"���" => 79,
			"���" => 79,
			"����" => 79,
			"���" => 79,
			"���" => 79,
			"���" => 79,
			"_��" => 78,
			"���" => 78,
			"_��" => 78,
			"�." => 77,
			"_��" => 77,
			"�._" => 76,
			"�." => 76,
			"_��" => 76,
			"��" => 76,
			"��" => 76,
			"��" => 76,
			"���" => 76,
			"�," => 75,
			"�" => 75,
			"��_" => 75,
			"�,_" => 75,
			"��_" => 75,
			"��" => 74,
			"_��" => 74,
			"���" => 74,
			"��" => 74,
			"��" => 74
		);
	}
} // END OF LanguageMap_ru_CP1251

?>

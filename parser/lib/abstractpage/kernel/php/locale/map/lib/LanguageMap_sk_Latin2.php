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
 
class LanguageMap_sk_Latin2 extends LanguageMap
{
	/**
	 * Constructor
	 */
	function LanguageMap_sk_Latin2()
	{
		$this->language = "sk";
		$this->charset  = "latin2";
		
		$this->_populate();
	}
	
	
	// private methods
	
	function _populate()
	{
		$this->map = array(
			".." => 3100,
			"..." => 3003,
			"sam" => 2889,
			"a�_na" => 2784,
			"oku_19" => 2682,
			"a_" => 1672,
			"_p" => 1352,
			"_s" => 1211,
			"e_" => 1188,
			"kia�" => 1149,
			"st" => 1041,
			"ov" => 1025,
			",_" => 881,
			"o_" => 860,
			"pr" => 839,
			"ch" => 821,
			"a_m" => 813,
			"_a" => 808,
			"po" => 804,
			"ie" => 795,
			"_pr" => 769,
			"to" => 754,
			"re" => 741,
			"no" => 714,
			"ro" => 713,
			"._" => 708,
			"ni" => 704,
			"os" => 689,
			"_k" => 681,
			"_n" => 681,
			"od" => 660,
			"va" => 651,
			"as" => 631,
			"ne" => 617,
			"i_" => 612,
			"ho" => 604,
			"na" => 600,
			"_po" => 600,
			"ko" => 599,
			"_o" => 587,
			"or" => 582,
			"_z" => 578,
			"ra" => 574,
			"an" => 557,
			"e_rok" => 557,
			"v_" => 555,
			"ka" => 553,
			"_t" => 546,
			"ti" => 536,
			"rov" => 530,
			"ia" => 525,
			"u_" => 524,
			"ch_" => 499,
			"do" => 498,
			"m_" => 494,
			"om" => 494,
			"a_p" => 486,
			"y_" => 478,
			"svedom" => 475,
			"te" => 474,
			"je" => 468,
			"ej" => 461,
			"_m" => 454,
			"sk" => 452,
			"_d" => 446,
			"le" => 442,
			"_na" => 442,
			"ed" => 428,
			"ci" => 427,
			"in" => 421,
			"vo" => 420,
			"_�" => 414,
			"nie_v_" => 404,
			"ri" => 404,
			"tr" => 404,
			"at" => 398,
			"ej_" => 389,
			"om_" => 385,
			"j_" => 383,
			"_j" => 383,
			"la" => 377,
			"�_" => 372,
			"li" => 362,
			"ve" => 361,
			"er" => 357,
			"ost" => 355,
			"me" => 353,
			"ie_" => 347,
			"ova" => 346,
			"a_o" => 338,
			"mi" => 337,
			"kt" => 336,
			"ta" => 336,
			"sa" => 331,
			"a_n" => 330,
			"tis" => 328,
			"al" => 327,
			"dn" => 326,
			"de" => 324,
			"aj" => 323,
			"u_t" => 318,
			"�_" => 318,
			"_v_" => 317,
			"ad" => 316,
			"a_nie_" => 314,
			"_spol" => 312,
			"a�" => 310,
			"akt" => 310,
			"vy" => 309,
			"ic" => 308,
			"ame" => 305,
			"av" => 305,
			"vi" => 303,
			"_b" => 303,
			"�ch_" => 296,
			"ti_vl�" => 295,
			"na_" => 294,
			"sti" => 294,
			"lo" => 293,
			"ak" => 292,
			"prot" => 291,
			"_je" => 290,
			"ot" => 289,
			"is" => 288,
			"o_by_a" => 288,
			"n�" => 288,
			"ol" => 286,
			"naj" => 284,
			"ca" => 283,
			"di" => 282,
			"it" => 282,
			"n�" => 281,
			"a_s" => 280,
			"dporov" => 279,
			"_sa" => 277,
			"uj" => 275,
			"dou" => 274,
			"ia_" => 271,
			"n�" => 269,
			"a_v" => 269,
			"ky" => 269,
			"�_" => 268,
			"niero" => 265,
			"ac" => 265,
			"eb" => 264,
			"pre" => 260,
			"on" => 259,
			"nej_" => 256,
			"eni" => 251,
			"_i" => 249,
			"_jed" => 247,
			"ol," => 247,
			"e�" => 245,
			"sa_" => 245,
			"tu" => 243,
			"na_ne" => 243,
			"rieme" => 241,
			"r�" => 237,
			"uzy_v" => 236,
			"nie" => 232,
			"�c" => 231,
			"E�I" => 231,
			"nie_z_" => 230,
			"�_" => 230,
			"tor" => 230,
			"_na_" => 229,
			"_f" => 228,
			"Doko" => 228,
			"uvie" => 226,
			"_are" => 226,
			"l�" => 225,
			"e," => 224,
			"oh" => 224,
			"mo" => 224,
			"ani" => 221,
			"_pod" => 220,
			"pod" => 219,
			"k_ori" => 219,
			",_ktor" => 218,
			"roti" => 217,
			"os�b." => 215,
			"nica_z" => 215,
			"pri_e" => 214,
			"_u" => 213,
			"pro" => 213,
			"am" => 211,
			"_s�_" => 210,
			"_ro" => 210,
			"_sa_" => 210,
			"oz" => 210,
			"epou" => 206,
			"�en" => 206,
			"ono" => 206,
			"rgan" => 205,
			"iar" => 203,
			"_ob" => 203,
			"to_" => 202,
			"ktor" => 201,
			"za" => 201,
			"_h" => 201,
			"robl" => 201,
			"ud" => 200,
			"kto" => 200,
			"tn" => 198,
			"not" => 198,
			"any)_-" => 198,
			"ar" => 197,
			"zu_" => 197,
			"_pri" => 197,
			".._" => 195,
			"_s�" => 193,
			"oc" => 193,
			"sl" => 192,
			"to_�e" => 192,
			"u�" => 190,
			"so" => 190,
			"_vy" => 190,
			"�v" => 189,
			"ska" => 187,
			"_ko" => 187,
			"pri" => 187,
			"red" => 187,
			"_i_f" => 186,
			"uk" => 186,
			"ti_" => 186,
			"d�_" => 185,
			"hod" => 185,
			"e_s" => 185,
			"ke" => 183,
			"si" => 182,
			"�e" => 182,
			"osti" => 182,
			"ou" => 181,
			"kon" => 181,
			"_to" => 181,
			"re_" => 180,
			"tis�co" => 179,
			"ny" => 179,
			"0-tis" => 178,
			"nika" => 178,
			"kov" => 178,
			"_c" => 177,
			"t�" => 177,
			"_(" => 177,
			"bo" => 176,
			"_orga" => 176,
			"a_m�_z" => 176,
			"rn" => 175,
			"v_p" => 175,
			"ier" => 174,
			"ym._Pr" => 174,
			"_v�" => 173,
			"re_s" => 172,
			"�_a_po" => 172,
			"nie_" => 171,
			"_vl�" => 171,
			"i_fak" => 171,
			"funk�" => 171,
			"k_" => 170,
			"�ho" => 170,
			"_�al" => 170,
			"a," => 169,
			"�_" => 169,
			"aniz�c" => 169,
			"n�_" => 169,
			"e_p" => 168,
			"lic" => 168,
			"ny_" => 168,
			"ent" => 167,
			"_ne" => 167,
			"_si" => 167,
			"dej_pr" => 167,
			"by" => 167,
			"s_" => 167,
			"sti_" => 166,
			"�n" => 166,
			"_s_" => 166,
			"okon�" => 165,
			"est" => 165,
			"uje_" => 165,
			"j_cirk" => 164,
			"ajm�_" => 164,
			"us" => 164,
			"vn" => 163,
			"uje" => 163,
			"ovi" => 163,
			"iv" => 163,
			"k�" => 162,
			"n�ch" => 162,
			"ovan" => 162,
			"�t" => 161,
			"ca_z" => 161,
			",_z�sk" => 160,
			"ganizo" => 160,
			"sv" => 159,
			"�k" => 158,
			"ba" => 158,
			"e_v" => 156,
			"oli_a_" => 156,
			"oj" => 156,
			"os�" => 156,
			"�kov," => 156,
			"1._�" => 155,
			"ick" => 155,
			"_ak" => 155,
			"a�" => 154,
			"ty" => 153,
			"da" => 153,
			"nov._P" => 153,
			"_kto" => 153,
			"_orni" => 153,
			"ozrej" => 153,
			"kr" => 152,
			"_ve" => 152,
			"orova" => 152,
			"spo" => 151,
			"ev" => 151,
			"ik" => 151,
			"i_fa" => 151,
			"ujem" => 150,
			"ur" => 150,
			"z�" => 150,
			"k�_" => 150,
			"_." => 150,
			"anie" => 149,
			"�i" => 149,
			"�l" => 149,
			"_zosta" => 148,
			"_Sledo" => 148,
			"zu_do" => 148,
			"�_OB�" => 148,
			"co" => 148,
			"ku)," => 148,
			"tre" => 148,
			"_st" => 147,
			"ru" => 147,
			"edn" => 147,
			"ne_" => 146,
			"_sp" => 146,
			"yst�m" => 146,
			"�it" => 146,
			"lad" => 146,
			"�o_by" => 146,
			"enie" => 146,
			"zo" => 146,
			"op" => 145,
			"ali" => 144,
			"rd" => 144,
			"a�_" => 144,
			"_re" => 144,
			"pr�le�" => 144,
			"ah" => 143,
			"ej_V._" => 143,
			"._P" => 143,
			"nosti" => 143,
			"o_s" => 143,
			"odn" => 143,
			"ovan�" => 142,
			"tra" => 142,
			"_S" => 142,
			"Naspat" => 142,
			"�_v" => 141,
			"_SR_" => 141,
			"nk" => 140,
			"�t,_z�" => 140,
			"�ho_" => 140,
			"t�tu" => 139,
			"..,_z�" => 139,
			",_a" => 139,
			"nia_" => 139,
			"zv" => 139,
			"iz" => 139,
			"ten�m" => 138,
			"hran" => 138,
			"vl�de" => 138,
			"ate" => 138,
			"ni�," => 138,
			"pr�" => 138,
			"r�" => 138,
			"skav" => 138,
			"ty_" => 138,
			"vu_." => 137,
			"s�" => 137,
			"�n" => 137,
			"zosta" => 137,
			"_z�" => 137,
			"�_a" => 137,
			"_V" => 136,
			"ton" => 136,
			"o_i_" => 136,
			"ale" => 135,
			"�mov�m" => 135,
			"�_pred" => 135,
			"�skava" => 134,
			"ez" => 134,
			"m�" => 134,
			"_�e_p" => 134,
			"e�ov_r" => 134,
			"o_v" => 133,
			"ktor�c" => 133,
			"postoj" => 133,
			"ou_a" => 132,
			"osti_" => 132,
			"a_pr" => 131,
			"v._" => 131,
			"nc" => 131,
			"de_" => 131,
			"last" => 131,
			"_z_" => 131,
			"las" => 131,
			"lu" => 131,
			"y_kom" => 131,
			"l_" => 131,
			"bl" => 131,
			"nn" => 131,
			"tu,"_" => 131,
			"dal_d" => 130,
			"a,_" => 130,
			"vi,_" => 130,
			"_najm�" => 129,
			"v�_ka" => 129,
			"treb" => 129,
			"kl" => 129,
			"dob" => 129,
			"_�t�t" => 129,
			"v_pr" => 129,
			"n_" => 129,
			"ako" => 129,
			",_kto" => 129,
			"pri_ka" => 128,
			"stvu_." => 128,
			",_p" => 128,
			"ierov" => 128,
			"_z_Bar" => 127,
			"te�" => 127,
			"ania" => 127,
			"cu" => 127,
			"je_pr" => 127,
			"m_p" => 127,
			"_�t" => 127,
			"mozre" => 126,
			"�_a_" => 126,
			"adenia" => 126,
			"dom" => 126,
			"_1" => 126,
			"tk" => 126,
			"_�" => 126,
			"ko_" => 125,
			"bud" => 125,
			"enu,_" => 125,
			"eri" => 125,
			"_a_s" => 125,
			"eg" => 125,
			"vani" => 125,
			"roz" => 124,
			"_Hau" => 124,
			"_ma" => 124,
			"a_na" => 124,
			"(Dokon" => 123,
			"_..._s" => 123,
			"_jedn" => 123,
			"ch_odb" => 123,
			"ahli_" => 123,
			"polnoh" => 123,
			"o_n" => 123,
			"_P" => 122,
			"riemer" => 122,
			"�pen" => 122,
			"�_..." => 122,
			"pl" => 121,
			"-_" => 121,
			"dp" => 121,
			"men" => 121,
			"ky_" => 121,
			"aru" => 120,
			"r�" => 120,
			"ude" => 120,
			"nos�" => 120,
			"ick�" => 120,
			"dov�et" => 120,
			"�i" => 120,
			"�k" => 119,
			"stv" => 119,
			"e�" => 119,
			"s�_" => 119,
			"okova�" => 119,
			"_.." => 119,
			"a_z" => 118,
			"_naj" => 118,
			"ud�_" => 118,
			"a_po" => 118,
			"i_5100" => 118,
			"slanc" => 118,
			"_ju_" => 118,
			"u,_" => 118,
			"nos�_" => 118,
			"rie" => 117,
			"tl" => 117,
			"str" => 117,
			"00_hek" => 117
		);
	}
} // END OF LanguageMap_sk_Latin2

?>

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


/**
 * @package locale_map_lib
 */
class LanguageMap extends PEAR
{
	/**
	 * @access public
	 */
	var $language = "";
	
	/**
	 * @access public
	 */
	var $charset  = "";

	/**
	 * @access public
	 */	
	var $map = array();
	
	
	/**
	 * @access public
	 * @static
	 */
	function &factory( $language = "", $charset = "" )
	{
		$class = LanguageMap::_getClass( $language, $charset );
		
		if ( PEAR::isError( $class ) )
			return $class;
			
		using( 'locale.map.lib.'. $class );
			
		if ( class_registered( $class ) )
			return new $class;
		else
			return PEAR::raiseError( "Unable to load driver." );
	}
	
	/**
	 * @access public
	 */	
	function getLanguage()
	{
		return $this->language;
	}
	
	/**
	 * @access public
	 */
	function getCharset()
	{
		return $this->charset;
	}
	
	
	// private methods
	
	/**
	 * @access private
	 * @static
	 */
	function _getClass( $language = "", $charset = "" )
	{
		static $avail_languages;
		$avail_languages = array(
			"af" => array(
				"latin1"			=> "LanguageMap_af_Latin1",
				"latin1_bible"		=> "LanguageMap_af_Latin1Bible"
			),
			"ar" => array(
				"arabic"			=> "LanguageMap_ar_Arabic",
				"arabic_quran"		=> "LanguageMap_ar_ArabicQuran",
				"cp1256"			=> "LanguageMap_ar_CP1256",
				"cp1256_quran"		=> "LanguageMap_ar_CP1256Quran"
			),
			"az" => array(
				"utf8"				=> "LanguageMap_az_UTF8"
			),
			"be" => array(
				"cp1251"			=> "LanguageMap_be_CP1251"
			),
			"bg" => array(
				"cp1251"			=> "LanguageMap_bg_CP1251",
				"cp1251_bible"		=> "LanguageMap_bg_CP1251Bible",
				"cyrillic"			=> "LanguageMap_bg_Cyrillic",
				"cyrillic_bible"	=> "LanguageMap_bg_CyrillicBible"
			),
			"br" => array(
				"latin1"			=> "LanguageMap_br_Latin1"
			),
			"bs" => array(
				"ascii"				=> "LanguageMap_bs_ASCII",
				"cp1250"			=> "LanguageMap_bs_CP1250",
				"latin2"			=> "LanguageMap_bs_Latin2"
			),
			"ca" => array(
				"latin1"			=> "LanguageMap_ca_Latin1",
				"latin1_lit"		=> "LanguageMap_ca_Latin1Lit"
			),
			"cs" => array(
				"cp1250"			=> "LanguageMap_cs_CP1250",
				"latin2"			=> "LanguageMap_cs_Latin2"
			),
			"cy" => array(
				"latin1"			=> "LanguageMap_cy_Latin1"
			),
			"da" => array(
				"latin1"			=> "LanguageMap_da_Latin1",
				"latin1_bible"		=> "LanguageMap_da_Latin1Bible"
			),
			"de" => array(	
				"latin1"			=> "LanguageMap_de_Latin1",
				"latin1_bible"		=> "LanguageMap_de_Latin1Bible"
			),
			"el" => array(
				"cp1253"			=> "LanguageMap_el_CP1253",
				"greek"				=> "LanguageMap_el_Greek"
			),
			"en" => array(
				"ascii"				=> "LanguageMap_en_ASCII"
			),
			"eo" => array(
				"latin1_h"			=> "LanguageMap_eo_Latin1H",
				"latin1_x"			=> "LanguageMap_eo_Latin1X",
				"latin3"			=> "LanguageMap_eo_Latin3",
				"latin3_as"			=> "LanguageMap_eo_Latin3AS",
				"utf8"				=> "LanguageMap_eo_UTF8AS"
			),
			"es" => array(
				"latin1"			=> "LanguageMap_es_Latin1",
				"latin1_bible"		=> "LanguageMap_es_Latin1Bible"
			),
			"et" => array(
				"cp1257"			=> "LanguageMap_et_CP1257",
				"latin4"			=> "LanguageMap_et_Latin4"
			),
			"eu" => array(
				"latin1"			=> "LanguageMap_eu_Latin1"
			),
			"fi" => array(
				"latin1"			=> "LanguageMap_fi_Latin1"
			),
			"fr" => array(
				"latin1"			=> "LanguageMap_fr_Latin1",
				"latin1_bible"		=> "LanguageMap_fr_Latin1Bible"
			),
			"ga" => array(
				"latin1"			=> "LanguageMap_ga_Latin1",
				"latin1_lit"		=> "LanguageMap_ga_Latin1Lit"
			),
			"he" => array(
				"hebrew"			=> "LanguageMap_he_Hebrew"
			),
			"hr" => array(
				"ascii"				=> "LanguageMap_hr_ASCII",
				"cp1250"			=> "LanguageMap_hr_CP1250",
				"cp1250_bible"		=> "LanguageMap_hr_CP1250Bible",
				"latin2"			=> "LanguageMap_hr_Latin2"
			),
			"hu" => array(
				"cp1250"			=> "LanguageMap_hu_CP1250",
				"latin2"			=> "LanguageMap_hu_Latin2"
			),
			"hy" => array(
				"armscii-8"			=> "LanguageMap_hy_ARMSCII8"
			),
			"is" => array(
				"latin1"			=> "LanguageMap_is_Latin1"
			),
			"it" => array(
				"latin1"			=> "LanguageMap_it_Latin1"
			),
			"la" => array(
				"ascii"				=> "LanguageMap_la_ASCII"
			),
			"lt" => array(
				"cp1257"			=> "LanguageMap_lt_CP1257",
				"latin4"			=> "LanguageMap_lt_Latin4"
			),
			"lv" => array(
				"cp1257"			=> "LanguageMap_lv_CP1257",
				"latin4"			=> "LanguageMap_lv_Latin4"
			),
			"nl" => array(
				"latin1"			=> "LanguageMap_nl_Latin1",
				"latin1_bible"		=> "LanguageMap_nl_Latin1Bible"
			),
			"no" => array(
				"latin1"			=> "LanguageMap_no_Latin1"
			),
			"pl" => array(
				"latin2"			=> "LanguageMap_pl_Latin2"
			),
			"pt" => array(
				"latin1"			=> "LanguageMap_pt_Latin1"
			),
			"pt_br" => array(
				"latin1"			=> "LanguageMap_pt_BR_Latin1"
			),
			"ro" => array(
				"ascii"				=> "LanguageMap_ro_ASCII",
				"cp1250"			=> "LanguageMap_ro_CP1250",
				"latin2"			=> "LanguageMap_ro_Latin2"
			),
			"ru" => array(
				"cp866"				=> "LanguageMap_ru_CP866",
				"cp866_lit"			=> "LanguageMap_ru_CP866Lit",
				"cp1251"			=> "LanguageMap_ru_CP1251",
				"cp1251_lit"		=> "LanguageMap_ru_CP1251Lit",
				"cyrillic"			=> "LanguageMap_ru_Cyrillic",
				"cyrillic_lit"		=> "LanguageMap_ru_CyrillicLit",
				"koi8-r"			=> "LanguageMap_ru_KOI8R",
				"koi8-r_lit"		=> "LanguageMap_ru_KOI8RLit",
				"maccyr"			=> "LanguageMap_ru_MacCyr",
				"maccyr_lit"		=> "LanguageMap_ru_MacCyrLit",
				"utf8_lit"			=> "LanguageMap_ru_UTF8Lit"
			),
			"sk" => array(
				"ascii"				=> "LanguageMap_sk_ASCII",
				"cp1250"			=> "LanguageMap_sk_CP1250",
				"latin2"			=> "LanguageMap_sk_Latin2"
			),
			"sl" => array(
				"ascii"				=> "LanguageMap_sl_ASCII",
				"cp1250"			=> "LanguageMap_sl_CP1250",
				"latin2"			=> "LanguageMap_sl_Latin2"
			),
			"sq" => array(
				"latin1"			=> "LanguageMap_sq_Latin1"
			),
			"sr" => array(
				"cp1250"			=> "LanguageMap_sr_CP1250",
				"latin2"			=> "LanguageMap_sr_Latin2"
			),
			"sv" => array(
				"latin1"			=> "LanguageMap_sv_Latin1",
				"latin1_bible"		=> "LanguageMap_sv_Latin1Bible"
			),
			"sw" => array(
				"latin1"			=> "LanguageMap_sw_Latin1"
			),
			"ta" => array(
				"tscii"				=> "LanguageMap_ta_TSCII"
			),
			"th" => array(
				"cp874"				=> "LanguageMap_th_CP874"
			),
			"tl" => array(
				"ascii"				=> "LanguageMap_tl_ASCII",
				"ascii_bible"		=> "LanguageMap_tl_ASCIIBible"
			),
			"tr" => array(
				"cp857"				=> "LanguageMap_tr_CP857",
				"cp1254"			=> "LanguageMap_tr_CP1254",
				"latin5"			=> "LanguageMap_tr_Latin5"
			),
			"ua" => array(
				"cp1251"			=> "LanguageMap_ua_CP1251",
				"koi8-u"			=> "LanguageMap_ua_KOI8U"
			),
			"vi" => array(
				"viscii"			=> "LanguageMap_vi_VISCII"
			),
			"zh" => array(
				"gb2312"			=> "LanguageMap_zh_GB2312"
			)
		);

		return ( $avail_languages[$language][$charset]?
			$avail_languages[$language][$charset] : 
			PEAR::raiseError( "Map not available." )
		);
	}
} // END OF LanguageMap

?>

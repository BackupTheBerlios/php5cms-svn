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
 * @package locale
 */
 
/**
 * Constructor
 *
 * @access public
 */
ISO639 = function()
{
	this.Base = Base;
	this.Base();
};


ISO639.prototype = new Base();
ISO639.prototype.constructor = ISO639;
ISO639.superclass = Base.prototype;

/**
 * @access public
 */
ISO639.prototype.finalize = function()
{
	// free some memory
	ISO639.codes = 0;
};

/**
 * @access public
 */
ISO639.prototype.hasCode = function( code )
{
	if ( eval( "ISO639.codes._" + code ) )
		return true;
	else
		return false;
};

/**
 * @access public
 */
ISO639.prototype.resolve = function( code )
{
	if ( !this.hasCode( code ) )
		return false

	return eval( "ISO639.codes._" + code );
};


/**
 * Note: in (Indonesian) is a reserved word,
 * so we have to built a workaround...
 *
 * @access public
 * @static
 */
ISO639.codes =
{
	_aa: "Afar",
	_ab: "Abkhazian",
	_af: "Afrikaans",
	_am: "Amharic",
	_ar: "Arabic",
	_as: "Assamese",
	_ay: "Aymara",
	_az: "Azerbaijani",
	_ba: "Bashkir",
	_be: "Byelorussian",
	_bg: "Bulgarian",
	_bh: "Bihari",
	_bi: "Bislama",
	_bn: "Bengali; Bangla",
	_bo: "Tibetan",
	_br: "Breton",
	_ca: "Catalan",
	_co: "Corsican",
	_cs: "Czech",
	_cy: "Welsh",
	_da: "Danish",
	_de: "German",
	_dz: "Bhutani",
	_el: "Greek",
	_en: "English",
	_eo: "Esperanto",
	_es: "Spanish",
	_et: "Estonian",
	_eu: "Basque",
	_fa: "Persian",
	_fi: "Finnish",
	_fj: "Fiji",
	_fo: "Faeroese",
	_fr: "French",
	_fy: "Frisian",
	_ga: "Irish",
	_gd: "Scots Gaelic",
	_gl: "Galician",
	_gn: "Guarani",
	_gu: "Gujarati",
	_ha: "Hausa",
	_hi: "Hindi",
	_hr: "Croatian",
	_hu: "Hungarian",
	_hy: "Armenian",
	_ia: "Interlingua",
	_ie: "Interlingue",
	_ik: "Inupiak",
	_in: "Indonesian",
	_is: "Icelandic",
	_it: "Italian",
	_iw: "Hebrew",
	_ja: "Japanese",
	_ji: "Yiddish",
	_jw: "Javanese",
	_ka: "Georgian",
	_kk: "Kazakh",
	_kl: "Greenlandic",
	_km: "Cambodian",
	_kn: "Kannada",
	_ko: "Korean",
	_ks: "Kashmiri",
	_ku: "Kurdish",
	_ky: "Kirghiz",
	_la: "Latin",
	_ln: "Lingala",
	_lo: "Laothian",
	_lt: "Lithuanian",
	_lv: "Latvian, Lettish",
	_mg: "Malagasy",
	_mi: "Maori",
	_mk: "Macedonian",
	_ml: "Malayalam",
	_mn: "Mongolian",
	_mo: "Moldavian",
	_mr: "Marathi",
	_ms: "Malay",
	_mt: "Maltese",
	_my: "Burmese",
	_na: "Nauru",
	_ne: "Nepali",
	_nl: "Dutch",
	_no: "Norwegian",
	_oc: "Occitan",
	_om: "(Afan) Oromo",
	_or: "Oriya",
	_pa: "Punjabi",
	_pl: "Polish",
	_ps: "Pashto, Pushto",
	_pt: "Portuguese",
	_qu: "Quechua",
	_rm: "Rhaeto-Romance",
	_rn: "Kirundi",
	_ro: "Romanian",
	_ru: "Russian",
	_rw: "Kinyarwanda",
	_sa: "Sanskrit",
	_sd: "Sindhi",
	_sg: "Sangro",
	_sh: "Serbo-Croatian",
	_si: "Singhalese",
	_sk: "Slovak",
	_sl: "Slovenian",
	_sm: "Samoan",
	_sn: "Shona",
	_so: "Somali",
	_sq: "Albanian",
	_sr: "Serbian",
	_ss: "Siswati",
	_st: "Sesotho",
	_su: "Sundanese",
	_sv: "Swedish",
	_sw: "Swahili",
	_ta: "Tamil",
	_te: "Tegulu",
	_tg: "Tajik",
	_th: "Thai",
	_ti: "Tigrinya",
	_tk: "Turkmen",
	_tl: "Tagalog",
	_tn: "Setswana",
	_to: "Tonga",
	_tr: "Turkish",
	_ts: "Tsonga",
	_tt: "Tatar",
	_tw: "Twi",
	_uk: "Ukrainian",
	_ur: "Urdu",
	_uz: "Uzbek",
	_vi: "Vietnamese",
	_vo: "Volapuk",
	_wo: "Wolof",
	_xh: "Xhosa",
	_yo: "Yoruba",
	_zh: "Chinese",
	_zu: "Zulu"
};
